<div id="ModalProfile" class="ModalProfile back-light-black">
    <div class="modal_prfl_info">

        <div class="modal_prfl_img">
            <img src="{{ $imagePath ? url('storage/' . $imagePath) : asset('image/ProfileImg.svg') }}"
                id="modal_prfl_img">
        </div>

        <p class="modal_prfl_name white-text fontcorpo" id="ModalName">{{ Auth::user()->nm_usuario }}</p>
        <p class="modal_prfl_email white-text fontcorpo">{{ Auth::user()->ds_email }}</p>

    </div>
    <div class="modal_prfl_section" onclick="OpenModalProfileComplete()">
        <img src="image/Config.png">
        <p class="modal_prfl_secText white-text fontcorpo">Editar perfil</p>
    </div>
    <form class="modal_prfl_section" id="logoutForm" action="/sair" method="POST">
        @csrf
        <button href="" onclick="logout()">
            <img src="image/LogOut.png">
            <p class="modal_prfl_secText white-text fontcorpo">Sair</p>
        </button>
    </form>
</div>
