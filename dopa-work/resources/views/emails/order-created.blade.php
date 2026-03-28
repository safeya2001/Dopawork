@php
    $client = $order->client;
    $freelancer = $order->freelancer;
    $isRecipientClient = $recipient === 'client';
    $emailUser = $isRecipientClient ? $client : $freelancer;
    $locale = $emailUser?->preferred_locale ?? 'ar';
    $isAr = $locale === 'ar';
@endphp
@extends('emails.layout', ['locale' => $locale])

@section('email_title', $isAr ? 'طلب جديد – دوبا وورك' : 'New Order – Dopa Work')

@section('email_body')
<p class="title">
    @if($isRecipientClient)
        {{ $isAr ? "تم إنشاء طلبك بنجاح ✓" : "Your Order Was Placed Successfully ✓" }}
    @else
        {{ $isAr ? "لديك طلب جديد! 🎉" : "You Have a New Order! 🎉" }}
    @endif
</p>

<p class="text">
    @if($isRecipientClient)
        {{ $isAr ? 'سيبدأ المستقل بتنفيذ طلبك قريباً. يمكنك متابعة الطلب من لوحة التحكم.' : 'The freelancer will start working on your order soon. Track it from your dashboard.' }}
    @else
        {{ $isAr ? 'قام عميل جديد بطلب خدمتك. ابدأ التنفيذ في أقرب وقت ممكن.' : 'A client has ordered your service. Please start working as soon as possible.' }}
    @endif
</p>

<div class="card">
    <div class="card-row">
        <span class="card-label">{{ $isAr ? 'الخدمة' : 'Service' }}</span>
        <span class="card-val">{{ $order->service?->display_title }}</span>
    </div>
    <div class="card-row">
        <span class="card-label">{{ $isAr ? 'المبلغ' : 'Amount' }}</span>
        <span class="card-val">{{ number_format($order->amount, 3) }} JOD</span>
    </div>
    <div class="card-row">
        <span class="card-label">{{ $isAr ? 'مدة التسليم' : 'Delivery' }}</span>
        <span class="card-val">{{ $order->delivery_days }} {{ $isAr ? 'يوم' : 'days' }}</span>
    </div>
    <div class="card-row">
        <span class="card-label">{{ $isAr ? ($isRecipientClient ? 'المستقل' : 'العميل') : ($isRecipientClient ? 'Freelancer' : 'Client') }}</span>
        <span class="card-val">{{ $isRecipientClient ? $freelancer?->name : $client?->name }}</span>
    </div>
</div>

<a href="{{ url('/client/orders/'.$order->id) }}" class="btn">
    {{ $isAr ? 'عرض الطلب ←' : 'View Order →' }}
</a>
@endsection
