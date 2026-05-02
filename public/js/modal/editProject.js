//-----------------------------------------------------Edit project modal-----------------------------------------------------

var MdEditProj = document.getElementById("ModalEditProject");
var MdEditProjFade = document.getElementById("ModalEditProjectFade");

function OpenModalEditProj(id, nm, ds, tipo, img) {
    MdEditProj.classList.add('ModalCreateProjectActive');
    MdEditProjFade.classList.add('ModalCreateProjectFadeActive');

    document.getElementById('pjt_name_edit').value = nm;
    document.getElementById('id_tipo_edit').value = tipo;
    document.getElementById('desc_edit').value = ds;

    if(img) {
        document.getElementById('editProjImg').src = img;
    }
    else {
        document.getElementById('editProjImg').src = "Image/ProjDefaultImg.png";
    }

    document.getElementById('FormEditProj').action = "/editarProjeto/" + id;
    
}

function CloseModalEditProj() {
    MdEditProj.classList.remove('ModalCreateProjectActive');
    MdEditProjFade.classList.remove('ModalCreateProjectFadeActive');
}

// Function to edit project
function EditProj() {
    document.getElementById('editProjBtn').disabled = true;

    const name = document.getElementById('pjt_name_edit').value.trim();
    const desc = document.getElementById('desc_edit').value.trim();

    // Verifies if the name is valid before sending to the server
    if (name.length < 3 || name.length > 45) {
        if (document.getElementsByClassName('notification').length === 0) {
            createNotfy("error", "O nome do projeto deve ter entre 3 e 45 dígitos");
        }
        document.getElementById('editProjBtn').disabled = false;
        document.getElementById('pjt_name_edit').focus();
        return;
    }

    // Verifies if the description is valid before sending to the server
    if (desc.length < 3 || desc.length > 500) {
        if (document.getElementsByClassName('notification').length === 0) {
            createNotfy("error", "A descrição do projeto deve ter entre 3 e 500 dígitos");
        }
        document.getElementById('editProjBtn').disabled = false;
        document.getElementById('desc_edit').focus();
        return;
    }

    // Verifies if the user already has a project with the same name. If not, updates it
    fetch('/consultarProjeto', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ nm_projeto: name })
    })
    .then(response => response.json())
    .then(data => {
    
        if (!data.exists) {
            document.getElementById('FormEditProj').submit();
        } else {
            if (document.getElementsByClassName('notification').length === 0) {
                createNotfy("error", "Você já possui um projeto com esse nome.");
            }
            document.getElementById('editProjBtn').disabled = false;
            document.getElementById('pjt_name_edit').focus();
        }
    })    
    .catch(error => {
        console.error('Erro de rede:', error);
        document.getElementById('editProjBtn').disabled = false;
    });
}

//-----------------------------------------------------Project image-----------------------------------------------------

document.getElementById('modal_proj_img').addEventListener('click', () => {
    document.getElementById('proj_img_import').click();
});

function setProjImgModal() {
    const input = document.getElementById('proj_img_import');
    const imgElement = document.querySelector('#modal_proj_img img');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            imgElement.src = e.target.result;
        };

        reader.readAsDataURL(input.files[0]);
    }
}