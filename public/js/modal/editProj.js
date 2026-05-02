//---------------------------------------------Edit project modal------------------------------------------------------

var MdEditProj = document.getElementById("ModalEditProject");
var MdEditProjFade = document.getElementById("ModalEditProjectFade");

var projForm = document.getElementById("FormEditProj");
var pjt_name = document.getElementById("pjt_name");
var pjt_desc = document.getElementById("desc");
var status_id = document.getElementById("status_id");

var proj_nameBD;
var pjt_descBD;
var status_idBD;

function OpenModalEditProj() {
    MdEditProj.classList.add('ModalEditProjectActive');
    MdEditProjFade.classList.add('ModalEditProjectFadeActive');

    proj_nameBD = pjt_name.value;
    pjt_descBD = pjt_desc.textContent;
    status_idBD = status_id.value;

    var ModalPorjOpt = document.getElementById("modal_opts");
    ModalPorjOpt.style.height = "0";
    ModalPorjOpt.style.padding = "0 2% 0 1%";
}

function CloseModalEditProj() {
    MdEditProj.classList.remove('ModalEditProjectActive');
    MdEditProjFade.classList.remove('ModalEditProjectFadeActive');

    pjt_name.value = proj_nameBD;
    pjt_desc.value = pjt_descBD;
    status_id.value = status_idBD;
}

function EditProj() {
    const name = pjt_name.value.trim();
    const desc = pjt_desc.value.trim();

    if (name.length >= 3 && name.length <= 45) {
        if (desc.length >= 3 && desc.length <= 500) {
            projForm.submit();
        } else {
            if (document.getElementsByClassName('notification').length === 0) {
                createNotfy("error", "A descrição do projeto deve ter entre 3 e 500 dígitos");
            }
            pjt_desc.focus();
        }
    } else {
        if (document.getElementsByClassName('notification').length === 0) {
            createNotfy("error", "O nome do projeto deve ter entre 3 e 45 dígitos");
        }
        pjt_name.focus();
    }
}

//---------------------------------------Project image-----------------------------------------

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