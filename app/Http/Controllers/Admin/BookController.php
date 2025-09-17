<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BookVariation;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    public function index()
    {
        return view('admin.books.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'image_chess'       => 'required|string',
            'color'              => 'required|string|max:10', // "red" hoặc "green"
            'move'              => 'required|string',
            'comment'           => 'nullable|string|max:255',
            'opening_id'        => 'nullable|integer',
            'step'              => 'nullable|integer',
            'parent_image_chess'=> 'nullable|string',
            'pre_move'          => 'nullable|string',
            'parent_move'        => 'nullable|string', 
        ]);

        // Tìm book đã tồn tại
        $book = Book::where('image_chess', $validated['image_chess'])
                    ->where('color', $validated['color'])
                    ->where('move', $validated['move'])
                    ->where('is_hidden', 0)
                    ->first();

        if ($book) {
            // Nếu có thì update
            $book->update([
                'comment' => $validated['comment'] ?? $book->comment,
            ]);
        } else {
            $book = Book::where('image_chess', $validated['image_chess'])
                    ->where('color', $validated['color'])
                    ->where('move', $validated['move'])
                    ->where('is_hidden', 1)
                    ->first();
            if ($book) {
                $book->update([
                    'comment' => $validated['comment'] ?? $book->comment,
                    'is_hidden' => 0
                ]);
            } else {
                $book = Book::create($validated);
            }
        }

        // Nếu có parent thì lưu biến thể
        $book_var = null;
        if (!empty($validated['parent_image_chess']) && !empty($validated['pre_move'])) {
            $parent = Book::where('image_chess', $validated['parent_image_chess'])
                          ->where('color', $validated['color'])
                          ->where('move', $validated['parent_move'])
                          ->where('is_hidden', 0)
                          ->first();

            if ($parent) {
                 $book_var = BookVariation::where('book_id', $parent->id)
                    ->where('move', $validated['pre_move'])
                    ->where('book_des_id', $book->id)
                    ->first();

                if (!$book_var) {
                    $book_var = $parent->variations()->create([
                        'book_id' => $parent->id,
                        'move'    => $validated['pre_move'],
                        'book_des_id' => $book->id,
                    ]);
                } 
            }
        }

        return response()->json([
            'success' => true,
            'data'    => $book
        ], 201);
    }

    public function getOpeningFirstStep($opening_id, $step)
    {
        // Cache book
        $book = Cache::remember("book_{$opening_id}_{$step}", now()->addMinutes(60), function () use ($opening_id, $step) {
            return Book::where('opening_id', $opening_id)
                        ->where('step', $step)
                        ->where('is_hidden', 0)
                        ->first();
        });

        if (!$book) {
            return response()->json([
                'success' => true,
                'message' => 'Không tìm thấy thế trận khởi đầu'
            ]);
        }

        // Lấy variations: book -> variations -> book_des
        $variations = BookVariation::join('books', 'books.id', '=', 'book_variations.book_des_id')
                        ->where('book_variations.book_id', $book->id)
                        ->where('books.is_hidden', 0)
                        ->select('book_variations.*', 'books.is_hidden')
                        ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'book'       => $book,
                'variations' => $variations 
            ],
        ]);
    }

    public function getBookFromVariation($id) 
    {
        // Tìm book dựa trên book_des_id
        $book = Cache::remember("book_by_variation_{$id}", now()->addMinutes(60), function () use ($id) {
            return Book::join('book_variations', 'books.id', '=', 'book_variations.book_des_id')
                       ->where('book_variations.id', $id)
                       ->where('books.is_hidden', 0)
                       ->select('books.*')
                       ->first();
        });
        
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thế trận'
            ], 404);
        }

        // Lấy các biến thể tiếp theo của book
        $variations = BookVariation::join('books', 'books.id', '=', 'book_variations.book_des_id')
                        ->where('book_variations.book_id', $book->id)
                        ->where('books.is_hidden', 0)
                        ->select('book_variations.*', 'books.is_hidden')
                        ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'book'       => $book,
                'variations' => $variations 
            ],
        ]);
    }


    public function getBookFromImage(Request $request) {
        // $book = Cache::remember(
        //     "book_by_image_{$request->color}_" . md5($request->image_chess), 
        //     now()->addMinutes(60), 
        //     function () use ($request) {
        //         return Book::where('image_chess', $request->image_chess)
        //                    ->where('color', $request->color)
        //                    ->where('is_hidden', 0)
        //                    ->first();
        //     }
        // );

        $book = Book::where('image_chess', $request->image_chess)
                   ->where('color', $request->color)
                   ->where('is_hidden', 0)
                   ->first();
        
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thế trận khởi đầu'
            ], 200);
        }

        // Lấy các biến thể của book
        $variations = BookVariation::join('books', 'books.id', '=', 'book_variations.book_des_id')
                        ->where('book_variations.book_id', $book->id)
                        ->where('books.is_hidden', 0)
                        ->select('book_variations.*', 'books.is_hidden')
                        ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'book' => $book,
                'variations' => $variations 
            ],
        ]);
    }

    public function hidden(Request $request) {
        $book = Book::where('image_chess', $request->image_chess)
                    ->where('move', $request->move)
                    ->where('color', $request->color)
                    ->first();

        $book->update(['is_hidden' => 1]);
        
        return response()->json([
            'success' => true,
            'book' => $book
        ]);
    }
}
