<?php

namespace App\Mail;

use App\Models\Profissional;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CadastroAprovadoMail extends Mailable
{
    use SerializesModels;

    public function __construct(public Profissional $profissional)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Sua conta foi aceita - Vênus');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cadastro-aprovado',
        );
    }
}
