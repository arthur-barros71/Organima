<?php

namespace App\Http\Controllers;

use App\Models\Tb_Usuario;
use App\Models\Tb_Projeto;
use App\Models\Tb_Roteiro;
use App\Models\Tb_Contribuidor;
use App\Models\Tb_Cena;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCode;
use App\Models\Tb_Cena_Projeto;
use App\Models\Tb_Cena_Roteiro;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserController extends Controller
{
    // Função para criação de usuário
    public function registrar(Request $request){
        $informed_inputs = $request->validate([
            'nm_usuario' => ['required', 'min:3', 'max:16'],
            'ds_email' => ['required', 'email', Rule::unique('tb_usuario', 'ds_email')],
            'cd_senha' => ['required', 'min:6', 'max:20']
        ]);

        $informed_inputs['cd_senha'] = bcrypt($informed_inputs['cd_senha']);
        $user = Tb_Usuario::create($informed_inputs);

        /** @var \App\Models\tb_usuario $user */
        $user->increment('qt_projeto');
        $user->increment('qt_roteiro');

        $projeto = Tb_Projeto::create([
            'nm_projeto' => 'Bem-vindo a Organima',
            'ds_projeto' => 'Projeto padrão para os novos usuários do nosso site. Fique à vontade para utilizar dele o quanto quiser',
            'dt_inicial' => date('Y-m-d H:i:s'),
            'ds_fps' => 60,
            'id_usuario' => $user->id,
            'id_tipo' => 1,
        ]);

        $origem  = public_path('storage/proj_default');
        $destino = public_path('storage/proj_' . $projeto->id);

        if (! File::exists($destino)) {
            File::makeDirectory($destino, 0755, true);
        }

        File::copyDirectory($origem, $destino);

        $userFolder = public_path("storage/user_{$user->id}");
        $userDefaultFolder = public_path('storage/user_default');

        if (!File::exists($userFolder)) {
            File::makeDirectory($userFolder, 0755, true);
        }

        $files = File::files($userDefaultFolder);
        if (!empty($files)) {
            // Escolhe aleatoriamente um arquivo
            $randomFile = $files[array_rand($files)];
            $im_usuario = pathinfo($randomFile, PATHINFO_FILENAME); //Salva a imagem escolhida
            // Extrai a extensão do arquivo
            $extension = pathinfo($randomFile, PATHINFO_EXTENSION);
            $destinationPath = $userFolder . '/profile.' . $extension;
            // Copia o arquivo para a pasta do usuário
            File::copy($randomFile, $destinationPath);
        }

        if ($im_usuario) {
            $user->im_usuario = $im_usuario;
            $user->save();
        }

        $roteiro = Tb_Roteiro::create([
            'nm_roteiro' => 'Bem-vindo a Organima',
            'ds_roteiro' => 'Roteiro padrão para os novos usuários do nosso site. Fique à vontade para utilizar dele o quanto quiser',
            'id_usuario' => $user->id,
        ]);

        $roteiro->increment('qt_cena');

        $cena = Tb_Cena_Roteiro::create([
            'nm_cena_roteiro' => 'Cena 1',
            'ds_cena_roteiro' => 'Introduza seu roteiro.',
            'nm_cor' => '#3ED582',
            'ds_texto' => '<div style="text-align: center;"><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text; font-family: &quot;Century Gothic&quot;, &quot;Century Gothic_EmbeddedFont&quot;, &quot;Century Gothic_MSFontService&quot;, sans-serif; font-size: 16px; font-variant-ligatures: none; text-align: justify; background-color: rgb(255, 255, 255);"><b></b></span></div><span style="font-size: 26px;"><div style="text-align: center;"><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text; font-family: &quot;Century Gothic&quot;, &quot;Century Gothic_EmbeddedFont&quot;, &quot;Century Gothic_MSFontService&quot;, sans-serif; font-size: 16px; font-variant-ligatures: none; text-align: justify; background-color: rgb(255, 255, 255);"><b><span style="font-size: 26px;"><span style="font-size: 36px;"><span style="font-family: &quot;Archivo Black&quot;;"><span style="font-family: Poppins;">Organima</span></span></span></span></b></span></div><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text; font-family: &quot;Century Gothic&quot;, &quot;Century Gothic_EmbeddedFont&quot;, &quot;Century Gothic_MSFontService&quot;, sans-serif; font-size: 16px; font-variant-ligatures: none; text-align: justify; background-color: rgb(255, 255, 255);"><div style="text-align: center;"></div></span></span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text; font-family: &quot;Century Gothic&quot;, &quot;Century Gothic_EmbeddedFont&quot;, &quot;Century Gothic_MSFontService&quot;, sans-serif; font-size: 16px; font-variant-ligatures: none; text-align: justify; background-color: rgb(255, 255, 255);"><div style="text-align: center;"><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text; font-family: &quot;Century Gothic&quot;, &quot;Century Gothic_EmbeddedFont&quot;, &quot;Century Gothic_MSFontService&quot;, sans-serif; font-size: 16px; font-variant-ligatures: none; text-align: justify; background-color: rgb(255, 255, 255);"><br></span></div><div style="text-align: justify;"><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="font-family: Montserrat;"><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;">&nbsp;&nbsp;U</span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;">m software voltado para organização e visualização de animações durante o</span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;"> </span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;">seu </span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;">processo d</span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;">e</span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;"> criação</span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;">, onde poderão ser importados os frames e organiz</span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;">á-los em</span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;"> cenas</span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;">. Dessa forma, os profissionais poderão identificar os erros que encontrarem na animação e contatar a equipe sem sair do site,</span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;"> busca</span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;">n</span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;">do</span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;"> assim</span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;"> </span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;">garantir</span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;"> maior</span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;"> facilidade </span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;">na manutenção d</span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;">a continuidade dos elementos presentes em cada cena</span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;">, assim como dos roteiros, que poderão ser visualizados e editados dentro do software</span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;">.</span></span><span class="NormalTextRun SCXW252251311 BCX0" style="-webkit-user-drag: none; -webkit-tap-highlight-color: transparent; margin: 0px; padding: 0px; user-select: text;"></span></div></span>',
            'id_roteiro' => $roteiro->id
        ]);

        Auth::login($user);
        return redirect('/');
    }

    public function enviarCodigo(Request $request) {
        $request->validate([
            'ds_email' => 'required|email'
        ]);

        try {
            $email = $request->ds_email;
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $code = '';
            for ($i = 0; $i < 5; $i++) {
                $code .= $characters[rand(0, strlen($characters) - 1)];
            }
            Log::info($code);

            // Armazena o código em cache por 15 minutos
            Cache::put('verification_code_' . $email, $code, now()->addMinutes(15));

            // Envia o e-mail
            Mail::to($email)->send(new VerificationCode($code));

            return response()->json([
                'success' => true,
                'message' => 'Código enviado com sucesso',
                // Em desenvolvimento, pode retornar o código para testes:
                'debug_code' => config('app.env') === 'local' ? $code : null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar código de verificação',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function verificarCodigo(Request $request) {
        
        $request->validate([
            'ds_email' => 'required|email',
            'code' => 'required|string'
        ]);

        $email = $request->ds_email;
        $userCode = $request->code;
        $storedCode = Cache::get('verification_code_' . $email);

        if (!$storedCode || $storedCode != $userCode) {
            return response()->json([
                'success' => false,
                'message' => 'Código inválido ou expirado.'
            ], 422);
        }

        // Código válido - limpa o cache
        Cache::forget('verification_code_' . $email);

        return response()->json([
            'success' => true,
            'message' => 'E-mail verificado com sucesso!'
        ]);
    }

    public function entrar(Request $request)
    {
        $informed_inputs = $request->validate([
            'ds_email' => 'required|email',
            'cd_senha' => 'required',
        ]);

        $user = Tb_Usuario::where('ds_email', $informed_inputs['ds_email'])->first();

        if ($user) {

            if ($user->ic_suspensao) {
                Log::warning("Tentativa de login de conta desativada", [
                    'email' => $informed_inputs['ds_email'],
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'success' => false,
                    'reason'  => 'account_suspended',
                    'message' => 'Esta conta está desativada. Entre em contato com o suporte.'
                ]);
            }

            // VERIFICA A SENHA
            if (Hash::check($informed_inputs['cd_senha'], $user->cd_senha)) {

                Auth::login($user);

                session(['user_id' => $user->id]);
                $request->session()->regenerate();

                return response()->json([
                    'success' => true,
                    'message' => 'Login realizado com sucesso.'
                ]);

            } else {

                Log::error("Falha no login - senha inválida", ['email' => $informed_inputs['ds_email']]);

                return response()->json([
                    'success' => false,
                    'reason'  => 'invalid_credentials',
                    'message' => 'Credenciais inválidas.'
                ]);
            }

        } else {

            Log::error("Falha no login - email não encontrado", ['email' => $informed_inputs['ds_email']]);

            return response()->json([
                'success' => false,
                'reason'  => 'invalid_credentials',
                'message' => 'Credenciais inválidas.'
            ]);
        }
    }

    //Função para logout de usuário
    public function sair(){
        Auth::logout();
        return redirect('/');
    }

    //Validar Email antes de enviar
    public function verificarEmail(Request $request)
    {
        Log::info($request->input('ds_email'));
        $ds_email = $request->input('ds_email');
        

        // Verificar se o e-mail já existe no banco de dados
        $emailExists = Tb_Usuario::where('ds_email', $ds_email)->exists();

        // Retornar o resultado como JSON
        return response()->json(['exists' => $emailExists]);
    }

    public function verificarSenha(Request $request)
    {
        // Pegar os dados da requisição
        $email = $request->input('ds_email');
        $password = $request->input('cd_senha'); // A senha enviada via AJAX

        // Verificar se o e-mail existe no banco de dados
        $user = Tb_Usuario::where('ds_email', $email)->first();

        // Verificar se o usuário existe e se a senha é válida
        if ($user && Hash::check($password, $user->cd_senha)) {
            // Senha correta
            return response()->json(['valid' => true, 'message' => 'Senha válida']);
        } else {
            // Senha incorreta ou usuário não encontrado
            return response()->json(['valid' => false, 'message' => 'Senha incorreta ou usuário não encontrado']);
        }
    }

    public function páginaIncial()
    {
        $user = Auth::user();
        if (! $user) {
            return view('home', [
                'projs'            => collect(),
                'rot'              => collect(),
                'percentuais'      => [],
                'mediaProgresso'   => 0,
                'corStatusGeral'   => '#FF0000', // vermelho padrão
            ]);
        }

        // Projetos próprios e de contribuições
        $proj        = tb_projeto::where('id_usuario', $user->id)->get();
        $contribIds  = tb_contribuidor::where('id_usuario', $user->id)->get();
        $contribProj = tb_projeto::whereIn('id', $contribIds->pluck('id_projeto'))->get();
        $contribRot  = tb_roteiro::whereIn('id', $contribIds->pluck('id_roteiro'))->get();

        $projs = $proj->merge($contribProj)->unique('id')->values();
        $rot   = tb_roteiro::where('id_usuario', $user->id)
                    ->orWhereIn('id', $contribIds->pluck('id_roteiro'))
                    ->get();

        // Busca totais e finalizadas em uma query
        $stats = Tb_Cena_Projeto::select('id_projeto',
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(ic_conclusao = 1) as finalizadas')
                )
                ->whereIn('id_projeto', $projs->pluck('id'))
                ->groupBy('id_projeto')
                ->get()
                ->keyBy('id_projeto');

        $percentuais = [];
        foreach ($projs as $p) {
            $stat = $stats->get($p->id, (object)['total'=>0,'finalizadas'=>0]);
            $percentuais[$p->id] = $stat->total > 0
                ? min(round(($stat->finalizadas / $stat->total) * 100, 2), 100)
                : 0;
        }

        $mediaProgresso = count($percentuais)
            ? round(array_sum($percentuais) / count($percentuais), 2)
            : 0;

        // Definindo a cor geral com base na média de progresso
        $corStatusGeral = '#FF0000'; // vermelho padrão
        if ($mediaProgresso >= 100) {
            $corStatusGeral = '#3ED582'; // verde
        } elseif ($mediaProgresso >= 45) {
            $corStatusGeral = '#D5B73E'; // amarelo
        }

        return view('home', compact(
            'projs',
            'rot',
            'percentuais',
            'mediaProgresso',
            'corStatusGeral'
        ));
    }

    public function alterarEmailRecuperacao(Request $request)
    {
        // Valida os inputs informados
        $validated = $request->validate([
            'ds_email_recuperacao' => ['required', 'email', 'max:255'],
        ]);

        // Obtém o usuário autenticado
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ], 401);
        }

        try {
            // Atualiza o email de recuperação do usuário
            Tb_Usuario::where('id', $user->id)->update(['ds_email_recuperacao' => $validated['ds_email_recuperacao']]);

            return response()->json([
                'success' => true,
                'message' => 'Email de recuperação atualizado com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar email de recuperação: ' . $e->getMessage()
            ], 500);
        }
    }

    public function alterarNome(Request $request)
    {
        // Valida os inputs informados
        $validated = $request->validate([
            'nm_usuario' => ['required', 'regex:/^[a-zA-ZÀ-ÿ\s]+$/u', 'min:3', 'max:16'],
        ]);

        // Obtém o usuário autenticado
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ], 401);
        }

        try {
            // Atualiza o telefone do usuário
            Tb_Usuario::where('id', $user->id)->update(['nm_usuario' => $validated['nm_usuario']]);

            return response()->json([
                'success' => true,
                'message' => 'Nome atualizado com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar o nome: ' . $e->getMessage()
            ], 500);
        }
    }

    public function atualizarFotoPerfil(Request $request)
    {
        if (!$request->hasFile('profile_image')) {
            Log::warning('Nenhum arquivo recebido no upload de imagem de perfil.');
            return response()->json(['status' => 'erro', 'mensagem' => 'Nenhum arquivo recebido.'], 400);
        }

        $userId = Auth::id();
        $image = $request->file('profile_image');

        // Define o caminho da pasta do usuário
        $folder = "user_{$userId}";

        // Excluir imagens existentes com o nome "profile" em qualquer extensão
        $extensions = ['.webp', '.png', '.jpg', '.jpeg'];
        foreach ($extensions as $extension) {
            $imagePath = "{$folder}/profile{$extension}";
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath); // Exclui a imagem existente
                Log::info('Imagem de perfil existente excluída:', ['path' => $imagePath]);
            }
        }

        // Pega a extensão original da imagem
        $extension = $image->getClientOriginalExtension();
        $filename = "profile.{$extension}"; // O nome do arquivo será sempre "profile.ext"

        // Salva a nova imagem
        $path = $image->storeAs($folder, $filename, 'public');

        Log::info('Imagem de perfil salva com sucesso em:', ['path' => $path]);

        $user = Auth::user();
        $user->im_usuario = '0';
        $user->save();

        return response()->json(['status' => 'recebido', 'path' => $path]);
    }

    public function escolherImagemPadrão(Request $request)
    {
        $imgNum = $request->imgNum;

        // Verifica se o número é válido (entre 1 e 8)
        if (!in_array($imgNum, range(1, 8))) {
            return response()->json(['error' => 'Número de imagem inválido.'], 400);
        }

        $user = Auth::user();
        $userId = $user->id;

        $userDefaultFolder = public_path('storage/user_default');
        $userFolder = public_path("storage/user_{$userId}");

        $sourceImage = $userDefaultFolder . "/{$imgNum}.png";
        $destinationImage = $userFolder . "/profile.png";

        if (!File::exists($sourceImage)) {
            return response()->json(['error' => 'Imagem padrão não encontrada.'], 404);
        }

        // Cria a pasta do usuário, se não existir
        if (!File::exists($userFolder)) {
            File::makeDirectory($userFolder, 0755, true);
        }

        // Copia a imagem padrão para a pasta do usuário
        File::copy($sourceImage, $destinationImage);

        $user->im_usuario = (string) $imgNum;
        $user->save();

        // Retorna o caminho relativo da imagem para uso no frontend
        return response()->json(['path' => "user_{$userId}/profile.png"]);
    }

    public function atualizarSenha(Request $request)
    {
        // Validação dos dados
        $request->validate([
            'ds_email' => 'email|exists:tb_usuario,ds_email',
            'cd_senha' => 'string|min:8',
        ]);
    
        // Normaliza o email (remove espaços e converte para minúsculas)
        $email = strtolower(trim($request->ds_email));
        Log::info('Email recebido para redefinição de senha: ' . $request->ds_email);

    
        // Busca o usuário
        $usuario = tb_usuario::where('ds_email', $email)->first();
    
        if (!$usuario) {
            return response()->json([
                'message' => 'Usuário não encontrado no sistema.'
            ], 404);
        }
    
        // Atualiza a senha com hash
        $usuario->cd_senha = Hash::make($request->cd_senha);
        $usuario->save();
    
        return response()->json([
            'message' => 'Senha atualizada com sucesso.'
        ]);
    }

    public function desativarConta(Request $request)
    {
        try {
            $user = Auth::user();
            \Log::info('Usuário antes da atualização:', ['user_id' => $user->id, 'ic_suspensao' => $user->ic_suspensao]);
            
            $updated = \DB::table('tb_usuario')
                        ->where('id', $user->id)
                        ->update([
                            'ic_suspensao' => true,
                            'dt_suspensao' => Carbon::now('America/Sao_Paulo'),
                        ]);
            
            // Debug: Verifique o resultado da atualização
            \Log::info('Resultado da atualização:', ['updated' => $updated]);
            
            if ($updated === 0) {
                throw new \Exception('Nenhum registro foi atualizado');
            }
            
            // Recarregar o usuário para verificar a mudança
            $user->refresh();
            \Log::info('Usuário após atualização:', ['ic_suspensao' => $user->ic_suspensao]);
            
            // Desloga o usuário
            Auth::logout();
            
            // Invalida a sessão atual
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return response()->json([
                'success' => true,
                'message' => 'Sua conta foi desativada com sucesso.',
                'debug' => [
                    'updated' => $updated,
                    'current_status' => $user->ic_suspensao
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao desativar conta: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao desativar conta: ' . $e->getMessage(),
                'debug' => [
                    'user_id' => Auth::id() ?? null,
                    'error' => $e->getTraceAsString()
                ]
            ], 500);
        }
    }    
}
