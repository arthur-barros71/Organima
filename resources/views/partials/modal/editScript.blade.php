<div class="ModalCreateRotFade" id="ModalEditRotFade">
    <div class="ModalCreateRot background" id="ModalEditRot">
        <div class="ModalCreateRotHeader">
            <p class="titlefont">Editar Bloco de roteiro</p>
            <img src="image/CloseModal.svg" onclick="CloseModalEditRot()">
        </div>
        <form id="FormEditRot" class="FormCreateRot" action="/editarRoteiro" method="POST">
            @csrf
            <div class="modal_Rot_Name">
                <p class="modal_Rot_text font fontcorpo">Nome do bloco de roteiro</p>
                <input type="text" class="font" id="Rot_name_edit" name="nm_roteiro">
            </div>
            <div class="modal_Rot_desc">
                <p class="modal_Rot_text font fontcorpo">Descrição do bloco de roteiro</p>
                <textarea class="font" id="desc_Rot_edit" name="ds_roteiro"></textarea>
            </div>
            <!-- Botão de criação -->
            <button type="button" class="modal_Rot_btn button-normal fontcorpo" id="editRot_btn"
                onclick="EditRot()">Editar bloco de roteiro</button>

        </form>
    </div>
</div>
