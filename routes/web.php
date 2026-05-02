<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ScriptController;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;
use Illuminate\Broadcasting\BroadcastController;
use Illuminate\Support\Facades\Http;

Route::post('/broadcasting/auth', [BroadcastController::class, 'authenticate'])
    ->middleware('auth');

Broadcast::routes();

//-------------------------------------------------------------UserController-------------------------------------------------------------

//Rota para página incial
Route::get('/', [UserController::class, 'páginaIncial'])->name('home');

//Rota para criação de conta
Route::post('/registrar', [UserController::class, 'registrar']);

//Rota para mandar o código de verificação
Route::post('/enviarCodigo', [UserController::class, 'enviarCodigo']);

//Rota para verificar o código
Route::post('/verificarCodigo', [UserController::class, 'verificarCodigo']);

//Rota para login
Route::post('/entrar', [UserController::class, 'entrar']);

//Rota para atualizar senha
Route::put('/atualizar_senha', [UserController::class, 'atualizarSenha']);

//Rota para verificação de logout
Route::post('/sair', [UserController::class, 'sair']);

//Rota para verificação de email
Route::post('/verificarEmail', [UserController::class, 'verificarEmail']);

//Rota para verificação de senha
Route::post('/verificarSenha', [UserController::class, 'verificarSenha']);

//Rota para alteração de telefone
Route::post('/alterarEmailRecuperacao', [UserController::class, 'alterarEmailRecuperacao']);

//Rota para alteração de nome
Route::post('/alterarNome', [UserController::class, 'alterarNome']);

//Atualizar foto de perfil do usuário
Route::post('/atualizarFotoPerfil', [UserController::class, 'atualizarFotoPerfil'])->name('atualizarFotoPerfil');

//Alterar foto padrão do usuário
Route::post('/escolherImagemPadrão', [UserController::class, 'escolherImagemPadrão']);

//Desativa a conta do usuário logado
Route::post('/desativarConta', [UserController::class, 'desativarConta'])
    ->middleware(['auth']);

//-------------------------------------------------------------ProjectController-------------------------------------------------------------

//Rota para criação de projeto
Route::post('/criarProjeto', [ProjectController::class, 'criarProjeto']);

//Rota para criação de projeto
Route::post('/consultarProjeto', [ProjectController::class, 'consultarProjeto']);

//Salvar id do projeto
Route::post('/guardarProjeto', [ProjectController::class, 'guardarProjeto']);

//Abrir projeto
Route::get('/abrirProjeto', [ProjectController::class, 'abrirProjeto']);

//Fechar projeto
Route::post('/fecharProjeto', [ProjectController::class, 'fecharProjeto']);

//Rota para criação de projeto
Route::put('/editarProjeto/{id}', [ProjectController::class, 'editarProjeto']);

//Deletar projeto
Route::post('/deletarProjeto', [ProjectController::class, 'deletarProjeto']);

//Importar frames
Route::post('/importarFrames', [ProjectController::class, 'importarFrames']);

//Importar vídeo
Route::post('/importarVideo', [ProjectController::class, 'importarVideo']);

//Resgatar progresso da importação de vídeo
Route::get('/resgatarProgresso', function (Request $request) {
    $projectId = session('proj_id', null);
    if (!$projectId) {
        return response()->json(['error' => 'ID do projeto não encontrado'], 400);
    }

    $progress = Cache::get("video_progress_{$projectId}", 0);
    return response()->json(['progress' => $progress]);
});

//Atualizar FPS
Route::put('/atualizarFPS/{id}', [ProjectController::class, 'atualizarFPS']);

//Deletar frames
Route::delete('/deletarFrames/{id}', [ProjectController::class, 'deletarFrames']);

//Atualizar data de modificação do projeto
Route::post('/atualizarDataModificação', [ProjectController::class, 'atualizarDataModificação']);

//Resgatar FPS do vídeo
Route::post('/resgagarFPS', [ProjectController::class, 'resgagarFPS']);

//Atualizar volume do áudio
Route::put('/atualizarVolume/{id}', [ProjectController::class, 'atualizarVolume']);

//Rota para criação de cena
Route::post('/criarCena/{id}', [ProjectController::class, 'criarCena']);

//Rota para edição de cena
Route::post('/editarCena/{id}', [ProjectController::class, 'editarCena']);

//Rota para ligar cenas
Route::post('/ligarCena', [ProjectController::class, 'ligarCena']);

//Rota para desligar cenas
Route::post('/desligarCena', [ProjectController::class, 'desligarCena']);

//Rota para compartilhamento de projeto
Route::post('/compartilharProjeto/{id}', [ProjectController::class, 'compartilharProjeto']);

//Rota para salvar nova permissão automaticamente
Route::post('/salvarNovoCargo/{id}', [ProjectController::class, 'salvarNovoCargo']);

//Rota para enviar um comentário
Route::post('/enviarComentario/{id}', [ProjectController::class, 'enviarComentario']);

//Rota para salvar um comentário
Route::post('/salvarComentario/{id}', [ProjectController::class, 'salvarComentario']);

//Mostrar comentário
Route::get('/comentarios/{proj_id}', [ProjectController::class, 'getComentarios']);

//Rota para enviar um erro
Route::post('/enviarErro/{id}', [ProjectController::class, 'enviarErro']);

//Rota para salvar um erro
Route::post('/salvarErro/{id}', [ProjectController::class, 'salvarErro']);

//Rota para corrigir um erro
Route::put('/corrigirErro/{id}', [ProjectController::class, 'corrigirErro']);

//Mostrar Erro
Route::get('/comentariosErro/{proj_id}', [ProjectController::class, 'getComentariosErro']);

