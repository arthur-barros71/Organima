<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Organima</title>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/pusher-js@7.0.3/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.3/dist/echo.iife.js"></script>

    <!--Including Pusher-->
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@7.0.3"></script>

    <!--Scripts-->
    <script src="{{ asset('js/Project.js') }}" defer></script>
    <script src="{{ asset('js/notfy.js') }}" defer></script>

    <script src="{{ asset('js/modal/projectProps.js') }}" defer></script>
    <script src="{{ asset('js/modal/addScript.js') }}" defer></script>
    <script src="{{ asset('js/modal/editProj.js') }}" defer></script>
    <script src="{{ asset('js/modal/shareProj.js') }}" defer></script>
    <script src="{{ asset('js/modal/projectExport.js') }}" defer></script>
    <script src="{{ asset('js/modal/createScene.js') }}" defer></script>

    <!--CSS-->
    <link rel="stylesheet" href="{{asset('css/Project.css')}}">
    <link rel="stylesheet" href="{{asset('css/pallet.css')}}">
    <link rel="stylesheet" href="{{asset('css/notifications.css')}}">

    @if (!Auth::check())
        <script>
            window.onload = function() {
                sessionStorage.removeItem('pageBegin');
                sessionStorage.removeItem('page');
                window.location.href = "/";
            }
        </script>
    @endif

