<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public string $recipient = 'client') {}

    public function envelope(): Envelope
    {
        $user    = $this->recipient === 'client' ? $this->order->client : $this->order->freelancer;
        $locale  = $user?->preferred_locale ?? 'ar';
        $subject = $locale === 'ar'
            ? 'تم إنشاء طلب جديد – دوبا وورك'
            : 'New Order Created – Dopa Work';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.order-created', with: [
            'order'     => $this->order,
            'recipient' => $this->recipient,
        ]);
    }
}
