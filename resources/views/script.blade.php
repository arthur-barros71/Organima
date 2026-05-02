<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Organima</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.jsdelivr.net/npm/pusher-js@7.0.3/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.3/dist/echo.iife.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Roboto&family=Open+Sans&family=Lora&family=Merriweather&family=Oswald&family=Slabo+27px&family=Anton&family=Raleway&family=Poppins&family=Quicksand&family=Playfair+Display&family=Montserrat&family=PT+Serif&family=Ubuntu&family=Source+Sans+Pro&family=Nunito&family=Oxygen&family=Cabin&family=Crete+Round&family=Arimo&family=Exo+2&family=Roboto+Slab&family=Indie+Flower&family=Pacifico&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!--Scripts-->
    <script src="{{ asset('js/Script.js') }}" defer></script>
    <script src="{{ asset('js/notfy.js') }}" defer></script>
    <script src="{{ asset('js/notfy.js') }}" defer></script>
    <script src="{{ asset('js/textEditor.js') }}" defer></script>

    <!--CSS-->
    <link rel="stylesheet" href="{{asset('css/Script.css')}}">
    <link rel="stylesheet" href="{{asset('css/pallet.css')}}">
    <link rel="stylesheet" href="{{asset('css/notifications.css')}}">
    <link rel="stylesheet" href="{{asset('css/textEditor.css')}}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"/>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>

    @if (!Auth::check())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                sessionStorage.removeItem('pageBegin');
                sessionStorage.removeItem('page');
                setTimeout(() => {
                    window.location.href = "/";
                }, 100);
            });
        </script>
    @endif

