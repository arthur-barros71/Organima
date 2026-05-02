<div class="ModalProfileFade" id="ModalProfileCompleteFade">

    <div class="ModalProfileComplete back-light-black" id="ModalProfileComplete">

        <div class="ModalHeader">
            <p class="white-text titlefont">Editar perfil</p>
            <img src="image/CloseModalWhite.svg" onclick="CloseModalProfileComplete()">
        </div>

        <form id="NameForm" class="ModalProfilePrfl" action="/alterarNome" method="POST">

            <div class="Perfil">
                <div class="modal_prfl_cmplt_img" id="setImgProfile">
                    <img src="{{ $imagePath ? url('storage/' . $imagePath) : asset('image/ProfileImg.svg') }}"
                        id="modalProfileImg">
                </div>
                <input type="file" id="importProfileImg" style="display: none" accept="image/*"
                    onchange="setProfileImg()">
                <input type="text" id="update_name" name="update_name" class="white-text"
                    value="{{ Auth::user()->nm_usuario }}" readonly />
            </div>

            <div class="modal_prfl_cmplt_editName">
                <img src="image/Cancel.png" id="name_Img_cancel" onclick="alterNameCancel()">
                <img src="image/Edit.svg" id="name_Img" onclick="alterName()">
            </div>

        </form>

        <div class="modal_prfl_cmplt_section">
            <div class="modal_prfl_cmplt_item">
                <p class="white-text fontcorpo">Email:</p>
                <p class="white-text fontcorpo">{{ Auth::user()->ds_email }}</p>
            </div>
        </div>

        <form id="recpEmailForm" class="modal_prfl_cmplt_section" action="/alterarEmailRecuperacao" method="POST">
            @csrf

            <div class="modal_prfl_cmplt_item">
                <p class="white-text fontcorpo">Email de recuperação:</p>
                <input type="email" id="recpEmail" max="15" name="ds_email_recuperacao" class="white-text"
                    value="{{ Auth::user()->ds_email_recuperacao ?? 'adicionar email' }}" readonly />
            </div>

            <div class="modal_prfl_cmplt_editItem">
                <img src="image/Cancel.png" style="opacity: 0; width: 23%; margin-right: 17%" id="recpEmail_Img_cancel"
                    onclick="alterRecpEmailCancel()">
                <img src="image/Edit.svg" id="recpEmail_Img" onclick="alterRecpEmail('{{ Auth::user()->ds_email }}')">
            </div>

        </form>

        <div class="modal_prfl_cmplt_section">
            <p class="fontcorpo esqueciBtn">Esqueci a senha</p>
        </div>


        <div class="modal_prfl_cmplt_section" style="border: none; height: 7vh">
            <p class="fontcorpo desativarBtn" onclick="desativarConta()">Desativar conta</p>
        </div>

    </div>

</div>
