<?php

namespace App\Http\Controllers;

use App\Models\tb_roteiro;
use App\Models\Tb_Cena_Roteiro;
use App\Models\Tb_Usuario;
use App\Models\Tb_Contribuidor;
use App\Models\Tb_Comentario;
use App\Models\Tb_Erro;
use App\Events\NovoComentarioRoteiro;
use App\Events\NovoErroRoteiro;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ScriptController extends Controller
{
    //Criar bloco de roteiro
    public function criarRoteiro(Request $request){
        $informed_inputs = $request->validate([
            'nm_roteiro' => ['required', 'min:3', 'max:45'],
            'ds_roteiro' => ['required', 'max:500'],
        ]);
    
        $user = Auth::user();
        $informed_inputs['id_usuario'] = $user->id;
    
        $rot = tb_roteiro::create($informed_inputs);

        /** @var \App\Models\tb_usuario $user */
        $user->increment('qt_roteiro');

        Tb_Cena_Roteiro::create([
            'nm_cena_roteiro' => 'Cena 1',
            'ds_cena_roteiro' => 'Introduza seu roteiro.',
            'nm_cor' => '#3ED582',
            'id_roteiro' => $rot->id
        ]);

        return redirect('/');
    }

    //Editar bloco de roteiro
    public function editarRoteiro(Request $request, $id){
        $informed_inputs = $request->validate([
            'nm_roteiro' => ['required', 'min:3', 'max:45'],
            'ds_roteiro' => ['required', 'max:500'],
        ]);

        // Busca o roteiro pelo ID
        $roteiro = tb_roteiro::findOrFail($id);

        // Atualiza os dados do roteiro
        $roteiro->update($informed_inputs);

        return redirect()->back();
    }

    //Salvar id do bloco de roteiro
    public function guardarRoteiro(Request $request)
    {
        // Armazena a variável no Laravel's session
        session(['id_roteiro' => $request->input('id_roteiro')]);

        Log::info($request->input('id_roteiro'));

        // Retorna uma resposta JSON para indicar que a operação foi bem-sucedida
        return response()->json(['success' => true]);
    }

    public function abrirRoteiro()
    {
        $variable = session('id_roteiro', 'default_value');
        $cenas_roteiro = Tb_Cena_Roteiro::where('id_roteiro', $variable)->get();

        $cenaSessao = session('id_cena_roteiro');

        if (!$cenaSessao || !$cenas_roteiro->contains('id', $cenaSessao)) {
            session(['id_cena_roteiro' => $cenas_roteiro->first()?->id]);
        }

        $roteiro = Tb_Roteiro::find($variable);
        $ownerId = $roteiro?->id_usuario ?? null;
        $contribuidorIds = tb_contribuidor::where('id_roteiro', $variable)->pluck('id_usuario')->toArray();

        $allUserIds = collect([$ownerId])->merge($contribuidorIds)->unique()->filter()->values();

        $users = tb_usuario::whereIn('id', $allUserIds)->get();

        $comentarios = Tb_Comentario::where('id_roteiro', $variable)->get();
        $erros = Tb_Erro::where('id_roteiro', $variable)->get();

        return view('script', [
            'id_roteiro' => $variable,
            'cenas_roteiro' => $cenas_roteiro,
            'cena_selecionada' => session('id_cena_roteiro'),
            'usuarios' => $users,
            'comentarios' => $comentarios,
            'erros' => $erros,
        ]);
    }

    //Salvar texto do roteiro automaticamente
    public function salvarTexto(Request $request)
    {
        $request->validate([
            'ds_texto' => 'required|string',
            'id_roteiro' => 'required|integer',
        ]);

        $idCena = $request->session()->get('id_cena_roteiro');

        $block = Tb_Cena_Roteiro::find($idCena);

        if (!$block) {
            return response()->json(['status' => 'error', 'message' => 'Roteiro não encontrado.'], 404);
        }

        $block->ds_texto = $request->ds_texto;
        $block->save();

        $roteiro = Tb_Roteiro::find(session('id_roteiro'));
    
        if ($roteiro) {
            $roteiro->updated_at = now();  // Atualiza o campo `updated_at` com o timestamp atual
            $roteiro->save();
        }

        return response()->json(['status' => 'ok']);
    }

    // Validar Nome de Projeto antes de enviar
    public function consultarRoteiro(Request $request)
    {
        $nm_roteiro = $request->input('nm_roteiro');
        $userId = Auth::user()->id;

        // Verificar se já existe um projeto com esse nome para o usuário
        $roteiroExists = tb_roteiro::where('nm_roteiro', $nm_roteiro)
            ->where('id_usuario', $userId)
            ->exists();

        // Contar quantos projetos o usuário já tem
        $roteiroCount = tb_roteiro::where('id_usuario', $userId)->count();

        // Retornar o resultado como JSON
        return response()->json([
            'exists' => $roteiroExists,
            'currentCount' => $roteiroCount
        ]);
    }

    public function criarCenaRoteiro(Request $request, $id)
    {
        $validator = \Validator::make($request->all(), [
            'nm_cena_roteiro' => ['required', 'min:3', 'max:20'],
            'ds_cena_roteiro' => ['required', 'max:500'],
            'nm_cor' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $informed_inputs = $validator->validated();
        $informed_inputs['id_roteiro'] = $id;
        Tb_Cena_Roteiro::create($informed_inputs);

        tb_roteiro::where('id', $id)->increment('qt_cena');

        return response()->json(['success' => true], 200);
    }

    public function editarCenaRoteiro(Request $request, $id)
    {
        // Validação dos dados recebidos
        $validator = \Validator::make($request->all(), [
            'nm_cena_roteiro' => ['required', 'min:3', 'max:20'],
            'ds_cena_roteiro' => ['required', 'max:500'],
            'nm_cor' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        // Se a validação falhar, retorna os erros
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        // Valida os dados
        $informed_inputs = $validator->validated();

        // Encontra a cena existente com o ID fornecido
        $cena = Tb_Cena_Roteiro::find($id);

        // Verifica se a cena foi encontrada
        if (!$cena) {
            return response()->json(['error' => 'Cena não encontrada'], 404);
        }

        // Atualiza os dados da cena
        $cena->update($informed_inputs);

        // Retorna uma resposta de sucesso
        return response()->json(['success' => true], 200);
    }

    public function deletarCenaRoteiro(Request $request, $id)
    {
        // Encontra a cena existente com o ID fornecido
        $cena = Tb_Cena_Roteiro::find($id);

        // Verifica se a cena foi encontrada
        if (!$cena) {
            return response()->json(['error' => 'Cena não encontrada'], 404);
        }

        // Atualiza os dados da cena
        $cena->delete();

        // Retorna uma resposta de sucesso
        return response()->json(['success' => true], 200);
    }

    public function ultimaCenaCriada()
    {
        $cena = Tb_Cena_Roteiro::latest('id')->first();

        if (!$cena) {
            return response()->json(['error' => 'Nenhuma cena encontrada'], 404);
        }

        return response()->json([
            'id' => $cena->id,
            'nm_cena_roteiro' => $cena->nm_cena_roteiro,
            'nm_cor' => $cena->nm_cor ?? '#cccccc',
        ]);
    }

    // Compartilhar roteiro
    public function compartilharRoteiro(Request $request, $id)
    {
        $request->validate([
            'ds_email' => 'required|email',
            'id_cargo' => 'required|integer'
        ]);        

        $id_roteiro = $id;
        $ds_email = $request->input('ds_email');
        $id_cargo = $request->input('id_cargo');

        $user = tb_usuario::where('ds_email', $ds_email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado.'
            ], 404);
        }

        $userId = $user->id;

        // Verificar se o usuário já está no projeto
        $usuarioCompartilhado = tb_contribuidor::where('id_roteiro', $id_roteiro)
            ->where('id_usuario', $userId)
            ->exists();

        if (!$usuarioCompartilhado) {
            tb_contribuidor::create([
                'id_usuario' => $userId,
                'id_roteiro' => $id_roteiro,
                'id_cargo'   => $id_cargo
            ]);
        
            return response()->json([
                'success' => true,
                'message' => 'Roteiro compartilhado com sucesso!',
                'id' => $userId,
                'nome' => $user->nm_usuario,
                'cargo' => $id_cargo,
                'email' => $ds_email,
            ]);
         } else {
            return response()->json([
                'success' => false,
                'message' => 'Usuário já é contribuidor deste roteiro.'
            ], 409);
        }

    }

    // Salvar novo cargo
    public function salvarNovoCargo(Request $request, $id)
    {
        $request->validate([
            'id_usuario' => 'required|integer',
            'id_cargo' => 'required|integer'
        ]);

        $id_roteiro = $id;
        $id_usuario = $request->input('id_usuario');
        $id_cargo = $request->input('id_cargo');

        // Atualiza o cargo do usuário no projeto
        $atualizado = tb_contribuidor::where('id_roteiro', $id_roteiro)
            ->where('id_usuario', $id_usuario)
            ->update(['id_cargo' => $id_cargo]);

        if ($atualizado) {
            return response()->json([
                'success' => true,
                'message' => 'Cargo atualizado com sucesso.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado no roteiro ou nada foi alterado.'
            ], 404);
        }
    }

    public function enviarComentarioRoteiro(Request $request, $id)
    {
        $request->validate([
            'ds_comentario' => 'required|string|max:500',
        ]);

        // Crie a nova mensagem no banco de dados
        $mensagem = new Tb_Comentario();
        $mensagem->id_usuario = Auth::id();  // Correção aqui
        $mensagem->id_roteiro = $id;
        $mensagem->ds_comentario = $request->ds_comentario;
        $mensagem->save();

        // Dispara o evento para todos os ouvintes no canal 'chat_proj.{projeto_id}'
        event(new NovoComentarioRoteiro($mensagem));

        return response()->json(['success' => true]);
    }

    public function salvarComentarioRoteiro(Request $request, $id)
    {
        // Obtendo os dados enviados na requisição
        $ds_comentario = $request->input('ds_comentario');
        $id_usuario = $request->input('id_usuario');
        
        // Verificar se já existe um comentário com esses dados na tabela 'tb_comentario'
        $comentarioExistente = DB::table('tb_comentario')
                                ->where('ds_comentario', $ds_comentario)
                                ->where('id_usuario', $id_usuario)
                                ->where('id_roteiro', $id)
                                ->where('id', $request->input('id'))
                                ->first();

        // Se o comentário já existe, retorne uma resposta informando que não será criado novamente
        if ($comentarioExistente) {
            return response()->json(['message' => 'Comentário já existe!'], 200);
        }

        // Caso o comentário não exista, criar um novo
        DB::table('tb_comentario')->insert([
            'ds_comentario' => $ds_comentario,
            'id_usuario' => $id_usuario,
            'id_roteiro' => $id,
        ]);

        // Retorna uma resposta de sucesso
        return response()->json(['message' => 'Comentário salvo com sucesso!'], 201);
    }

    public function getComentarios($id)
    {
        $comentarios = DB::table('tb_comentario')
                        ->where('id_roteiro', $id)
                        ->orderBy('id')
                        ->get();

        return view('partials.comentariosRot', [
            'comentarios' => $comentarios,
            'id_roteiro'  => $id
        ])->render();
    }

    public function enviarErroRoteiro(Request $request, $id)
    {
        $request->validate([
            'ds_erro' => 'required|string|max:500',
        ]);

        // Crie a nova mensagem no banco de dados
        $mensagem = new Tb_Erro();
        $mensagem->id_usuario = Auth::id();  // Correção aqui
        $mensagem->id_roteiro = $id;
        $mensagem->ds_erro = $request->ds_erro;
        $mensagem->nr_frame = 0;
        $mensagem->ic_conclusao = 0;
        $mensagem->save();

        // Dispara o evento para todos os ouvintes no canal 'chat_proj.{projeto_id}'
        event(new NovoErroRoteiro($mensagem));

        return response()->json(['success' => true]);
    }

    public function salvarErroRoteiro(Request $request, $id)
    {
        // Obtendo os dados enviados na requisição
        $ds_erro = $request->input('ds_erro');
        $id_usuario = $request->input('id_usuario');
        
        // Verificar se já existe um erro com esses dados na tabela 'tb_comentario'
        $comentarioExistente = DB::table('tb_erro')
                                ->where('ds_erro', $ds_erro)
                                ->where('id_usuario', $id_usuario)
                                ->where('id_roteiro', $id)
                                ->where('id', $request->input('id'))
                                ->first();

        // Se o erro já existe, retorne uma resposta informando que não será criado novamente
        if ($comentarioExistente) {
            return response()->json(['message' => 'Erro já existe!'], 200);
        }

        // Caso o erro não exista, criar um novo
        DB::table('tb_erro')->insert([
            'ds_erro' => $ds_erro,
            'id_usuario' => $id_usuario,
            'id_roteiro' => $id,
            'nr_frame' => 0,
            'ic_conclusao' => 0,
        ]);

        // Retorna uma resposta de sucesso
        return response()->json(['message' => 'Erro salvo com sucesso!'], 201);
    }

    public function corrigirErroRoteiro($id)
    {
        try {
            // Atualiza o erro
            $updated = DB::table('tb_erro')
                ->where('id', $id)
                ->update([
                    'ic_conclusao' => 1,
                    'nm_concluidor' => Auth::user()->nm_usuario,
                    'dt_conclusao' => Carbon::now('America/Sao_Paulo'),
                ]);

            // Se não atualizou nenhuma linha, talvez o ID não exista
            if ($updated === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro não encontrado ou já atualizado.',
                ], 404);
            }

            // Sucesso
            return response()->json([
                'success' => true,
                'message' => 'Erro corrigido com sucesso.',
            ]);

        } catch (\Exception $e) {
            // Caso alguma exceção ocorra
            return response()->json([
                'success' => false,
                'message' => 'Erro ao corrigir erro: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getComentariosErroRoteiro($id)
    {
        $erros = DB::table('tb_erro')
                 ->where('id_roteiro', $id)
                 ->orderBy('id')
                 ->get();

        return view('partials.comentariosErroRot', [
            'erros' => $erros,
            'id_roteiro' => $id
        ])->render();
    }

    //Deletar roteiro
    public function deletarRoteiro(Request $request)
    {
        $informed_inputs = $request->validate([
            'id' => ['required', 'exists:tb_roteiro,id'],
        ]);

        $user = Auth::user();

        $rot = tb_roteiro::find($informed_inputs['id']);

        if ($rot) {

            if ($rot->id_usuario == $user->id) {

                //Atualizar quantidade de projetos

                /** @var \App\Models\tb_usuario $user */
                $user->decrement('qt_roteiro');

                $rot->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Bloco de roteiro deletado com sucesso'
                ], 200);

            } else {

                Log::alert('Você não tem permissão');
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para deletar este bloco de roteiro.'
                ], 403);

            }

        } else {

            Log::alert('Roteiro não encontrado');
            return response()->json([
                'success' => false,
                'message' => 'Bloco de roteiro não encontrado.'
            ], 404);

        }
    }

    //Fechar Roteiro
    public function fecharRoteiro()
    {       
        return redirect('/');
    }
}
