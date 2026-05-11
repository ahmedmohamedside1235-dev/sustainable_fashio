<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        // Check if the user is already logged in
        if (Auth::guard('user')->check() && Auth::guard('user')->user()->role == 'admin') {
            return redirect()->back()->with('success', 'The admin has been created successfully');
        } 
        
        // if the user try log in while they are already logged in, redirect them to home page with error message
        else if (Auth::guard('user')->check()) {
            return redirect()->route('home')->with('errorLogin', 'You are already logged in');
        }
        return view('auth.login');
    }

    // Handle the login request
    public function login(Request $request)
    {
        // if the user try log in while they are already logged in, redirect them to home page with error message
        if (Auth::guard('user')->check()) {
            return redirect()->route('home')->with('errorLogin', 'You are already logged in');
        }

        // get data from the form and validate it
        $request->validate([
            "email"    => ['required'],
            "password" => ['required'],
        ]);

        // attempt to log in the user with the provided credentials
        $data = [
            'email'    => $request->email,
            'password' => $request->password,
        ];

        // if the email and password are correct 
        if (Auth::guard('user')->attempt($data)) {
            $request->session()->regenerate();
            return redirect()->route('home');
        } 
        
        // if the email or password are incorrect, redirect back to the login page with an error message
        else {
            return redirect()->back()->with('invalid', 'Invalid email or password');
        }

    }

    // Handle the logout request
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
