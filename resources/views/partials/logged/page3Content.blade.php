<div class="noResult" id="noResultRot" style="display: none">
    <img src="Image/noResult.svg">
</div>

<section class="tools fontcorpo">

    <div class="search">
        <img src="Image/search.svg">
        <input type="text" id="searchInputRot" class="search_bar" placeholder="Pesquisar . . ."
            onkeyup="filterScripts()">
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
        <p class="pageTitle titlefont">Blocos de roteiro</p>
        <div class="Create">
            <p id="CreateRotText" class="CreateRotText fontcorpo">Criar Bloco de roteiro</p>
            <img id="CreateRotBtn" class="CreateImg" src="image/Add.svg" onmouseover="CreateRotBtnAnimation()"
                onmouseout="CreateRotBtnAnimationOff()" onclick="OpenModalCreateRot()">
        </div>
    </div>

    <div class="pageLine back-black"></div>

</div>

<div class="content">
    <div class="section">
        <div class="list">

            <!--Adicionar os projetos-->
            <div class="rot_cont" id="your_blocks">

                @if ($rot)
                    @forelse ($rot as $roteiro)
                        <div class="rot fontcorpo" id="rot_{{ $roteiro->id }}">
                            <div class="rotMargin">
                                <div class="rot_img fontcorpo" onclick="OpenScript({{ $roteiro->id }})"
                                    style="justify-content: center">
                                    <div id="textArea_{{ $roteiro->id }}" contenteditable="true"
                                        style="zoom: 0.4; margin: 3vh 0 0 0; width: 90%">

                                        @php
                                            $cena = DB::table('tb_cena_roteiro')
                                                ->where('id_roteiro', $roteiro->id)
                                                ->first();
                                        @endphp

                                        {!! $cena?->ds_texto !!}

                                    </div>
                                </div>
                                <div class="rotOpt">
                                    <div class="rot_status"></div>
                                    <div class="rot_info">
                                        <h3 class="rotName fontcorpo" onclick="OpenScript({{ $roteiro->id }})">
                                            {{ $roteiro->nm_roteiro }}</h3>
                                        <div class="rot_stt">
                                            <p></p>
                                            <img class="rot_info_btn_{{ $roteiro->id }}"
                                                onclick="openModalRotProps('modal_rot_open_{{ $roteiro->id }}', 'rot_info_btn_{{ $roteiro->id }}')"
                                                src="image/Triple_dot2.svg">

                                            <div class="modal_proj_open_out">
                                                <div class="modal_rot_open" id="modal_rot_open_{{ $roteiro->id }}">
                                                    <p class="modal_proj_open_title">{{ $roteiro->nm_roteiro }}</p>
                                                    <p onclick="OpenScript({{ $roteiro->id }})">Abrir</p>
                                                    <p
                                                        onclick="OpenModalEditRot('{{ $roteiro->id }}', '{{ $roteiro->nm_roteiro }}', '{{ $roteiro->ds_roteiro }}')">
                                                        Editar</p>
                                                    <p
                                                        onclick="excluirRotModal({{ $roteiro->id }}, 'rot_{{ $roteiro->id }}', 'rotIni_{{ $roteiro->id }}')">
                                                        Excluir</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rot_info1">
                                        <p>
                                            <span class="font_medium">Última modificação: </span>
                                            {{ $roteiro->updated_at ? $roteiro->updated_at->locale('pt_BR')->diffForHumans() : 'Nunca modificado' }}
                                        </p>
                                        <p><span class="font_medium">Proprietário:
                                            </span>{{ $roteiro->usuario->nm_usuario ?? 'Desconhecido' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @empty

                        <div class="emptyItems fontcorpo">
                            <p>Não há nenhum bloco de roteiro atribuído a você.</p>
                        </div>
                    @endforelse
                @endif

            </div>
        </div>
    </div>

    <div class="stts_cont fontcorpo">

        <p></p>
        <p class="font_medium">Quantidade de blocos de roteiro:
            <span class="bolder" style="opacity: 0.8"
                id="rot_qtd">{{ str_pad(Auth::user()->qt_roteiro, 2, '0', STR_PAD_LEFT) }}</span>
        </p>

    </div>

</div>
</div>
