<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\View;
use App\Models\Comment;

class StudentController extends Controller
{
    public function index()
    {
        $students = Registration::latest()->paginate(20);
        return view('admin.students.index', compact('students'));
    }

    public function view()
    {
        // Lấy danh sách view, phân trang 10 bản ghi mỗi trang
        $views = View::orderBy('updated_at', 'desc')->paginate(20);

        return view('admin.students.view', compact('views'));
    }

    public function comment()
    {
        // Lấy danh sách view, phân trang 10 bản ghi mỗi trang
        $comments = Comment::orderBy('updated_at', 'desc')->paginate(20);

        return view('admin.students.comment', compact('comments'));
    }
}

