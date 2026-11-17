<?php

namespace App\Mail;

use App\Models\Profissional;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReenvioDocumentosMail extends Mailable
{
    use SerializesModels;

    public function __construct(public Profissional $profissional, public string $motivo, public array $nomesDocumentos, public string $link, public int $dias)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Reenvio de documentos - Vênus');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reenvio-documentos',
        );
    }
}