//Rota para importar bloco de roteiro
Route::post('/importarRoteiro/{id}', [ProjectController::class, 'importarRoteiro']);

//Rota para importar bloco de roteiro
Route::post('/removerRoteiro/{id}', [ProjectController::class, 'removerRoteiro']);

//Rota para salvar proporção do vídeo
Route::post('/salvarProporcao/{id}', [ProjectController::class, 'salvarProporcao']);

//Exportar vídeo
Route::post('/exportarVideo/{id}', [ProjectController::class, 'exportarVideo']);

//Exportar frames
Route::post('/exportarFrames/{id}', [ProjectController::class, 'exportarFrames']);

//Exportar frames
Route::post('/exportarFramesSelecionados/{id}', [ProjectController::class, 'exportarFramesSelecionados']);

//Substituir frames
Route::post('/substituirFrame/{id}', [ProjectController::class, 'substituirFrame']);

//Mover frames
Route::post('/moverFrame/{id}', [ProjectController::class, 'moverFrame']);

//Exportar gif
Route::post('/exportarGif/{id}', [ProjectController::class, 'exportarGif']);

//Atualizar situação da cena
Route::post('/atualizarSituacaoCena/{id}', [ProjectController::class, 'atualizarSituacaoCena']);

//Mostrar gráfico atualizado
Route::get('/graficoData/{id}', [ProjectController::class, 'getGraficoData']);

Route::get('/contar_frames/{projectId}', [ProjectController::class, 'contarFrames']); //!!! Não sei o que é

//-------------------------------------------------------------ScriptController-------------------------------------------------------------

//Rota para criação de bloco de roteiro
Route::post('/criarRoteiro', [ScriptController::class, 'criarRoteiro']);

//Rota para criação de bloco de roteiro
Route::post('/editarRoteiro/{id}', [ScriptController::class, 'editarRoteiro']);

//Rota para criação de projeto
Route::post('/consultarRoteiro', [ScriptController::class, 'consultarRoteiro']);

//Salvar id do bloco de roteiro
Route::post('/guardarRoteiro', [ScriptController::class, 'guardarRoteiro']);

//Abrir bloco de roteiro
Route::get('/abrirRoteiro', [ScriptController::class, 'abrirRoteiro']);

//Deletar roteiro
Route::post('/deletarRoteiro', [ScriptController::class, 'deletarRoteiro']);

//Salvar texto do roteiro automaticamente
Route::post('/salvarTexto', [ScriptController::class, 'salvarTexto']);

//Rota para criação de cena
Route::post('/criarCenaRoteiro/{id}', [ScriptController::class, 'criarCenaRoteiro']);

Route::get('/ultimacena', [ScriptController::class, 'ultimaCenaCriada']);

//Rota para edição de cena
Route::post('/editarCenaRoteiro/{id}', [ScriptController::class, 'editarCenaRoteiro']);

//Rota para deleção de cena
Route::post('/deletarCenaRoteiro/{id}', [ScriptController::class, 'deletarCenaRoteiro']);

Route::post('/alterarCena', function (Illuminate\Http\Request $request) {

    if ($request->has('id_cena_roteiro')) {
        session(['id_cena_roteiro' => $request->id_cena_roteiro]);
        return response()->json(['success' => true]);
    }
    return response()->json(['error' => 'id_cena_roteiro não foi enviado'], 400);
});

//Rota para compartilhamento de roteiro
Route::post('/compartilharRoteiro/{id}', [ScriptController::class, 'compartilharRoteiro']);

//Rota para salvar nova permissão automaticamente
Route::post('/salvarNovoCargoRoteiro/{id}', [ScriptController::class, 'salvarNovoCargo']);

//Rota para enviar um comentário
Route::post('/enviarComentarioRoteiro/{id}', [ScriptController::class, 'enviarComentarioRoteiro']);

//Rota para salvar um comentário
Route::post('/salvarComentarioRoteiro/{id}', [ScriptController::class, 'salvarComentarioRoteiro']);

//Mostrar comentário
Route::get('/comentariosRoteiro/{id}', [ScriptController::class, 'getComentarios']);

//Rota para enviar um erro
Route::post('/enviarErroRoteiro/{id}', [ScriptController::class, 'enviarErroRoteiro']);

//Rota para salvar um erro
Route::post('/salvarErroRoteiro/{id}', [ScriptController::class, 'salvarErroRoteiro']);

//Rota para corrigir um erro
Route::put('/corrigirErroRoteiro/{id}', [ScriptController::class, 'corrigirErroRoteiro']);

//Mostrar erro
Route::get('/comentariosErroRoteiro/{id}', [ScriptController::class, 'getComentariosErroRoteiro']);

//Fechar projeto
Route::post('/fecharRoteiro', [ScriptController::class, 'fecharRoteiro']);

//-------------------------------------------------------------ContactController-------------------------------------------------------------

//Contato
Route::post('/contato', [ContactController::class, 'enviarFeedback'])->name('contato.enviar');

//-------------------------------------------------------------Partials-------------------------------------------------------------

Route::get('/cenasProjeto', function () {
    $id_projeto = session('id_projeto');
    $cena = \App\Models\Tb_Cena_Projeto::where('id_projeto', $id_projeto)->get();
    return view('partials.cenasProjeto', compact('cena', 'id_projeto'));
});

//-------------------------------------------------------------API-------------------------------------------------------------

Route::get('/api/google-fonts', function () {
    $apiKey = env('GOOGLE_FONTS_KEY');
    $response = Http::get("https://www.googleapis.com/webfonts/v1/webfonts?key={$apiKey}");
    
    return $response->json();
});