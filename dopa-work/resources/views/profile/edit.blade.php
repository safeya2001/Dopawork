@extends('layouts.app')
@section('title', app()->getLocale()==='ar' ? 'ملفي الشخصي' : 'My Profile')

@section('content')
@php
    $cities = ['عمان','إربد','الزرقاء','العقبة','السلط','مادبا','جرش','عجلون','الكرك','معان','الطفيلة','المفرق','رمثا','إربد - المدينة','الرصيفة','الزرقاء الجديدة'];
    $isFreelancer = auth()->user()->isFreelancer();
@endphp

<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">
            {{ app()->getLocale()==='ar' ? 'ملفي الشخصي' : 'My Profile' }}
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            {{ app()->getLocale()==='ar' ? 'تحديث معلوماتك الشخصية والمهنية' : 'Update your personal and professional information' }}
        </p>
    </div>

    @if(session('success'))
        <div class="mb-5 bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-800 font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Tab Bar --}}
    @if($isFreelancer)
    <div class="flex gap-1 bg-gray-100 rounded-2xl p-1 mb-6">
        <button type="button" onclick="switchTab('personal')" id="tab-personal"
                class="flex-1 py-2.5 text-sm font-medium rounded-xl transition-all tab-btn tab-active">
            👤 {{ app()->getLocale()==='ar' ? 'معلومات الحساب' : 'Account Info' }}
        </button>
        <button type="button" onclick="switchTab('professional')" id="tab-professional"
                class="flex-1 py-2.5 text-sm font-medium rounded-xl transition-all tab-btn">
            🎯 {{ app()->getLocale()==='ar' ? 'الملف المهني' : 'Professional Profile' }}
        </button>
    </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- =================== TAB 1: PERSONAL =================== --}}
        <div id="pane-personal">

            {{-- Avatar --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
                <h2 class="text-sm font-semibold text-gray-700 mb-4">
                    {{ app()->getLocale()==='ar' ? 'الصورة الشخصية' : 'Profile Picture' }}
                </h2>
                <div class="flex items-center gap-5">
                    <img id="avatarPreview"
                        src="{{ $user->avatar ? Storage::url($user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=3b82f6&background=dbeafe&size=96' }}"
                        class="w-20 h-20 rounded-full object-cover border-2 border-primary-100">
                    <div>
                        <label class="cursor-pointer inline-flex items-center gap-2 bg-primary-50 hover:bg-primary-100 text-primary-700 text-sm font-medium px-4 py-2 rounded-lg transition-colors border border-primary-200">
                            📷 {{ app()->getLocale()==='ar' ? 'اختر صورة' : 'Choose Photo' }}
                            <input type="file" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                        </label>
                        <p class="text-xs text-gray-400 mt-1">{{ app()->getLocale()==='ar' ? 'JPG, PNG — حد أقصى 2MB' : 'JPG, PNG — max 2MB' }}</p>
                        @error('avatar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Basic Info --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
                <h2 class="text-sm font-semibold text-gray-700 mb-4">
                    {{ app()->getLocale()==='ar' ? 'المعلومات الأساسية' : 'Basic Information' }}
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ app()->getLocale()==='ar' ? 'الاسم (إنجليزي)' : 'Full Name (English)' }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('name') border-red-400 @enderror"
                               placeholder="Ahmed Al-Rashid">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ app()->getLocale()==='ar' ? 'الاسم (عربي)' : 'Full Name (Arabic)' }}</label>
                        <input type="text" name="name_ar" value="{{ old('name_ar', $user->name_ar) }}" dir="rtl"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                               placeholder="أحمد الراشد">
                    </div>
                    @if(!$isFreelancer)
                    <div class="sm:col-span-2">
                        <label class="block text-xs text-gray-500 mb-1">{{ app()->getLocale()==='ar' ? 'اسم الشركة (اختياري)' : 'Company Name (optional)' }}</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $user->company_name) }}"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                               placeholder="{{ app()->getLocale()==='ar' ? 'اسم شركتك أو مؤسستك' : 'Your company or organization' }}">
                    </div>
                    @endif
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ app()->getLocale()==='ar' ? 'البريد الإلكتروني' : 'Email' }}</label>
                        <input type="email" value="{{ $user->email }}" readonly
                               class="w-full border border-gray-100 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-400 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ app()->getLocale()==='ar' ? 'رقم الهاتف' : 'Phone Number' }}</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('phone') border-red-400 @enderror"
                               placeholder="+962 7X XXX XXXX">
                        @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ app()->getLocale()==='ar' ? 'المدينة (الأردن)' : 'City (Jordan)' }}</label>
                        <select name="city" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                            <option value="">{{ app()->getLocale()==='ar' ? '-- اختر مدينة --' : '-- Select city --' }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}" {{ old('city', $user->city) === $city ? 'selected' : '' }}>{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs text-gray-500 mb-1">{{ app()->getLocale()==='ar' ? 'نبذة شخصية' : 'Bio' }}</label>
                        <textarea name="bio" rows="3" maxlength="1000" id="bioTextarea"
                                  class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none @error('bio') border-red-400 @enderror"
                                  placeholder="{{ app()->getLocale()==='ar' ? 'اكتب نبذة مختصرة عنك...' : 'Write a short bio about yourself...' }}">{{ old('bio', $user->bio) }}</textarea>
                        <p class="text-xs text-gray-400 mt-1 text-end" id="bioCount">{{ strlen($user->bio ?? '') }} / 1000</p>
                    </div>
                </div>
            </div>

            {{-- Password --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
                <h2 class="text-sm font-semibold text-gray-700 mb-1">{{ app()->getLocale()==='ar' ? 'تغيير كلمة المرور' : 'Change Password' }}</h2>
                <p class="text-xs text-gray-400 mb-4">{{ app()->getLocale()==='ar' ? 'اتركها فارغة إذا لا تريد التغيير' : 'Leave blank to keep current password' }}</p>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ app()->getLocale()==='ar' ? 'كلمة المرور الحالية' : 'Current Password' }}</label>
                        <input type="password" name="current_password"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('current_password') border-red-400 @enderror"
                               placeholder="••••••••">
                        @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">{{ app()->getLocale()==='ar' ? 'كلمة المرور الجديدة' : 'New Password' }}</label>
                            <input type="password" name="new_password"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('new_password') border-red-400 @enderror"
                                   placeholder="••••••••">
                            @error('new_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">{{ app()->getLocale()==='ar' ? 'تأكيد كلمة المرور' : 'Confirm New Password' }}</label>
                            <input type="password" name="new_password_confirmation"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                                   placeholder="••••••••">
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- end pane-personal --}}

        {{-- =================== TAB 2: PROFESSIONAL (freelancer only) =================== --}}
        @if($isFreelancer)
        <div id="pane-professional" class="hidden space-y-5">

            {{-- Availability --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-sm text-gray-900">{{ app()->getLocale()==='ar' ? 'متاح للعمل' : 'Available for Work' }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ app()->getLocale()==='ar' ? 'يظهر شارة "متاح" في ملفك أمام العملاء' : 'Shows an "Available" badge on your public profile' }}</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_available" value="0">
                        <input type="checkbox" name="is_available" value="1" id="is_available" class="sr-only peer"
                               {{ old('is_available', $freelancerProfile?->is_available) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-primary-600
                                    after:content-[''] after:absolute after:top-[2px] after:start-[2px]
                                    after:bg-white after:border after:rounded-full after:h-5 after:w-5
                                    after:transition-all peer-checked:after:translate-x-full"></div>
                    </label>
                </div>
            </div>

            {{-- Professional Title --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                <h3 class="text-sm font-semibold text-gray-700">{{ app()->getLocale()==='ar' ? 'المسمى المهني' : 'Professional Title' }}</h3>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">{{ app()->getLocale()==='ar' ? 'بالإنجليزية' : 'English' }}</label>
                    <input type="text" name="professional_title"
                           value="{{ old('professional_title', $freelancerProfile?->professional_title) }}"
                           placeholder="e.g. Full-Stack Web Developer"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">{{ app()->getLocale()==='ar' ? 'بالعربية' : 'Arabic' }}</label>
                    <input type="text" name="professional_title_ar" dir="rtl"
                           value="{{ old('professional_title_ar', $freelancerProfile?->professional_title_ar) }}"
                           placeholder="مثال: مطور مواقع متكامل"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            {{-- Overview --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                <h3 class="text-sm font-semibold text-gray-700">{{ app()->getLocale()==='ar' ? 'نبذة مهنية' : 'Professional Overview' }}</h3>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">{{ app()->getLocale()==='ar' ? 'بالإنجليزية' : 'English' }}</label>
                    <textarea name="overview" rows="4"
                              placeholder="Tell clients about your skills, experience, and what makes you unique..."
                              class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none">{{ old('overview', $freelancerProfile?->overview) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">{{ app()->getLocale()==='ar' ? 'بالعربية' : 'Arabic' }}</label>
                    <textarea name="overview_ar" rows="4" dir="rtl"
                              placeholder="اكتب نبذة عن مهاراتك وخبراتك..."
                              class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none">{{ old('overview_ar', $freelancerProfile?->overview_ar) }}</textarea>
                </div>
            </div>

            {{-- Skills --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-1">{{ app()->getLocale()==='ar' ? 'المهارات' : 'Skills' }}</h3>
                <p class="text-xs text-gray-500 mb-3">{{ app()->getLocale()==='ar' ? 'اضغط Enter أو فاصلة لإضافة مهارة' : 'Press Enter or comma to add a skill' }}</p>
                <div id="skills-tags" class="flex flex-wrap gap-2 mb-2 min-h-6"></div>
                <input type="text" id="skills-input"
                       placeholder="{{ app()->getLocale()==='ar' ? 'مثال: PHP, Laravel, Vue.js' : 'e.g. PHP, Laravel, Vue.js' }}"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <input type="hidden" name="skills" id="skills-hidden"
                       value="{{ old('skills', is_array($freelancerProfile?->skills) ? implode(', ', $freelancerProfile->skills) : '') }}">
            </div>

            {{-- Languages --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-1">{{ app()->getLocale()==='ar' ? 'اللغات' : 'Languages' }}</h3>
                <p class="text-xs text-gray-500 mb-3">{{ app()->getLocale()==='ar' ? 'اضغط Enter أو فاصلة لإضافة لغة' : 'Press Enter or comma to add a language' }}</p>
                <div id="lang-tags" class="flex flex-wrap gap-2 mb-2 min-h-6"></div>
                <input type="text" id="lang-input"
                       placeholder="{{ app()->getLocale()==='ar' ? 'مثال: العربية, الإنجليزية' : 'e.g. Arabic, English' }}"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <input type="hidden" name="languages" id="lang-hidden"
                       value="{{ old('languages', is_array($freelancerProfile?->languages) ? implode(', ', $freelancerProfile->languages) : '') }}">
            </div>

            {{-- Experience & Rate --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">{{ app()->getLocale()==='ar' ? 'مستوى الخبرة والسعر' : 'Experience & Rate' }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ app()->getLocale()==='ar' ? 'مستوى الخبرة' : 'Experience Level' }}</label>
                        <select name="experience_level" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                            <option value="entry" {{ old('experience_level', $freelancerProfile?->experience_level) === 'entry' ? 'selected' : '' }}>{{ app()->getLocale()==='ar' ? 'مبتدئ' : 'Entry Level' }}</option>
                            <option value="intermediate" {{ old('experience_level', $freelancerProfile?->experience_level) === 'intermediate' ? 'selected' : '' }}>{{ app()->getLocale()==='ar' ? 'متوسط' : 'Intermediate' }}</option>
                            <option value="expert" {{ old('experience_level', $freelancerProfile?->experience_level) === 'expert' ? 'selected' : '' }}>{{ app()->getLocale()==='ar' ? 'خبير' : 'Expert' }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ app()->getLocale()==='ar' ? 'السعر بالساعة (دينار)' : 'Hourly Rate (JOD)' }}</label>
                        <div class="relative">
                            <span class="absolute end-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">JOD</span>
                            <input type="number" name="hourly_rate" step="0.001" min="0"
                                   value="{{ old('hourly_rate', $freelancerProfile?->hourly_rate) }}"
                                   placeholder="0.000"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 pe-12 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Education & Portfolio --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-4">
                <h3 class="text-sm font-semibold text-gray-700">{{ app()->getLocale()==='ar' ? 'التعليم والمحفظة' : 'Education & Portfolio' }}</h3>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">{{ app()->getLocale()==='ar' ? 'التعليم' : 'Education' }}</label>
                    <textarea name="education" rows="2"
                              placeholder="{{ app()->getLocale()==='ar' ? 'مثال: بكالوريوس علوم حاسوب - جامعة الأردن 2020' : 'e.g. BSc Computer Science - University of Jordan, 2020' }}"
                              class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none">{{ old('education', $freelancerProfile?->education) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">{{ app()->getLocale()==='ar' ? 'رابط المحفظة' : 'Portfolio URL' }}</label>
                    <input type="url" name="portfolio_url"
                           value="{{ old('portfolio_url', $freelancerProfile?->portfolio_url) }}"
                           placeholder="https://myportfolio.com"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('portfolio_url') border-red-400 @enderror">
                    @error('portfolio_url')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

        </div>{{-- end pane-professional --}}
        @endif

        {{-- Submit --}}
        <div class="flex justify-end gap-3 pt-1">
            <button type="submit"
                    class="px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition-colors text-sm">
                💾 {{ app()->getLocale()==='ar' ? 'حفظ التعديلات' : 'Save Changes' }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
// ---- Tab switching ----
function switchTab(tab) {
    document.getElementById('pane-personal').classList.toggle('hidden', tab !== 'personal');
    @if($isFreelancer)
    document.getElementById('pane-professional').classList.toggle('hidden', tab !== 'professional');
    @endif
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('tab-active', 'bg-white', 'shadow-sm', 'text-primary-700');
        b.classList.add('text-gray-500');
    });
    document.getElementById('tab-' + tab).classList.add('tab-active', 'bg-white', 'shadow-sm', 'text-primary-700');
    document.getElementById('tab-' + tab).classList.remove('text-gray-500');
}
// init on load
document.addEventListener('DOMContentLoaded', () => {
    // If there are errors in professional fields, land on that tab
    const profFields = ['professional_title','overview','overview_ar','hourly_rate','portfolio_url'];
    const hasProfError = profFields.some(f => document.querySelector('[name="'+f+'"]')?.classList.contains('border-red-400'));
    @if($isFreelancer)
    switchTab(hasProfError ? 'professional' : 'personal');
    initTagInput('skills-tags', 'skills-input', 'skills-hidden');
    initTagInput('lang-tags',   'lang-input',   'lang-hidden');
    @endif
});

// ---- Tag input ----
function initTagInput(containerId, inputId, hiddenId) {
    const container = document.getElementById(containerId);
    const input     = document.getElementById(inputId);
    const hidden    = document.getElementById(hiddenId);
    if (!container) return;

    let tags = hidden.value ? hidden.value.split(',').map(t => t.trim()).filter(Boolean) : [];

    function render() {
        container.innerHTML = '';
        tags.forEach((tag, i) => {
            const span = document.createElement('span');
            span.className = 'inline-flex items-center gap-1 bg-primary-100 text-primary-700 text-xs font-medium px-2.5 py-1 rounded-full';
            span.innerHTML = `${tag} <button type="button" class="hover:text-red-500 leading-none" data-i="${i}">&times;</button>`;
            span.querySelector('button').addEventListener('click', () => { tags.splice(i, 1); render(); });
            container.appendChild(span);
        });
        hidden.value = tags.join(', ');
    }

    const addTag = () => {
        const val = input.value.trim().replace(/,$/, '');
        if (val && !tags.includes(val)) { tags.push(val); render(); }
        input.value = '';
    };

    input.addEventListener('keydown', e => { if ([',', 'Enter'].includes(e.key)) { e.preventDefault(); addTag(); } });
    input.addEventListener('blur', addTag);
    render();
}

// ---- Avatar preview ----
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('avatarPreview').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}

// ---- Bio counter ----
const bioTA = document.getElementById('bioTextarea');
const bioCount = document.getElementById('bioCount');
if (bioTA) bioTA.addEventListener('input', () => bioCount.textContent = bioTA.value.length + ' / 1000');
</script>
<style>
.tab-active { background: white; box-shadow: 0 1px 3px rgba(0,0,0,.08); color: var(--color-primary-700, #3b82f6); }
.tab-btn:not(.tab-active) { color: #6b7280; }
</style>
@endpush
@endsection

@section('content')
@php
    $cities = ['عمان','إربد','الزرقاء','العقبة','السلط','مادبا','جرش','عجلون','الكرك','معان','الطفيلة','المفرق','رمثا','إربد - المدينة','الرصيفة','الزرقاء الجديدة'];
@endphp

<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">
            {{ app()->getLocale()==='ar' ? 'الملف الشخصي' : 'My Profile' }}
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            {{ app()->getLocale()==='ar' ? 'تحديث معلوماتك الشخصية' : 'Update your personal information' }}
        </p>
    </div>

    @if(session('success'))
        <div class="mb-5 bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-800 font-medium">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Avatar --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">
                {{ app()->getLocale()==='ar' ? 'الصورة الشخصية' : 'Profile Picture' }}
            </h2>
            <div class="flex items-center gap-5">
                <img id="avatarPreview"
                    src="{{ $user->avatar ? Storage::url($user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=3b82f6&background=dbeafe&size=96' }}"
                    class="w-20 h-20 rounded-full object-cover border-2 border-primary-100">
                <div>
                    <label class="cursor-pointer inline-flex items-center gap-2 bg-primary-50 hover:bg-primary-100 text-primary-700 text-sm font-medium px-4 py-2 rounded-lg transition-colors border border-primary-200">
                        📷 {{ app()->getLocale()==='ar' ? 'اختر صورة' : 'Choose Photo' }}
                        <input type="file" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                    </label>
                    <p class="text-xs text-gray-400 mt-1">{{ app()->getLocale()==='ar' ? 'JPG، PNG — حد أقصى 2MB' : 'JPG, PNG — max 2MB' }}</p>
                    @error('avatar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Basic Info --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">
                {{ app()->getLocale()==='ar' ? 'المعلومات الأساسية' : 'Basic Information' }}
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ app()->getLocale()==='ar' ? 'الاسم (إنجليزي)' : 'Full Name (English)' }}
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 @error('name') border-red-400 @enderror"
                        placeholder="Ahmed Al-Rashid">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Name AR --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ app()->getLocale()==='ar' ? 'الاسم (عربي)' : 'Full Name (Arabic)' }}
                    </label>
                    <input type="text" name="name_ar" value="{{ old('name_ar', $user->name_ar) }}" dir="rtl"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100"
                        placeholder="أحمد الراشد">
                </div>

                {{-- Company Name --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ app()->getLocale()==='ar' ? 'اسم الشركة (اختياري)' : 'Company Name (optional)' }}
                    </label>
                    <input type="text" name="company_name" value="{{ old('company_name', $user->company_name) }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100"
                        placeholder="{{ app()->getLocale()==='ar' ? 'اسم شركتك أو مؤسستك' : 'Your company or organization' }}">
                    @error('company_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Email (readonly) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ app()->getLocale()==='ar' ? 'البريد الإلكتروني' : 'Email' }}
                    </label>
                    <input type="email" value="{{ $user->email }}" readonly
                        class="w-full border border-gray-100 bg-gray-50 rounded-xl px-4 py-2.5 text-sm text-gray-400 cursor-not-allowed">
                    <p class="text-xs text-gray-400 mt-1">{{ app()->getLocale()==='ar' ? 'لا يمكن تغيير البريد الإلكتروني' : 'Email cannot be changed' }}</p>
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ app()->getLocale()==='ar' ? 'رقم الهاتف' : 'Phone Number' }}
                    </label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 @error('phone') border-red-400 @enderror"
                        placeholder="+962 7X XXX XXXX">
                    @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- City --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ app()->getLocale()==='ar' ? 'المدينة (الأردن)' : 'City (Jordan)' }}
                    </label>
                    <select name="city" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 bg-white">
                        <option value="">{{ app()->getLocale()==='ar' ? '-- اختر مدينة --' : '-- Select city --' }}</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ old('city', $user->city) === $city ? 'selected' : '' }}>
                                {{ $city }}
                            </option>
                        @endforeach
                    </select>
                    @error('city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Bio --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">
                {{ app()->getLocale()==='ar' ? 'نبذة شخصية' : 'About / Bio' }}
            </h2>
            <textarea name="bio" rows="4" maxlength="1000"
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 resize-none @error('bio') border-red-400 @enderror"
                placeholder="{{ app()->getLocale()==='ar' ? 'اكتب نبذة مختصرة عنك...' : 'Write a short bio about yourself...' }}">{{ old('bio', $user->bio) }}</textarea>
            <p class="text-xs text-gray-400 mt-1 text-end" id="bioCount">{{ strlen($user->bio ?? '') }} / 1000</p>
            @error('bio')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Password Change --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-1">
                {{ app()->getLocale()==='ar' ? 'تغيير كلمة المرور' : 'Change Password' }}
            </h2>
            <p class="text-xs text-gray-400 mb-4">{{ app()->getLocale()==='ar' ? 'اتركها فارغة إذا لا تريد التغيير' : 'Leave blank to keep current password' }}</p>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ app()->getLocale()==='ar' ? 'كلمة المرور الحالية' : 'Current Password' }}
                    </label>
                    <input type="password" name="current_password"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 @error('current_password') border-red-400 @enderror"
                        placeholder="••••••••">
                    @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ app()->getLocale()==='ar' ? 'كلمة المرور الجديدة' : 'New Password' }}
                        </label>
                        <input type="password" name="new_password"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 @error('new_password') border-red-400 @enderror"
                            placeholder="••••••••">
                        @error('new_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ app()->getLocale()==='ar' ? 'تأكيد كلمة المرور' : 'Confirm New Password' }}
                        </label>
                        <input type="password" name="new_password_confirmation"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100"
                            placeholder="••••••••">
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit"
            class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3.5 rounded-xl transition-colors text-sm">
            💾 {{ app()->getLocale()==='ar' ? 'حفظ التعديلات' : 'Save Changes' }}
        </button>
    </form>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('avatarPreview').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
const bioTextarea = document.querySelector('[name="bio"]');
const bioCount = document.getElementById('bioCount');
if (bioTextarea) {
    bioTextarea.addEventListener('input', () => {
        bioCount.textContent = bioTextarea.value.length + ' / 1000';
    });
}
</script>
@endsection
