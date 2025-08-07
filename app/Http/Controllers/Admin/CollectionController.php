<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\Category;
use Str;

class CollectionController extends Controller
{
    public function index()
    {
        $collections = Collection::with('category')->latest()->paginate(10);
        return view('admin.collections.index', compact('collections'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.collections.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        $collection = new Collection();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();

            $destinationPath = public_path('images/posts');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $collection->image = 'images/posts/' . $filename;
        }

        $collection->slug = Str::slug($request->title);
        $collection->title = $request->title;
        $collection->description = $request->description;
        $collection->category_id = $request->category_id;
        $collection->save();

        return redirect()->route('collections.index')->with('success', 'Tuyển tập đã được tạo.');
    }

    public function edit(Collection $collection)
    {
        $categories = Category::all();
        return view('admin.collections.edit', compact('collection', 'categories'));
    }

    public function update(Request $request, Collection $collection)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();

            $destinationPath = public_path('images/posts');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $collection->image = 'images/posts/' . $filename; // hoặc $collection->image nếu ở controller Collection
        }

        $collection->slug = Str::slug($request->title);
        $collection->update($request->all());

        return redirect()->route('collections.index')->with('success', 'Tuyển tập đã được cập nhật.');
    }

    public function destroy(Collection $collection)
    {
        $collection->delete();

        return redirect()->route('admin.collections.index')->with('success', 'Tuyển tập đã bị xóa.');
    }
}