</head>
<body>

    <p style="display: none" id="script_id">{{ $id_roteiro }}</p>
    <p id="user_auth_id" style="display: none">{{ Auth::user()->id }}</p>

    <!-- Modal para criação de Blocos de roteiro -->
    <div class="ModalEditRotFade" id="ModalEditRotFade">
        <div class="ModalEditRot background" id="ModalEditRot">
            <div class="ModalEditRotHeader">
                <p class="titlefont">Editar Bloco de roteiro</p>
                <img src="image/CloseModal.svg" onclick="CloseModalEditRot()">
            </div>
            <form id="FormEditRot" class="FormEditRot" action="/editarRoteiro/{{$id_roteiro}}" method="POST">
                @csrf
                <div class="modal_Rot_Name">
                    <p class="modal_Rot_text font fontcorpo">Nome do bloco de roteiro</p>
                    <input type="text" class="font fontcorpo" id="Rot_name" name="nm_roteiro" value="{{ DB::table('tb_roteiro')->where('id', $id_roteiro)->value('nm_roteiro') }}">
                </div>
                <div class="modal_Rot_desc">
                    <p class="modal_Rot_text font fontcorpo">Descrição do bloco de roteiro</p>
                    <textarea class="font fontcorpo" id="desc_Rot" name="ds_roteiro">{{ DB::table('tb_roteiro')->where('id', $id_roteiro)->value('ds_roteiro') }}</textarea>
                </div>
                <!-- Botão de criação -->
                <p class="modal_Rot_btn button-normal fontcorpo" id="editRot_btn" onclick="EditRot()">Editar bloco de roteiro</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            </form>
        </div>
    </div>

    <!--Modal de compartilhamento de roteiros-->
    <div class="ModalShareProjectFade" id="ModalShareProjectFade">
        <div class="ModalShareProject background" id="ModalShareProject">
            <div class="ModalShareProjectHeader">
                <p class="titlefont">Compartilhar {{ DB::table('tb_roteiro')->where('id', $id_roteiro)->value('nm_roteiro') }}</p>
                <img src="image/CloseModal.svg" onclick="closeModalShareProj()">
            </div>
            <form id="FormShareProj" class="FormShareProj fontcorpo" action="/compartilharRoteiro/{{ $id_roteiro }}" method="POST">
                @csrf

                <div class="share_campos">

                    <p class="modal_proj_text font">Email</p>
                    <input type="email" class="font" id="ds_email" name="ds_email">

                    <div class="colab" id="colab">

                        <p class="modal_proj_text font">Colaboradores</p>
                    
                        @php
                            // Defining image extensions
                            $extensions = ['.webp', '.png', '.jpg', '.jpeg', '.jfif'];
                            $imagePath = null;
                    
                            // Check the main user's image
                            foreach ($extensions as $extension) {
                                $possiblePath = "user_" . DB::table('tb_roteiro')->where('id', $id_roteiro)->value('id_usuario') . "/profile{$extension}";
                                if (Storage::disk('public')->exists($possiblePath)) {
                                    $imagePath = $possiblePath;
                                    break;
                                }
                            }
                        @endphp
                    
                        <div class="colabUser">
                    
                            @php
                                $usuarioProjetoId = DB::table('tb_roteiro')->where('id', $id_roteiro)->value('id_usuario');
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
                                    <p style="font-size: 13px">{{ DB::table('tb_usuario')->where('id', DB::table('tb_roteiro')->where('id', $id_roteiro)->value('id_usuario'))->value('ds_email') }}</p>
                                </div>                                    
                            </div>
                    
                            <p id="colabProp">Proprietário</p>                           
                    
                        </div>
                    
                        @foreach(DB::table('tb_usuario')
                            ->whereIn('id', function($query) use ($id_roteiro) {
                                $query->select('id_usuario')
                                    ->from('tb_contribuidor')
                                    ->where('id_roteiro', $id_roteiro);
                            })->get() as $usuario)
                    
                            @php
                                // For each collaborator, define the image separately
                                $userImagePath = null;
                    
                                // Check the collaborator's images
                                foreach ($extensions as $ext) {
                                    $possiblePath = "user_" . $usuario->id . "/profile{$ext}";
                                    if (Storage::disk('public')->exists($possiblePath)) {
                                        $userImagePath = $possiblePath;
                                        break; // Exit the loop after finding the first valid image
                                    }
                                }
                    
                                // Fetch the collaborator's role
                                $id_cargo = DB::table('tb_contribuidor')
                                    ->where('id_usuario', $usuario->id)
                                    ->where('id_roteiro', $id_roteiro)
                                    ->value('id_cargo');
                            @endphp
                    
                            @php
                                // Ensure $usuarioProjetoId is correct and not mixing up collaborators
                                $usuarioProjetoId = DB::table('tb_roteiro')->where('id', $id_roteiro)->value('id_usuario');
                                $idCargoContribuidor = DB::table('tb_contribuidor')
                                ->where('id_usuario', Auth::user()->id)
                                ->where('id_roteiro', $id_roteiro)
                                ->value('id_cargo');
                            @endphp
                    
                            <div class="colabUser">
                                <div class="colabInfo">
                                    <div class="colabImg">
                                        <!-- Use $userImagePath which is specific to each collaborator -->
                                        <img src="{{ $userImagePath ? url('storage/' . $userImagePath) : asset('image/ProfileImg.svg') }}">
                                    </div>
                    
                                    <div class="colabTexts">
                                        <p>{{ $usuario->nm_usuario }}
                                            @if($usuario->id == Auth::id())
                                                (you)
                                            @endif
                                        </p>
                                        <p style="font-size: 13px">{{ $usuario->ds_email }}</p>
                                    </div>
                                </div>
                    
                                <select oninput="salvarNovoCargo({{ $usuario->id }})" id="cargo_{{ $usuario->id }}"
                                @if($usuarioProjetoId != Auth::user()->id && $idCargoContribuidor != 3 || $usuario->id == Auth::user()->id)
                                    disabled
                                @endif>
                                    <option value="1" {{ $id_cargo == 1 ? 'selected' : '' }}>Reader</option>
                                    <option value="2" {{ $id_cargo == 2 ? 'selected' : '' }}>Commenter</option>
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
                            $usuarioProjetoId = DB::table('tb_roteiro')->where('id', $id_roteiro)->value('id_usuario');
                            $idCargoContribuidor = DB::table('tb_contribuidor')
                                ->where('id_usuario', Auth::user()->id)
                                ->where('id_roteiro', $id_roteiro)
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

    <!--Modal de criação de cenas-->
    <div class="ModalCreateCenaFade" id="ModalCreateCenaFade">

        <div class="ModalCreateCena" id="ModalCreateCena">

            <div class="ModalCreateCenaHeader titlefont">
                <p>Criar nova cena</p>
                <img src="image/CloseModal.svg" onclick="CloseModalCreateCena()">
            </div>

            <form class="ModalCreateCenaForm fontcorpo" id="createCenaForm" action="/criarCenaRoteiro/{{ $id_roteiro }}" method="POST">
                @csrf

                <div class="CenaRow">

                    <div class="CenaColumn" style="width: 75%">

                        <p>Título da cena:</p>
                        <input type="text" id="nm_cena" maxlength="45" name="nm_cena_roteiro">

                    </div>                    

                    <div class="CenaColumn" style="width: 20%">

                        <p>Cor da etiqueta:</p>
                        <input type="color" name="nm_cor" id="ds_cor" style="padding: 0; width: 100%; height: 100%; border-radius: 10px; -webkit-appearance: none; margin: 0; background-color: #000000;">

                    </div>                    

                </div>                 

                <p>Descrição da cena:</p>
                <textarea class="fontcorpo" name="ds_cena_roteiro" id="ds_cena" maxlength="250" resizable="false"></textarea>

                <button type="button" class="createCenaBtn" onclick="criarCena()">Criar cena</button>

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

            <form class="ModalCreateCenaForm fontcorpo" id="editCenaForm" action="/editarCenaRoteiro/" method="POST">
                @csrf

                <div class="CenaRow">

                    <div class="CenaColumn" style="width: 75%">

                        <p>Título da cena:</p>
                        <input type="text" id="nm_cena_edit" maxlength="45" name="nm_cena_roteiro">

                    </div>                    

                    <div class="CenaColumn" style="width: 20%">

                        <p>Cor da etiqueta:</p>
                        <input type="color" name="nm_cor" id="ds_cor_edit" style="padding: 0; width: 100%; height: 100%; border-radius: 10px; -webkit-appearance: none; margin: 0; background-color: #000000;">

                    </div>                    

                </div>                 

                <p>Descrição da cena:</p>
                <textarea class="fontcorpo" name="ds_cena_roteiro" id="ds_cena_edit" maxlength="250" resizable="false"></textarea>

                <button type="button" class="createCenaBtn" onclick="editarCena()">Editar cena</button>
                <button type="button" class="deleteCenaBtn" onclick="deleteCena()">Deletar cena</button>

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

                        <p class="ModalProps_Name">{{ DB::table('tb_roteiro')->where('id', $id_roteiro)->value('nm_roteiro') }}</p>
    
                        <div class="ModalProps_Sec">
    
                            <p>Status do roteiro: 0%</p>
                            <p>Quantidade de caracteres: <span id="qt_char"></span></p>
                            <p>Quantidade de palavras: <span id="qt_palavras"></span></p>
                            <p>Data de criação: {{ \Carbon\Carbon::parse(DB::table('tb_roteiro')->where('id', $id_roteiro)->value('created_at'))->format('d/m/Y') }}                            </p>
    
                        </div>
    
                        <div class="ModalProps_Sec">
    
                            <p>Proprietário(a): {{  DB::table('tb_roteiro')
                                ->join('tb_usuario', 'tb_roteiro.id_usuario', '=', 'tb_usuario.id')
                                ->where('tb_roteiro.id', $id_roteiro)
                                ->value('tb_usuario.nm_usuario') }}</p>
                            <p>Colaboradores: 
                                @forelse(DB::table('tb_usuario')
                                    ->whereIn('id', function($query) use ($id_roteiro) {
                                        $query->select('id_usuario')
                                            ->from('tb_contribuidor')
                                            ->where('id_roteiro', $id_roteiro);
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
                    <p class="ModalProps_Desc">{{ DB::table('tb_roteiro')->where('id', $id_roteiro)->value('ds_roteiro') }}</p>

                </div>

            </div>

        </div>

    </div>

    <!--Modal de opções-->
    <div class="modal_opts fontcorpo" id="modal_opts">

        <div class="modal_opts_title">
            <p>{{ DB::table('tb_roteiro')->where('id', $id_roteiro)->value('nm_roteiro') }}</p>
        </div>

        <div class="modal_opts_sec">

            <p @if($usuarioProjetoId == Auth::user()->id || $idCargoContribuidor == 3)
                onclick="OpenModalEditRot()"
            @else
                style="color: #acacac; cursor: default;"
            @endif>Editar</p>

            <p onclick="OpenModalProps()">Propriedades</p>
        </div>
        <form id="CloseProjForm" action="/fecharRoteiro" method="POST">
            @csrf
            <p onclick="CloseRot()">Fechar</p>
        </form>
        

    </div>

    <!--Header-->
    <div class="Proj_header fontcorpo">
        <img class="ModalPorjOptBtn" src="Image/Proj_opt.svg" onclick="OpenModalRotOpt()">
        <p>{{ DB::table('tb_roteiro')->where('id', $id_roteiro)->value('nm_roteiro') }}</p>

        @php
            use Carbon\Carbon;

            Carbon::setLocale('pt_BR');
            $updatedAt = DB::table('tb_roteiro')->where('id', $id_roteiro)->value('updated_at');
            $diff = Carbon::parse($updatedAt)->diffInSeconds(now());
        @endphp

        <p class="lastSave" id="updated-time" data-updated-at="{{ \Carbon\Carbon::parse($updatedAt)->toIso8601String() }}">
            Salvo {{ $diff < 5 ? 'agora' : Carbon::parse($updatedAt)->diffForHumans() }}
        </p>

        <script>
            let intervaloAtualizacao = null;
            const el = document.getElementById("updated-time");

            function tempoRelativo(dataISO) {
                const updated = new Date(dataISO);
                const agora = new Date();
                const segundos = Math.floor((agora - updated) / 1000);

                if (segundos < 5) return "Salvo agora";
                if (segundos < 60) return `Salvo há ${segundos} segundo${segundos > 1 ? 's' : ''}`;
                const minutos = Math.floor(segundos / 60);
                if (minutos < 60) return `Salvo há ${minutos} minuto${minutos > 1 ? 's' : ''}`;
                const horas = Math.floor(minutos / 60);
                if (horas < 24) return `Salvo há ${horas} hora${horas > 1 ? 's' : ''}`;
                const dias = Math.floor(horas / 24);
                return `Salvo há ${dias} dia${dias > 1 ? 's' : ''}`;
            }

            function atualizarTempo() {
                const data = el.getAttribute("data-updated-at");
                el.textContent = tempoRelativo(data);
            }

            function iniciarAtualizacao() {
                atualizarTempo();
                intervaloAtualizacao = setInterval(atualizarTempo, 1000);
            }

            function mostrarSalvando() {
                clearInterval(intervaloAtualizacao);
                el.textContent = "Salvando...";
            }

            function restaurarTextoSalvo(novaDataISO = null) {
                if (novaDataISO) {
                    el.setAttribute("data-updated-at", novaDataISO);
                }
                iniciarAtualizacao();
            }

            iniciarAtualizacao();
        </script>

    </div>

    <div class="Script fontcorpo">

        <div class="leftPanel">

            <div class="panelRow">

                <p>Formatação de texto</p>
                <div class="panelLinha"></div>

            </div>             
            
            <div class="Editor">

                <div class="Editor_Left">

                    <p>Fonte</p> 
                    
                    <div class="Editor_row">
    
                        <select id="font">
                            <!-- Fontes do sistema -->
                            <option value="Arial" style="font-family: Arial;">Arial</option>
                            <option value="Times New Roman" style="font-family: 'Times New Roman';">Times New Roman</option>
                            <option value="Courier New" style="font-family: 'Courier New';">Courier New</option>
                            <option value="Georgia" style="font-family: Georgia;">Georgia</option>
                            <option value="Verdana" style="font-family: Verdana;">Verdana</option>
                            <option value="Tahoma" style="font-family: Tahoma;">Tahoma</option>
                            <option value="Comic Sans MS" style="font-family: 'Comic Sans MS';">Comic Sans MS</option>
                            <option value="Impact" style="font-family: Impact;">Impact</option>
                        </select>
    
                    </div>   
    
                    <div class="Editor_row">
    
                        <button id="bold"><i class="fas fa-bold"></i></button>
                        <button id="italic"><i class="fas fa-italic"></i></button>
                        <button id="underline"><i class="fas fa-underline"></i></button>
                        <button id="strikethrough"><i class="fas fa-strikethrough"></i></button>
    
                    </div>

                    <div class="Editor_row">
    
                        <button id="alignLeft"><i class="fas fa-align-left"></i></button>
                        <button id="alignCenter"><i class="fas fa-align-center"></i></button>
                        <button id="alignRight"><i class="fas fa-align-right"></i></button>
                        <button id="alignJustify"><i class="fas fa-align-justify"></i></button>
    
                    </div>
                    
                    <div class="Editor_row">

                        <button id="orderedList"><i class="fas fa-list-ol"></i></button>
                        <button id="unorderedList"><i class="fas fa-list-ul"></i></button>
    
                    </div>
    
                </div>

                <div class="Editor_Right">

                    <p>Tamanho</p>               

                    <div class="Editor_row">

                        <select id="fontSize">
                            <option value="8">8</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="14">14</option>
                            <option value="16">16</option>
                            <option value="18">18</option>
                            <option value="20">20</option>
                            <option value="22">22</option>
                            <option value="24">24</option>
                            <option value="26">26</option>
                            <option value="28">28</option>
                            <option value="30">30</option>
                            <option value="32">32</option>
                            <option value="36">36</option>
                            <option value="40">40</option>
                        </select>

                    </div>
                    
                    <div class="Editor_row_right">
    
                        <button id="textColor"><i class="fas fa-font"></i></button>
                        <button id="bgColor"><i class="fas fa-fill-drip"></i></button>

                        <input type="color" id="textColorPicker" style="display: none">
                        <input type="color" id="bgColorPicker" style="display: none">
    
                    </div>

                    <div class="Editor_row_right">
    
                        <button id="superscript"><i class="fas fa-superscript"></i></button>
                        <button id="subscript"><i class="fas fa-subscript"></i></button>
    
                    </div>

                    <div class="Editor_row_right">
    
                        <button id="indent"><i class="fas fa-indent"></i></button>
                        <button id="outdent"><i class="fas fa-outdent"></i></button>
    
                    </div>

                </div>

            </div>
            
            <div class="panelRow2">

                <p>Sumário</p>
                <div class="panelLinha"></div>

            </div>

            <div class="Summary" id="Summary">

                @foreach($cenas_roteiro as $cena)

                    <div class="cena" data-id="{{ $cena->id }}" onclick="setCena({{ $cena->id }}, '{{ $cena->nm_cena_roteiro }}')">

                        <div style="display: flex; justify-content: space-between">
                            <p class="cena_title">{{ $cena->nm_cena_roteiro }}</p>
                            <img src="Image/EditBlack.svg" onclick="OpenEditCenaModal({{ $cena->id }}, '{{ e($cena->nm_cena_roteiro) }}', '{{ e($cena->ds_cena_roteiro) }}', '{{ $cena->nm_cor }}')">
                        </div>
                        
                        <div class="cenaColor" 
                            style="background-color: {{ $cena->nm_cor }}; @if(session('id_cena_roteiro') == $cena->id)width: 100%; @endif">
                        </div>

                    </div>
                
                @endforeach

                <button class="add_cena" id="add_cena" 
                        @if($usuarioProjetoId == Auth::user()->id || $idCargoContribuidor == 3)
                            onclick="OpenCreateCenaModal()"
                        @else
                            style="color: #acacac; cursor: default; background-color: #56596e;"
                        @endif>Adicionar cena</button>             

            </div>
            
        </div>
        
        <div class="centralPanel">

            <div class="scrollTextArea" id="scrollTextArea">

                <script>
                    window.idCena = {{ $cena_selecionada }};
                </script>

                @foreach($cenas_roteiro as $cena)
                    
                    <div class="textArea" id="textArea" data-idCena="{{ $cena->id }}"
                    @if($usuarioProjetoId == Auth::user()->id || $idCargoContribuidor == 3)
                        contenteditable="true"
                    @endif                    
                    @if(session('id_cena_roteiro') != $cena->id)
                        style="display: none;"
                    @endif>{!! $cena->ds_texto !!}</div>  
                    
                @endforeach

            </div>

        </div>

        <div class="rightPanel">
            
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
                                ->where('id_roteiro', $id_roteiro)
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

                        @include('partials.comentariosRot')

                    </div>

                    <div class="ErrorsCont" id="ErrorsCont" style="display: none">

                        @include('partials.comentariosErroRot')

                    </div>

                    <form class="CommentSender" action="" method="POST">
                        @csrf

                        <input type="text" placeholder="Digite seu comentário..." id="commentInput" oninput="atualizarIconSend()"
                        @if($usuarioProjetoId != Auth::user()->id && $idCargoContribuidor == 1)
                            disabled
                        @endif>

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

                        <div class="propRow">
                            <p>{{ DB::table('tb_roteiro')->where('id', session('id_roteiro'))->value('nm_roteiro') }}</p>
                            <div class="propLine"></div>
                        </div>

                        <div class="props">

                            <p>Número de cenas: {{ DB::table('tb_cena_roteiro')->where('id_roteiro', $id_roteiro)->count() }}</p>

                        </div>

                        <div class="propRow">
                            <p id="cenaAtual" data-idCena="{{session('id_cena_roteiro')}}">Cena atual: {{ DB::table('tb_cena_roteiro')->where('id', session('id_cena_roteiro'))->value('nm_cena_roteiro') }}</p>
                            <div class="propLine"></div>
                        </div>

                        <div class="props">

                            <p>Número de palavras: <span id="qt_words_cena"></span></p>
                            <p>Número de caracteres: <span id="qt_char_cena"></span></p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

   <script>
        window.onload = function() {
            setCena({{ session('id_cena_roteiro') }}, '{{ DB::table('tb_cena_roteiro')->where('id', session('id_cena_roteiro'))->value('nm_cena_roteiro') }}');
        };
    </script>
    
</body>
</html>