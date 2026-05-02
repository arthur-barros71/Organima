@php
    $previousUserId2 = null;
@endphp

@php
    $usuarioProjetoId = DB::table('tb_roteiro')->where('id', $id_roteiro)->value('id_usuario');
    $idCargoContribuidor = DB::table('tb_contribuidor')
        ->where('id_usuario', Auth::user()->id)
        ->where('id_roteiro', $id_roteiro)
        ->value('id_cargo');
@endphp

@foreach($erros as $erro)

    <div class="commentNameErr fontcorpo"
    @if($erro->id_usuario == Auth::user()->id)
        style="justify-content: flex-end;"
     @endif>
        @php
            // Monta o style para .errorFrameView
            $efvStyle = '';
            if ($erro->ic_conclusao == 1) {
                $efvStyle .= 'border-top-left-radius: 5px; border-bottom-left-radius: 5px;';
            }
            if ($erro->id_usuario != Auth::user()->id) {
                $efvStyle .= 'right: -1%;';
            }

            // Monta o style para o <p> interno
            $pStyle = $erro->ic_conclusao == 1
                ? 'margin-left: 5%;'
                : '';
        @endphp

        <div class="errorFrameView" id="errorFrameView_{{ $erro->id }}"
        @if($efvStyle)
            style="{{ $efvStyle }}"
        @endif>
            <svg viewBox="0 0 15 15" fill="none"
                xmlns="http://www.w3.org/2000/svg"
                id="errorIconView_{{ $erro->id }}"
                @if($erro->ic_conclusao == 1)
                    style="display: none;"
                @endif
            >
                <circle cx="7.5" cy="7.5" r="6.7" fill="#14141B" stroke="#FEFEFE" stroke-width="0.4"/>
                <circle cx="7.25" cy="7.25" r="6.25" fill="#D53E41"/>
                <line x1="0.5" y1="-0.5" x2="6.05319" y2="-0.5"
                    transform="matrix(-0.65396 0.756529 -0.508161 -0.861262 9.28516 5)"
                    stroke="#FEFEFE" stroke-linecap="round"/>
                <line x1="0.5" y1="-0.5" x2="6.08506" y2="-0.5"
                    transform="matrix(-0.650781 -0.759266 0.511363 -0.859365 10 10)"
                    stroke="#FEFEFE" stroke-linecap="round"/>
            </svg>

            <p @if($pStyle) style="{{ $pStyle }}" @endif>
                Erro no roteiro
            </p>
        </div>

        <p @if($erro->id_usuario == Auth::user()->id)
            style="margin: 1vh 3% 0 2%;"
        @endif>
            {{ DB::table('tb_usuario')->where('id', $erro->id_usuario)->value('nm_usuario') }}
            @if($erro->id_usuario == Auth::user()->id)
                (você)
            @endif
        </p>
    </div>                     

    <div class="comment_cont" user="{{ $erro->id_usuario }}" @if($erro->id_usuario == Auth::user()->id) style="justify-content: flex-end;" @endif data-dt_erro="{{ $erro->dt_erro }}">

        <div class="commentErro">
            <p id="commentTxt_{{ $erro->id }}"
            @if($erro->ic_conclusao == 1)
                style="font-style: italic; text-decoration: line-through; opacity: 0.54;"
            @endif>{{ $erro->ds_erro }}</p>

            <div class="corrigirErro">

                <div class="corrigirCont" id="corrigirCont_{{ $erro->id }}"
                @if($erro->ic_conclusao == 1)
                    style="background-color: #DCF8EA; color: #8FCDAC;"
                @endif>
                    <label class="switch">
                        <input type="checkbox" id="corrigir_{{ $erro->id }}"
                        @if($usuarioProjetoId != Auth::user()->id && $idCargoContribuidor == 1)
                            disabled
                        @endif
                        @if($erro->ic_conclusao == 1)
                            checked
                            disabled
                        @endif                        
                        onchange="corrigirErro('{{ $erro->id }}', 'corrigir_{{ $erro->id }}', 'corrigirCont_{{ $erro->id }}', 'errorFrameView_{{ $erro->id }}', 'errorIconView_{{ $erro->id }}', 'commentTxt_{{ $erro->id }}')">
                        <span class="slider round"></span>
                    </label>

                    <p>Marcar como corrigido</p>
                </div>                   

            </div>

        </div>
                                    
    </div>

    @php
        $previousUserId2 = $erro->id_usuario;
    @endphp

    @if($erro->ic_conclusao == 1)

        <div class="consertoMargin" data-dt="{{ $erro->dt_conclusao }}">

            <div class="consertoCont">
                <p>{{ $erro->nm_concluidor }} marcou um erro como <span style="color: #8EB7A0">corrigido</span></p>
            </div>

        </div>

    @endif  

@endforeach

