<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BookVariation;

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
        ]);

        // Tìm book đã tồn tại
        $book = Book::where('image_chess', $validated['image_chess'])
                    ->where('color', $validated['color'])
                    ->first();

        if ($book) {
            // Nếu có thì update
            $book->update([
                'move'    => $validated['move'],
                'comment' => $validated['comment'] ?? $book->comment,
                'is_hidden' => 0
            ]);
        } else {
            // Nếu chưa có thì create
            $book = Book::create($validated);
        }

        // Nếu có parent thì lưu biến thể
        $book_var = null;
        if (!empty($validated['parent_image_chess']) && !empty($validated['pre_move'])) {
            $parent = Book::where('image_chess', $validated['parent_image_chess'])
                          ->where('color', $validated['color'])
                          ->first();

            if ($parent) {
                $book_var = BookVariation::where('book_id', $parent->id)
                    ->where('move', $validated['pre_move'])
                    ->first();

                if (!$book_var) {
                    $book_var = $parent->variations()->create([
                        'book_id' => $parent->id,
                        'move'    => $validated['pre_move'],
                    ]);
                } 
            }
        }

        // Chỉ update nếu book_var tồn tại
        if ($book_var) {
            $book->update([
                'book_variation_id' => $book_var->id
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => $book
        ], 201);
    }

    public function getOpeningFirstStep($opening_id, $step)
    {
        // Tìm bản ghi đầu tiên theo opening_id và step = 1
        $book = Book::where('opening_id', $opening_id)
                    ->where('step', $step)
                    ->where('is_hidden', 0)
                    ->first();

        if (!$book) {
            return response()->json([
                'success' => true,
                'message' => 'Không tìm thấy thế trận khởi đầu'
            ]);
        }

        $variations = BookVariation::join('books', 'books.book_variation_id', '=', 'book_variations.id')
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

    public function getBookFromVariation($id) {
        $book = Book::where('book_variation_id', $id)->where('is_hidden', 0)->first();
        
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thế trận khởi đầu'
            ], 404);
        }

        // Lấy các biến thể của book
        $variations = BookVariation::join('books', 'books.book_variation_id', '=', 'book_variations.id')
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

    public function getBookFromImage(Request $request) {
        $book = Book::where('image_chess', $request->image_chess)
                    ->where('color', $request->color)
                    ->where('is_hidden', 0)->first();
        
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thế trận khởi đầu'
            ], 200);
        }

        // Lấy các biến thể của book
        $variations = BookVariation::join('books', 'books.book_variation_id', '=', 'book_variations.id')
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
                    ->where('color', $request->color)->first();

        $book->update(['is_hidden' => 1]);
        
        return response()->json([
            'success' => true,
            'book' => $book
        ]);
    }
}
