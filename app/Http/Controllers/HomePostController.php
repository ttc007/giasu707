<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use App\Models\Collection;

class HomePostController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $collections = Collection::with('category') // nếu có quan hệ category
                                ->latest()
                                ->paginate(12); // mỗi trang 6 collection

        return view('home.posts.index', compact('categories', 'collections'));
    }

    public function category($slug)
    {
        $categories = Category::all();
        $category = Category::where('slug', $slug)->firstOrFail();

        $collections = Collection::with('category')
                                ->where('category_id', $category->id)
                                ->latest()
                                ->paginate(12);


        return view('home.posts.index', compact('categories', 'collections', 'category'));
    }

    public function collection($slug)
    {
        $collection = Collection::where('slug', $slug)->firstOrFail();
        $posts = Post::where('collection_id', $collection->id)->paginate(12);

        return view('home.posts.collection', compact('collection', 'posts'));
    }

    public function show($slug, $post_slug)
    {
        $post = Post::with('category', 'collection')->where('slug', $post_slug)->firstOrFail();

        return view('home.posts.show', compact('post'));
    }
}

