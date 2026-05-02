<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\Tb_Projeto;
use App\Models\tb_usuario;
use App\Models\Tb_Roteiro;
use App\Models\Tb_Cena_Roteiro;
use App\Models\tb_contribuidor;
use App\Models\Tb_Cena_Projeto;
use DB;
use Illuminate\Support\Facades\Storage;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Log;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFProbe;
use Illuminate\Support\Facades\Cache;
use App\Events\NovoComentario;
use App\Events\NovoErro;
use App\Models\Tb_Comentario;
use App\Models\Tb_Erro;
use Illuminate\Support\Carbon;

class ProjectController extends Controller
{
    //Criar projeto
    public function criarProjeto(Request $request)
    {
        $informed_inputs = $request->validate([
            'nm_projeto' => ['required', 'min:3', 'max:45'],
            'ds_projeto' => ['required', 'max:500'],
            'id_tipo' => ['required'],
            'proj_img_import' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Associar ao usuário logado
        $user = Auth::user();
        $informed_inputs['id_usuario'] = $user->id;
        $informed_inputs['dt_inicial'] = date('Y-m-d H:i:s');

        // Criar o projeto
        $proj = tb_projeto::create($informed_inputs);

        // Criar diretório de imagem, se tiver imagem enviada
        if ($request->hasFile('proj_img_import')) {
            $folder = "proj_{$proj->id}/CoverImage";
            if (!Storage::disk('public')->exists($folder)) {
                Storage::disk('public')->makeDirectory($folder);
            }

            $image = $request->file('proj_img_import');
            $filename = 'cover.' . $image->getClientOriginalExtension();

            $image->storeAs($folder, $filename, 'public');
        }

        // Atualizar quantidade de projetos
        /** @var \App\Models\tb_usuario $user */
        $user->increment('qt_projeto');

        return redirect('/');
    }

    //Salvar id do projeto
    public function guardarProjeto(Request $request)
    {
        // Valida se o ID do projeto foi enviado
        $request->validate([
            'id_projeto' => 'required|integer|exists:tb_projeto,id', // Certifique-se de que o ID existe na tabela de projetos
        ]);

        // Salva o ID do projeto na sessão
        session(['id_projeto' => $request->id_projeto]);

        // Apenas retorne sucesso
        return response()->json(['success' => true]);
    }

    //Abrir projeto
    public function abrirProjeto()
    {
        $projectId = session('id_projeto', null);

        if (!$projectId) {
            return redirect()->route('home')->with('error', 'Nenhum projeto foi selecionado.');
        }        

        $folderPath = "proj_{$projectId}";
        $images = [];
        $audios = [];

        if (Storage::disk('public')->exists($folderPath)) {
            // Ordenar os arquivos de frames numericamente
            $files = collect(Storage::disk('public')->files($folderPath))
                ->filter(fn($file) => preg_match('/frame_\d+\.\w+$/', $file)) 
                ->sortBy(function ($file) {
                    preg_match('/frame_(\d+)\.\w+$/', $file, $matches);
                    return isset($matches[1]) ? (int)$matches[1] : PHP_INT_MAX;
                })
                ->values();

            $images = $files->map(fn($file) => asset("storage/{$file}"));

            // Verifica se a pasta de áudios existe
            $audioFolderPath = "{$folderPath}/audioFiles";
            if (Storage::disk('public')->exists($audioFolderPath)) {
                $audioFiles = collect(Storage::disk('public')->files($audioFolderPath))
                    ->filter(fn($file) => preg_match('/audio_\d+\.mp3$/', $file))
                    ->sortBy(function ($file) {
                        preg_match('/audio_(\d+)\.mp3$/', $file, $matches);
                        return isset($matches[1]) ? (int)$matches[1] : PHP_INT_MAX;
                    })
                    ->values();

                $audios = $audioFiles->map(fn($file) => asset("storage/{$file}"));
            }

        } else {
            if (Storage::disk('public')->makeDirectory($folderPath)) {
                session()->flash('success', 'Pasta do projeto criada com sucesso.');
            } else {
                session()->flash('error', 'Erro ao criar a pasta do projeto.');
            }
        }

        $cena = Tb_Cena_Projeto::where('id_projeto', $projectId)->get();

        $project = Tb_Projeto::find($projectId);
        $ownerId = $project?->id_usuario ?? null;
        $contribuidorIds = tb_contribuidor::where('id_projeto', $projectId)->pluck('id_usuario')->toArray();

        $allUserIds = collect([$ownerId])->merge($contribuidorIds)->unique()->filter()->values();
        $users = tb_usuario::whereIn('id', $allUserIds)->get();

        $comentarios = Tb_Comentario::where('id_projeto', $projectId)->get();
        $erros = Tb_Erro::where('id_projeto', $projectId)->get();

        //--- INÍCIO: lógica de cálculo de progresso copiada de nomeDoMetodoQueRetornaViewProject ---
        $totalCenas = Tb_Cena_Projeto::where('id_projeto', $projectId)->count();
        $cenasFinalizadas = Tb_Cena_Projeto::where('id_projeto', $projectId)
                            ->where('ic_conclusao', 1)
                            ->count();

        $percentual = 0;
        if ($totalCenas > 0) {
            $percentual = ($cenasFinalizadas / $totalCenas) * 100;
        }
        $percentual = round($percentual, 2);

        // Definindo a cor com base no percentual
        $corProgresso = '#FF0000';
        if ($percentual >= 100) {
            $corProgresso = '#3ED582';
        } elseif ($percentual >= 45) {
            $corProgresso = '#D5B73E';
        }

        $circunferencia    = 100;
        $valorDash         = ($percentual / 100) * $circunferencia;
        $strokeDasharray   = $valorDash . ', ' . $circunferencia;

        Log::info('Dados do progresso do projeto:', [
            'percentual'       => $percentual,
            'corProgresso'     => $corProgresso,
            'totalCenas'       => $totalCenas,
            'cenasFinalizadas' => $cenasFinalizadas
        ]);
        //--- FIM: lógica de cálculo de progresso ---

        return view('project', [
            'id_projeto'     => $projectId,
            'cena'           => $cena,
            'images'         => $images,
            'audios'         => $audios,
            'usuarios'       => $users,
            'comentarios'    => $comentarios,
            'erros'          => $erros,
            'percentual'     => $percentual,
            'corProgresso'   => $corProgresso,
            'strokeDasharray'=> $strokeDasharray,
        ]);
    }

    //Fechar Projeto
    public function fecharProjeto()
    {       
        return redirect('/');
    }

    // Validar Nome de Projeto antes de enviar
    public function consultarProjeto(Request $request)
    {
        $nm_projeto = $request->input('nm_projeto');
        $userId = Auth::user()->id;

        // Verificar se já existe um projeto com esse nome para o usuário
        $projetoExists = Tb_Projeto::where('nm_projeto', $nm_projeto)
            ->where('id_usuario', $userId)
            ->exists();

        // Contar quantos projetos o usuário já tem
        $projetoCount = Tb_Projeto::where('id_usuario', $userId)->count();

        // Retornar o resultado como JSON
        return response()->json([
            'exists' => $projetoExists,
            'currentCount' => $projetoCount
        ]);
    }

    //Editar projeto
    public function editarProjeto(Request $request, $id)
    {
        $informed_inputs = $request->validate([
            'nm_projeto' => ['required', 'min:3', 'max:20'],
            'ds_projeto' => ['required', 'max:500'],
            'id_tipo' => ['required'],
            'proj_img_import' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $proj = Tb_Projeto::find($id);
        if (!$proj) {
            return redirect()->back()->with('error', 'Projeto não encontrado.');
        }

        $informed_inputs['id_usuario'] = $proj->id_usuario;

        $proj->update($informed_inputs);

        // Se uma nova imagem foi enviada
        if ($request->hasFile('proj_img_import')) {
            $folder = "proj_{$proj->id}/CoverImage";

            // Remove todas as imagens existentes na pasta
            Storage::disk('public')->deleteDirectory($folder);
            Storage::disk('public')->makeDirectory($folder);

            // Salva a nova imagem com a extensão original
            $image = $request->file('proj_img_import');
            $extension = $image->getClientOriginalExtension();
            $filename = 'cover.' . $extension;

            $image->storeAs($folder, $filename, 'public');
        }

        return redirect()->back()->with('success', 'Projeto atualizado com sucesso!');
    }

    // Importar imagens
    public function importarFrames(Request $request)
    {
        $request->validate([
            'image' => 'required',
            'image.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'id_projeto' => 'required|integer',
        ]);

        $projectId = $request->id_projeto;
        $folderPath = "proj_{$projectId}"; // Remove a duplicação de "public"

        // Garante que o diretório existe dentro de storage/app/public/
        if (!Storage::disk('public')->exists($folderPath)) {
            Storage::disk('public')->makeDirectory($folderPath);
        }

        // Lista os arquivos existentes para nomeação sequencial
        $files = collect(Storage::disk('public')->files($folderPath))
            ->filter(fn($file) => preg_match('/frame_\d+\.\w+$/', $file));

        $frameNumber = $files->count() + 1;
        $paths = [];

        // Verifica se há apenas um arquivo ou vários
        $images = is_array($request->file('image')) ? $request->file('image') : [$request->file('image')];

        foreach ($images as $image) {
            $extension = $image->getClientOriginalExtension();
            $fileName = "frame_{$frameNumber}.{$extension}";

            // Salva corretamente dentro de storage/app/public/proj_X/
            $path = $image->storeAs($folderPath, $fileName, 'public');
            $paths[] = asset("storage/{$folderPath}/{$fileName}");

            $frameNumber++;
        }

        return response()->json(['success' => true, 'paths' => $paths]);
    }

    public function importarVideo(Request $request)
    {
        ignore_user_abort(true);
        set_time_limit(0);
        ini_set('max_execution_time', 0);

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        ob_implicit_flush(true);

        // Função auxiliar para enviar SSE
        $sendEvent = function ($data) {
            echo "data: " . json_encode($data) . "\n\n";
            @ob_flush();
            flush();
        };

        // Validação do arquivo de vídeo
        $request->validate([
            'import_video' => 'required|mimes:mp4,avi,mkv,flv|max:512000',
        ]);

        $videoFile = $request->file('import_video');
        if (!$videoFile->isValid()) {
            $sendEvent(['resgatarProgresso' => 100, 'error' => 'Arquivo de vídeo inválido.']);
            exit();
        }

        // Armazena o vídeo temporariamente
        $tempPath = $videoFile->storeAs('temp', $videoFile->getClientOriginalName(), 'public');
        $videoPath = storage_path('app/public/' . $tempPath);

        // Obtém o ID do projeto
        $projectId = session('id_projeto', null);
        if (!$projectId) {
            $sendEvent(['resgatarProgresso' => 100, 'error' => 'ID do projeto não encontrado.']);
            exit();
        }

        $sendEvent(['resgatarProgresso' => 0]);

        // Configura os caminhos dos binários do FFmpeg/FFprobe (estão dentro do projeto)
        $ffmpegPath  = public_path('ffmpeg-7.1.1-essentials_build/bin/ffmpeg.exe');
        $ffprobePath = public_path('ffmpeg-7.1.1-essentials_build/bin/ffprobe.exe');

        // Obter informações do vídeo via FFProbe (php-FFMpeg)
        $ffprobe = \FFMpeg\FFProbe::create([
            'ffmpeg.binaries'  => $ffmpegPath,
            'ffprobe.binaries' => $ffprobePath,
        ]);

        try {
            $frameRateRaw = $ffprobe->streams($videoPath)->videos()->first()->get('avg_frame_rate');
            $fps = eval('return ' . $frameRateRaw . ';');
            $duration = $ffprobe->format($videoPath)->get('duration');
            $totalFrames = intval($fps * $duration);
            if (!$fps || !$duration) {
                throw new \Exception("Erro ao obter FPS ou duração.");
            }
        } catch (\Exception $e) {
            \Log::error("Erro ao obter dados do vídeo: " . $e->getMessage());
            $sendEvent(['resgatarProgresso' => 100, 'error' => 'Erro ao obter dados do vídeo.']);
            exit();
        }

        // Cria o diretório de saída para os frames
        $folderPath = "proj_{$projectId}";
        Storage::disk('public')->makeDirectory($folderPath);

        $sendEvent(['resgatarProgresso' => 5]);

        // Define o padrão de saída para os frames; observe que usamos "frame_%04d.jpg" (ou .png, .jpeg, etc.)
        // Garanta que cada frame seja extraído como uma imagem separada e não como animação.
        $outputPattern = storage_path("app/public/{$folderPath}/frame_%04d.jpg");  // Usando .jpg para evitar animação

        /*
        * Monta o comando FFmpeg para extrair todos os frames:
        * - Cada caminho é envolvido em aspas duplas para que os espaços sejam respeitados.
        * - -vsync 0 e -frame_pts 1 garantem a extração de todos os frames.
        * - -q:v 1 define a melhor qualidade.
        * - -progress pipe:1 faz com que o FFmpeg emita informações de progresso.
        */
        $command = '"' . $ffmpegPath . '" -i "' . $videoPath . '" -vsync 0 -frame_pts 1 -q:v 1 "' . $outputPattern . '"';

        \Log::info("Comando FFmpeg: {$command}");

        // Executa o comando FFmpeg usando exec() e captura a saída
        $output = [];
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            \Log::error('Erro ao executar o FFmpeg: ' . implode("\n", $output));
            $sendEvent(['resgatarProgresso' => 100, 'error' => 'Erro ao extrair frames.']);
            exit();
        }

        // Contabiliza os frames extraídos para atualizar o progresso (por exemplo, de 5% até 80%)
        $frameFiles = glob(storage_path("app/public/{$folderPath}/frame_*.jpg"));
        $extractedFrames = is_array($frameFiles) ? count($frameFiles) : 0;
        $progressExtract = 5 + (($extractedFrames / $totalFrames) * 75); // varia de 5% até 80%
        $progressExtract = min(round($progressExtract), 80);
        $sendEvent(['resgatarProgresso' => $progressExtract]);

        $sendEvent(['resgatarProgresso' => 80]);

        // Extração do áudio utilizando o serviço customizado
        $audioFolderPath = "{$folderPath}/audioFiles";
        Storage::disk('public')->makeDirectory($audioFolderPath);

        $audioService = new \App\Services\FFmpegService();
        $audioNumber = collect(Storage::disk('public')->files($audioFolderPath))
                        ->filter(fn($file) => preg_match('/audio_\d+\.mp3$/', $file))
                        ->count() + 1;
        $audioOutputPath = "{$audioFolderPath}/audio_{$audioNumber}.mp3";
        $audioResult = $audioService->extractAudio($tempPath, $audioOutputPath);

        // Coleta os caminhos dos frames extraídos
        $paths = collect(Storage::disk('public')->files($folderPath))
                    ->filter(fn($file) => preg_match('/frame_\d{4}\.jpg$/', $file))
                    ->sort()
                    ->map(fn($file) => asset("storage/{$file}"))
                    ->values()
                    ->all();

        $sendEvent([
            'resgatarProgresso' => 100,
            'success'           => true,
            'paths'             => $paths,
            'audio_path'        => $audioResult['success'] ? asset("storage/{$audioOutputPath}") : null,
        ]);

        exit();
    }

    public function exportarVideo(Request $request, $id)
    {
        $projeto     = Tb_Projeto::findOrFail($id);
        $nomeProjeto = str_replace(' ', '_', $projeto->nm_projeto);

        $fps         = (int) $request->input('fps', 30);
        $aspectRatio = preg_replace('/[^0-9:]/', '', $request->input('proporcao'));
        $format      = strtolower($request->input('format', 'mp4'));
        $volume      = max(0.0, min(1.0, (float) $request->input('volume', 1.0)));
        $includeAudio = filter_var($request->input('audio', true), FILTER_VALIDATE_BOOLEAN);

        $aspectRatios = [
            '16:9'  => [1920, 1080],
            '16:10' => [1920, 1200],
            '9:16'  => [1080, 1920],
        ];
        if (!isset($aspectRatios[$aspectRatio])) {
            throw new \Exception("Proporção inválida");
        }
        [$width, $height] = $aspectRatios[$aspectRatio];

        $allowedFormats = ['mp4', 'webm', 'mkv', 'avi'];
        if (!in_array($format, $allowedFormats)) {
            throw new \Exception("Formato inválido");
        }

        $folder          = "proj_{$id}";
        $frameDir        = storage_path("app/public/{$folder}");
        $audioPath       = storage_path("app/public/{$folder}/audioFiles/audio_1.mp3");
        $outputFilename  = "{$nomeProjeto}.{$format}";
        $outputVideoPath = storage_path("app/public/{$folder}/{$outputFilename}");

        Storage::makeDirectory("public/{$folder}");
        if (file_exists($outputVideoPath)) {
            unlink($outputVideoPath);
        }

        $all    = scandir($frameDir);
        $frames = array_filter($all, fn($f) => preg_match('/^frame_\d{4}\.(jpg|webp)$/i', $f));
        if (empty($frames)) {
            Log::error("Nenhum frame em {$frameDir}: " . json_encode($all));
            throw new \Exception("Nenhum frame encontrado.");
        }
        sort($frames);
        $ext = pathinfo($frames[0], PATHINFO_EXTENSION);

        if ($includeAudio && !file_exists($audioPath)) {
            Log::error("Áudio não encontrado em: {$audioPath}");
            throw new \Exception("Áudio não encontrado em: {$audioPath}");
        }

        if ($format === 'webm') {
            $vCodec = 'libvpx-vp9';
            $aCodec = 'libopus';
        } else {
            $vCodec = 'libx264';
            $aCodec = 'aac';
        }

        $audioFilter = "volume={$volume}";

        $ffmpegPath  = str_replace('\\', '/', public_path('ffmpeg-7.1.1-essentials_build/bin/ffmpeg.exe'));
        $frameDir    = str_replace('\\', '/', $frameDir);
        $audioPath   = str_replace('\\', '/', $audioPath);
        $outPath     = str_replace('\\', '/', $outputVideoPath);

        $vf = "scale={$width}:{$height}:force_original_aspect_ratio=decrease,"
            . "pad={$width}:{$height}:(ow-iw)/2:(oh-ih)/2:color=black";

        $cmd = "\"{$ffmpegPath}\""
            . " -framerate {$fps}"
            . " -i \"{$frameDir}/frame_%04d.{$ext}\"";

        if ($includeAudio) {
            $cmd .= " -i \"{$audioPath}\"";
        }

        $cmd .= " -vf \"{$vf}\"";

        if ($includeAudio) {
            $cmd .= " -af \"{$audioFilter}\""
                . " -c:a {$aCodec}"
                . " -shortest";
        } else {
            $cmd .= " -an";
        }

        $cmd .= " -c:v {$vCodec}";
        $cmd .= " -y \"{$outPath}\"";

        Log::info("CMD FFmpeg: {$cmd}");

        exec($cmd . ' 2>&1', $outputLines, $returnVar);

        if ($returnVar !== 0) {
            Log::error("FFmpeg falhou com código {$returnVar}. Saída completa:\n" . implode("\n", $outputLines));
            throw new \Exception("Falha ao gerar vídeo. Veja os logs para mais detalhes.");
        }

        return response()
            ->download($outputVideoPath, $outputFilename)
            ->deleteFileAfterSend(true);
    }

    public function exportarFrames(Request $request, $id)
    {
        $projeto     = Tb_Projeto::findOrFail($id);
        $nomeProjeto = str_replace(' ', '_', $projeto->nm_projeto);

        $aspectRatio = preg_replace('/[^0-9:]/', '', $request->input('proporcao'));
        $format      = strtolower($request->input('format', 'png'));

        $allowedFormats = ['png', 'jpg', 'jpeg', 'bmp', 'tiff', 'webp'];
        if (!in_array($format, $allowedFormats)) {
            throw new \Exception("Formato inválido");
        }

        $aspectRatios = [
            '16:9'  => [1920, 1080],
            '16:10' => [1920, 1200],
            '9:16'  => [1080, 1920],
        ];
        if (!isset($aspectRatios[$aspectRatio])) {
            throw new \Exception("Proporção inválida");
        }
        [$width, $height] = $aspectRatios[$aspectRatio];

        $folder   = "proj_{$id}";
        $frameDir = storage_path("app/public/{$folder}");
        $zipName  = "frames_{$nomeProjeto}.zip";
        $zipPath  = storage_path("app/public/{$folder}/{$zipName}");
        $tempDir  = storage_path("app/public/{$folder}/export_frames_temp");
        $tarPath  = storage_path("app/public/{$folder}/frames_{$nomeProjeto}.tar");

        Storage::makeDirectory("public/{$folder}");
        if (file_exists($zipPath)) {
            unlink($zipPath);
        }
        if (file_exists($tarPath)) {
            unlink($tarPath);
        }
        if (file_exists($tempDir)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($tempDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $fileinfo) {
                $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                $todo($fileinfo->getRealPath());
            }
            rmdir($tempDir);
        }
        mkdir($tempDir);

        $all    = scandir($frameDir);
        $frames = array_filter($all, fn($f) => preg_match('/^frame_\d{4}\.(jpg|webp)$/i', $f));
        if (empty($frames)) {
            Log::error("Nenhum frame em {$frameDir}: " . json_encode($all));
            throw new \Exception("Nenhum frame encontrado.");
        }
        sort($frames);
        $ext = pathinfo($frames[0], PATHINFO_EXTENSION);

        $ffmpegPath = str_replace('\\', '/', public_path('ffmpeg-7.1.1-essentials_build/bin/ffmpeg.exe'));
        $frameDir   = str_replace('\\', '/', $frameDir);
        $tempDir    = str_replace('\\', '/', $tempDir);

        $vf = "scale={$width}:{$height}:force_original_aspect_ratio=decrease,"
            . "pad={$width}:{$height}:(ow-iw)/2:(oh-ih)/2:color=black";

        $inputPattern  = "\"{$frameDir}/frame_%04d.{$ext}\"";
        $outputPattern = "\"{$tempDir}/frame_%04d.{$format}\"";

        $cmd = "\"{$ffmpegPath}\""
            . " -i {$inputPattern}"
            . " -vf \"{$vf}\""
            . " -y {$outputPattern}";

        Log::info("CMD FFmpeg (frames): {$cmd}");
        exec($cmd . ' 2>&1', $outputLines, $returnVar);
        if ($returnVar !== 0) {
            Log::error("FFmpeg (frames) falhou com código {$returnVar}. Saída completa:\n" . implode("\n", $outputLines));
            throw new \Exception("Falha ao gerar frames. Veja os logs para mais detalhes.");
        }

        $phar = new \PharData($tarPath);
        $phar->buildFromDirectory($tempDir);
        $phar->convertToData(\Phar::ZIP);

        $generatedZip = str_replace('.tar', '.zip', $tarPath);
        if (!file_exists($generatedZip)) {
            throw new \Exception("Falha ao criar ZIP intermediário.");
        }
        rename($generatedZip, $zipPath);

        if (file_exists($tarPath)) {
            unlink($tarPath);
        }
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($tempDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }
        rmdir($tempDir);

        return response()
            ->download($zipPath, $zipName)
            ->deleteFileAfterSend(true);
    }

    public function exportarGif(Request $request, $id)
    {
        $projeto     = Tb_Projeto::findOrFail($id);
        $nomeProjeto = str_replace(' ', '_', $projeto->nm_projeto);

        $aspectRatio = preg_replace('/[^0-9:]/', '', $request->input('proporcao'));
        $format      = 'gif';

        $aspectRatios = [
            '16:9'  => [1920, 1080],
            '16:10' => [1920, 1200],
            '9:16'  => [1080, 1920],
        ];
        if (!isset($aspectRatios[$aspectRatio])) {
            throw new \Exception("Proporção inválida");
        }
        [$width, $height] = $aspectRatios[$aspectRatio];

        $folder        = "proj_{$id}";
        $frameDir      = storage_path("app/public/{$folder}");
        $outputName    = "{$nomeProjeto}.gif";
        $outputGifPath = storage_path("app/public/{$folder}/{$outputName}");

        Storage::makeDirectory("public/{$folder}");
        if (file_exists($outputGifPath)) {
            unlink($outputGifPath);
        }

        $all    = scandir($frameDir);
        $frames = array_filter($all, fn($f) => preg_match('/^frame_\d{4}\.(jpg|webp)$/i', $f));
        if (empty($frames)) {
            Log::error("Nenhum frame em {$frameDir}: " . json_encode($all));
            throw new \Exception("Nenhum frame encontrado.");
        }
        sort($frames);
        $ext = pathinfo($frames[0], PATHINFO_EXTENSION);

        $ffmpegPath = str_replace('\\', '/', public_path('ffmpeg-7.1.1-essentials_build/bin/ffmpeg.exe'));
        $frameDir   = str_replace('\\', '/', $frameDir);
        $outPath    = str_replace('\\', '/', $outputGifPath);

        $vf = "scale={$width}:{$height}:force_original_aspect_ratio=decrease,"
            . "pad={$width}:{$height}:(ow-iw)/2:(oh-ih)/2:color=black,fps=30";

        $inputPattern = "\"{$frameDir}/frame_%04d.{$ext}\"";

        $cmd = "\"{$ffmpegPath}\""
            . " -i {$inputPattern}"
            . " -vf \"{$vf}\""
            . " -y \"{$outPath}\"";

        Log::info("CMD FFmpeg (GIF): {$cmd}");
        exec($cmd . ' 2>&1', $outputLines, $returnVar);
        if ($returnVar !== 0) {
            Log::error("FFmpeg (GIF) falhou com código {$returnVar}. Saída completa:\n" . implode("\n", $outputLines));
            throw new \Exception("Falha ao gerar GIF. Veja os logs para mais detalhes.");
        }

        return response()
            ->download($outputGifPath, $outputName)
            ->deleteFileAfterSend(true);
    }

    public function getTotalFrames()
    {
        $projectId = session('id_projeto', null);
        if (!$projectId) {
            return response()->json(['error' => 'ID do projeto não encontrado na sessão.'], 400);
        }

        $totalFrames = session("total_frames_{$projectId}", 0);
        return response()->json(['total_frames' => $totalFrames]);
    }

    public function atualizarFPS(Request $request, $id)
    {
        try {
            $request->validate([
                'ds_fps' => 'required|numeric|min:1', // Validação
            ]);

            $video = Tb_Projeto::findOrFail($id);
            $video->ds_fps = $request->ds_fps; // Alterado para garantir que bate com o JSON enviado
            $video->save();

            return response()->json(['message' => 'FPS atualizado com sucesso!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function atualizarVolume(Request $request, $id)
    {
        try {
            $request->validate([
                'qt_volume' => 'required', // Validação
            ]);

            $video = Tb_Projeto::findOrFail($id);
            $video->qt_volume = floatval($request->qt_volume); // Alterado para garantir que bate com o JSON enviado
            $video->save();

            return response()->json(['message' => 'Volume atualizado com sucesso!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deletarFrames(Request $request, $proj_id)
    {
        $request->validate([
            'frames' => 'required|array',
            'frames.*' => 'numeric'
        ]);

        $framesToDelete = $request->input('frames');
        $deletedFiles = [];
        $notFoundFiles = [];

        // Caminho da pasta do projeto
        $projectPath = "proj_{$proj_id}";
        $extensoes = ['jpg', 'png', 'jpeg', 'webp']; // Extensões possíveis

        foreach ($framesToDelete as $frameId) {
            $arquivoEncontrado = false;

            foreach ($extensoes as $ext) {
                $filePath = "{$projectPath}/frame_{$frameId}.{$ext}";

                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                    $deletedFiles[] = $filePath;
                    $arquivoEncontrado = true;
                    break; // Sai do loop ao encontrar o arquivo
                }
            }

            if (!$arquivoEncontrado) {
                Log::error("Arquivo não encontrado: frame_{$frameId} em {$projectPath}");
                $notFoundFiles[] = "frame_{$frameId}";
            }
        }

        // Renomeia os arquivos restantes para manter a sequência
        $remainingFiles = Storage::files($projectPath);

        // Filtra apenas os frames e extrai os números
        $frames = [];
        foreach ($remainingFiles as $file) {
            if (preg_match("/frame_(\d+)/", $file, $matches)) {
                $frames[] = ['path' => $file, 'number' => (int)$matches[1]];
            }
        }

        // Ordena os frames por número
        usort($frames, fn($a, $b) => $a['number'] <=> $b['number']);

        // Renomeia em ordem crescente
        $newIndex = 1;
        $renamedFiles = [];
        $newPaths = []; // Aqui armazenamos os novos caminhos

        foreach ($frames as $frame) {
            $ext = pathinfo($frame['path'], PATHINFO_EXTENSION); // Obtém a extensão original
            $newFilePath = "{$projectPath}/frame_{$newIndex}.{$ext}";

            if ($frame['path'] !== $newFilePath) {
                Storage::move($frame['path'], $newFilePath);
                $renamedFiles[] = ["from" => $frame['path'], "to" => $newFilePath];
            }

            $newPaths[] = $newFilePath; // Adiciona ao array de novos caminhos
            $newIndex++;
        }

        $newPaths = [];
        $remainingFiles = Storage::files($projectPath);

        // Filtra apenas os frames e extrai os números
        $frames = [];
        foreach ($remainingFiles as $file) {
            if (preg_match("/frame_(\d+)/", $file, $matches)) {
                $frames[] = ['path' => $file, 'number' => (int)$matches[1]];
            }
        }

        // Ordena os frames por número (garantindo ordem correta)
        usort($frames, fn($a, $b) => $a['number'] <=> $b['number']);

        // Gera os caminhos corretos
        foreach ($frames as $frame) {
            $newPaths[] = asset("storage/" . $frame['path']);
        }

        return response()->json([
            'deleted' => $deletedFiles,
            'not_found' => $notFoundFiles,
            'renamed' => $renamedFiles,
            'new_paths' => $newPaths // Agora os paths estão em ordem
        ]);
    }

    public function exportarFramesSelecionados(Request $request, $id)
    {
        // 1) Validação básica de entrada
        $request->validate([
            'frames'   => 'required|array',
            'frames.*' => 'numeric',
        ]);

        $selectedIds = $request->input('frames', []);
        if (empty($selectedIds)) {
            throw new \Exception("Nenhum frame selecionado para exportação.");
        }

        // 2) Obter dados do projeto e nome para ZIP ou arquivo único
        $projeto      = Tb_Projeto::findOrFail($id);
        $nomeProjeto  = str_replace(' ', '_', $projeto->nm_projeto);
        $folder       = "proj_{$id}";
        $frameDir     = storage_path("app/public/{$folder}"); 
        $tempDir      = storage_path("app/public/{$folder}/export_frames_selected_temp");
        $zipName      = "selected_frames_{$nomeProjeto}.zip";
        $zipPath      = storage_path("app/public/{$folder}/{$zipName}");

        // 3) Preparar pasta temporária (limpar se já existir)
        if (file_exists($tempDir)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($tempDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $fileinfo) {
                $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                $todo($fileinfo->getRealPath());
            }
            rmdir($tempDir);
        }
        mkdir($tempDir, 0755, true);

        // 4) Extensões possíveis
        $extensoes = ['jpg', 'png', 'jpeg', 'webp'];

        // 5) Caminho do binário do FFmpeg
        $ffmpegPath = str_replace('\\', '/', public_path('ffmpeg-7.1.1-essentials_build/bin/ffmpeg.exe'));

        $convertedFiles = [];
        $notFoundIds    = [];

        // 6) Para cada ID de frame selecionado, procurar o arquivo original (com zero‐padding de 4 dígitos)
        foreach ($selectedIds as $index => $frameId) {
            $found = false;
            // Gera o nome base com padding: frame_0003, frame_0004 etc.
            $paddedName = sprintf("frame_%04d", $frameId);

            foreach ($extensoes as $ext) {
                // Monta o caminho completo no disco: storage/app/public/proj_{id}/frame_0003.jpg
                $inputFullPath = "{$frameDir}/{$paddedName}.{$ext}";

                if (file_exists($inputFullPath)) {
                    $found = true;

                    // 6.1) Define o nome de saída (sempre .jpg) em /export_frames_selected_temp
                    $outputFilename = sprintf("frame_%04d.jpg", $index + 1);
                    $outputFullPath = "{$tempDir}/{$outputFilename}";

                    // 7) Executar conversão para JPG com FFmpeg
                    //    - Se quiser redimensionar, adicione "-vf scale=…", mas aqui só converte formato
                    $cmd = "\"{$ffmpegPath}\" -i \"{$inputFullPath}\" -y \"{$outputFullPath}\"";
                    exec($cmd . ' 2>&1', $outLines, $returnVar);

                    if ($returnVar !== 0 || !file_exists($outputFullPath)) {
                        \Log::error("Falha ao converter {$paddedName}.{$ext}. Cmd: {$cmd}\nSaída: " . implode("\n", $outLines));
                        throw new \Exception("Falha ao converter o frame {$frameId} para JPG. Veja os logs para detalhes.");
                    }

                    $convertedFiles[] = $outputFullPath;
                    break; // sai do loop de extensões ao encontrar/Converter
                }
            }

            if (! $found) {
                $notFoundIds[] = $frameId;
            }
        }

        // 7) Se algum ID não foi encontrado, lança exceção (ou trate como preferir)
        if (! empty($notFoundIds)) {
            \Log::error("Alguns frames selecionados não foram encontrados", ['ids' => $notFoundIds]);
            throw new \Exception("Os seguintes frames não foram encontrados: " . implode(', ', $notFoundIds));
        }

        // 8) Se houver mais de 1 arquivo convertido, empacotar em ZIP
        if (count($convertedFiles) > 1) {
            // 8.1) Remover ZIP antigo, se houver
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }

            // 8.2) Criar arquivo .tar e converter para .zip
            $tarPath = storage_path("app/public/{$folder}/selected_frames_{$nomeProjeto}.tar");
            if (file_exists($tarPath)) {
                unlink($tarPath);
            }

            $phar = new \PharData($tarPath);
            $phar->buildFromDirectory($tempDir);
            $phar->convertToData(\Phar::ZIP);

            $generatedZip = str_replace('.tar', '.zip', $tarPath);
            if (! file_exists($generatedZip)) {
                throw new \Exception("Falha ao criar arquivo ZIP intermediário.");
            }
            rename($generatedZip, $zipPath);

            // 8.3) Remover o .tar intermediário
            if (file_exists($tarPath)) {
                unlink($tarPath);
            }

            // 8.4) Limpar a pasta temporária
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($tempDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $fileinfo) {
                $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                $todo($fileinfo->getRealPath());
            }
            rmdir($tempDir);

            // 8.5) Enviar o ZIP para o navegador e excluir depois
            return response()
                ->download($zipPath, $zipName)
                ->deleteFileAfterSend(true);
        }

        // 9) Se for apenas 1 arquivo convertido, retorna diretamente o JPG
        $singlePath = $convertedFiles[0];
        $singleName = basename($singlePath); // ex.: frame_0001.jpg

        return response()
            ->download($singlePath, $singleName)
            ->deleteFileAfterSend(true);
    }

    public function substituirFrame(Request $request, $id)
    {
        $request->validate([
            'frames'   => 'required|array|min:1|max:1',
            'frames.*' => 'numeric',
            'frame'    => 'required|file|image|max:10240',
        ]);

        $frameId = (int) $request->input('frames')[0] - 1;

        // 3) Define pasta do projeto e nome base “frame_000X”
        $folder   = "proj_{$id}";
        $frameDir = storage_path("app/public/{$folder}");
        $padded   = sprintf("frame_%04d", $frameId);

        // 4) Cria a pasta se não existir
        if (! is_dir($frameDir)) {
            mkdir($frameDir, 0755, true);
        }

        // 5) Remove qualquer arquivo antigo com esse “frame_000X.*”
        $extensoesPossiveis = ['jpg','jpeg','png','webp','bmp','tiff'];
        foreach ($extensoesPossiveis as $ext) {
            $oldFullPath = "{$frameDir}/{$padded}.{$ext}";
            if (file_exists($oldFullPath)) {
                unlink($oldFullPath);
            }
        }

        // 6) Salva o upload em um arquivo temporário dentro do mesmo diretório
        $uploadedFile = $request->file('frame');
        $origExt      = strtolower($uploadedFile->getClientOriginalExtension());
        $tmpName      = "{$padded}_tmp.{$origExt}";
        $tmpFullPath  = "{$frameDir}/{$tmpName}";

        // Move o upload para $tmpFullPath
        try {
            $uploadedFile->move($frameDir, $tmpName);
        } catch (\Exception $e) {
            \Log::error("Falha ao salvar upload temporário: " . $e->getMessage());
            return response()->json([
                'error' => 'Não foi possível salvar o arquivo temporário.',
            ], 500);
        }

        // 7) Converte o arquivo temporário para JPEG via FFmpeg
        //    Defina o caminho correto para o executável do FFmpeg no seu servidor
        $ffmpegPath = str_replace('\\', '/', public_path('ffmpeg-7.1.1-essentials_build/bin/ffmpeg.exe'));
        $inputPath  = str_replace('\\', '/', $tmpFullPath);
        $outputName = "{$padded}.jpg";
        $outputFull = str_replace('\\', '/', "{$frameDir}/{$outputName}");

        // Monta o comando: ffmpeg -i inputPath -y outputFull
        $cmd = "\"{$ffmpegPath}\" -i \"{$inputPath}\" -y \"{$outputFull}\"";

        exec($cmd . ' 2>&1', $outLines, $returnVar);
        if ($returnVar !== 0 || !file_exists($outputFull)) {
            \Log::error("FFmpeg falhou na conversão para JPEG: CMD={$cmd}\nSaída:\n" . implode("\n", $outLines));
            // Apaga o temporário antes de retornar erro
            if (file_exists($tmpFullPath)) {
                unlink($tmpFullPath);
            }
            return response()->json([
                'error' => 'Falha ao converter a imagem para JPEG. Verifique os logs.',
            ], 500);
        }

        // 8) Remove o arquivo temporário
        if (file_exists($tmpFullPath)) {
            unlink($tmpFullPath);
        }

        // 9) Monta a URL pública (presume que storage:link já foi executado)
        $storagePath = "storage/{$folder}/{$outputName}";
        $url = asset($storagePath);

        return response()->json([
            'success'      => true,
            'new_filename' => $outputName,
            'url'          => $url,
        ]);
    }

    public function moverFrame(Request $request, $id)
    {
        // 1) Validação
        $request->validate([
            'frameAtual' => 'required|numeric|min:1',
            'toFrame'    => 'required|numeric|min:1',
        ]);

        $frameAtual = (int) $request->input('frameAtual') - 1;
        $toFrame    = (int) $request->input('toFrame') - 1;

        // Se tentativa de mover para a mesma posição, nada a fazer
        if ($frameAtual === $toFrame) {
            return response()->json(['success' => true]);
        }

        // 2) Define pasta do projeto e nome base
        $folder   = "proj_{$id}";
        $frameDir = storage_path("app/public/{$folder}");

        // Garante que a pasta exista
        if (! is_dir($frameDir)) {
            return response()->json([
                'error' => "A pasta do projeto não existe."
            ], 404);
        }

        // Função auxiliar para montar filename completo
        $getPath = function(int $n) use ($frameDir): string {
            return "{$frameDir}/" . sprintf("frame_%04d.jpg", $n);
        };

        // 3) Verifica se o frameAtual existe
        $pathAtual = $getPath($frameAtual);
        if (! file_exists($pathAtual)) {
            return response()->json([
                'error' => "Frame atual não encontrado: frame_".sprintf("%04d", $frameAtual).".jpg"
            ], 404);
        }

        // 4) Conta quantos frames há na pasta (opcional, mas útil se quisermos evitar gaps)
        //    Aqui assumimos que a numeração é contínua; caso queira validar, faça um glob/iterate.

        // 5) Renomeia o frameAtual para nome temporário
        $tempName     = "{$frameDir}/__move_temp__.jpg";
        if (file_exists($tempName)) {
            // Remove qualquer temp antigo
            unlink($tempName);
        }
        rename($pathAtual, $tempName);

        // 6) Dependendo se vamos “para frente” ou “para trás”, ajustamos a sequência
        if ($toFrame > $frameAtual) {
            // Ex.: mover 5 → 8
            // • frame_0005.jpg → temp
            // • frame_0006.jpg → frame_0005.jpg
            // • frame_0007.jpg → frame_0006.jpg
            // • frame_0008.jpg → frame_0007.jpg
            // • temp → frame_0008.jpg

            for ($i = $frameAtual + 1; $i <= $toFrame; $i++) {
                $orig = $getPath($i);
                $dest = $getPath($i - 1);
                if (file_exists($orig)) {
                    rename($orig, $dest);
                } else {
                    // Se algum frame entre estiver faltando, apenas continue
                    // (ou registre log se preferir)
                    continue;
                }
            }
        } else {
            // Ex.: mover 8 → 5
            // • frame_0008.jpg → temp
            // • frame_0007.jpg → frame_0008.jpg
            // • frame_0006.jpg → frame_0007.jpg
            // • frame_0005.jpg → frame_0006.jpg
            // • temp → frame_0005.jpg

            for ($i = $frameAtual - 1; $i >= $toFrame; $i--) {
                $orig = $getPath($i);
                $dest = $getPath($i + 1);
                if (file_exists($orig)) {
                    rename($orig, $dest);
                } else {
                    continue;
                }
            }
        }

        // 7) Finalmente, retira o temp para a posição destino
        $finalPath = $getPath($toFrame);
        rename($tempName, $finalPath);

        // 8) Retorna sucesso (pode-se também devolver nova lista de URLs, se quiser)
        return response()->json(['success' => true]);
    }

    public function atualizarDataModificação(Request $request) {
        // Valida a entrada
        $request->validate([
            'updated_at' => 'required|date',
            'id_projeto' => 'required|integer',
        ]);
    
        // Converte a data para o formato correto do MySQL
        $formattedDate = date('Y-m-d H:i:s', strtotime($request->input('updated_at')));
    
        // Atualiza no banco
        DB::table('tb_projeto')
            ->where('id', $request->input('id_projeto'))
            ->update(['updated_at' => $formattedDate]);
    
        return response()->json(['message' => 'Última modificação atualizada com sucesso.']);
    }

    public function resgagarFPS(Request $request)
    {
        Log::info('Recebendo requisição para obter FPS do vídeo.');

        // Verifica se um arquivo foi enviado
        if (!$request->hasFile('video')) {
            Log::error('Nenhum arquivo foi enviado.');
            return response()->json(['error' => 'Nenhum arquivo foi enviado.'], 400);
        }

        $file = $request->file('video');

        // Caminhos do FFmpeg e FFprobe
        $ffmpegPath  = base_path(env('FFMPEG_BINARIES', 'bin/ffmpeg.exe'));
        $ffprobePath = base_path(env('FFPROBE_BINARIES', 'bin/ffprobe.exe'));

        Log::info("FFmpeg: $ffmpegPath | FFprobe: $ffprobePath");

        // Criar instância do FFProbe
        try {
            $ffprobe = FFProbe::create([
                'ffmpeg.binaries'  => $ffmpegPath,
                'ffprobe.binaries' => $ffprobePath,
            ]);

            $fps = $ffprobe->streams($file->getRealPath())
                           ->videos()
                           ->first()
                           ->get('r_frame_rate');

            Log::info("FPS obtido: $fps");

            // Converte FPS para valor numérico
            $fpsParts = explode('/', $fps);
            $fpsValue = count($fpsParts) == 2 ? round($fpsParts[0] / $fpsParts[1], 2) : (float) $fps;

            return response()->json(['fps' => $fpsValue]);
        } catch (\Exception $e) {
            Log::error("Erro ao processar FPS: " . $e->getMessage());
            return response()->json(['error' => 'Erro ao obter FPS.'], 500);
        }
    }

    //Criar cena
    public function criarCena(Request $request, $id)
    {
        $validator = \Validator::make($request->all(), [
            'nm_cena_projeto' => ['required', 'min:3', 'max:45'],
            'ds_cena_projeto' => ['required', 'max:500'],
            'nm_cor' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'nr_frame_inicial' => ['required', 'numeric'],
            'nr_frame_final' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        if ($request->nr_frame_inicial > $request->nr_frame_final) {
            return response()->json(['message' => 'O frame inicial não pode ser maior que o final.'], 422);
        }

        $cenasExistentes = Tb_Cena_Projeto::where('id_projeto', $id)->get();

        foreach ($cenasExistentes as $cena) {
            $inicio = $cena->nr_frame_inicial;
            $fim = $cena->nr_frame_final;

            if (
                ($request->nr_frame_inicial >= $inicio && $request->nr_frame_inicial <= $fim) ||
                ($request->nr_frame_final >= $inicio && $request->nr_frame_final <= $fim) ||
                ($request->nr_frame_inicial <= $inicio && $request->nr_frame_final >= $fim)
            ) {
                return response()->json(['message' => 'Os frames informados se sobrepõem a outra cena.'], 422);
            }
        }

        $informed_inputs = $validator->validated();
        $informed_inputs['id_projeto'] = $id;
        Tb_Cena_Projeto::create($informed_inputs);

        return response()->json(['success' => true], 200);
    }

    //Editar cena
    public function editarCena(Request $request, $id)
    {
        $validator = \Validator::make($request->all(), [
            'nm_cena_projeto' => ['required', 'min:3', 'max:45'],
            'ds_cena_projeto' => ['required', 'max:500'],
            'nm_cor' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'nr_frame_inicial' => ['required', 'numeric'],
            'nr_frame_final' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        if ($request->nr_frame_inicial > $request->nr_frame_final) {
            return response()->json(['message' => 'O frame inicial não pode ser maior que o final.'], 422);
        }

        $cenasExistentes = Tb_Cena_Projeto::where('id_projeto', $id)->get();

        Log::info($request['id_cena_projeto']);

        foreach ($cenasExistentes as $cena) {

            if($cena->id != $request['id_cena_projeto']) {
                $inicio = $cena->nr_frame_inicial;
                $fim = $cena->nr_frame_final;

                if (
                    ($request->nr_frame_inicial >= $inicio && $request->nr_frame_inicial <= $fim) ||
                    ($request->nr_frame_final >= $inicio && $request->nr_frame_final <= $fim) ||
                    ($request->nr_frame_inicial <= $inicio && $request->nr_frame_final >= $fim)
                ) {
                    return response()->json(['message' => 'Os frames informados se sobrepõem a outra cena.'], 422);
                }
            }
            
        }

        $informed_inputs = $validator->validated();
        Tb_Cena_Projeto::where('id', $request['id_cena_projeto'])->update($informed_inputs);

        return response()->json(['success' => true], 200);
    }

    //Ligar Cena
    public function ligarCena(Request $request)
    {
        $id_cena_roteiro = $request->input('id_cena_roteiro');
        $id = $request->input('id');

        // Verifica se o roteiro ainda existe
        if (!Tb_Cena_Roteiro::find($id_cena_roteiro)) {
            return response()->json(['success' => false, 'message' => 'A cena do roteiro selecionada não existe mais.']);
        }

        Tb_Cena_Projeto::where('id', $id)->update([
            'id_cena_roteiro' => $id_cena_roteiro
        ]);

        return response()->json(['success' => true]);
    }

    //Desligar Cena
    public function desligarCena(Request $request)
    {
        $id = $request->input('id');

        Tb_Cena_Projeto::where('id', $id)->update([
            'id_cena_roteiro' => null
        ]);

        return response()->json(['success' => true]);
    }

    // Compartilhar projeto
    public function compartilharProjeto(Request $request, $id)
    {
        $request->validate([
            'ds_email' => 'required|email',
            'id_cargo' => 'required|integer'
        ]);        

        $id_projeto = $id;
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
        $usuarioCompartilhado = tb_contribuidor::where('id_projeto', $id_projeto)
            ->where('id_usuario', $userId)
            ->exists();

        if (!$usuarioCompartilhado) {
            tb_contribuidor::create([
                'id_usuario' => $userId,
                'id_projeto' => $id_projeto,
                'id_cargo'   => $id_cargo
            ]);
        
            return response()->json([
                'success' => true,
                'message' => 'Projeto compartilhado com sucesso!',
                'id' => $userId,
                'nome' => $user->nm_usuario,
                'cargo' => $id_cargo,
                'email' => $ds_email,
            ]);
         } else {
            return response()->json([
                'success' => false,
                'message' => 'Usuário já é contribuidor deste projeto.'
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

        $id_projeto = $id;
        $id_usuario = $request->input('id_usuario');
        $id_cargo = $request->input('id_cargo');

        // Atualiza o cargo do usuário no projeto
        $atualizado = tb_contribuidor::where('id_projeto', $id_projeto)
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
                'message' => 'Usuário não encontrado no projeto ou nada foi alterado.'
            ], 404);
        }
    }

    //Deletar projeto
    public function deletarProjeto(Request $request)
    {
        $informed_inputs = $request->validate([
            'id_projeto' => ['required', 'exists:tb_projeto,id'],
        ]);

        Log::info($informed_inputs['id_projeto']);

        $user = Auth::user();
        Log::info($user);

        $proj = Tb_Projeto::find($informed_inputs['id_projeto']);
        Log::info($proj);

        if ($proj) {
            if ($proj->id_usuario == $user->id) {
                // Deletar pasta do projeto
                $folder = "proj_{$proj->id}";
                if (Storage::disk('public')->exists($folder)) {
                    Storage::disk('public')->deleteDirectory($folder);
                }

                // Atualizar quantidade de projetos
                /** @var \App\Models\tb_usuario $user */
                $user->decrement('qt_projeto');

                // Deletar o projeto do banco
                $proj->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Projeto deletado com sucesso'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para deletar este projeto.'
                ], 403);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Projeto não encontrado.'
            ], 404);
        }
    }

    public function enviarComentario(Request $request, $id)
    {
        $request->validate([
            'ds_comentario' => 'required|string|max:500',
        ]);

        // Crie a nova mensagem no banco de dados
        $mensagem = new Tb_Comentario();
        $mensagem->id_usuario = Auth::id();  // Correção aqui
        $mensagem->id_projeto = $id;
        $mensagem->ds_comentario = $request->ds_comentario;
        $mensagem->save();

        // Dispara o evento para todos os ouvintes no canal 'chat_proj.{projeto_id}'
        event(new NovoComentario($mensagem));

        return response()->json(['success' => true]);
    }

    public function salvarComentario(Request $request, $id)
    {
        // Obtendo os dados enviados na requisição
        $ds_comentario = $request->input('ds_comentario');
        $id_usuario = $request->input('id_usuario');
        
        // Verificar se já existe um comentário com esses dados na tabela 'tb_comentario'
        $comentarioExistente = DB::table('tb_comentario')
                                ->where('ds_comentario', $ds_comentario)
                                ->where('id_usuario', $id_usuario)
                                ->where('id_projeto', $id)
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
            'id_projeto' => $id,
        ]);

        // Retorna uma resposta de sucesso
        return response()->json(['message' => 'Comentário salvo com sucesso!'], 201);
    }

    public function getComentarios($proj_id)
    {
        $comentarios = DB::table('tb_comentario')
                        ->where('id_projeto', $proj_id)
                        ->orderBy('id')
                        ->get();

        return view('partials.comentarios', [
            'comentarios' => $comentarios,
            'id_projeto'  => $proj_id
        ])->render();
    }

    public function enviarErro(Request $request, $id)
    {
        $request->validate([
            'ds_erro' => 'required|string|max:500',
            'nr_frame' => 'required|integer',
            'ic_conclusao' => 'integer',
        ]);

        // Crie a nova mensagem no banco de dados
        $mensagem = new Tb_Erro();
        $mensagem->id_usuario = Auth::id();  // Correção aqui
        $mensagem->id_projeto = $id;
        $mensagem->nr_frame = $request->nr_frame;
        $mensagem->ic_conclusao = 0;
        $mensagem->ds_erro = $request->ds_erro;
        $mensagem->save();

        // Dispara o evento para todos os ouvintes no canal 'chat_proj.{projeto_id}'
        event(new NovoErro($mensagem));

        return response()->json(['success' => true]);
    }

    public function salvarErro(Request $request, $id)
    {
        // Obtendo os dados enviados na requisição
        $ds_erro = $request->input('ds_erro');
        $id_usuario = $request->input('id_usuario');
        $nr_frame = $request->input('nr_frame');
        
        // Verificar se já existe um erro com esses dados na tabela 'tb_erro'
        $comentarioExistente = DB::table('tb_erro')
                                ->where('ds_erro', $ds_erro)
                                ->where('id_usuario', $id_usuario)
                                ->where('id_projeto', $id)
                                ->where('nr_frame', $nr_frame)
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
            'ic_conclusao' => 0,
            'nr_frame' => $nr_frame,
            'id_projeto' => $id,
        ]);

        // Retorna uma resposta de sucesso
        return response()->json(['message' => 'Erro salvo com sucesso!'], 201);
    }

    public function corrigirErro($id)
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

    public function getComentariosErro($proj_id)
    {
        $erros = DB::table('tb_erro')
                 ->where('id_projeto', $proj_id)
                 ->orderBy('id')
                 ->get();

        return view('partials.comentariosErro', [
            'erros' => $erros,
            'id_projeto'  => $proj_id
        ])->render();
    }

    public function importarRoteiro(Request $request, $id)
    {
        $request->validate([
            'id_roteiro' => 'required|integer',
        ]);

        // Log para verificar o valor de id_roteiro
        $id_roteiro = $request->id_roteiro;

        $roteiro = DB::table('tb_roteiro')->where('id', $id_roteiro)->first();

        if ($roteiro) {
            DB::table('tb_projeto')->where('id', $id)->update([
                'id_roteiro' => $id_roteiro,
            ]);
            return response()->json(['success' => 'Roteiro atualizado com sucesso.', 'id' => $id_roteiro, 'nome' => $roteiro->nm_roteiro, 'usuario' => DB::table('tb_usuario')->where('id', $roteiro->id_usuario)->value('nm_usuario')]);
        } else {
            return response()->json(['error' => 'Roteiro não encontrado.'], 404);
        }
    }

    public function removerRoteiro(Request $request, $id)
    {
        // Zera o ID do roteiro no projeto
        DB::table('tb_projeto')->where('id', $id)->update([
            'id_roteiro' => null,
        ]);

        // Zera os ids das cenas ligadas ao roteiro
        DB::table('tb_cena_projeto')
            ->where('id_projeto', $id)
            ->update(['id_cena_roteiro' => null]);

        return response()->json(['success' => 'Roteiro atualizado com sucesso.']);
    }

     // Salvar proporção do vídeo
    public function salvarProporcao(Request $request, $id)
    {
        $request->validate([
            'nm_proporcao' => 'required'
        ]);

        $id_projeto = $id;
        $nm_proporcao = $request->input('nm_proporcao');

        // Atualiza o cargo do usuário no projeto
        $atualizado = tb_projeto::where('id', $id_projeto)
            ->update(['nm_proporcao' => $nm_proporcao]);

        if ($atualizado) {
            return response()->json([
                'success' => true,
                'message' => 'Proporção atualizada com sucesso.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Projeto não encontrado ou nada foi alterado.'
            ], 404);
        }
    }

    public function atualizarSituacaoCena(Request $request, $id)
    {
        $cena = Tb_Cena_Projeto::findOrFail($id);
        $cena->ic_conclusao = $request->input('ic_conclusao') ? 1 : 0;
        $cena->save();

        return response()->json(['success' => true]);
    }

    public function getGraficoData($proj_id)
    {
        $totalCenas = Tb_Cena_Projeto::where('id_projeto', $proj_id)->count();
        $cenasFinalizadas = Tb_Cena_Projeto::where('id_projeto', $proj_id)->where('ic_conclusao', 1)->count();

        $percentual = 0;
        if ($totalCenas > 0) {
            $percentual = ($cenasFinalizadas / $totalCenas) * 100;
        }

        $percentual = min(round($percentual, 2), 100);

        return response()->json([
            'percentual' => $percentual
        ]);
    }

}