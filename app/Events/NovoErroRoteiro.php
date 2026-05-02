<?php

namespace App\Events;

use App\Models\Tb_Erro;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\tb_usuario;

class NovoErroRoteiro implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $mensagem;  // Dados da mensagem que será transmitida

    // Construtor recebe a mensagem
    public function __construct(Tb_Erro $mensagem)
    {
        $this->mensagem = $mensagem;
    }

    // Definir em qual canal o evento será transmitido
    public function broadcastOn()
    {
        Log::info("enviando mensagem: " . $this->mensagem);
        return new PrivateChannel('chat_rot.' . $this->mensagem->id_roteiro);
    }

    public function broadcastWith()
    {
        // Retorna os dados específicos que você quer enviar para o frontend
        return [
            'id' => $this->mensagem->id,
            'ds_erro' => $this->mensagem->ds_erro,
            'id_usuario' => $this->mensagem->id_usuario,
            'nm_usuario' => tb_usuario::where('id', $this->mensagem->id_usuario)->value('nm_usuario'),
            'nr_frame' => $this->mensagem->nr_frame,
        ];
    }
}