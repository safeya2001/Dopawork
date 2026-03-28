@extends('layouts.app')
@section('title', app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Sign In')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-primary-50 to-blue-50 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        {{-- Card --}}
        <div class="bg-white rounded-3xl shadow-xl p-8">
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-bold text-2xl">D</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ app()->getLocale() === 'ar' ? 'مرحباً بك في دوبا وورك' : 'Welcome to Dopa Work' }}
                </h1>
                <p class="text-gray-500 text-sm mt-1">
                    {{ app()->getLocale() === 'ar' ? 'سجل دخولك للمتابعة' : 'Sign in to continue' }}
                </p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition-all @error('email') border-red-400 @enderror"
                        placeholder="{{ app()->getLocale() === 'ar' ? 'email@example.com' : 'email@example.com' }}">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-sm font-medium text-gray-700">
                            {{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }}
                        </label>
                        <a href="#" class="text-xs text-primary-600 hover:text-primary-700">
                            {{ app()->getLocale() === 'ar' ? 'نسيت كلمة المرور؟' : 'Forgot password?' }}
                        </a>
                    </div>
                    <input type="password" name="password" required autocomplete="current-password"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition-all">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember" class="rounded border-gray-300 text-primary-600">
                    <label for="remember" class="text-sm text-gray-600">
                        {{ app()->getLocale() === 'ar' ? 'تذكرني' : 'Remember me' }}
                    </label>
                </div>

                <button type="submit"
                    class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 rounded-xl transition-colors text-sm">
                    {{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Sign In' }}
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                {{ app()->getLocale() === 'ar' ? 'ليس لديك حساب؟' : "Don't have an account?" }}
                <a href="{{ route('register') }}" class="text-primary-600 hover:text-primary-700 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'سجل الآن' : 'Sign Up' }}
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
