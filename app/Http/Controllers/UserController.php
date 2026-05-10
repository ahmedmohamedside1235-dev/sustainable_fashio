<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /* ======== Collections ======== */
    public function indexCollection()
    {
        if (! Auth::guard('user')->check()) {
            return redirect()->route('login')->with('errorLogin', 'You must login first');
        }

        return view('users.collections');
    }

    /* ======== Request ======== */
    public function indexRequest()
    {
        if (! Auth::guard('user')->check()) {
            return redirect()->route('login')->with('errorLogin', 'You must login first');
        }
        return view('users.request');
    }

    /* ======== Swap ======== */
    public function indexSwap()
    {
        if (! Auth::guard('user')->check()) {
            return redirect()->route('login')->with('errorLogin', 'You must login first');
        }
        return view('users.swap');
    }
}
