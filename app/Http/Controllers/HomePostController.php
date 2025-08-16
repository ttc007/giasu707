<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use App\Models\Collection;
use DB;

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

    public function collection($slug, Request $request)
    {
        $collection = Collection::where('slug', $slug)->firstOrFail();
        $posts = Post::where('collection_id', $collection->id)->paginate(12);

        $ip = $request->ip();
        $model = 'Collection';
        $twentyFourHoursAgo = now()->subHours(24);
        $id = $collection->id;

        $existingView = DB::table('views')
            ->where('model_type', $model)
            ->where('model_id', $id)
            ->where('ip_address', $ip)
            ->first();

        if (!$existingView) {
            DB::table('views')->insert([
                'model_type' => $model,
                'model_id'   => $id,
                'ip_address' => $ip,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $lastUpdated = \Carbon\Carbon::parse($existingView->updated_at);
            if ($lastUpdated->lt($twentyFourHoursAgo)) {
                DB::table('views')
                    ->where('model_type', $model)
                    ->where('model_id', $id)
                    ->where('ip_address', $ip)
                    ->update(['updated_at' => now()]);
            }
        }

        $ip = $request->ip();
        $modelClass = 'Collection';
        $model_id = $collection->id;
        
        $liked = DB::table('favorites')
            ->where('ip_address', $ip)
            ->where('model_type', $modelClass)
            ->where('model_id', $model_id)
            ->exists();

        return view('home.posts.collection', compact('collection', 'posts', 'liked'));
    }

    public function show($slug, $post_slug, Request $request)
    {
        $post = Post::with('category', 'collection')->where('slug', $post_slug)->firstOrFail();

        $ip = $request->ip();
        $model = 'Post';
        $twentyFourHoursAgo = now()->subHours(24);
        $id = $post->id;

        $existingView = DB::table('views')
            ->where('model_type', $model)
            ->where('model_id', $id)
            ->where('ip_address', $ip)
            ->first();

        if (!$existingView) {
            DB::table('views')->insert([
                'model_type' => $model,
                'model_id'   => $id,
                'ip_address' => $ip,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $lastUpdated = \Carbon\Carbon::parse($existingView->updated_at);
            if ($lastUpdated->lt($twentyFourHoursAgo)) {
                DB::table('views')
                    ->where('model_type', $model)
                    ->where('model_id', $id)
                    ->where('ip_address', $ip)
                    ->update(['updated_at' => now()]);
            }
        }

        $ip = $request->ip();
        $modelClass = 'Post';
        $model_id = $post->id;
        
        $liked = DB::table('favorites')
            ->where('ip_address', $ip)
            ->where('model_type', $modelClass)
            ->where('model_id', $model_id)
            ->exists();

        return view('home.posts.show', compact('post', 'liked'));
    }
}

