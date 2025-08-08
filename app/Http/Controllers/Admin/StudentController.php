<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;

class StudentController extends Controller
{
    public function index()
    {
        $students = Registration::latest()->paginate(20);
        return view('admin.students.index', compact('students'));
    }
}

