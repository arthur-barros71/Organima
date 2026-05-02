<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeedbackEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $nome;
    public $email;
    public $mensagem;

    /**
     * Criar uma nova instância de mensagem.
     *
     * @param string $nome
     * @param string $email
     * @param string $mensagem
     */
    public function __construct($nome, $email, $mensagem)
    {
        $this->nome = $nome;
        $this->email = $email;
        $this->mensagem = $mensagem;
    }

    /**
     * Construir a mensagem.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Novo feedback de {$this->nome}")
                    ->from($this->email, $this->nome)
                    ->to('organima.br@gmail.com')
                    ->view('emails.feedback')  // A view que será usada para o corpo do e-mail
                    ->with([
                        'mensagem' => $this->mensagem,
                    ]);
    }
}