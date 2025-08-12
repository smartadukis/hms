<?php
// app/Http/Controllers/AuthController.php

/**
 * AuthController
 *
 * Handles all user authentication logic, including registration, login, and logout.
 * This controller serves views for authentication and processes authentication requests.
 *
 * @package App\Http\Controllers
 */

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Display the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle the registration request and create a new user account.
     *
     * @param  \Illuminate\Http\Request $request Incoming HTTP request containing user details
     * @return \Illuminate\Http\RedirectResponse Redirects to login page after successful registration
     */
    public function register(Request $request)
    {
        // Validate the user registration input
        $request->validate([
            'name' => 'required',
            'email' => 'nullable|email|unique:users',
            'phone' => 'required|numeric|unique:users',
            'address' => 'nullable|string',
            'password' => 'required|min:6|confirmed'
        ]);

        // Create new user with default "staff" role and hashed password
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => 'staff', // default role
            'password' => Hash::make($request->password)
        ]);

        // Redirect to login page with success message
        return redirect('/login')->with('success', 'Account created successfully. Please login.');
    }

    /**
     * Display the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle the login request and authenticate the user.
     *
     * @param  \Illuminate\Http\Request $request Incoming HTTP request containing login credentials
     * @return \Illuminate\Http\RedirectResponse Redirects to dashboard if successful, back to login with error otherwise
     */
    public function login(Request $request)
    {
        // Extract only phone and password fields from the request
        $credentials = $request->only('phone', 'password');

        // Attempt authentication with the provided credentials
        if (Auth::attempt($credentials)) {
            // Regenerate session ID for security
            $request->session()->regenerate();
            return redirect('/dashboard');
        }

        // Return with validation error if authentication fails
        return back()->withErrors(['phone' => 'Invalid login details']);
    }

    /**
     * Log out the currently authenticated user.
     *
     * @param  \Illuminate\Http\Request $request Current HTTP request instance
     * @return \Illuminate\Http\RedirectResponse Redirects to login page
     */
    public function logout(Request $request)
    {
        // Perform logout action
        Auth::logout();

        // Invalidate the current session
        $request->session()->invalidate();

        // Regenerate the CSRF token
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
