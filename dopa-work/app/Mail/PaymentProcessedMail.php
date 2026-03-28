<?php

namespace App\Mail;

use App\Models\WalletTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentProcessedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public WalletTransaction $transaction) {}

    public function envelope(): Envelope
    {
        $locale  = $this->transaction->user?->preferred_locale ?? 'ar';
        $subject = $locale === 'ar'
            ? 'تم معالجة الدفع – دوبا وورك'
            : 'Payment Processed – Dopa Work';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-processed', with: ['transaction' => $this->transaction]);
    }
}
