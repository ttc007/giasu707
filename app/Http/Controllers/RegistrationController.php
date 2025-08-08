<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function create()
    {
        return view('registrations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'email'   => 'nullable|email|max:255',
            'subject' => 'required|string|max:100',
            'note'    => 'nullable|string',
        ]);

        Registration::create($request->only(['name', 'phone', 'email', 'subject', 'note']));

        return redirect()->back()->with('success', 'Đăng ký thành công!');
    }
}