</head>
<body>

    @php
        $usuarioProjetoId = DB::table('tb_projeto')->where('id', $id_projeto)->value('id_usuario');
        $idCargoContribuidor = DB::table('tb_contribuidor')
            ->where('id_usuario', Auth::user()->id)
            ->where('id_projeto', $id_projeto)
            ->value('id_cargo');
    @endphp

    <p id="proj_id" style="display: none">{{ $id_projeto }}</p>
    <p id="user_auth_id" style="display: none">{{ Auth::user()->id }}</p>

    <div class="Load" id="loading">
        <div class="LoadImg2"></div>    
    </div>

    <!--Modal de carregamento-->
    <div class="LoadFade" id="LoadFade">
        <div class="LoadModal fontcorpo" id="LoadModal">

            <img src="Image/LoadImg.gif">       
            <progress id="progressBar" value="0" max="100"></progress>
            <div style="display: flex" class="loadText">
                <span id="LoadMainText">Importando vídeo</span><span id="retic">...</span>
            </div>  

        </div>
    </div>

    <!--Modal de exportação-->
    <div class="modalExportFade" id="modalExportFade">

        <div class="modalExportLoad fontcorpo" id="modalExportLoad">
            <img src="Image/exportLoad.gif">
            <p id="exportLoadText">Exportando vídeo mp4<span id="exportLoadRet">...</span></p>
        </div>

        <div class="modalExport" id="modalExport">

            <div class="modalExportHeader titlefont">
                <p>Exportar vídeo</p>
                <img src="image/CloseModal.svg" onclick="closeModalExport()">
            </div>

            <div class="modalExportView fontcorpo">
                <p id="modalExportFrameNum">Frame 1</p>
                <div class="modalExportViewFrame" id="modalExportViewFrame">
                    <img id="modalExportFrameImg">
                </div>                
                <input type="range" min="1" id="modalExportRange" oninput="updateExportFrame()">
            </div>

            <div class="modalExportInfo fontcorpo">

                <div class="modalExportInfoFormats">
                    <div class="modalExportFormat modalExportFormatSelected" id="exportMP4" onclick="exportType('mp4', 'exportMP4')">
                        <p>Vídeo (.mp4)</p>
                    </div>
                    <div class="modalExportFormat" id="exportWEBM" onclick="exportType('webm', 'exportWEBM')">
                        <p>Vídeo (.webm)</p>
                    </div>
                    <div class="modalExportFormat" id="exportMKV" onclick="exportType('mkv', 'exportMKV')">
                        <p>Vídeo (.mkv)</p>
                    </div>
                    <div class="modalExportFormat" id="exportAVI" onclick="exportType('avi', 'exportAVI')">
                        <p>Vídeo (.avi)</p>
                    </div>
                    <div class="modalExportFormat" id="exportZIP" onclick="exportType('frames', 'exportZIP')">
                        <p>Frames (.zip)</p>
                    </div>
                    <div class="modalExportFormat" id="exportGIF" onclick="exportType('gif', 'exportGIF')">
                        <p>Gif (.gif)</p>
                    </div>
                </div>

                <div class="modalExportInfoColumn">

                    <div class="modalExportInfoVideo" id="modalExportInfoVideo">
                        <p>FPS: <span id="exportFPSVideo">30</span></p>
                        <p>Proporção: <span id="exportProporcaoVideo">16:9</span></p>
                        <p>Duração: <span id="exportDuracaoVideo">00:00:00</span></p>
                        <p>Quantidade de frames: <span id="exportQtdFramesVideo">100</span></p>
                        <p>Volume: <span id="exportVolumeVideo">100</span></p>
                    </div>

                    <div class="modalExportInfoFrames" id="modalExportInfoFrames" style="display: none">
                        <p>Proporção: <span id="exportProporcaoFrames">16:9</span></p>
                        <p>Quantidade de frames: <span id="exportQtdFramesFrames">100</span></p>
                        <div class="modalExportInfoFramesFormat">
                            <p>Formato: </p>
                            <select id="exportFormatoFrames">
                                <option value="png">PNG</option>
                                <option value="jpg">JPG</option>
                                <option value="jpeg">JPEG</option>
                                <option value="bmp">BMP</option>
                                <option value="tiff">TIFF</option>
                                <option value="webp">WEBP</option>
                            </select>
                        </div>
                    </div>

                    <div class="modalExportInfoGif" id="modalExportInfoGif" style="display: none">
                        <p>FPS: <span id="exportFPSGif">30</span></p>
                        <p>Proporção: <span id="exportProporcaoGif">16:9</span></p>
                        <p>Duração: <span id="exportDuracaoGif">00:00:00</span></p>
                        <p>Quantidade de frames: <span id="exportQtdFramesGif">100</span></p>
                    </div>

                    <div class="modalExportInfoExport">
                        <div class="modalExportInfoAudio" id="modalExportInfoAudio">
                            <p>Incluir áudio: </p>
                            <input type="checkbox" checked id="exportarAudioCheck">
                        </div>
                        <button id="modalExportButton" type="button" onclick="exportar()">Exportar <span id="export">vídeo</span></button>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!--Modal de criação de cenas-->
    <div class="ModalCreateCenaFade" id="ModalCreateCenaFade">

        <div class="ModalCreateCena" id="ModalCreateCena">

            <div class="ModalCreateCenaHeader titlefont">
                <p>Criar nova cena</p>
                <img src="image/CloseModal.svg" onclick="CloseModalCreateCena()">
            </div>

            <form class="ModalCreateCenaForm fontcorpo" id="createCenaForm" action="/criarCena/{{ $id_projeto }}" method="POST">
                @csrf

                <div class="CenaRow">

                    <div class="CenaColumn" style="width: 75%">

                        <p>Título da cena:</p>
                        <input type="text" id="nm_cena" maxlength="45" name="nm_cena_projeto">

                    </div>                    

                    <div class="CenaColumn" style="width: 20%">

                        <p>Cor da etiqueta:</p>
                        <input type="color" name="nm_cor" id="ds_cor" style="padding: 0; width: 100%; height: 100%; border: none; border-radius: 10px; -webkit-appearance: none;">

                    </div>                    

                </div>                 

                <p>Descrição da cena:</p>
                <textarea class="fontcorpo" name="ds_cena_projeto" id="ds_cena" maxlength="250" resizable="false"></textarea>                

                <p>Intervalo de frames:</p>
                
                <div class="wrapper">
                    <span id="range1">
                        0
                    </span>
                    <div class="container">
                        <div class="slider-track"></div>
                        <input type="range" min="1" max="100" value="30" name="nr_frame_inicial" class="frameInterval" id="frameInicial" oninput="slideOne()">
                        <input type="range" min="1" max="100" value="70" name="nr_frame_final" class="frameInterval" id="frameFinal" oninput="slideTwo()">
                    </div>
                    <span id="range2">
                        100
                    </span>
                </div>

                <button type="button" class="createCenaBtn" onclick="criarCena()"                        
                    @if($usuarioProjetoId != Auth::user()->id && $idCargoContribuidor != 3)
                        disabled
                    @endif>Criar cena</button>

            </form>

        </div>

    </div>

    <!--Modal de edição de cenas-->
    <div class="ModalCreateCenaFade" id="ModalEditCenaFade">

        <div class="ModalCreateCena" id="ModalEditCena">

            <div class="ModalCreateCenaHeader titlefont">
                <p>Editar cena</p>
                <img src="image/CloseModal.svg" onclick="CloseModalEditCena()">
            </div>

            <form class="ModalCreateCenaForm fontcorpo" id="editCenaForm" action="/editarCena/{{ $id_projeto }}" method="POST">
                @csrf

                <div class="CenaRow">

                    <div class="CenaColumn" style="width: 75%">

                        <p>Título da cena:</p>
                        <input type="text" id="nm_cena_edit" maxlength="45" name="nm_cena_projeto">

                    </div>                    

                    <div class="CenaColumn" style="width: 20%">

                        <p>Cor da etiqueta:</p>
                        <input type="color" name="nm_cor" id="ds_cor_edit" style="padding: 0; width: 100%; height: 100%; border: none; border-radius: 10px; -webkit-appearance: none;">

                    </div>                    

                </div>                 

                <p>Descrição da cena:</p>
                <textarea class="fontcorpo" name="ds_cena_projeto" id="ds_cena_edit" maxlength="250" resizable="false"></textarea>                

                <p>Intervalo de frames:</p>

                <div class="wrapper">
                    <span id="range1Edit">0</span>
                    <div class="container">
                        <div class="slider-trackEdit"></div> <!-- Corrigido -->
                        <input type="range" min="1" max="100" value="30" name="nr_frame_inicial" id="nr_frame_inicial_edit" class="frameInterval" oninput="slideOneEdit()">
                        <input type="range" min="1" max="100" value="70" name="nr_frame_final" id="nr_frame_final_edit" class="frameInterval" oninput="slideTwoEdit()">
                    </div>
                    <span id="range2Edit">100</span>
                </div>

                <button type="button" class="createCenaBtn" onclick="editCena()"                        
                    @if($usuarioProjetoId != Auth::user()->id && $idCargoContribuidor != 3)
                        disabled
                    @endif>Editar cena</button>

            </form>

        </div>

    </div>

    <!--Modal de ligação de cenas-->
    @if(DB::table('tb_projeto')->where('id', $id_projeto)->value('id_roteiro') != null)
        <div class="modalLigarCenaFade" id="modalLigarCenaFade">

            <div class="modalLigarCena" id="modalLigarCena">

                <div class="ligarCenaHeader">
                    <p class="titlefont">Ligar cenas</p>
                    <img src="image/CloseModal.svg" onclick="CloseModalLigarCena()">
                </div>

                @php
                    $idRoteiro = DB::table('tb_projeto')->where('id', $id_projeto)->value('id_roteiro');
                    $cenaRoteiro = DB::table('tb_cena_roteiro')->where('id_roteiro', $idRoteiro)->get();
                @endphp

                <div class="ligarCenaCont">

                    @foreach($cenaRoteiro as $cenaRot)
                        <div class="cenaLigar fontcorpo" id="ligarCenaSummary_{{ $cenaRot->id }}" data-corCena="{{ $cenaRot->nm_cor }}" onclick="setLigarCena('{{ $cenaRot->id }}', 'ligarCenaSummary_{{ $cenaRot->id }}')">
                            <svg width="16" height="22" viewBox="0 0 16 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g opacity="0.65">
                                <mask id="path-1-inside-1_3170_611" fill="white">
                                <rect width="16" height="22" rx="1"/>
                                </mask>
                                <rect width="16" height="22" rx="1" stroke="#4A4A5F" stroke-width="3" mask="url(#path-1-inside-1_3170_611)"/>
                                <line x1="4.75" y1="7.25098" x2="11.25" y2="7.25098" stroke="#4A4A5F" stroke-width="1.5" stroke-linecap="round"/>
                                <line x1="4.75" y1="11.249" x2="11.25" y2="11.249" stroke="#4A4A5F" stroke-width="1.5" stroke-linecap="round"/>
                                <line x1="4.75" y1="15.249" x2="11.25" y2="15.249" stroke="#4A4A5F" stroke-width="1.5" stroke-linecap="round"/>
                                </g>
                            </svg>
                            <p>{{ $cenaRot->nm_cena_roteiro }}</p>
                        </div>
                    @endforeach

                </div>                

                <button class="btnLigarCena" onclick="ligarCenaRoteiro()">
                    Ligar cena
                </button>

            </div>

        </div>
    @endif

    @if(DB::table('tb_projeto')->where('id', $id_projeto)->value('id_roteiro') != null)
        <div class="modalVerCenaFade" id="modalVerCenaFade">

            <div class="modalVerCena" id="modalVerCena">

                <div class="modalVerCenaHeader titlefont">
                    <p id="verCenaName">Visualizar Nome da Cena</p>
                    <img src="image/CloseModal.svg" onclick="closeModalVerCena()">
                </div>

                <div class="modalVerCenaRow fontcorpo">

                    <div class="modalVerCenaLeftpanel">
                        <p style="margin-top: 0"><span class="bold">Título: </span><span id="verCenaTitle">Nome da cena</span></p>
                        <p class="bold" style="margin-bottom: 0">Descrição:</p>
                        <p style="font-size: 15px; margin-top: 1vh" id="verCenaDesc">Roteiro padrão para os novos usuários do nosso site. Fique à vontade para utilizar dele o quanto quiser.</p>

                        <div class="verCenaLine"></div>

                        <p><span class="bold">Número de palavras: </span><span id="verCenaNumWords">xxxx</span></p>
                        <p><span class="bold">Número de caracteres: </span><span id="verCenaNumChar">xxxx</span></p>

                        <div class="verCenaLine"></div>

                        <div class="modalVerCenaRow" style="align-items: center">
                            <p><span class="bold">Roteiro: </span><span id="verCenaRoteiro"></span></p>
                            <button onclick="goRoteiro()">Abrir Roteiro</button>
                        </div>
                        <p><span class="bold">Proprietário: </span><span id="verCenaProprietario">Nome</span></p>
                    </div>

                    <div class="modalVerCenaRIghtPanel">
                        <div class="verCenaCont" id="verCenaCont"></div>
                    </div>

                </div>

            </div>

        </div>
    @endif

    <!--Dashboard-->
    <div class="DashMargin" id="DashMargin">

        <div class="Dashboard">
            <div class="dahboardMargin">
                <div class="dashboard-wrapper" style="display: flex; gap: 20px; width: 100%; height: 100%;">

                    <!-- Lista de cenas com rolagem -->
                    <div class="cenas-lista">
                        <!-- Cabeçalho fixo -->
                        <div class="dashTitleCont">
                            <p class="dashTitle titlefont" onclick="closeDashboard()">
                                <span style="color: #5C5C5C; font-size: 30px"><</span> Cronograma
                            </p>
                        </div>

                        <div class="dashSummary fontcorpo">
                            <button onclick="filtrarCenas('todas')" class="aba" id="aba-todas">Todas</button>
                            <button onclick="filtrarCenas('concluidas')" class="aba" id="aba-concluidas">Concluídas</button>
                            <button onclick="filtrarCenas('naoConcluidas')" class="aba" id="aba-naoConcluidas">Não concluídas</button>
                        </div>


                        <!-- Container da lista com rolagem -->
                        <div class="dashListCont">
                            @foreach($cena as $cenas)
                                @php
                                    $statusClass = $cenas->ic_conclusao ? 'concluida' : 'naoConcluida';
                                @endphp

                                <div class="dashCenaColor {{ $statusClass }}" id="cena_{{ $cenas->id }}" style="background-color: {{ $cenas->nm_cor }};">
                                    <div class="dashCena fontcorpo" style="border-color: {{ $cenas->nm_cor }};">
                                        <input type="checkbox"
                                            onchange="atualizarSituacaoCena({{ $cenas->id }}, this.checked)"
                                            {{ $cenas->ic_conclusao ? 'checked' : '' }}>
                                        <div class="dashColumn">
                                            <p class="dashCenaTitle" style="font-weight: bold; margin: 0;">{{ $cenas->nm_cena_projeto }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Detalhes do projeto -->
                    <div class="projeto-detalhes fontcorpo">
                        <div style="padding: 1vh 5% 0 1%;">
                            <!-- Gráfico de pizza -->

                            <div id="grafico">
                                @include('partials.grafico')
                            </div>                            
                            
                            <!-- Dados do projeto -->
                            <p class="fontCronograma">Nome do projeto: <strong>{{ DB::table('tb_projeto')->where('id', $id_projeto)->value('nm_projeto') }}</strong></p>
                            <hr>
                            <p class="fontCronograma">Data de início: <strong>{{ \Carbon\Carbon::parse(DB::table('tb_projeto')->where('id', $id_projeto)->value('dt_inicial'))->format('d/m/Y') }}</strong></p>

                            <div class="marrgin">
                                <hr>
                                <p >Descrição:</p>
                                <p  style="text-align: justify;">{{ DB::table('tb_projeto')->where('id', $id_projeto)->value('ds_projeto') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Modal de edição de projetos-->
    <div class="ModalEditProjectFade" id="ModalEditProjectFade">
        <div class="ModalEditProject background" id="ModalEditProject">
            <div class="ModalEditProjectHeader">
                <p class="titlefont">Editar projeto</p>
                <img src="image/CloseModal.svg" onclick="CloseModalEditProj()">
            </div>
            <form id="FormEditProj" class="FormEditProj fontcorpo" action="/editarProjeto/{{ $id_projeto }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="modal_proj_img" id="modal_proj_img">
                        
                        @php
                            $extensions = ['.webp', '.png', '.jpg', '.jpeg', '.jfif']; // Adicione outras extensões conforme necessário
                            $imagePath = null;

                            foreach ($extensions as $extension) {
                                $imagePath = "proj_{$id_projeto}/CoverImage/cover{$extension}";
                                if (Storage::disk('public')->exists($imagePath)) {
                                    break;
                                }
                                $imagePath = null;
                            }
                        @endphp

                        @if ($imagePath)
                            <img src="{{ url('storage/' . $imagePath) }}" alt="Imagem do projeto" onclick="OpenProject({{ $id_projeto }})">
                        @else
                            <img src="{{ asset('image/ProjDefaultImg.png') }}" alt="Imagem padrão do projeto" onclick="OpenProject({{ $id_projeto }})">
                        @endif

                    </div>
                    <input type="file" id="proj_img_import" name="proj_img_import" style="opacity: 0; position: absolute;" accept=".webp, .png, .jpg, .jpeg, .jfif" onchange="setProjImgModal()">

                    <div class="proj_campos fontcorpo">

                        <p class="modal_proj_text font">Nome do projeto</p>
                        <input type="text" class="font" id="pjt_name" name="nm_projeto" value="{{ DB::table('tb_projeto')->where('id', $id_projeto)->value('nm_projeto') }}">
                        
                        <p class="modal_proj_text font modal_proj_text_margin">Status do projeto</p>
                        <select class="font" name="id_tipo" id="status_id">
                            <option value="1" @if(DB::table('tb_projeto')->where('id', $id_projeto)->value('id_tipo') == 1) selected @endif>Animação 2D ou 3D</option>
                            <option value="2" @if(DB::table('tb_projeto')->where('id', $id_projeto)->value('id_tipo') == 2) selected @endif>Animação Stop Motion</option>
                            <option value="3" @if(DB::table('tb_projeto')->where('id', $id_projeto)->value('id_tipo') == 3) selected @endif>Produção cinematográfica</option>
                        </select>

                    </div>

                </div>
                <div class="modal_proj_desc fontcorpo">
                    <p class="modal_proj_text fontcorpo">Descrição do projeto</p>
                    <!-- Atualizado para desc -->
                    <textarea class="font" id="desc" name="ds_projeto" class="fontcorpo">{{ DB::table('tb_projeto')->where('id', $id_projeto)->value('ds_projeto') }}</textarea>
                </div>
                <p class="modal_proj_btn button-normal fontcorpo" onclick="EditProj()">Salvar alterações</p>
            </form>                
        </div>
    </div>

    <!--Modal de compartilhamento de projetos-->
    <div class="ModalShareProjectFade" id="ModalShareProjectFade">
        <div class="ModalShareProject background" id="ModalShareProject">
            <div class="ModalShareProjectHeader">
                <p class="titlefont">Compartilhar {{ DB::table('tb_projeto')->where('id', $id_projeto)->value('nm_projeto') }}</p>
                <img src="image/CloseModal.svg" onclick="closeModalShareProj()">
            </div>
            <form id="FormShareProj" class="FormShareProj fontcorpo" action="/compartilharProjeto/{{ $id_projeto }}" method="POST">
                @csrf

                <div class="share_campos">

                    <p class="modal_proj_text font">Email</p>
                    <input type="email" class="font" id="ds_email" name="ds_email">

                    <div class="colab" id="colab">

                        <p class="modal_proj_text font">Colaboradores</p>
                    
                        @php
                            // Definindo as extensões de imagem
                            $extensions = ['.webp', '.png', '.jpg', '.jpeg', '.jfif'];
                            $imagePath = null;
                    
                            // Verifica a imagem do usuário principal
                            foreach ($extensions as $extension) {
                                $possiblePath = "user_" . DB::table('tb_projeto')->where('id', $id_projeto)->value('id_usuario') . "/profile{$extension}";
                                if (Storage::disk('public')->exists($possiblePath)) {
                                    $imagePath = $possiblePath;
                                    break;
                                }
                            }
                        @endphp
                    
                        <div class="colabUser">
                    
                            @php
                                $usuarioProjetoId = DB::table('tb_projeto')->where('id', $id_projeto)->value('id_usuario');
                                $nomeUsuarioProjeto = DB::table('tb_usuario')->where('id', $usuarioProjetoId)->value('nm_usuario');
                            @endphp
                    
                            <div class="colabInfo">
                                <div class="colabImg">
                                    <img src="{{ $imagePath ? url('storage/' . $imagePath) : asset('image/ProfileImg.svg') }}">
                                </div>
                                         
                                <div class="colabTexts">
                                    <p>
                                        {{ $nomeUsuarioProjeto }}
                                        @if($usuarioProjetoId == Auth::id())
                                            (você)
                                        @endif
                                    </p>
                                    <p style="font-size: 13px">{{ DB::table('tb_usuario')->where('id', DB::table('tb_projeto')->where('id', $id_projeto)->value('id_usuario'))->value('ds_email') }}</p>
                                </div>                                    
                            </div>
                    
                            <p id="colabProp">Proprietário</p>                           
                    
                        </div>
                    
                        @foreach(DB::table('tb_usuario')
                            ->whereIn('id', function($query) use ($id_projeto) {
                                $query->select('id_usuario')
                                    ->from('tb_contribuidor')
                                    ->where('id_projeto', $id_projeto);
                            })->get() as $usuario)
                    
                            @php
                                // Para cada colaborador, defina a imagem separadamente
                                $userImagePath = null;
                    
                                // Verifica as imagens do colaborador
                                foreach ($extensions as $ext) {
                                    $possiblePath = "user_" . $usuario->id . "/profile{$ext}";
                                    if (Storage::disk('public')->exists($possiblePath)) {
                                        $userImagePath = $possiblePath;
                                        break; // Sai do loop após encontrar a primeira imagem válida
                                    }
                                }
                    
                                // Busca o cargo do colaborador
                                $id_cargo = DB::table('tb_contribuidor')
                                    ->where('id_usuario', $usuario->id)
                                    ->where('id_projeto', $id_projeto)
                                    ->value('id_cargo');
                            @endphp
                    
                            @php
                                // Para garantir que a variável $usuarioProjetoId está correta e não esteja confundindo os colaboradores
                                $usuarioProjetoId = DB::table('tb_projeto')->where('id', $id_projeto)->value('id_usuario');
                                $idCargoContribuidor = DB::table('tb_contribuidor')
                                ->where('id_usuario', Auth::user()->id)
                                ->where('id_projeto', $id_projeto)
                                ->value('id_cargo');
                            @endphp
                    
                            <div class="colabUser">
                                <div class="colabInfo">
                                    <div class="colabImg">
                                        <!-- Aqui usamos a variável $userImagePath que é específica de cada colaborador -->
                                        <img src="{{ $userImagePath ? url('storage/' . $userImagePath) : asset('image/ProfileImg.svg') }}">
                                    </div>
                    
                                    <div class="colabTexts">
                                        <p>{{ $usuario->nm_usuario }}
                                            @if($usuario->id == Auth::id())
                                                (você)
                                            @endif
                                        </p>
                                        <p style="font-size: 13px">{{ $usuario->ds_email }}</p>
                                    </div>
                                </div>
                    
                                <select oninput="salvarNovoCargo({{ $usuario->id }})" id="cargo_{{ $usuario->id }}"
                                @if($usuarioProjetoId != Auth::user()->id && $idCargoContribuidor != 3 || $usuario->id == Auth::user()->id)
                                    disabled
                                @endif>
                                    <option value="1" {{ $id_cargo == 1 ? 'selected' : '' }}>Leitor</option>
                                    <option value="2" {{ $id_cargo == 2 ? 'selected' : '' }}>Comentador</option>
                                    <option value="3" {{ $id_cargo == 3 ? 'selected' : '' }}>Editor</option>
                                </select>
                    
                            </div>
                    
                        @endforeach
                    
                    </div>
                    
                        
                    <p class="modal_proj_text font modal_proj_text_margin">Permissões</p>

                    <div class="shareSelect">

                        <svg viewBox="0 0 58 58" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18.125 10.875C18.125 7.99077 19.2708 5.22467 21.3102 3.18521C23.3497 1.14576 26.1158 0 29 0C31.8842 0 34.6503 1.14576 36.6898 3.18521C38.7292 5.22467 39.875 7.99077 39.875 10.875C39.875 13.7592 38.7292 16.5253 36.6898 18.5648C34.6503 20.6042 31.8842 21.75 29 21.75C26.1158 21.75 23.3497 20.6042 21.3102 18.5648C19.2708 16.5253 18.125 13.7592 18.125 10.875ZM27.1875 28.0938V58L21.7047 55.2586C19.3371 54.0805 16.777 53.3328 14.1375 53.0723L3.2625 51.9848C1.41602 51.7922 0 50.2402 0 48.3711V25.375C0 23.3699 1.61992 21.75 3.625 21.75H7.05742C14.2621 21.75 21.2855 23.9703 27.1875 28.0938ZM30.8125 58V28.0938C36.7145 23.9703 43.7379 21.75 50.9426 21.75H54.375C56.3801 21.75 58 23.3699 58 25.375V48.3711C58 50.2289 56.584 51.7922 54.7375 51.9734L43.8625 53.0609C41.2344 53.3215 38.6629 54.0691 36.2953 55.2473L30.8125 58Z" fill="#14141B"/>
                        </svg>
                            
                        @php
                            $usuarioProjetoId = DB::table('tb_projeto')->where('id', $id_projeto)->value('id_usuario');
                            $idCargoContribuidor = DB::table('tb_contribuidor')
                                ->where('id_usuario', Auth::user()->id)
                                ->where('id_projeto', $id_projeto)
                                ->value('id_cargo');
                        @endphp

                        <select class="font" name="id_cargo" id="id_cargo"
                            @if($usuarioProjetoId != Auth::user()->id && $idCargoContribuidor != 3)
                                disabled
                            @endif>
                            <option value="1">Apenas leitura</option>
                            <option value="2">Autorizar comentários</option>
                            <option value="3">Autorizar edição</option>
                        </select>

                    </div>                    

                </div>
                
                <p class="modal_proj_btn button-normal fontcorpo" id="shareProjBtn" 
                @if($usuarioProjetoId == Auth::user()->id || $idCargoContribuidor == 3)
                    onclick="ShareProj()"
                @else
                    style="color: #acacac; background-color: #23242b; cursor: default;"
                @endif>Compartilhar</p>
            </form>                
        </div>
    </div>

    <!--Modal de adição de Blocos de Roteiro-->
    <div class="ModalAddBlockFade" id="ModalAddBlockFade">
        <div class="ModalAddBlock background" id="ModalAddBlock">
            <div class="ModalAddBlockHeader">
                <p class="titlefont">Importar Bloco de Roteiro</p>
                <img src="image/CloseModal.svg" onclick="CloseModalAddBlock()">
            </div>
            <form id="FormAddBlock" class="FormAddBlock fontcorpo" action="/importarRoteiro/{{ $id_projeto }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <p class="AddBlockTitle">Seus roteiros</p>

                <div class="importRotSearch">

                    <svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.57" d="M4.12413 9.34238C4.12413 11.404 4.76439 13.3084 5.84296 14.8535L0.402853 20.5443C-0.134283 21.1057 -0.134283 22.0175 0.402853 22.5789C0.939989 23.1404 1.8123 23.1404 2.34943 22.5789L7.78954 16.8882C9.26774 18.02 11.0897 18.6848 13.0621 18.6848C17.9994 18.6848 22 14.5031 22 9.34238C22 4.18161 17.9994 0 13.0621 0C8.12471 0 4.12413 4.18161 4.12413 9.34238ZM13.0621 15.8102C12.2495 15.8102 11.4448 15.6429 10.6941 15.3179C9.94336 14.9928 9.26122 14.5164 8.68663 13.9158C8.11204 13.3152 7.65625 12.6022 7.34528 11.8175C7.03431 11.0328 6.87426 10.1917 6.87426 9.34238C6.87426 8.49302 7.03431 7.65197 7.34528 6.86726C7.65625 6.08255 8.11204 5.36954 8.68663 4.76895C9.26122 4.16836 9.94336 3.69195 10.6941 3.36691C11.4448 3.04187 12.2495 2.87458 13.0621 2.87458C13.8747 2.87458 14.6793 3.04187 15.43 3.36691C16.1808 3.69195 16.8629 4.16836 17.4375 4.76895C18.0121 5.36954 18.4679 6.08255 18.7788 6.86726C19.0898 7.65197 19.2499 8.49302 19.2499 9.34238C19.2499 10.1917 19.0898 11.0328 18.7788 11.8175C18.4679 12.6022 18.0121 13.3152 17.4375 13.9158C16.8629 14.5164 16.1808 14.9928 15.43 15.3179C14.6793 15.6429 13.8747 15.8102 13.0621 15.8102Z" fill="#77777D"/>
                    </svg>

                    <input class="fontcorpo" type="search" placeholder="Pesquisar . . .">

                </div>

                <div class="rotCont">

                    @forelse ( App\Models\tb_roteiro::where('id_usuario', Auth::user()->id)->get() as $roteiro)

                        <div class="rotAdd" rotId="{{ $roteiro->id }}" id="rotAdd_{{ $roteiro->id }}" onclick="selectRot('rotAdd_{{ $roteiro->id }}')">

                            <div class="rotAddInfo1">

                                <svg width="45" height="62" viewBox="0 0 45 62" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g opacity="0.65">
                                    <mask id="path-1-inside-1_2988_515" fill="white">
                                    <rect width="44.8182" height="61.625" rx="2"/>
                                    </mask>
                                    <rect width="44.8182" height="61.625" rx="2" stroke="#77777D" stroke-width="6" mask="url(#path-1-inside-1_2988_515)"/>
                                    <line x1="12.7051" y1="20.9092" x2="32.1142" y2="20.9092" stroke="#77777D" stroke-width="3" stroke-linecap="round"/>
                                    <line x1="12.7051" y1="32.1138" x2="32.1142" y2="32.1138" stroke="#77777D" stroke-width="3" stroke-linecap="round"/>
                                    <line x1="12.7051" y1="43.3184" x2="32.1142" y2="43.3184" stroke="#77777D" stroke-width="3" stroke-linecap="round"/>
                                    </g>
                                </svg>

                                <p class="rotAddName">{{ $roteiro->nm_roteiro }}</p>

                            </div>                            

                            <div class="rotAddInfo">
                                <p>Quantidade de cenas: {{ $roteiro->qt_cena }}</p>
                                <p>Proprietário: {{ App\Models\tb_usuario::where('id', $roteiro->id_usuario)->value('nm_usuario') }}</p>
                            </div>

                        </div>

                    @empty

                    @endforelse

                </div>            

                <p class="modal_proj_btn button-normal fontcorpo" onclick="AddBlock()">Importar</p>
            </form>                
        </div>
    </div>

    <!--Modal de propriedades-->
    <div class="ModalPropsFade" id="ModalPropsFade">

        <div class="ModalProps" id="ModalProps">

            <div class="ModalProps_Header titlefont">
                <p>Propriedades</p>
                <img src="Image/CloseModal.svg" onclick="CloseModalProps()">
            </div>

            <div class="ModalProps_Margin fontcorpo">

                <div class="ModalProps_Row">

                    <div class="ModalProps_Props">

                        <p class="ModalProps_Name">{{ DB::table('tb_projeto')->where('id', $id_projeto)->value('nm_projeto') }}</p>
    
                        @php
                            use App\Models\Tb_Cena_Projeto;

                            $totalCenas = Tb_Cena_Projeto::where('id_projeto', $id_projeto)->count();
                            $cenasFinalizadas = Tb_Cena_Projeto::where('id_projeto', $id_projeto)->where('ic_conclusao', 1)->count();

                            $percentual = 0;
                            if ($totalCenas > 0) {
                                $percentual = ($cenasFinalizadas / $totalCenas) * 100;
                            }

                            $percentual = min(round($percentual, 2), 100);
                            $circunferencia = 100;
                            $valorDash = ($percentual / 100) * $circunferencia;
                            $strokeDasharray = $valorDash . ', ' . $circunferencia;

                            // Agora define a cor com base no percentual
                            $corProgresso = '#FF0000'; // Vermelho
                            
                            if ($percentual >= 100) {
                                $corProgresso = '#3ED582'; // Verde
                            } elseif ($percentual >= 85) {
                                $corProgresso = '#3E52D5'; // Azul
                            } elseif ($percentual >= 45) {
                                $corProgresso = '#D5B73E'; // Amarelo
                            }
                        @endphp

                        <div class="ModalProps_Sec">
    
                            <p>Status do projeto: <span style="color: {{ $corProgresso }}; font-weight: bolder;">{{ $percentual }}%</span></p>
                            <p>Tempo de duração: <span id="ProjTime"></span></p>
                            <p>Quantidade de frames: <span id="qtd_frames"></span></p>
                            <p>Data de criação: {{ \Carbon\Carbon::parse(DB::table('tb_projeto')->where('id', $id_projeto)->value('created_at'))->format('d/m/Y') }}                            </p>
    
                        </div>
    
                        <div class="ModalProps_Sec">
    
                            <p>Proprietário(a): {{  DB::table('tb_projeto')
                                ->join('tb_usuario', 'tb_projeto.id_usuario', '=', 'tb_usuario.id')
                                ->where('tb_projeto.id', $id_projeto)
                                ->value('tb_usuario.nm_usuario') }}</p>
                            <p>Colaboradores: 
                                @forelse(DB::table('tb_usuario')
                                    ->whereIn('id', function($query) use ($id_projeto) {
                                        $query->select('id_usuario')
                                            ->from('tb_contribuidor')
                                            ->where('id_projeto', $id_projeto);
                                    })->get() as $index => $usuario)
                                    <span style="color: #5C5C5C">{{ $usuario->nm_usuario }}@if(!$loop->last), </span>@endif
                                @empty
                                    <span style="color: #5C5C5C">Não há colaboradores nesse projeto</span>
                                @endforelse
                            </p>
    
                        </div>
    
                    </div>

                </div>

                <div class="ModalProps_Description">

                    <p class="ModalProps_Bold">Descrição</p>
                    <p class="ModalProps_Desc">{{ DB::table('tb_projeto')->where('id', $id_projeto)->value('ds_projeto') }}</p>

                </div>

            </div>

        </div>

    </div>

    <!--Modal de opções-->
    <div class="modal_opts fontcorpo" id="modal_opts">

        <div class="modal_opts_title">
            <p>{{ DB::table('tb_projeto')->where('id', $id_projeto)->value('nm_projeto') }}</p>
        </div>

        <div class="modal_opts_sec">

            <p @if($usuarioProjetoId == Auth::user()->id || $idCargoContribuidor == 3)
                onclick="OpenModalEditProj()"
            @else
                style="color: #acacac; cursor: default;"
            @endif>Editar</p>

            <p onclick="OpenModalProps()">Propriedades</p>

        </div>
        <div class="modal_opts_sec">

            <p id="import_vd_button" @if($usuarioProjetoId == Auth::user()->id || $idCargoContribuidor == 3)
                onclick="importVideo()"
            @else
                style="color: #acacac; cursor: default;"
            @endif>Importar vídeo</p>
            <input type="file" id="import_video" name="import_video" style="opacity: 0; position: absolute; bottom: 13.5vh;" accept="video/*" onchange="handleFileChange(event)">

            <p id="import_button" @if($usuarioProjetoId == Auth::user()->id || $idCargoContribuidor == 3)
                onclick="importImages()"
            @else
                style="color: #acacac; cursor: default;"
            @endif>Importar imagem</p>
            <input type="file" id="import_frames" name="import_frames" style="opacity: 0; position: absolute;" accept="image/*" multiple>

            <p @if($usuarioProjetoId == Auth::user()->id || $idCargoContribuidor == 3)
                
            @else
                style="color: #acacac; cursor: default;"
            @endif>Importar faixa de áudio</p>

            <p id="export_vd_button" @if($usuarioProjetoId == Auth::user()->id || $idCargoContribuidor == 3)
                onclick="openModalExport()"
            @else
                style="color: #acacac; cursor: default;"
            @endif>Exportar vídeo</p>
        </div>
        <form id="CloseProjForm" action="/fecharProjeto" method="POST">
            @csrf
            <p onclick="CloseProj()">Fechar</p>
        </form>
        
    </div>

    <!--Header-->
    <div class="Proj_header fontcorpo">
        <img class="ModalPorjOptBtn" src="Image/Proj_opt.svg" onclick="OpenModalPorjOpt()">
        <p>{{ DB::table('tb_projeto')->where('id', $id_projeto)->value('nm_projeto') }}</p>
    </div>

    <div class="Project fontcorpo">

        <!--Painel esquerdo - Ferramentas e roteiros-->
        <div class="leftPanel">

            <div class="PanelMargin">

                <div class="Summary" id="Summary">

                    @include('partials.cenasProjeto')

                </div>
    
                <div class="blocks" id="blocks">
                    <p class="SecTitle">Blocos de roteiro</p>

                    @if(App\Models\tb_projeto::find($id_projeto)->id_roteiro == null)

                        <div class="addBlock" id="addBlock" 

                            @if($usuarioProjetoId == Auth::user()->id || $idCargoContribuidor == 3)

                                @if(App\Models\tb_roteiro::where('id_usuario', Auth::user()->id)->get()->isEmpty())
                                    style="color: #acacac; cursor: default; background-color: #14141B;"
                                @else
                                    onclick="openModalAddBlock()"
                                @endif

                            @else
                                style="color: #acacac; cursor: default; background-color: #14141B;"
                            @endif>

                            <p><span class="add">+</span> Adicionar</p>

                        </div>

                    @else

                        <div class="addedBlock" id="addedBlock">

                            <div class="addedBlockInfo" onclick="openRoteiro({{ DB::table('tb_projeto')->where('id', $id_projeto)->value('id_roteiro') }})">

                                <p class="addedBlockInfo_Name">{{ DB::table('tb_roteiro')->where('id', DB::table('tb_projeto')->where('id', $id_projeto)->value('id_roteiro'))->value('nm_roteiro') }}</p>
                                <p class="addedBlockInfo_User">Proprietário: {{ DB::table('tb_usuario')->where('id', DB::table('tb_roteiro')->where('id', DB::table('tb_projeto')->where('id', $id_projeto)->value('id_roteiro'))->value('id_usuario'))->value('nm_usuario') }}</p>

                            </div>

                            <img src="Image/CloseModal.svg" onclick="removeRot()">

                        </div>

                    @endif

                </div>

            </div>
            
        </div>

        <!--Painel central - Visualização da animação, linha do tempo e áudio-->
        <div class="CentalPanel">
            
            <div class="CenterMargin">

                <div class="Visu fullscreen-container" id="fullscreenContainer">
    
                    <div class="porporcao fontcorpo" id="proporcao">

                        <div class="proporcaoCont" onclick="openProporcaoModal()">
                            <img id="proporcaoIcon" src="Image/{{ str_replace(':', '_', DB::table('tb_projeto')->where('id', $id_projeto)->value('nm_proporcao')) }}.svg">
                            <p id="proporcaoValor">{{ DB::table('tb_projeto')->where('id', $id_projeto)->value('nm_proporcao') }}</p>
                            <img src="Image/SetaProporcao.svg" id="proporcaoImg">
                        </div>

                        <div class="proporcaoContainer" id="proporcaoContainer">
                            
                            <div class="proporcaoRow" id="16:9" onclick="changeProporcao('16:9')" @if(DB::table('tb_projeto')->where('id', $id_projeto)->value('nm_proporcao') != "16:9") style="opacity: 0.3" @endif>
                                <img src="Image/16_9.svg">
                                <p>16:9</p>
                            </div>

                            <div class="proporcaoRow" id="16:10" onclick="changeProporcao('16:10')" @if(DB::table('tb_projeto')->where('id', $id_projeto)->value('nm_proporcao') != "16:10") style="opacity: 0.3" @endif>
                                <img src="Image/16_10.svg">
                                <p>16:10</p>
                            </div>

                            <div class="proporcaoRow" id="9:16" onclick="changeProporcao('9:16')" @if(DB::table('tb_projeto')->where('id', $id_projeto)->value('nm_proporcao') != "9:16") style="opacity: 0.3" @endif>
                                <img src="Image/9_16.svg">
                                <p>9:16</p>
                            </div>

                        </div>

                    </div>

                    <div class="frameVisu-wrapper" id="frameWrapper">
                        <img id="FrameVisu" src="image/FrameTeste.png">
                        <canvas class="diffCanvas" id="canvasPrev1"></canvas>
                        <canvas class="diffCanvas" id="canvasPrev2"></canvas>
                        <canvas class="diffCanvas" id="canvasPrev3"></canvas>
                        <canvas class="diffCanvas" id="canvasPost1"></canvas>
                        <canvas class="diffCanvas" id="canvasPost2"></canvas>
                        <canvas class="diffCanvas" id="canvasPost3"></canvas>
                    </div>
                    
                    <!-- A BARRA FICA DENTRO DO CONTAINER -->
                    <div class="visu-inputs" id="visu_inputs">
                        <div class="input-row" id="input-row">

                            <input type="range" min="1" max="100" value="30" class="Visu_Scroll" id="Visu_Scroll" oninput="UpdateFrame()" onchange="handleInput()" >

                            <button class="fullscreen-btn" onclick="enterCustomFullscreen()" id="exitFullscreenBtn">
                                <img src="image/fullscreen.svg" alt="Tela cheia">
                            </button>

                        </div>
                        <input type="range" min="0" max="100" step="0.01" id="Audio_Scroll" style="display: none">
                    </div>
                
                    <div class="player" id="player">

                        <div class="playLeft" id="playLeft">
                            <p id="frame_count">Frame: 1</p>
                        </div>
    
                        <div class="playCenter" id="playCenter">
                            <img src="Image/PlayerBack.svg" id="playPrev" onclick="playBack()">
                            <img class="play" id="play" src="Image/PlayerPlay.svg" onclick="playIcon()">
                            <img src="Image/PlayerNext.svg" id="playNext" onclick="playEnd()">
                        </div>
    
                        <div class="playRight" id="playRight">
                            <p><span id="time">00:00:00</span> / <span id="MaxTime">00:00:00</span></p>
                        </div>
    
                    </div>

                </div>

                <div class="timeline_row">

                    <div class="cursor" id="cursor">
                        <img src="Image/Cursor.svg" id="cursor_img">
                    </div>

                    <div class="frameOptMargin fontcorpo" id="frameOptMargin">

                        <div class="moveCont" id="moveCont">
                            <div class="moveInput">
                                <p>Frame:</p>
                                <input type="number" id="moveFrameInput" min="1">

                                <svg width="9" height="11" viewBox="0 0 9 11" fill="none" xmlns="http://www.w3.org/2000/svg" onclick="moverFrame()" style="cursor: pointer">
                                    <path d="M0 1.86852C0 1.06982 0.890145 0.59343 1.5547 1.03647L7.6354 5.09027C8.25832 5.50555 8.22079 6.4329 7.56634 6.79648L1.48564 10.1746C0.819113 10.5449 0 10.063 0 9.30049V6V1.86852Z" fill="#616187"/>
                                </svg>
                            </div>
                        </div>

                        <div class="frameOpt" id="frameOpt">

                            <svg viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" id="moveBtn" onclick="showMoverFrame()">
                                <rect id="moveIconFundo" opacity="0.26" x="0.5" y="0.5" width="29" height="29" rx="4.5" fill="#FEFEFE" stroke="#616187"/>
                                <path id="moveIcon" d="M15.1944 19.998C15.5325 19.998 15.8056 19.5513 15.8056 18.9984C15.8056 18.4455 15.5325 17.9988 15.1944 17.9988H13.0554C12.7173 17.9988 12.4442 17.5521 12.4442 16.9992V13.0008H13.0554C13.3017 13.0008 13.5252 12.7571 13.6207 12.3823C13.7162 12.0074 13.6627 11.5795 13.4889 11.2921L12.2666 9.29285C12.0279 8.90238 10.6402 8.90238 10.4014 9.29285L9.1791 11.2921C9.0034 11.5795 8.95183 12.0074 9.04732 12.3823C9.14282 12.7571 9.36436 13.0008 9.61264 13.0008H10.2238V16.9992C10.2238 18.6548 11.045 19.998 12.0573 19.998H15.1963H15.1944ZM15.8056 10.002C15.4675 10.002 15.1944 10.4487 15.1944 11.0016C15.1944 11.5545 15.4675 12.0012 15.8056 12.0012H17.9446C18.2827 12.0012 18.5558 12.4479 18.5558 13.0008V16.9992H17.9446C17.6983 16.9992 17.4748 17.2429 17.3793 17.6177C17.2838 17.9926 17.3373 18.4205 17.5111 18.7079L18.7334 20.7071C18.9721 21.0976 20.3598 21.0976 20.5986 20.7071L21.8209 18.7079C21.9966 18.4205 22.0482 17.9926 21.9527 17.6177C21.8572 17.2429 21.6356 16.9992 21.3874 16.9992H20.7762V13.0008C20.7762 11.3452 19.955 10.002 18.9427 10.002H15.8056Z" fill="#616187"/>
                            </svg>

                            <svg viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" id="substBtn" onclick="substituirFrameInput()">
                                <rect opacity="0.26" x="0.5" y="0.5" width="29" height="29" rx="4.5" fill="#FEFEFE" stroke="#616187"/>
                                <path d="M13.8887 9.0696C13.8887 8.47965 14.3455 8 14.9073 8H17.454V10.1392C17.454 10.435 17.6816 10.674 17.9633 10.674H20.0006V12.8132V13.6154V15.4872C20.0006 16.0771 19.5438 16.5568 18.982 16.5568H14.9073C14.3455 16.5568 13.8887 16.0771 13.8887 15.4872V9.0696ZM20.0006 10.1392H17.9633V8L20.0006 10.1392Z" fill="#616187"/>
                                <path d="M12.5156 12.6904C12.6535 12.6906 12.7655 12.8026 12.7656 12.9404V15.0303C12.7659 15.2245 12.9123 15.364 13.0742 15.3643H15.3115V20.3779C15.3115 21.0688 14.7749 21.6472 14.0938 21.6475H10.0186C9.33725 21.6474 8.79988 21.0689 8.7998 20.3779V13.96C8.79994 13.269 9.33729 12.6905 10.0186 12.6904H12.5156ZM12.875 13.0156C12.875 12.8182 13.0852 12.7096 13.2422 12.7949L13.3057 12.8428L15.1768 14.8076L15.2207 14.8711C15.2979 15.0282 15.1876 15.2295 14.9951 15.2295H13.125C12.9869 15.2295 12.875 15.1176 12.875 14.9795V13.0156Z" fill="#616187" stroke="#FEFEFE" stroke-width="0.4"/>
                                <path d="M12.4081 9.2222C12.5505 9.2222 12.6655 9.3215 12.6655 9.44441C12.6655 9.56732 12.5505 9.66662 12.4081 9.66662H11.5073C11.365 9.66662 11.25 9.76591 11.25 9.88882V10.7776H11.5073C11.6111 10.7776 11.7052 10.8318 11.7454 10.9151C11.7856 10.9985 11.7631 11.0936 11.6899 11.1575L11.1752 11.6019C11.0747 11.6887 10.9114 11.6887 10.8109 11.6019L10.2961 11.1575C10.2221 11.0936 10.2004 10.9985 10.2406 10.9151C10.2808 10.8318 10.3741 10.7776 10.4787 10.7776H10.7361V9.88882C10.7361 9.52079 11.0819 9.2222 11.5082 9.2222H12.4089H12.4081Z" fill="#616187"/>
                                <path d="M16.5919 20.2241C16.4495 20.2241 16.3345 20.1248 16.3345 20.0019C16.3345 19.879 16.4495 19.7797 16.5919 19.7797H17.4927C17.635 19.7797 17.75 19.6804 17.75 19.5575V18.6686H17.4927C17.3889 18.6686 17.2948 18.6145 17.2546 18.5312C17.2144 18.4478 17.2369 18.3527 17.3101 18.2888L17.8248 17.8444C17.9253 17.7576 18.0886 17.7576 18.1891 17.8444L18.7039 18.2888C18.7779 18.3527 18.7996 18.4478 18.7594 18.5312C18.7192 18.6145 18.6259 18.6686 18.5213 18.6686H18.2639V19.5575C18.2639 19.9255 17.9181 20.2241 17.4918 20.2241H16.5911H16.5919Z" fill="#616187"/>
                            </svg>

                            <input type="file" id="substFrame" accept="image/*" style="display: none" onchange="substituirFrame()">

                            <svg viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" id="expBtn" onclick="exportSelectedFrames()">
                                <rect opacity="0.26" x="0.5" y="0.5" width="29" height="29" rx="4.5" fill="#FEFEFE" stroke="#616187"/>
                                <path d="M8 10.625C8 9.72871 8.74733 9 9.66652 9H13.8328V12.25C13.8328 12.6994 14.2052 13.0625 14.6661 13.0625H17.9991V16.3125H13.6245C13.2782 16.3125 12.9996 16.5842 12.9996 16.9219C12.9996 17.2596 13.2782 17.5312 13.6245 17.5312H17.9991V20.375C17.9991 21.2713 17.2518 22 16.3326 22H9.66652C8.74733 22 8 21.2713 8 20.375V10.625ZM17.9991 17.5312V16.3125H20.8661L19.8505 15.3223C19.6058 15.0836 19.6058 14.6977 19.8505 14.4615C20.0953 14.2254 20.4911 14.2229 20.7333 14.4615L22.8164 16.4928C23.0612 16.7314 23.0612 17.1174 22.8164 17.3535L20.7333 19.3848C20.4885 19.6234 20.0927 19.6234 19.8505 19.3848C19.6084 19.1461 19.6058 18.7602 19.8505 18.524L20.8661 17.5338L17.9991 17.5312ZM17.9991 12.25H14.6661V9L17.9991 12.25Z" fill="#616187"/>
                            </svg>

                            <svg viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" id="delBtn" onclick="deleteSelectedFrames()">
                                <rect opacity="0.26" x="0.5" y="0.5" width="29" height="29" rx="4.5" fill="#FEFEFE" stroke="#616187"/>
                                <path d="M13.0179 9.41484L12.8571 9.75H10.7143C10.3192 9.75 10 10.0852 10 10.5C10 10.9148 10.3192 11.25 10.7143 11.25H19.2857C19.6808 11.25 20 10.9148 20 10.5C20 10.0852 19.6808 9.75 19.2857 9.75H17.1429L16.9821 9.41484C16.8616 9.15938 16.6138 9 16.3438 9H13.6563C13.3862 9 13.1384 9.15938 13.0179 9.41484ZM19.2857 12H10.7143L11.1875 19.9453C11.2232 20.5383 11.692 21 12.2567 21H17.7433C18.308 21 18.7768 20.5383 18.8125 19.9453L19.2857 12Z" fill="#616187"/>
                            </svg>

                        </div>

                    </div>

                    <div class="faixa">

                        <div class="time">

                            <div class="time_view_invis">
        
                                <div class="line_invis"></div>
                                <p class="invis">00:00</p>
        
                            </div>

                        </div>

                        <img class="video" src="image/Video.svg">
                        <img class="audio" src="image/Audio.svg">

                    </div>

                    <div class="timeline_column">

                        <div class="time" id="timeView">

                            @foreach ($images as $image)

                                <div class="time_view" index="{{ str_pad($loop->index, 2, '0', STR_PAD_LEFT) }}">
                                    <div class="line"></div>
                                    <p>00:00</p>
                                </div>

                            @endforeach

                            <div class="time_view_invis">
        
                                <div class="line_invis"></div>
                                <p class="invis">00:00</p>
        
                            </div>                                                
        
                        </div>

                        <div class="timeline">

                            <div class="timelineWrapper" id="timelineWrapper">
                                <div class="timelineCena" id="timelineCena">

                                    @foreach ($cena as $cenas)

                                        <div class="cenaTime" id="cenaTime_{{ $cenas->id }}" 
                                             style="background-color: {{ $cenas->nm_cor }};" 
                                             quant="{{ $cenas->nr_frame_final - $cenas->nr_frame_inicial }}"
                                             inicio="{{ $cenas->nr_frame_inicial }}">
                                        </div>

                                    @endforeach

                                </div>
                            </div>

                            <div class="Frames" id="Frames">

                                @foreach ($images as $image)
                                
                                    <div class="frame" id="frame_{{ $loop->index + 1 }}"
                                        @if($usuarioProjetoId == Auth::user()->id || $idCargoContribuidor == 3)
                                            onclick="SelectFrame('frame_{{ $loop->index + 1 }}')"
                                            onmouseover="showFrameBtns('frame_img_{{ $loop->index + 1 }}')"
                                            onmouseout="hideFramesBtns('frame_img_{{ $loop->index + 1 }}')"
                                        @else
                                            style="cursor: default;"
                                        @endif>
                                        <div class="frameInfoWrapper">
                                            <div class="frameInfo">
                                                <p class="frameNum fontcorpo">{{ $loop->index + 1 }}</p>
                                            </div>
                                        </div>                                        
                                        <img src="{{ $image }}" loading="lazy" id="frame_img_{{ $loop->index + 1 }}">
                                    </div>

                                @endforeach
        
                                <img src="Image/AddFrame.svg"
                                @if($usuarioProjetoId == Auth::user()->id || $idCargoContribuidor == 3)
                                    onclick="importImages()"
                                @endif>
            
                            </div>

                            <div class="audio_editor">

                                @if (!Storage::disk('public')->exists("proj_{$id_projeto}/audioFiles") || empty(Storage::disk('public')->files("proj_{$id_projeto}/audioFiles")))
                                    
                                    <div class="audio_add">
                                        <p>+  Adicionar faixa de áudio</p>
                                    </div>

                                @else

                                    @foreach ($audios as $audio)

                                        <audio class="audio_player" id="audioPlayer" style="display: none" controls preload="auto">
                                            <source src="{{ $audio }}" type="audio/mpeg">
                                            Your browser does not support the audio element.
                                        </audio>

                                        <div class="waveDiv" id="waveDiv">
                                            <canvas id="waveCanvas"></canvas>   
                                        </div>                                                                      
                            
                                    @endforeach

                                @endif

                            </div>                            
        
                        </div>
                        
                    </div>

                </div>

            </div>

        </div>

        <!--Painel direito - Comentários e propriedades-->
        <div class="RightPanel">
            
            <div class="PanelMargin">

                <div class="Share" onmouseover="ShareAppear()" onmouseout="ShareOut()">

                    @foreach ($usuarios as $user)

                        @php
                            $extensions = ['.webp', '.png', '.jpg', '.jpeg', '.jfif'];
                            $userImagePath = null;

                            foreach ($extensions as $ext) {
                                $possiblePath = "user_" . $user->id . "/profile{$ext}";
                                if (Storage::disk('public')->exists($possiblePath)) {
                                    $userImagePath = $possiblePath;
                                    break;
                                }
                            }

                            // Busca o cargo uma única vez
                            $id_cargo = DB::table('tb_contribuidor')
                                ->where('id_usuario', $user->id)
                                ->where('id_projeto', $id_projeto)
                                ->value('id_cargo');
                        @endphp

                        <div class="UserShare" id="UserShare" style="left: {{ 2 * ($loop->count - $loop->iteration + 1) }}%">
                            <img src="{{ $userImagePath ? url('storage/' . $userImagePath) : asset('image/ProfileImg.svg') }}">
                        </div>
                        
                    @endforeach
                    
                    <img id="addShare" src="Image/addShare.svg">
                    <div class="ShareTextBtn" id="ShareTextBtn" onclick="openModalShareProj()">
                        <p>Compartilhar</p>
                    </div>
                </div>

                <div class="comments">

                    <div class="CommentsTitle">

                        <div class="commentTypes">

                            <div class="commentType" id="ic1" style="background-color: #E6E6E6;" onclick="showComments(1)">
                                <p>Comentários</p>
                            </div>

                            <div class="commentType" id="ic0" style="color: #8A8A94;" onclick="showComments(0)">
                                <p>Erros</p>
                            </div>

                        </div>
                        
                        <button>Ver todos</button>
                    </div>

                    <div class="CommentsCont" id="CommentsCont">

                        @include('partials.comentarios')

                    </div>

                    <div class="ErrorsCont" id="ErrorsCont" style="display: none">

                        @include('partials.comentariosErro')

                    </div>

                    <form class="CommentSender" action="" method="POST">
                        @csrf

                        <input type="text" placeholder="Digite seu comentário..." id="commentInput" oninput="atualizarIconSend()"
                        @if($usuarioProjetoId != Auth::user()->id && $idCargoContribuidor == 1)
                            disabled
                        @endif>

                        <div class="frameSelectMargin" id="frameSelectMargin">
                            <div class="frameSelect fontcorpo" id="frameSelect">
                                <p>Frame:</p>
                                <input type="number" id="frameSelectInp" min="1">
                            </div>
                        </div>

                        <img id="frameErrorIcon" src="Image/FrameError.svg" style="display: none" onclick="openFrameSelector()">

                        <img id="sendCommentImg"
                        @if($usuarioProjetoId != Auth::user()->id && $idCargoContribuidor == 1)
                            src="Image/SendCommentDesactivated.svg"
                        @else
                            src="Image/SendComment.svg"
                        @endif>

                    </form>

                </div>

                <div class="proprierties">

                    <div class="propTitle">
                        <p>Propriedades</p>
                    </div>

                    <div class="PropCont">

                        <div class="propsRow">

                            <div class="propsBtn" id="videoPropsBtn" onclick="showPropsVideo()">
                                <p>Vídeo</p>
                            </div>

                            <div class="propsBtn" id="audioPropsBtn" onclick="showPropsAudio()">
                                <p>Áudio</p>
                            </div>

                        </div>

                        <div class="propSec" id="videoProps">

                            <p>Velocidade / FPS</p>
                            <div class="FPS">

                                <input type="range" min="1" max="360" value="{{ DB::table('tb_projeto')->where('id', $id_projeto)->value('ds_fps') }}" class="FPS_Select" id="FPS_Select" oninput="UpdateFPS()"
                                @if($usuarioProjetoId != Auth::user()->id && $idCargoContribuidor != 3)
                                    disabled
                                @endif>
                                <p id="FPS">30</p>

                            </div>

                            <div class="Repeat">

                                <p>Tocar em loop</p>
                                <input type="checkbox" id="isRepeat">                            
    
                            </div>

                            <div class="Repeat">

                                <p>Mostrar número dos frames</p>
                                <input type="checkbox" id="isShowNum" checked onchange="showFrameNum()">                            
    
                            </div>

                            <div class="Repeat">

                                <p>Ativar Onion Skinning</p>
                                <input type="checkbox" id="isOnion" onchange="showOnion()">                            
    
                            </div>

                            <div class="onionConfig" id="onionConfig">

                                <div class="addOnionLayers">

                                    <div class="addOnionNumbers">

                                        <div class="onionVisuWrapper" onclick="activeOnion(-3, 'onionVisuNum-3', 'onionVisu-3')">
                                            <p id="onionVisuNum-3">-3</p>
                                        </div>
                                        <div class="onionVisuWrapper" onclick="activeOnion(-2, 'onionVisuNum-2', 'onionVisu-2')">
                                            <p id="onionVisuNum-2">-2</p>
                                        </div>
                                        <div class="onionVisuWrapper" onclick="activeOnion(-1, 'onionVisuNum-1', 'onionVisu-1')">
                                            <p style="background-color: #3ED582" id="onionVisuNum-1">-1</p>
                                        </div>
                                        <div class="onionVisuWrapper">
                                            <p style="background-color: #767683; cursor: default">0</p>
                                        </div>
                                        <div class="onionVisuWrapper">
                                            <p style="background-color: #D53E40" id="onionVisuNum1">1</p>
                                        </div>
                                        <div class="onionVisuWrapper" onclick="activeOnion(2, 'onionVisuNum2', 'onionVisu2')">
                                            <p id="onionVisuNum2">2</p>
                                        </div>
                                        <div class="onionVisuWrapper" onclick="activeOnion(3, 'onionVisuNum3', 'onionVisu3')">
                                            <p id="onionVisuNum3">3</p>
                                        </div>

                                    </div>

                                    <div class="addOnionVisu">

                                        <div class="onionVisuWrapperAct" onclick="activeOnion(-3, 'onionVisuNum-3', 'onionVisu-3')">
                                            <div class="onionVisu" id="onionVisu-3" style="height: 3.5vh"></div>
                                        </div>
                                        <div class="onionVisuWrapperAct" onclick="activeOnion(-2, 'onionVisuNum-2', 'onionVisu-2')">
                                            <div class="onionVisu" id="onionVisu-2" style="height: 4vh"></div>
                                        </div>
                                        <div class="onionVisuWrapperAct" onclick="activeOnion(-1, 'onionVisuNum-1', 'onionVisu-1')">
                                            <div class="onionVisu" id="onionVisu-1" style="height: 4.5vh; background-color: #3ED582"></div>
                                        </div>
                                        <div class="onionVisuWrapperAct">
                                            <div class="onionVisu" style="height: 5vh; background-color: #767683"></div>
                                        </div>
                                        <div class="onionVisuWrapperAct" onclick="activeOnion(1, 'onionVisuNum1', 'onionVisu1')">
                                            <div class="onionVisu" id="onionVisu1" style="height: 4.5vh; background-color: #D53E40"></div>
                                        </div>
                                        <div class="onionVisuWrapperAct" onclick="activeOnion(2, 'onionVisuNum2', 'onionVisu2')">
                                            <div class="onionVisu" id="onionVisu2" style="height: 4vh"></div>
                                        </div>
                                        <div class="onionVisuWrapperAct" onclick="activeOnion(3, 'onionVisuNum3', 'onionVisu3')">
                                            <div class="onionVisu" id="onionVisu3" style="height: 3.5vh"></div>
                                        </div>

                                    </div>

                                </div>

                                <div class="onionColors">
                                    <p>Frames anteriores</p>
                                    <input type="color" id="prevFrameColor" value="#3ED582" onchange="setOnionColor('prev')">
                                    <p>Frames posteriores</p>
                                    <input type="color" id="postFrameColor" value="#D53E41" onchange="setOnionColor('post')">
                                </div>

                            </div>

                        </div>
    
                        <div class="propSec" id="audioProps">
    
                            <p>Volume do áudio</p>
                            <div class="FPS">

                                <input type="range" min="0" max="1" step="0.01" value="{{ DB::table('tb_projeto')->where('id', $id_projeto)->value('qt_volume') }}" class="FPS_Select" id="Vol_Select"
                                @if($usuarioProjetoId != Auth::user()->id && $idCargoContribuidor != 3)
                                    disabled
                                @endif>
                                <p id="Volume">100</p>

                            </div>
    
                        </div>

                    </div>

                </div>

                <div class="dash" onclick="openDashboard()">
                    <div class="DashBtn">
                        <img src="image/Dash.svg">
                        <p>Cronograma</p>
                    </div>
                </div>

            </div>

        </div>

    </div>

</body>
</html>