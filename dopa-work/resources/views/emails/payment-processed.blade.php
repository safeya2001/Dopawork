@php
    $user = $transaction->user;
    $locale = $user?->preferred_locale ?? 'ar';
    $isAr = $locale === 'ar';
    $typeLabel = match($transaction->type) {
        'deposit'    => $isAr ? 'إيداع' : 'Deposit',
        'withdrawal' => $isAr ? 'سحب' : 'Withdrawal',
        'payment'    => $isAr ? 'دفعة' : 'Payment',
        'refund'     => $isAr ? 'استرداد' : 'Refund',
        'commission' => $isAr ? 'عمولة' : 'Commission',
        default      => ucfirst($transaction->type),
    };
@endphp
@extends('emails.layout', ['locale' => $locale])

@section('email_title', $isAr ? 'تم معالجة الدفع – دوبا وورك' : 'Payment Processed – Dopa Work')

@section('email_body')
<p class="title">{{ $isAr ? 'تم معالجة عملية الدفع ✓' : 'Payment Processed ✓' }}</p>

<p class="text">
    {{ $isAr ? 'تمت معالجة عملية مالية على حسابك في دوبا وورك بنجاح.' : 'A financial transaction was successfully processed on your Dopa Work account.' }}
</p>

<div class="card">
    <div class="card-row">
        <span class="card-label">{{ $isAr ? 'نوع العملية' : 'Type' }}</span>
        <span class="card-val">{{ $typeLabel }}</span>
    </div>
    <div class="card-row">
        <span class="card-label">{{ $isAr ? 'المبلغ' : 'Amount' }}</span>
        <span class="card-val">{{ number_format($transaction->amount, 3) }} JOD</span>
    </div>
    <div class="card-row">
        <span class="card-label">{{ $isAr ? 'التاريخ' : 'Date' }}</span>
        <span class="card-val">{{ $transaction->created_at->format('Y-m-d H:i') }}</span>
    </div>
    <div class="card-row">
        <span class="card-label">{{ $isAr ? 'رصيد المحفظة' : 'Wallet Balance' }}</span>
        <span class="card-val">{{ number_format($transaction->balance_after ?? $user->wallet_balance, 3) }} JOD</span>
    </div>
</div>

@if($transaction->description)
<p class="text" style="color:#374151;">{{ $transaction->description }}</p>
@endif

<a href="{{ url('/wallet') }}" class="btn">
    {{ $isAr ? 'عرض المحفظة ←' : 'View Wallet →' }}
</a>
@endsection
