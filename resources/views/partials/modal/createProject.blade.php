<div class="ModalCreateProjectFade" id="ModalCreateProjectFade">
    <div class="ModalCreateProject background" id="ModalCreateProject">
        <div class="ModalCreateProjectHeader">
            <p class="titlefont">Criar projeto</p>
            <img src="image/CloseModal.svg" onclick="CloseModalCreateProj()">
        </div>
        <form id="FormCreateProj" class="FormCreateProj" action="/criarProjeto" method="POST"
            enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="modal_proj_img" id="modal_proj_img">
                    <img src="image/ProjDefaultImage.svg" alt="Imagem padrão do projeto">
                </div>
                <input type="file" id="proj_img_import" name="proj_img_import"
                    style="opacity: 0; position: absolute;" accept=".webp, .png, .jpg, .jpeg, .jfif"
                    oninput="setProjImgModal()">
                <div class="proj_campos fontcorpo">

                    <p class="modal_proj_text font fontcorpo">Nome do projeto</p>
                    <input type="text" class="font fontcorpo" id="pjt_name" name="nm_projeto">

                    <p class="modal_proj_text font modal_proj_text_margin fontcorpo">Tipos de projeto</p>
                    <select class="font fontcorpo" name="id_tipo" id="id_tipo">
                        <option value="1">Animação 2D ou 3D</option>
                        <option value="2">Animação Stop Motion</option>
                        <option value="3">Produção cinematográfica</option>
                    </select>

                </div>
            </div>
            <div class="modal_proj_desc">
                <p class="modal_proj_text font fontcorpo">Descrição do projeto</p>
                <!-- Atualizado para desc -->
                <textarea class="font fontcorpo" id="desc" name="ds_projeto"></textarea>
            </div>
            <button id="createProjBtn" class="modal_proj_btn button-normal fontcorpo font_medium"
                onclick="CreateProj()">Criar projeto</button>
        </form>
    </div>
</div>
