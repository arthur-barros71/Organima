<?php

namespace App\Services;

use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Mp3;
use Illuminate\Support\Facades\Storage;

class FFmpegService
{
    public function extractAudio($videoPath, $audioOutputPath)
    {

        // Caminho para o ffmpeg e ffprobe
        $ffmpegPath  = base_path(env('FFMPEG_BINARIES', 'bin/ffmpeg.exe'));
        $ffprobePath = base_path(env('FFPROBE_BINARIES', 'bin/ffprobe.exe'));

        // Criar instâncias do FFProbe e FFMpeg
        $ffprobe = \FFMpeg\FFProbe::create([
            'ffmpeg.binaries'  => $ffmpegPath,
            'ffprobe.binaries' => $ffprobePath,
        ]);

        $ffmpeg = \FFMpeg\FFMpeg::create([
            'ffmpeg.binaries'  => $ffmpegPath,
            'ffprobe.binaries' => $ffprobePath,
        ]);

        // Use o FFmpeg para abrir o vídeo a partir de seu caminho completo
        $video = $ffmpeg->open(Storage::path($videoPath));   // Recupera o caminho completo do arquivo

        // Defina o formato de áudio (Mp3 neste caso)
        $audioFormat = new Mp3();

        // Extraia o áudio do vídeo para o caminho de saída
        $video->save($audioFormat, Storage::path($audioOutputPath));  // Caminho completo para salvar o áudio

        // Verifique se o arquivo de áudio foi criado
        if (Storage::exists($audioOutputPath)) {
            return ['success' => true, 'path' => $audioOutputPath];
        }

        return ['success' => false, 'message' => 'Erro ao extrair áudio'];
    }
}