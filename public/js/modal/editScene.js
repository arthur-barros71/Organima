//----------------------------------------------------------Edit scene----------------------------------------------------------

var id_cena_edit;
var frameInicialEdit;
var frameFinalEdit;

function openModalEditCena(nome, desc, cor, frameIncial, frameFinal, id) {
    let ModalEditCenaFade = document.getElementById('ModalEditCenaFade');
    let ModalEditCena = document.getElementById('ModalEditCena');

    ModalEditCenaFade.style.backgroundColor = "rgba(0, 0, 0, 0.7)";
    ModalEditCenaFade.style.pointerEvents = "all";
    ModalEditCena.style.transform = "translateY(0)";
    ModalEditCena.style.opacity = 1;
    ModalEditCena.style.pointerEvents = "all";

    document.getElementById('nm_cena_edit').value = nome;
    document.getElementById('ds_cor_edit').value = cor;
    document.getElementById('ds_cena_edit').textContent = desc;
    document.getElementById('nr_frame_inicial_edit').value = frameIncial;
    document.getElementById('nr_frame_final_edit').value = frameFinal;

    // Sliders
    const sliderOneEdit = document.getElementById("nr_frame_inicial_edit");
    const sliderTwoEdit = document.getElementById("nr_frame_final_edit");
    sliderOneEdit.max = quantidade;
    sliderTwoEdit.max = quantidade;
    slideOneEdit();
    slideTwoEdit();

    id_cena_edit = "cenaTime_" + id;
    frameInicialEdit = frameIncial;
    frameFinalEdit = frameFinal;

    document.getElementById(id_cena_edit).style.opacity = 0.7;

    setTimeout(() => {
        setFrameAtual(parseInt(frameFinal));
        UpdateFrame();
    }, 10);
    
}

function CloseModalEditCena() {
    let ModalEditCenaFade = document.getElementById('ModalEditCenaFade');
    let ModalEditCena = document.getElementById('ModalEditCena');

    ModalEditCenaFade.style.backgroundColor = "rgba(0, 0, 0, 0)";
    ModalEditCenaFade.style.pointerEvents = "none";
    ModalEditCena.style.transform = "translateY(30vh)";
    ModalEditCena.style.opacity = 0;
    ModalEditCena.style.pointerEvents = "none";

    setTimeout(() => {
        document.getElementById('nm_cena_edit').value = null;
        document.getElementById('ds_cor_edit').value = "#000000";
        document.getElementById('ds_cena_edit').textContent = null;
        document.getElementById('nr_frame_inicial_edit').value = frameInicialEdit;
        document.getElementById('nr_frame_final_edit').value = frameFinalEdit;

        updateEditCena(id_cena_edit);
        document.getElementById(id_cena_edit).style.opacity = 1;

        id_cena_edit = null;
        frameInicialEdit = null;
        frameFinalEdit = null;
    }, 300);
    
}

function editCena() {
    const form = document.getElementById("editCenaForm");
    const url = form.action;
    const formData = new FormData(form);
    const onlyNumbers = id_cena_edit.toString().replace(/\D/g, '');
    formData.append("id_cena_projeto", onlyNumbers);

    const nm_cena = document.getElementById("nm_cena_edit");
    const frameInicial = document.getElementById("nr_frame_inicial_edit");
    const frameFinal = document.getElementById("nr_frame_final_edit");
    const ds_cena = document.getElementById("ds_cena_edit");

    if (nm_cena.value?.trim() && ds_cena.value?.trim() && frameInicial.value?.trim() && frameFinal.value?.trim()) {

        if (nm_cena.value.length < 3 || nm_cena.value.length > 45) {
            showNotification("info", "Scene name must be between 3 and 45 characters.");
            return;
        }

        if (ds_cena.value.length < 3 || ds_cena.value.length > 250) {
            showNotification("info", "Scene description must be between 3 and 250 characters.");
            return;
        }

        if (parseInt(frameInicial.value) >= parseInt(frameFinal.value)) {
            showNotification("info", "The initial frame cannot be equal to or greater than the final frame.");
            return;
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: formData
        })
        .then(async response => {
            if (!response.ok) {
                const data = await response.json();

                if (data.errors) {
                    showNotification("error", "Error editing scene.");
                } else if (data.message) {
                    showNotification("error", "There is already a scene in the selected frame range.");
                }
            } else {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification("error", "Unexpected error while editing the scene.");
        });

    } else {
        showNotification("info", "Please fill in all fields.");
    }
}