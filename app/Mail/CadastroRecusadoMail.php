<?php

namespace App\Mail;

use App\Models\Profissional;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CadastroRecusadoMail extends Mailable
{
    use SerializesModels;

    public function __construct(public Profissional $profissional, public string $motivo)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Cadastro recusado - Vênus');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cadastro-recusado',
        );
    }
}
