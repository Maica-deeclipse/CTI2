<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'password' => 'required|string|min:8|confirmed',
        ]);

        $username = $validated['email'];
        $password = md5($validated['password']);

        DB::table('demo_users')->insert([
            'username' => $username,
            'password' => $password,
        ]);

        return redirect('/')->with('message', 'Successfully registered! You can now log in.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $users = DB::table('demo_users')
            ->where('username', $request->input('email'))
            ->get();

        foreach ($users as $user) {
            if ($user->password === md5($request->input('password'))) {
                return "Login success (INSECURE)";
            }
        }

        return "Invalid credentials";
    }
}


