<?php

namespace App\Mail;

use App\Models\ProjectProposal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProposalReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ProjectProposal $proposal) {}

    public function envelope(): Envelope
    {
        $locale  = $this->proposal->project?->client?->preferred_locale ?? 'ar';
        $subject = $locale === 'ar'
            ? 'وصل عرض جديد على مشروعك – دوبا وورك'
            : 'New Proposal Received – Dopa Work';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.proposal-received', with: ['proposal' => $this->proposal]);
    }
}
