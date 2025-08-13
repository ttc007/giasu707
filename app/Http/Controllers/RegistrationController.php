<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;
use App\Models\Collection;
use DB;
use App\Models\Section;
use App\Models\Lesson;
use App\Models\Post;

class RegistrationController extends Controller
{
    public function index()
    {
        return view('registrations.index');
    }

    public function apiShow($client_id)
    {
        $registration = DB::table('registrations')
            ->where('client_id', $client_id)
            ->first();

        if (!$registration) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $registrationId = $registration->id;

        // Recent Views (10 view gần nhất)
        $recentViews = DB::table('views')
            ->where('registration_id', $registrationId)
            ->orderByDesc('created_at')
            ->limit(12)
            ->get()
            ->map(function($view) {
                $modelClass = $view->model_type;
                if ($modelClass == 'Section') {
                    $section = Section::with('lesson.chapter.subject')->find($view->model_id);
                    if (!$section) return null;

                    $url = route('show.section', [
                        'subject_slug' => $section->lesson->chapter->subject->slug,
                        'chapter_slug' => $section->lesson->chapter->slug,
                        'section_slug' => $section->slug,
                    ]);

                    return [
                        'id' => $section->id,
                        'title' => $section->title,
                        'slug' => $section->slug,
                        'image' => null,
                        'url' => $url,
                        'type' => 'Section',
                    ];
                } elseif ($modelClass == 'Lesson') {
                    $lesson = Lesson::with('chapter.subject')->find($view->model_id);
                    if (!$lesson) return null;

                    $url = route('show.lesson', [
                        'subject_slug' => $lesson->chapter->subject->slug,
                        'chapter_slug' => $lesson->chapter->slug,
                        'lesson_slug' => $lesson->slug,
                    ]);

                    return [
                        'id' => $lesson->id,
                        'title' => $lesson->title,
                        'slug' => $lesson->slug,
                        'image' => null,
                        'url' => $url,
                        'type' => 'Lesson',
                    ];
                } elseif ($modelClass == 'Collection') {
                    $collection = Collection::find($view->model_id);
                    if (!$collection) return null;

                    $url = route('home.collection', [
                        'slug' => $collection->slug,
                    ]);

                    return [
                        'id' => $collection->id,
                        'title' => $collection->title,
                        'slug' => $collection->slug,
                        'image' => $collection->image,
                        'url' => $url,
                        'type' => 'Collection',
                    ];
                } else {
                    $post = Post::with('collection')->find($view->model_id);
                    if (!$post) return null;

                    $url = route('home.post.show', [
                        'slug' => $post->collection->slug,
                        'post_slug' => $post->slug,
                    ]);

                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'slug' => $post->slug,
                        'image' => $post->image,
                        'url' => $url,
                        'type' => 'Post',
                    ];
                }

            })
            ->filter(); // bỏ null

        return response()->json([
            'id' => $registration->id,
            'name' => $registration->name,
            'email' => $registration->email,
            'phone' => $registration->phone,
            'subject' => $registration->subject,
            'note' => $registration->note,
            'recent_views' => $recentViews,
        ]);
    }

    public function create()
    {
        return view('registrations.create');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'email'   => 'nullable|email|max:255',
            'subject' => 'required|string|max:100',
            'note'    => 'nullable|string',
        ]);

        $registration = Registration::where('client_id', $request->client_id)->firstOrFail();

        $registration->update($request->only(['name', 'phone', 'email', 'subject', 'note']));

        return redirect()->route('registration.index')->with('success', 'Cập nhật thành công!');
    }

    public function store(Request $request)
    {
        $registration = Registration::create([
            'name' => 'Chưa cập nhật', // để trống, sẽ cập nhật sau
            'email' => 'Chưa cập nhật',
            'phone' => 'Chưa cập nhật',
            'subject' => 'Chưa cập nhật',
            'client_id' => uniqid('client_', true), // gen ID tạm
        ]);

        return response()->json([
            'client_id' => $registration->client_id,
        ]);
    }

    public function isFavorite(Request $request, $model, $model_id)
    {
        $client_id = $request->query('client_id');
        $registration = Registration::where('client_id', $client_id)->first();

        if (!$registration) {
            return response()->json(['liked' => false]);
        }

        // Lấy đối tượng theo slug và model
        $modelClass = ucfirst($model);
        $liked = DB::table('favorites')
            ->where('registration_id', $registration->id)
            ->where('model_type', $modelClass)
            ->where('model_id', $model_id)
            ->exists();

        return response()->json(['liked' => $liked]);
    }

    public function like(Request $request, $model, $model_id)
    {
        $client_id = $request->input('client_id');
        $registration = Registration::where('client_id', $client_id)->first();

        if (!$registration) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đăng ký'], 404);
        }

        $modelClass = ucfirst($model);
        // Kiểm tra tồn tại rồi mới insert
        $exists = DB::table('favorites')
            ->where('registration_id', $registration->id)
            ->where('model_type', $modelClass)
            ->where('model_id', $model_id)
            ->exists();

        if (!$exists) {
            DB::table('favorites')->insert([
                'registration_id' => $registration->id,
                'model_type' => $modelClass,
                'model_id' => $model_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function unlike(Request $request, $model, $model_id)
    {
        $client_id = $request->input('client_id');
        $registration = Registration::where('client_id', $client_id)->first();

        if (!$registration) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đăng ký'], 404);
        }

        $modelClass = ucfirst($model);
        DB::table('favorites')
            ->where('registration_id', $registration->id)
            ->where('model_type', $modelClass)
            ->where('model_id', $model_id)
            ->delete();

        return response()->json(['success' => true]);
    }

    public function view(Request $request, $model)
    {
        $client_id = $request->input('client_id');
        $registration = Registration::where('client_id', $client_id)->first();
        
        $model_id = $request->input("model_id");

        if (!$registration) {
            return response()->json(['success' => false, 'message' => 'Registration not found'], 404);
        }

        $model = ucfirst($model);
        $eightHoursAgo = now()->subHours(24);
        $existingView = DB::table('views')
            ->where('model_type', $model)
            ->where('model_id', $model_id)
            ->where('registration_id', $registration->id)
            ->where('created_at', '>=', $eightHoursAgo)
            ->first();

        if (!$existingView) {
            DB::table('views')->insert([
                'model_type' => $model, // Ví dụ: "Collection"
                'model_id'   => $model_id,
                'registration_id'  => $registration->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json(['success' => true]);
    }
}
