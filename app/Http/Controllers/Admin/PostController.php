<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Collection;
use Illuminate\Http\Request;
use Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['category', 'collection'])->latest()->paginate(10);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'category_id' => 'nullable|exists:categories,id',
            'collection_id' => 'nullable|exists:collections,id',
        ]);

        $post = new Post();
        $post->title = $request->title;
        $post->slug = Str::slug($request->title);
        $post->content = $request->content;
        $post->category_id = $request->category_id;
        $post->collection_id = $request->collection_id;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();

            $destinationPath = public_path('images/posts');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $post->image = 'images/posts/' . $filename; // hoặc $collection->image nếu ở controller Collection
        }

        $post->save();

        return redirect()->route('posts.index')->with('success', 'Post created!');
    }

    public function edit(Post $post)
    {
        $categories = Category::all();
        $collections = Collection::where('category_id', $post->category_id)->get();
        return view('admin.posts.edit', compact('post', 'categories', 'collections'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'category_id' => 'nullable|exists:categories,id',
            'collection_id' => 'nullable|exists:collections,id',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();

            $destinationPath = public_path('images/posts');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $post->image = 'images/posts/' . $filename; // hoặc $collection->image nếu ở controller Collection
        }

        $post->slug = Str::slug($request->title);
        $post->update($request->all());

        return redirect()->route('posts.index')->with('success', 'Post updated!');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Post deleted!');
    }
}
