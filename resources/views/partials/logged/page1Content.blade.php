<section class="tools fontcorpo">

    <div class="search">
        <img src="Image/search.svg">
        <input type="text" class="search_bar" placeholder="Pesquisar . . .">
        <img src="Image/filter.svg">
        <select>
            <option class="SearchSelectOpt">Todos</option>
            <option class="SearchSelectOpt">Concluídos</option>
            <option class="SearchSelectOpt">Atrasados</option>
        </select>
    </div>

</section>

<div class="pageMargin">

    <p class="pageTitle titlefont">Página inicial</p>
    <div class="pageLine back-black"></div>

</div>

@php
    // Função simples para escurecer cor
    function darkenColor($hex, $percent)
    {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = max(0, $r - ($r * $percent) / 100);
        $g = max(0, $g - ($g * $percent) / 100);
        $b = max(0, $b - ($b * $percent) / 100);

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
@endphp

<div class="content">

    <div class="ProjRot">

        <div class="sectionIni">

            <p class="secTitleIni fontcorpo">Projetos recentes</p>
            <div class="listIni">

                <!--Adicionar os projetos-->
                <div class="project_contIni" id="recent_projs">

                    <div class="noResult1" id="noResultProj1" style="display: none">
                        <img src="Image/noResult.svg">
                    </div>

                    <div class="projGradientNext @if (count($projs) > 4) projGradientVisible @endif"
                        @if (count($projs) > 4) style="pointer-events: all" @else style="pointer-events: none" @endif
                        id="nextproj1">
                        <img src="image/NextProj.svg" id="recent_projs_NextImg"
                            @if (count($projs) > 4) style="pointer-events: all" @else style="pointer-events: none" @endif
                            onclick="PassRecentProjPage('recent_projs', {{ count($projs) }}, 'prevproj1',  'nextproj1', 'recent_projs_NextImg', 'recent_projs_PrevImg')">
                    </div>

                    <div class="projGradientPrev" id="prevproj1" style="pointer-events: none">
                        <img src="image/PrevProj.svg" id="recent_projs_PrevImg" style="pointer-events: none"
                            onclick="returnRecentProjPage('recent_projs', 'prevproj1',  'nextproj1', 'recent_projs_NextImg', 'recent_projs_PrevImg')">
                    </div>

                    @forelse ($projs as $proj)
                        @php
                            if ($percentuais[$proj->id] >= 100) {
                                $corProgresso = '#3ED582'; // Verde
                                $corShadow = darkenColor($corProgresso, 30);
                            } elseif ($percentuais[$proj->id] >= 85) {
                                $corProgresso = '#3E52D5'; // Azul
                                $corShadow = darkenColor($corProgresso, 30);
                            } elseif ($percentuais[$proj->id] >= 45) {
                                $corProgresso = '#D5B73E'; // Amarelo
                                $corShadow = darkenColor($corProgresso, 30);
                            } else {
                                $corProgresso = '#D53E41'; // Vermelho (por exemplo)
                                $corShadow = darkenColor($corProgresso, 30);
                            }
                        @endphp

                        <div class="projIni fontcorpo" id="projIni_{{ $proj->id }}">

                            <div class="projMarginIni">

                                <div class="projIniOptMargin">
                                    <div class="projIniOpt"
                                        onclick="ShowProprierties('modal_projIni_open_{{ $proj->id }}', 'projIni_info_btn_{{ $proj->id }}')">

                                        <img src="image/Triple_dot.svg" class="projIni_info_btn_{{ $proj->id }}">

                                        @php
                                            $coverPath = public_path("storage/proj_$proj->id/CoverImage");
                                            $coverUrl = null;

                                            // Procurar extensões comuns
                                            $extensions = ['jpg', 'jpeg', 'png', 'webp'];

                                            foreach ($extensions as $ext) {
                                                if (file_exists("$coverPath/Cover.$ext")) {
                                                    $coverUrl = asset("storage/proj_$proj->id/CoverImage/Cover.$ext");
                                                    break;
                                                }
                                            }
                                        @endphp

                                        <div class="modal_proj_open_out">

                                            <div class="modal_proj_open" style="top: 2vh"
                                                id="modal_projIni_open_{{ $proj->id }}">
                                                <p class="modal_proj_open_title">{{ $proj->nm_projeto }}</p>
                                                <p onclick="OpenProject({{ $proj->id }})">Abrir</p>
                                                <p
                                                    onclick="OpenModalEditProj('{{ $proj->id }}', '{{ $proj->nm_projeto }}', '{{ $proj->ds_projeto }}', '{{ $proj->id_tipo }}', '{{ $coverUrl }}')">
                                                    Editar</p>
                                                <p
                                                    onclick="excluirProjModal({{ $proj->id }}, 'projIni_{{ $proj->id }}', 'proj_{{ $proj->id }}')">
                                                    Excluir</p>
                                            </div>

                                        </div>

                                    </div>
                                </div>

                                <div class="proj_imgIni" style="background-color: {{ $corProgresso }}">

                                    <div class="imgPrcentMargin">
                                        <div class="textPercent">
                                            <div class="textPercent2">
                                                <p
                                                    style="color: {{ $corProgresso }}; text-shadow: 1px 1px 0px {{ $corShadow }};">
                                                    {{ $percentuais[$proj->id] ?? 0 }}%</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="proj_imgIni1">

                                        @php
                                            $extensions = ['.webp', '.png', '.jpg', '.jpeg', '.jfif']; // Adicione outras extensões conforme necessário
                                            $imagePath = null;

                                            foreach ($extensions as $extension) {
                                                $imagePath = "proj_{$proj->id}/CoverImage/cover{$extension}";
                                                if (Storage::disk('public')->exists($imagePath)) {
                                                    break;
                                                }
                                                $imagePath = null;
                                            }
                                        @endphp

                                        @if ($imagePath)
                                            <img src="{{ url('storage/' . $imagePath) }}" alt="Imagem do projeto"
                                                onclick="OpenProject({{ $proj->id }})">
                                        @else
                                            <img src="{{ asset('image/ProjDefaultImg.png') }}"
                                                alt="Imagem padrão do projeto"
                                                onclick="OpenProject({{ $proj->id }})">
                                        @endif

                                    </div>

                                </div>

                                <div class="projOptIni">
                                    <h3 class="projNameIni fontcorpo" onclick="OpenProject({{ $proj->id }})">
                                        {{ $proj->nm_projeto }}</h3>
                                    <p style="font-size: 15px; margin: 1vh 0 0 0">
                                        @if ($proj->updated_at)
                                            Editado {{ $proj->updated_at->locale('pt_BR')->diffForHumans() }}
                                        @else
                                            Nunca editado
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="EmptyCenter fontcorpo">
                            <p>Não há nenhum projeto atribuído a você</p>
                        </div>
                    @endforelse

                </div>

            </div>
        </div>
        <div class="sectionIni">
            <p class="secTitleIni fontcorpo" style="margin-top: 1vh">Blocos de roteiro recentes</p>
            <div class="listIni">

                <!--Adicionar os projetos-->
                <div class="project_contIni" id="recent_blocks">

                    <div class="noResult1" id="noResultRot1" style="display: none; top: 54vh;">
                        <img src="Image/noResult.svg">
                    </div>

                    <div class="projGradientNext @if (count($rot) > 4) projGradientVisible @endif"
                        @if (count($rot) > 4) style="pointer-events: all" @else style="pointer-events: none" @endif
                        id="nextproj2">
                        <img src="image/NextProj.svg" id="recent_blocks_NextImg"
                            @if (count($rot) > 4) style="pointer-events: all" @else style="pointer-events: none" @endif
                            onclick="PassRecentProjPage('recent_blocks', {{ count($rot) }}, 'prevproj2',  'nextproj2', 'recent_blocks_NextImg', 'recent_blocks_PrevImg')">
                    </div>

                    <div class="projGradientPrev" id="prevproj2" style="pointer-events: none">
                        <img src="image/PrevProj.svg" id="recent_blocks_PrevImg" style="pointer-events: none"
                            onclick="returnRecentProjPage('recent_blocks', 'prevproj2',  'nextproj2', 'recent_blocks_NextImg', 'recent_blocks_PrevImg')">
                    </div>

                    @forelse ($rot as $rots)
                        <div class="rotIni fontcorpo" id="rotIni_{{ $rots->id }}">

                            <div class="rotMarginIni">

                                <div class="projIniOptMargin" style="left: 100%">
                                    <div class="projIniOpt"
                                        onclick="openModalRotProps('modal_rotIni_open_{{ $rots->id }}', 'rotIni_info_btn_{{ $rots->id }}')">

                                        <img class="rotIni_info_btn_{{ $rots->id }}" src="image/Triple_dot.svg">

                                        <div class="modal_proj_open_out">

                                            <div class="modal_proj_open" style="top: 2vh"
                                                id="modal_rotIni_open_{{ $rots->id }}">
                                                <p class="modal_proj_open_title">{{ $rots->nm_roteiro }}</p>
                                                <p onclick="OpenScript({{ $rots->id }})">Abrir</p>
                                                <p
                                                    onclick="OpenModalEditRot('{{ $rots->id }}', '{{ $rots->nm_roteiro }}', '{{ $rots->ds_roteiro }}')">
                                                    Editar</p>
                                                <p
                                                    onclick="excluirRotModal({{ $rots->id }}, 'rotIni_{{ $rots->id }}', 'rot_{{ $rots->id }}')">
                                                    Excluir</p>
                                            </div>

                                        </div>

                                    </div>
                                </div>

                                <div class="rot_imgIni fontcorpo" onclick="OpenScript({{ $rots->id }})">

                                    @php
                                        $cena = DB::table('tb_cena_roteiro')->where('id_roteiro', $rots->id)->first();
                                    @endphp

                                    <div id="textArea" contenteditable="true" style="zoom: 0.4">
                                        {!! $cena?->ds_texto !!}
                                    </div>

                                </div>

                                <div class="rotOpt">

                                    <div class="rot_status"></div>

                                    <div class="rot_info">

                                        <h3 class="rotName fontcorpo">{{ $rots->nm_roteiro }}</h3>
                                        <div class="rot_stt"></div>

                                    </div>

                                    <div class="rot_info1Ini">

                                        <p>
                                            <span class="font_medium">Última modificação: </span>
                                            @if ($rots->updated_at)
                                                {{ $rots->updated_at->locale('pt_BR')->diffForHumans() }}
                                            @else
                                                Nunca modificado
                                            @endif
                                        </p>
                                        <p><span class="font_medium">Proprietário:
                                            </span>{{ DB::table('tb_usuario')->where('id', $rots->id_usuario)->value('nm_usuario') }}
                                        </p>

                                    </div>

                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="EmptyCenter fontcorpo">
                            <p>Não há nenhum bloco de roteiro atribuído a você.</p>
                        </div>
                    @endforelse

                </div>

            </div>

        </div>

    </div>

    <div class="stts_cont fontcorpo">

        <p class="font_medium">Status geral da conta: <span class="bolder" id="statusGeral"
                style="color: {{ $corStatusGeral }}">{{ $mediaProgresso ?? 0 }}%</span></p>

        <p class="font_medium" style="margin-left: 40%">Quantidade de projetos:
            <span class="bolder" style="opacity: 0.8"
                id="proj_qtd1">{{ str_pad(Auth::user()->qt_projeto, 2, '0', STR_PAD_LEFT) }}</span>
        </p>

        <p class="font_medium">Quantidade de blocos de roteiro:
            <span class="bolder" style="opacity: 0.8"
                id="rot_qtd1">{{ str_pad(Auth::user()->qt_roteiro, 2, '0', STR_PAD_LEFT) }}</span>
        </p>

    </div>

</div>
