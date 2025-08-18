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
        $registrationId = session('studentId');

        // Điều kiện chung
        $query = DB::table('views')
            ->where('model_type', $model)
            ->where('model_id', $id);

        if ($registrationId) {
            $query->where('registration_id', $registrationId);
        } else {
            $query->where('ip_address', $ip);
        }

        $existingView = $query->first();

        if (!$existingView) {
            DB::table('views')->insert([
                'model_type'      => $model,
                'model_id'        => $id,
                'ip_address'      => $ip,
                'user_agent'      => $request->userAgent(),
                'registration_id' => $registrationId,
                'created_at'      => now(),
                'updated_at'      => now()
            ]);
        } else {
            $lastUpdated = \Carbon\Carbon::parse($existingView->updated_at);
            if ($lastUpdated->lt($twentyFourHoursAgo)) {
                $query->update(['updated_at' => now()]);
            }
        }
        
        $liked = DB::table('favorites')
            ->where('registration_id', $registrationId)
            ->where('model_type', $model)
            ->where('model_id', $id)
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
        $registrationId = session('studentId');

        // Điều kiện chung
        $query = DB::table('views')
            ->where('model_type', $model)
            ->where('model_id', $id);

        if ($registrationId) {
            $query->where('registration_id', $registrationId);
        } else {
            $query->where('ip_address', $ip);
        }

        $existingView = $query->first();

        if (!$existingView) {
            DB::table('views')->insert([
                'model_type'      => $model,
                'model_id'        => $id,
                'ip_address'      => $ip,
                'user_agent'      => $request->userAgent(),
                'registration_id' => $registrationId,
                'created_at'      => now(),
                'updated_at'      => now()
            ]);
        } else {
            $lastUpdated = \Carbon\Carbon::parse($existingView->updated_at);
            if ($lastUpdated->lt($twentyFourHoursAgo)) {
                $query->update(['updated_at' => now()]);
            }
        }

        $liked = DB::table('favorites')
            ->where('registration_id', $registrationId)
            ->where('model_type', $model)
            ->where('model_id', $id)
            ->exists();

        $comments = $post->commentsPaginate(5);

        return view('home.posts.show', compact('post', 'liked', 'comments'));
    }
}

