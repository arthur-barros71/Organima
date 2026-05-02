<div class="ModalCreateRotFade" id="ModalCreateRotFade">
    <div class="ModalCreateRot background" id="ModalCreateRot">
        <div class="ModalCreateRotHeader">
            <p class="titlefont">Criar Bloco de roteiro</p>
            <img src="image/CloseModal.svg" onclick="CloseModalCreateRot()">
        </div>
        <form id="FormCreateRot" class="FormCreateRot" action="/criarRoteiro" method="POST">
            @csrf
            <div class="modal_Rot_Name">
                <p class="modal_Rot_text font fontcorpo">Nome do bloco de roteiro</p>
                <input type="text" class="font" id="Rot_name" name="nm_roteiro">
            </div>
            <div class="modal_Rot_desc">
                <p class="modal_Rot_text font fontcorpo">Descrição do bloco de roteiro</p>
                <textarea class="font" id="desc_Rot" name="ds_roteiro"></textarea>
            </div>
            <!-- Botão de criação -->
            <button type="button" class="modal_Rot_btn button-normal fontcorpo" id="createRot_btn"
                onclick="CreateRot()">Criar bloco de roteiro</button>

        </form>
    </div>
</div>
