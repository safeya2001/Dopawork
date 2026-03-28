@php
    $client = $proposal->project?->client;
    $locale = $client?->preferred_locale ?? 'ar';
    $isAr = $locale === 'ar';
@endphp
@extends('emails.layout', ['locale' => $locale])

@section('email_title', $isAr ? 'عرض جديد على مشروعك' : 'New Proposal on Your Project')

@section('email_body')
<p class="title">{{ $isAr ? 'وصل عرض جديد على مشروعك! 📨' : 'New Proposal Received! 📨' }}</p>

<p class="text">
    {{ $isAr
        ? 'قدّم مستقل عرضاً على مشروعك. راجعه وابدأ بالتفاوض.'
        : 'A freelancer has submitted a proposal to your project. Review it and start negotiating.' }}
</p>

<div class="card">
    <div class="card-row">
        <span class="card-label">{{ $isAr ? 'المشروع' : 'Project' }}</span>
        <span class="card-val">{{ $proposal->project?->title }}</span>
    </div>
    <div class="card-row">
        <span class="card-label">{{ $isAr ? 'المستقل' : 'Freelancer' }}</span>
        <span class="card-val">{{ $proposal->freelancer?->name }}</span>
    </div>
    <div class="card-row">
        <span class="card-label">{{ $isAr ? 'الميزانية المقترحة' : 'Proposed Budget' }}</span>
        <span class="card-val">{{ number_format($proposal->budget, 3) }} JOD</span>
    </div>
    <div class="card-row">
        <span class="card-label">{{ $isAr ? 'مدة التسليم' : 'Delivery' }}</span>
        <span class="card-val">{{ $proposal->delivery_days }} {{ $isAr ? 'يوم' : 'days' }}</span>
    </div>
</div>

<a href="{{ url('/client/projects/'.$proposal->project_id) }}" class="btn">
    {{ $isAr ? 'مراجعة العرض ←' : 'Review Proposal →' }}
</a>
@endsection
