<div class="ModalCreateProjectFade" id="ModalEditProjectFade">
    <div class="ModalCreateProject background" id="ModalEditProject">
        <div class="ModalCreateProjectHeader">
            <p class="titlefont">Editar projeto</p>
            <img src="image/CloseModal.svg" onclick="CloseModalEditProj()">
        </div>
        <form id="FormEditProj" class="FormCreateProj" action="/editarProjeto/" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="modal_proj_img" id="modal_proj_img">
                    <img id="editProjImg" src="image/ProjDefaultImage.svg" alt="Imagem padrão do projeto">
                </div>
                <input type="file" id="proj_img_change" name="proj_img_import"
                    style="opacity: 0; position: absolute;" accept=".webp, .png, .jpg, .jpeg, .jfif"
                    oninput="setProjImgModal()">
                <div class="proj_campos fontcorpo">

                    <p class="modal_proj_text font fontcorpo">Nome do projeto</p>
                    <input type="text" class="font fontcorpo" id="pjt_name_edit" name="nm_projeto">

                    <p class="modal_proj_text font modal_proj_text_margin fontcorpo">Tipos de projeto</p>
                    <select class="font fontcorpo" name="id_tipo" id="id_tipo_edit">
                        <option value="1">Animação 2D ou 3D</option>
                        <option value="2">Animação Stop Motion</option>
                        <option value="3">Produção cinematográfica</option>
                    </select>

                </div>
            </div>
            <div class="modal_proj_desc">
                <p class="modal_proj_text font fontcorpo">Descrição do projeto</p>
                <!-- Atualizado para desc -->
                <textarea class="font fontcorpo" id="desc_edit" name="ds_projeto"></textarea>
            </div>
            <button id="editProjBtn" class="modal_proj_btn button-normal fontcorpo font_medium"
                onclick="EditProj()">Editar projeto</button>
        </form>
    </div>
</div>
