<div class="noResult" id="noResultProj" style="display: none">
    <img src="Image/noResult.svg">
</div>

<section class="tools fontcorpo">

    <div class="search">
        <img src="Image/search.svg">
        <input type="text" id="searchInput" class="search_bar" placeholder="Pesquisar . . ." onkeyup="filterProjects()">
        <img src="Image/filter.svg">
        <select>
            <option class="SearchSelectOpt">Todos</option>
            <option class="SearchSelectOpt">Concluídos</option>
            <option class="SearchSelectOpt">Atrasados</option>
        </select>
    </div>

</section>

<div class="pageMargin">

    <div class="pageTitleCont">
        <p class="pageTitle titlefont">Projetos</p>
        <div class="Create">
            <p id="CreateText" class="CreateText fontcorpo">Criar projeto</p>
            <img id="CreateBtn" class="CreateImg" src="image/Add.svg" onmouseover="CreateBtnAnimation()"
                onmouseout="CreateBtnAnimationOff()" onclick="OpenModalCreateProj()">
        </div>

    </div>

    <div class="pageLine back-black"></div>

</div>

<div class="content">

    <div class="section">
        <div class="list">

            <!--Adicionar os projetos-->
            <div class="project_cont" id="your_projs">

                @if ($projs)
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

                        <div class="proj" id="proj_{{ $proj->id }}">
                            <div class="projMargin">
                                <div class="proj_img">

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
                                        <img class="proj_img1" src="{{ url('storage/' . $imagePath) }}"
                                            alt="Imagem do projeto" onclick="OpenProject({{ $proj->id }})">
                                    @else
                                        <img class="proj_img1" src="{{ asset('image/ProjDefaultImg.png') }}"
                                            alt="Imagem padrão do projeto" onclick="OpenProject({{ $proj->id }})">
                                    @endif

                                </div>
                                <div class="projOpt">
                                    <div class="status_color" style="background-color: {{ $corProgresso }}"></div>

                                    <div class="proj_info fontcorpo">

                                        <div class="proj_info_header">

                                            <h3 class="projName fontcorpo" onclick="OpenProject({{ $proj->id }})">
                                                {{ $proj->nm_projeto }}</h3>

                                            <div class="proj_info_row">
                                                <img src="image/Triple_dot.svg"
                                                    class="proj_info_btn_{{ $proj->id }}"
                                                    onclick="ShowProprierties('modal_proj_open_{{ $proj->id }}', 'proj_info_btn_{{ $proj->id }}')">

                                                @php
                                                    $coverPath = public_path("storage/proj_$proj->id/CoverImage");
                                                    $coverUrl = null;

                                                    // Procurar extensões comuns
                                                    $extensions = ['jpg', 'jpeg', 'png', 'webp'];

                                                    foreach ($extensions as $ext) {
                                                        if (file_exists("$coverPath/Cover.$ext")) {
                                                            $coverUrl = asset(
                                                                "storage/proj_$proj->id/CoverImage/Cover.$ext",
                                                            );
                                                            break;
                                                        }
                                                    }
                                                @endphp

                                                <div class="modal_proj_open_out">

                                                    <div class="modal_proj_open"
                                                        id="modal_proj_open_{{ $proj->id }}">
                                                        <p class="modal_proj_open_title">{{ $proj->nm_projeto }}</p>
                                                        <p onclick="OpenProject({{ $proj->id }})">Abrir</p>
                                                        <p
                                                            onclick="OpenModalEditProj('{{ $proj->id }}', '{{ $proj->nm_projeto }}', '{{ $proj->ds_projeto }}', '{{ $proj->id_tipo }}', '{{ $coverUrl }}')">
                                                            Editar</p>
                                                        <p
                                                            onclick="excluirProjModal({{ $proj->id }}, 'proj_{{ $proj->id }}', 'projIni_{{ $proj->id }}')">
                                                            Excluir</p>
                                                    </div>

                                                </div>
                                            </div>

                                        </div>

                                        <div class="proj_info_desc">

                                            <p>{{ $proj->ds_projeto }}</p>

                                        </div>

                                        <div class="proj_info_status">

                                            <p><span class="font_medium">Progresso: </span><span
                                                    class="progressoProjeto"
                                                    style="color: {{ $corProgresso }}; text-shadow: 1px 1px 0px {{ $corShadow }};">{{ $percentuais[$proj->id] ?? 0 }}%</span>
                                            </p>

                                            <div class="proj_info_status1">

                                                <p><span class="font_medium">Data de criação:
                                                    </span>{{ $proj->created_at->format('d/m/Y') }}</p>

                                                <p>
                                                    <span class="font_medium">Última modificação: </span>
                                                    {{ $proj->updated_at ? $proj->updated_at->locale('pt_BR')->diffForHumans() : 'Nunca modificado' }}
                                                </p>
                                                <p><span class="font_medium">Proprietário(a):
                                                    </span>{{ DB::table('tb_usuario')->where('id', $proj->id_usuario)->value('nm_usuario') }}
                                                </p>

                                            </div>

                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>

                    @empty

                        <div class="emptyItems fontcorpo">
                            <p>Não há nenhum projeto atribuído a você.</p>
                        </div>
                    @endforelse
                @endif

            </div>
        </div>
    </div>

    <div class="stts_cont fontcorpo">

        <p class="font_medium">Status geral dos projetos: <span class="bolder" id="statusProj"
                style="color: {{ $corStatusGeral }}">{{ $mediaProgresso ?? 0 }}%</span></p>
        <p class="font_medium">Quantidade de projetos:
            <span class="bolder" style="opacity: 0.8"
                id="proj_qtd">{{ str_pad(Auth::user()->qt_projeto, 2, '0', STR_PAD_LEFT) }}</span>
        </p>

    </div>

</div>
