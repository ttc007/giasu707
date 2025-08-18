<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentActivation;
use App\Models\Registration;

class StudentController extends Controller
{
    public function showLoginForm()
    {
        return view('registrations.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $student = Registration::where('email', $request->email)
                               ->where('is_active', true)
                               ->first();

        if ($student && Hash::check($request->password, $student->password)) {
            session(['studentId' => $student->id]); // lưu object student
            return redirect()->route('registration.index');
        }

        return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng']);
    }

    public function logout(Request $request)
    {
        session()->forget('studentId');
        return redirect()->route('student.login')->with('success', 'Đăng xuất thành công');
    }

    // Hiển thị form đăng ký
    public function showRegisterForm()
    {
        return view('registrations.register');
    }

    // Xử lý đăng ký
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:50',
            'email'    => 'required|email|unique:registrations,email',
            'password' => 'required|string|min:6|confirmed', // password_confirmation
        ]);

        $activationKey = Str::random(40);

        $student = Registration::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'is_active'      => 1,
            'activation_key' => $activationKey,
            'phone' => 'Chưa cập nhật',
            'subject' => 'Chưa cập nhật',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Gửi email kích hoạt
        //Mail::to($student->email)->send(new StudentActivation($student));

        return redirect()->route('student.login')
                         ->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
    }

    // Kích hoạt tài khoản
    public function activate($key)
    {
        $student = Registration::where('activation_key', $key)->firstOrFail();

        $student->update([
            'is_active' => true,
            'activation_key' => null,
        ]);

        return redirect()->route('student.login')->with('success', 'Tài khoản đã được kích hoạt, bạn có thể đăng nhập.');
    }
}
