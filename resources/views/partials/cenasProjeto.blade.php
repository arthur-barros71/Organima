@php
    // Para garantir que a variável $usuarioProjetoId está correta e não esteja confundindo os colaboradores
    $usuarioProjetoId = DB::table('tb_projeto')->where('id', $id_projeto)->value('id_usuario');
    $idCargoContribuidor = DB::table('tb_contribuidor')
        ->where('id_usuario', Auth::user()->id)
        ->where('id_projeto', $id_projeto)
        ->value('id_cargo');
@endphp

<div class="propTitle">
    <p>Sumário de cenas</p>
</div>

@php
    function hex2rgb($hex) {
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 6) {
            list($r, $g, $b) = str_split($hex, 2);
            return [hexdec($r), hexdec($g), hexdec($b)];
        } elseif (strlen($hex) == 3) {
            list($r, $g, $b) = str_split($hex, 1);
            return [hexdec($r.$r), hexdec($g.$g), hexdec($b.$b)];
        }
        return [0, 0, 0];
    }
@endphp

@foreach ($cena as $cenas)
    <div class="cena">
        <div class="cena_row" onclick="gotoCena('{{ $cenas->nr_frame_inicial }}')">
            <p class="cena_title">{{ $cenas->nm_cena_projeto }}</p>
            <div class="cena_btns" @if(App\Models\tb_projeto::find($id_projeto)->id_roteiro == null) style="justify-content: end;" @endif>
                @if(App\Models\tb_projeto::find($id_projeto)->id_roteiro != null)
                    <img src="Image/cena_seta.svg" class="showInfoCena" id="showInfoCena_{{ $cenas->id }}" onclick="showInfoCena('ligarCena_{{ $cenas->id }}', 'showInfoCena_{{ $cenas->id }}')">
                @endif
                <img src="Image/EditBlack.svg" class="editCenaBtn" onclick="openModalEditCena('{{ $cenas->nm_cena_projeto }}', '{{ $cenas->ds_cena_projeto }}', '{{ $cenas->nm_cor }}', '{{ $cenas->nr_frame_inicial }}', '{{ $cenas->nr_frame_final }}', '{{ $cenas->id }}')">
            </div>
        </div>
                            
        <div class="cenaLine" data-frame-inicial="{{ $cenas->nr_frame_inicial }}" data-frame-final="{{ $cenas->nr_frame_final }}" style="background-color: {{ $cenas->nm_cor }}"></div>

            @php
                if($cenas->id_cena_roteiro != null) {
                    $rgbColor = hex2rgb( App\Models\Tb_Cena_Roteiro::find($cenas->id_cena_roteiro)->nm_cor);
                }
                else {
                    $rgbColor = hex2rgb( $cenas->nm_cor);
                }
                
            @endphp

            <button class="ligarCena" id="ligarCena_{{ $cenas->id }}" 
                @if($cenas->id_cena_roteiro != null)
                    style="background-color: rgba({{ $rgbColor[0] }}, {{ $rgbColor[1] }}, {{ $rgbColor[2] }}, 0.2); cursor: pointer;"
                    {{-- onclick="goRoteiro({{ App\Models\Tb_Roteiro::find(App\Models\Tb_Cena_Roteiro::find($cenas->id_cena_roteiro)->id_roteiro)->id }})" --}}
                @else
                    onclick="openModalLigarCena('{{ $cenas->id }}')"
                @endif>
                    @if($cenas->id_cena_roteiro == null)
                        <img src="Image/AddRotCena.svg">Ligar Cena
                    @else
                        <div class="rotCenaImg" onclick="openModalVerCena()">
                            <svg width="9" height="11" viewBox="0 0 8 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g opacity="0.65">
                                    <rect x="0.35" y="0.35" width="6.57273" height="9.3" rx="0.65" stroke="#4A4A5F" stroke-width="0.7"/>
                                    <line x1="2.16836" y1="3.28672" x2="5.10472" y2="3.28672" stroke="#4A4A5F" stroke-width="0.7" stroke-linecap="round"/>
                                    <line x1="2.16836" y1="5.1041" x2="5.10472" y2="5.1041" stroke="#4A4A5F" stroke-width="0.7" stroke-linecap="round"/>
                                    <line x1="2.16836" y1="6.92246" x2="5.10472" y2="6.92246" stroke="#4A4A5F" stroke-width="0.7" stroke-linecap="round"/>
                                </g>
                            </svg>
                        </div>                       

                        @php
                            $cenaRoteiro = App\Models\Tb_Cena_Roteiro::find($cenas->id_cena_roteiro);
                            $nomeCenaRoteiro = $cenaRoteiro ? $cenaRoteiro->nm_cena_roteiro : 'Cena não encontrada';
                            $descricaoCenaRoteiro = $cenaRoteiro ? $cenaRoteiro->ds_cena_roteiro : 'Descrição não disponível';
                            $idRoteiro = $cenaRoteiro ? $cenaRoteiro->id_roteiro : null;
                            $nomeRoteiro = $idRoteiro ? App\Models\Tb_Roteiro::find($idRoteiro)->nm_roteiro : 'Roteiro não encontrado';
                            $usuarioId = $idRoteiro ? App\Models\Tb_Roteiro::find($idRoteiro)->id_usuario : null;
                            $nomeUsuario = $usuarioId ? App\Models\Tb_Usuario::find($usuarioId)->nm_usuario : 'Usuário não encontrado';
                            $textoCena = $cenaRoteiro->ds_texto;
                        @endphp

                        <div class="nm_cena_roteiro" onclick="openModalVerCena(
                            '{{ $cenaRoteiro->id_cena_roteiro }}', 
                            '{{ $nomeCenaRoteiro }}', 
                            '{{ $descricaoCenaRoteiro }}', 
                            '{{ $cenaRoteiro->id_roteiro }}', 
                            '{{ $nomeRoteiro }}', 
                            '{{ $nomeUsuario }}',
                            '{{ $textoCena }}'
                        )">
                            <p>{{ $nomeCenaRoteiro }}</p>
                        </div>
                        <div class="nm_roteiro_cena" onclick="desligarCena({{ $cenas->id }})">
                            <svg width="8" height="8" viewBox="0 0 5 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <line x1="0.5" y1="-0.5" x2="5.15685" y2="-0.5" transform="matrix(-0.707107 0.707107 0.707107 0.707107 4.80078 1)" stroke="#4A4A5F" stroke-linecap="round"/>
                                <line x1="0.5" y1="-0.5" x2="5.15685" y2="-0.5" transform="matrix(-0.707107 -0.707107 -0.707107 0.707107 4 5)" stroke="#4A4A5F" stroke-linecap="round"/>
                            </svg>
                        </div>

                    @endif
            </button>
    </div>
@endforeach

<button class="add_cena" id="add_cena" 
    @if($usuarioProjetoId == Auth::user()->id || $idCargoContribuidor == 3)
        onclick="OpenCreateCenaModal()"
    @else
        style="color: #acacac; cursor: default; background-color: #56596e;"
    @endif>Adicionar cena</button>