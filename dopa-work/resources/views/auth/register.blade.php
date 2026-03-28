@extends('layouts.app')
@section('title', app()->getLocale() === 'ar' ? 'إنشاء حساب' : 'Create Account')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-primary-50 to-blue-50 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-lg">
        <div class="bg-white rounded-3xl shadow-xl p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-bold text-2xl">D</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ app()->getLocale() === 'ar' ? 'انضم إلى دوبا وورك' : 'Join Dopa Work' }}
                </h1>
                <p class="text-gray-500 text-sm mt-1">
                    {{ app()->getLocale() === 'ar' ? 'أنشئ حسابك في ثوانٍ' : 'Create your account in seconds' }}
                </p>
            </div>

            {{-- Role Toggle --}}
            <div class="flex bg-gray-100 rounded-xl p-1 mb-6" id="roleToggle">
                <button type="button" onclick="selectRole('client')"
                    id="btn-client"
                    class="flex-1 py-2.5 rounded-lg text-sm font-semibold transition-all bg-white text-primary-700 shadow-sm">
                    {{ app()->getLocale() === 'ar' ? '🛒 أنا عميل' : '🛒 I\'m a Client' }}
                </button>
                <button type="button" onclick="selectRole('freelancer')"
                    id="btn-freelancer"
                    class="flex-1 py-2.5 rounded-lg text-sm font-semibold transition-all text-gray-500">
                    {{ app()->getLocale() === 'ar' ? '💼 أنا مستقل' : '💼 I\'m a Freelancer' }}
                </button>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="role" id="roleInput" value="{{ old('role', 'client') }}">

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ app()->getLocale() === 'ar' ? 'الاسم الكامل' : 'Full Name' }}
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition-all @error('name') border-red-400 @enderror"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'محمد أحمد' : 'John Doe' }}">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition-all @error('email') border-red-400 @enderror"
                        placeholder="email@example.com">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ app()->getLocale() === 'ar' ? 'رقم الهاتف (اختياري)' : 'Phone Number (Optional)' }}
                    </label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition-all"
                        placeholder="+962791234567">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }}
                        </label>
                        <input type="password" name="password" required minlength="8"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ app()->getLocale() === 'ar' ? 'تأكيد كلمة المرور' : 'Confirm Password' }}
                        </label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition-all">
                    </div>
                </div>

                <input type="hidden" name="locale" value="{{ app()->getLocale() }}">

                <button type="submit"
                    class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 rounded-xl transition-colors text-sm mt-2">
                    {{ app()->getLocale() === 'ar' ? 'إنشاء الحساب' : 'Create Account' }}
                </button>

                <p class="text-center text-xs text-gray-400">
                    {{ app()->getLocale() === 'ar'
                        ? 'بإنشاء حسابك، أنت توافق على شروط الاستخدام وسياسة الخصوصية'
                        : 'By creating an account, you agree to our Terms of Service and Privacy Policy' }}
                </p>
            </form>

            <p class="text-center text-sm text-gray-500 mt-5">
                {{ app()->getLocale() === 'ar' ? 'لديك حساب بالفعل؟' : 'Already have an account?' }}
                <a href="{{ route('login') }}" class="text-primary-600 hover:text-primary-700 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'سجل دخولك' : 'Sign In' }}
                </a>
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
function selectRole(role) {
    document.getElementById('roleInput').value = role;
    const btnClient = document.getElementById('btn-client');
    const btnFreelancer = document.getElementById('btn-freelancer');

    if (role === 'client') {
        btnClient.className = 'flex-1 py-2.5 rounded-lg text-sm font-semibold transition-all bg-white text-primary-700 shadow-sm';
        btnFreelancer.className = 'flex-1 py-2.5 rounded-lg text-sm font-semibold transition-all text-gray-500';
    } else {
        btnFreelancer.className = 'flex-1 py-2.5 rounded-lg text-sm font-semibold transition-all bg-white text-primary-700 shadow-sm';
        btnClient.className = 'flex-1 py-2.5 rounded-lg text-sm font-semibold transition-all text-gray-500';
    }
}

// Set initial state based on old value
selectRole('{{ old('role', 'client') }}');
</script>
@endpush
@endsection
