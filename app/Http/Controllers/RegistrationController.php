<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function index()
    {
        return view('registrations.index');
    }

    public function apiShow($client_id)
    {
        $registration = Registration::where('client_id', $client_id)->first();

        if (!$registration) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json($registration);
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
}
