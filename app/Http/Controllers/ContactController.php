<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Mail\FeedbackEmail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function enviarFeedback(Request $request)
    {
        // Validação dos dados
        $validated = $request->validate([
            'nome' =>  ['required', 'min:3', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'mensagem' => ['required', 'min:5', 'max:255'],
        ]);

        // Enviar o e-mail usando o Mailable
        try {
            Mail::to('organima.br@gmail.com')->send(new FeedbackEmail(
                $validated['nome'],
                $validated['email'],
                $validated['mensagem']
            ));            

            // Retornar o resultado como JSON
            return response()->json([
                'success' => true,
                'message' => 'Email enviado com sucesso.'
            ], 200);

        } catch (\Exception $e) {
            // Log detalhado do erro
            Log::error('Erro ao enviar e-mail: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar e-mail.'
            ], 500);
        }
    }
}