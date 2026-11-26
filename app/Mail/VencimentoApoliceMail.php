<?php

namespace App\Mail;

use App\Models\Apolice;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class VencimentoApoliceMail extends Mailable
{
    public function __construct(public Apolice $apolice) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Sua apólice '.$this->apolice->numero.' está perto de vencer');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.vencimento');
    }
}
