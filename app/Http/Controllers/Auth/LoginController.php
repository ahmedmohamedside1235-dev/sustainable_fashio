<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::guard('user')->check() && Auth::guard('user')->user()->role == 'admin') {
            return redirect()->back()->with('success', 'The admin has been created successfully');
        } else if (Auth::guard('user')->check()) {
            return redirect()->route('home')->with('errorLogin', 'You are already logged in');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        if (Auth::guard('user')->check()) {
            return redirect()->route('home')->with('errorLogin', 'You are already logged in');
        }

        $request->validate([
            "email"    => ['required'],
            "password" => ['required'],
        ]);

        $data = [
            'email'    => $request->email,
            'password' => $request->password,
        ];

        if (Auth::guard('user')->attempt($data)) {
            $request->session()->regenerate();
            return redirect()->route('home');
        } else {
            return redirect()->back()->with('invalid', 'Invalid email or password');
        }

    }

    public function logout(Request $request)
    {
        if (Auth::guard('user')->check()) {
            Auth::guard('user')->logout();
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
