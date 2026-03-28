<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors(['email' => __('auth.failed')])->withInput();
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->isFreelancer()) {
            return redirect()->route('freelancer.dashboard');
        }

        return redirect()->route('client.dashboard');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => 'required|in:client,freelancer',
            'locale' => 'nullable|in:ar,en',
        ]);

        // Clients are active immediately; freelancers need identity verification
        $isClient = $request->role === 'client';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'locale' => $request->locale ?? 'ar',
            'status' => $isClient ? 'active' : 'pending_verification',
        ]);

        // Create freelancer profile if registering as freelancer
        if ($user->isFreelancer()) {
            $user->freelancerProfile()->create([
                'member_since' => now()->format('Y'),
            ]);
        }

        Auth::login($user);

        // Send welcome email
        try { Mail::to($user->email)->queue(new WelcomeMail($user)); } catch (\Throwable) {}

        // Clients go straight to their dashboard
        if ($isClient) {
            return redirect()->route('client.dashboard')
                ->with('success', app()->getLocale() === 'ar'
                    ? 'مرحباً بك في دوبا وورك! يمكنك البدء بتصفح الخدمات والطلب مباشرة.'
                    : 'Welcome to Dopa Work! You can start browsing services right away.');
        }

        return redirect()->route('verification.upload')
            ->with('success', __('Welcome to Dopa Work! Please verify your identity to start.'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
