//-----------------------------------------------------Create project modal-----------------------------------------------------

var MdCreateProj = document.getElementById("ModalCreateProject");
var MdCreateProjFade = document.getElementById("ModalCreateProjectFade");

function OpenModalCreateProj() {
    MdCreateProj.classList.add('ModalCreateProjectActive');
    MdCreateProjFade.classList.add('ModalCreateProjectFadeActive');
}

function CloseModalCreateProj() {
    MdCreateProj.classList.remove('ModalCreateProjectActive');
    MdCreateProjFade.classList.remove('ModalCreateProjectFadeActive');
}

//-----------------------------------------------------Create project-----------------------------------------------------

var projForm = document.getElementById("FormCreateProj");
var pjt_name = document.getElementById("pjt_name");
var pjt_desc = document.getElementById("desc");

function CreateProj() {

    document.getElementById('createProjBtn').disabled = true;

    const name = pjt_name.value.trim();
    const desc = pjt_desc.value.trim();

    // Verifies if the name is valid before sending to the server
    if (name.length < 3 || name.length > 45) {
        if (document.getElementsByClassName('notification').length === 0) {
            createNotfy("error", "O nome do projeto deve ter entre 3 e 45 dígitos");
        }
        document.getElementById('createProjBtn').disabled = false;
        pjt_name.focus();
        return;
    }

    // Verifies if the description is valid before sending to the server
    if (desc.length < 3 || desc.length > 500) {
        if (document.getElementsByClassName('notification').length === 0) {
            createNotfy("error", "A descrição do projeto deve ter entre 3 e 500 dígitos");
        }
        document.getElementById('createProjBtn').disabled = false;
        pjt_desc.focus();
        return;
    }

    // Verifies if the user already has a project with the same name. If not, creates it
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
            projForm.submit();
        } else {
            if (document.getElementsByClassName('notification').length === 0) {
                createNotfy("error", "Você já possui um projeto com esse nome.");
            }
            document.getElementById('createProjBtn').disabled = false;
            pjt_name.focus();
        }
    })    
    .catch(error => {
        console.error('Erro de rede:', error);
        document.getElementById('createProjBtn').disabled = false;
    });
}