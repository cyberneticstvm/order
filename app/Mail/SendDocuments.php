<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendDocuments extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Devi Eye Hospitals - Documents',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'backend.email.send-documents',
            with: $this->data
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $docs = array();
        if ($this->data['is_invoice'])
            array_push($docs, Attachment::fromData(fn() => $this->data['invoice']->output(), 'invoice.pdf')->withMime('application/pdf'));
        if ($this->data['is_receipt'])
            array_push($docs, Attachment::fromData(fn() => $this->data['receipt']->output(), 'receipt.pdf')->withMime('application/pdf'));
        if ($this->data['is_prescription'])
            array_push($docs, Attachment::fromData(fn() => $this->data['prescription']->output(), 'prescription.pdf')->withMime('application/pdf'));
        return $docs;
    }
}
