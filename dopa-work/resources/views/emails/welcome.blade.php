@php
    $locale = $user->preferred_locale ?? 'ar';
    $isAr = $locale === 'ar';
@endphp
@extends('emails.layout', ['locale' => $locale])

@section('email_title', $isAr ? 'أهلاً بك في دوبا وورك' : 'Welcome to Dopa Work')

@section('email_body')
<p class="title">{{ $isAr ? 'مرحباً، '.$user->name.' 👋' : 'Welcome, '.$user->name.'! 👋' }}</p>
<p class="text">
    {{ $isAr
        ? 'يسعدنا انضمامك إلى منصة دوبا وورك. أنت الآن جزء من مجتمع العمل الحر في الوطن العربي.'
        : 'We\'re thrilled to have you join Dopa Work — the leading Arabic freelance marketplace.' }}
</p>

<div class="card">
    <div class="card-row">
        <span class="card-label">{{ $isAr ? 'الاسم' : 'Name' }}</span>
        <span class="card-val">{{ $user->name }}</span>
    </div>
    <div class="card-row">
        <span class="card-label">{{ $isAr ? 'البريد' : 'Email' }}</span>
        <span class="card-val">{{ $user->email }}</span>
    </div>
    <div class="card-row">
        <span class="card-label">{{ $isAr ? 'نوع الحساب' : 'Account Type' }}</span>
        <span class="card-val">{{ $isAr ? ($user->role === 'freelancer' ? 'مستقل' : 'عميل') : ucfirst($user->role) }}</span>
    </div>
</div>

<p class="text">
    {{ $isAr
        ? 'ابدأ الآن بتصفح الخدمات والمشاريع المتاحة.'
        : 'Start exploring available services and projects right now.' }}
</p>

<a href="{{ url('/') }}" class="btn">
    {{ $isAr ? 'ابدأ الآن ←' : 'Get Started →' }}
</a>
@endsection
