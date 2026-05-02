//-----------------------------------------------------Create script modal-----------------------------------------------------
var MdCreateRot = document.getElementById("ModalCreateRot");
var MdCreateRotFade = document.getElementById("ModalCreateRotFade");

function OpenModalCreateRot() {
    MdCreateRot.classList.add('ModalCreateRotActive');
    MdCreateRotFade.classList.add('ModalCreateRotFadeActive');
}

function CloseModalCreateRot() {
    MdCreateRot.classList.remove('ModalCreateRotActive');
    MdCreateRotFade.classList.remove('ModalCreateRotFadeActive');
}

//-----------------------------------------------------Create script-----------------------------------------------------
var RotForm = document.getElementById("FormCreateRot");
var Rot_name = document.getElementById("Rot_name");
var Rot_desc = document.getElementById("desc_Rot");

function CreateRot() {

    document.getElementById('createRot_btn').disabled = true;

    if(Rot_name.value.trim() == "" || Rot_desc.value.trim() == "") {
        if (document.getElementsByClassName('notification').length === 0) {
            createNotfy("error", "Preencha todos os campos.");
        }
        document.getElementById('createRot_btn').disabled = false;
        return;
    }

    if (Rot_name.value.length >= 3 && Rot_name.value.length <= 45) {

        if (Rot_desc.value.length >= 3 && Rot_desc.value.length <= 500) {
            
            fetch('/consultarRoteiro', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ nm_roteiro: Rot_name.value })
            })
            .then(response => response.json())
            .then(data => {
            
                if (!data.exists) {
                    RotForm.submit();
                } else {
                    if (document.getElementsByClassName('notification').length === 0) {
                        createNotfy("error", "Você já possui um roteiro com esse nome.");
                    }
                    document.getElementById('createRot_btn').disabled = false;
                    Rot_name.value = null;
                    Rot_name.focus();
                }
            })
            .catch(error => {
                console.error('Erro de rede:', error);
                document.getElementById('createRot_btn').disabled = false;
            });
            
        } else {
            if (document.getElementsByClassName('notification').length === 0) {
                createNotfy("error", "A descrição do bloco de roteiro deve ter entre 3 e 500 dígitos.");
            }
            Rot_desc.focus();
            document.getElementById('createRot_btn').disabled = false;
        }
    } else {
        if (document.getElementsByClassName('notification').length === 0) {
            createNotfy("error", "O nome do bloco de roteiro deve ter entre 3 e 45 dígitos.");
        }
        Rot_name.focus();
        document.getElementById('createRot_btn').disabled = false;
    }
}