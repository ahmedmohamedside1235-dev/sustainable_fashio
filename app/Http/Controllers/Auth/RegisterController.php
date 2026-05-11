<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function index()
    {
        return view('auth.register');
    }

    // Handle the registration request
    public function register(Request $request)
    {
        // validate the incoming request data
        $request->validate([
            "role"     => ['required', 'in:admin,seller,buyer'],
            "name"     => ['required', 'regex:/^[A-Za-z]+(\s[A-Za-z]+){0,2}$/'],
            "email"    => ['required', 'regex:/^[A-Za-z\-_\.0-9]+@(gmail|yahoo)\.(com|org)$/', 'unique:users'],
            "password" => ['required', 'min:8'],
            "phone"    => ['required', 'regex:/^01(0|1|2|5)[0-9]{8}$/'],
        ]);

        // check if the user is trying to create an admin account and if the current user is an admin
        if ($request->role == 'admin') {
            if (Auth::guard('user')->check() && Auth::guard('user')->user()->role == 'admin') {
                $this->create_user($request);
                return redirect()->route('login')->with('success', 'Admin has been created Successfully');
            } else {
                return redirect()->back()->with('error', 'Only admins can create admin accounts');
            }
        } else {
            $this->create_user($request);
            return redirect()->route('login')->with('success', 'Account has been created Successfully');
        }
    }

    // create a new user in the database
    private function create_user(Request $request)
    {
        $user           = new UserData();
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->password = Hash::make($request->password);
        $user->phone    = $request->phone;
        $user->role     = $request->role;
        $user->save();
    }
}
