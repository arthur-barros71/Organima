//-----------------------------------------------------Edit script modal-----------------------------------------------------

var MdEditRot = document.getElementById("ModalEditRot");
var MdEditRotFade = document.getElementById("ModalEditRotFade");

function OpenModalEditRot(id, nm, ds) {
    MdEditRot.classList.add('ModalCreateRotActive');
    MdEditRotFade.classList.add('ModalCreateRotFadeActive');

    document.getElementById('Rot_name_edit').value = nm;
    document.getElementById('desc_Rot_edit').value = ds;
    document.getElementById('FormEditRot').action = "/editarRoteiro/" + id;
}

function CloseModalEditRot() {
    MdEditRot.classList.remove('ModalCreateRotActive');
    MdEditRotFade.classList.remove('ModalCreateRotFadeActive');
}

//-----------------------------------------------------Edit script-----------------------------------------------------

var RotFormEdit = document.getElementById("FormEditRot");
var Rot_nameEdit = document.getElementById("Rot_name_edit");
var Rot_descEdit = document.getElementById("desc_Rot_edit");

function EditRot() {

    document.getElementById('editRot_btn').disabled = true;

    if(Rot_nameEdit.value.trim() == "" || Rot_descEdit.value.trim() == "") {
        if (document.getElementsByClassName('notification').length === 0) {
            createNotfy("error", "Preencha todos os campos.");
        }
        document.getElementById('editRot_btn').disabled = false;
        return;
    }

    if (Rot_nameEdit.value.length >= 3 && Rot_nameEdit.value.length <= 45) {

        if (Rot_descEdit.value.length >= 3 && Rot_descEdit.value.length <= 500) {
            
            fetch('/consultarRoteiro', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ nm_roteiro: Rot_nameEdit.value })
            })
            .then(response => response.json())
            .then(data => {
            
                if (!data.exists) {
                    RotFormEdit.submit();
                } else {
                    if (document.getElementsByClassName('notification').length === 0) {
                        createNotfy("error", "Você já possui um roteiro com esse nome.");
                    }
                    document.getElementById('editRot_btn').disabled = false;
                    Rot_nameEdit.value = null;
                    Rot_nameEdit.focus();
                }
            })
            .catch(error => {
                console.error('Erro de rede:', error);
                document.getElementById('editRot_btn').disabled = false;
            });
            
        } else {
            if (document.getElementsByClassName('notification').length === 0) {
                createNotfy("error", "A descrição do bloco de roteiro deve ter entre 3 e 500 dígitos.");
            }
            Rot_descEdit.focus();
            document.getElementById('editRot_btn').disabled = false;
        }
    } else {
        if (document.getElementsByClassName('notification').length === 0) {
            createNotfy("error", "O nome do bloco de roteiro deve ter entre 3 e 45 dígitos.");
        }
        Rot_nameEdit.focus();
        document.getElementById('editRot_btn').disabled = false;
    }
}