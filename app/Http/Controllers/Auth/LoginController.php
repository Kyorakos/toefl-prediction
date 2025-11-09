<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        $credentials = $request->only('email', 'password');
        
        // Custom authentication for plain text passwords
        $user = \App\Models\User::where('email', $credentials['email'])->first();
        
        if ($user && $user->password === $credentials['password']) {
            Auth::login($user);
            
            // Check for concurrent login
            $this->checkConcurrentLogin($user);
            
            return $this->sendLoginResponse($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

    protected function checkConcurrentLogin($user)
    {
        // Force logout other sessions
        $user->tokens()->delete(); // For API tokens
        
        // Update last activity
        $user->update(['last_activity' => now()]);
    }

    protected function redirectTo()
    {
        if (Auth::user()->isAdmin()) {
            return '/admin/dashboard';
        }
        
        return '/student/dashboard';
    }
}
