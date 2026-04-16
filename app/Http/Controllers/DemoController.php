<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class DemoController extends Controller {
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    $exists = DB::table('demo_users')
                        ->where('username', $value)
                        ->exists();
                    if ($exists) {
                        $fail('This email is already registered.');
                    }
                },
            ],
            // ✅ SECURE: Strong password rules enforced
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
        ]);

        // ✅ SECURE: bcrypt via Hash::make() — salted, adaptive hashing
        DB::table('demo_users')->insert([
            'username' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect('/')->with('message', 'Successfully registered! You can now log in.');
    }

    public function login(Request $request)
    {
        // ✅ SECURE: Input validation before any DB query
        $request->validate([
            'email'    => 'required|email|max:255',
            'password' => 'required|string|max:255',
        ]);

        // ✅ SECURE: Fetch single record — no loop enumeration
        $user = DB::table('demo_users')
            ->where('username', $request->input('email'))
            ->first();

        // ✅ SECURE: Hash::check() uses constant-time comparison (prevents timing attacks)
        if ($user && Hash::check($request->input('password'), $user->password)) {
            return redirect('/')->with('message', 'Login successful! Welcome back.');
        }

        // ✅ SECURE: Generic error — does not reveal whether email or password was wrong
        return back()
            ->withErrors(['email' => 'The provided credentials are incorrect.'])
            ->withInput($request->only('email'));
    }
}


