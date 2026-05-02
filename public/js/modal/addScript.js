//---------------------------------------------------Add script modal---------------------------------------------------------

var MdAddBlock = document.getElementById("ModalAddBlock");
var MdAddBlockFade = document.getElementById("ModalAddBlockFade");

function openModalAddBlock() {
    MdAddBlock.classList.add('ModalAddBlockActive');
    MdAddBlockFade.classList.add('ModalAddBlockFadeActive');
}

function CloseModalAddBlock() {
    MdAddBlock.classList.remove('ModalAddBlockActive');
    MdAddBlockFade.classList.remove('ModalAddBlockFadeActive');

    document.querySelectorAll('.rotAdd').forEach(element => {
        element.style.backgroundColor = "#fdfdfd";
    });
    AddRotSelected = 0;
}

var AddRotSelected = 0;

// Search script
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('.importRotSearch input[type="search"]');
    const rotItems = document.querySelectorAll('.rotAdd');

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase();

        rotItems.forEach(rot => {
            const nome = rot.querySelector('.rotAddName').textContent.toLowerCase();
            if (nome.includes(query)) {
                rot.style.display = 'flex';
            } else {
                rot.style.display = 'none';
            }
        });
    });
});

// Select script
function selectRot(id) {

    document.querySelectorAll('.rotAdd').forEach(element => {
        element.style.backgroundColor = "#fdfdfd";
    });

    document.getElementById(id).style.backgroundColor = "rgb(250, 250, 250)";
    AddRotSelected = id;
}

// Adds script
function AddBlock() {

    if(AddRotSelected == 0) {
        if (document.getElementsByClassName('notification').length === 0) {
            createNotfy("info", "Selecione um roteiro para importar");
        }
        return;
    }
    else {

        fetch(`/importarRoteiro/${document.getElementById("proj_id").textContent.trim()}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ id_roteiro: AddRotSelected.replace(/\D/g, '') })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (document.getElementsByClassName('notification').length === 0) {
                    createNotfy("success", "Roteiro importado com sucesso.");
                }
                CloseModalAddBlock();
                document.getElementById('addBlock').remove();
        
                const addedBlock = document.createElement('div');
                addedBlock.className = 'addedBlock';
                addedBlock.id = 'addedBlock';
        
                const infoDiv = document.createElement('div');
                infoDiv.className = 'addedBlockInfo';
                infoDiv.onclick = () => openRoteiro(data.id);
        
                const pNome = document.createElement('p');
                pNome.className = 'addedBlockInfo_Name';
                pNome.textContent = data.nome;
        
                const pUsuario = document.createElement('p');
                pUsuario.className = 'addedBlockInfo_User';
                pUsuario.textContent = `Proprietário: ${data.usuario}`;
        
                infoDiv.appendChild(pNome);
                infoDiv.appendChild(pUsuario);
        
                const closeImg = document.createElement('img');
                closeImg.src = 'Image/CloseModal.svg';
                closeImg.onclick = removeRot;
        
                addedBlock.appendChild(infoDiv);
                addedBlock.appendChild(closeImg);
        
                document.getElementById('blocks').appendChild(addedBlock);

                recarregarBlocoCenas();
        
            } else {
                if (document.getElementsByClassName('notification').length === 0) {
                    createNotfy("error", data.error || "Ocorreu um erro ao importar o roteiro.");
                }
            }
        })        
        .catch(error => {
            if (document.getElementsByClassName('notification').length === 0) {
                createNotfy("error", "Ocorreu um erro ao fazer a requisição.");
            }
            console.error("Erro na requisição:", error);
        });        

    }

}

// Removes script
function removeRot() {

    fetch(`/removerRoteiro/${document.getElementById("proj_id").textContent.trim()}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (document.getElementsByClassName('notification').length === 0) {
                createNotfy("success", "Roteiro removido com sucesso.");
            }
            const addBlock = document.createElement('div');
            addBlock.className = 'addBlock';
            addBlock.id = 'addBlock';
            addBlock.onclick = openModalAddBlock;

            const p = document.createElement('p');
            p.innerHTML = '<span class="add">+</span> Adicionar';

            addBlock.appendChild(p);

            document.getElementById('blocks').appendChild(addBlock);

            document.querySelectorAll('.showInfoCena').forEach(element => {
                element.remove();
            });
            recarregarBlocoCenas();

            CloseModalAddBlock();
            document.getElementById('addedBlock').remove();
        }
    })
    .catch(error => {
        if (document.getElementsByClassName('notification').length === 0) {
            createNotfy("error", "Ocorreu um erro ao fazer a requisição.");
        }
        console.error("Erro na requisição:", error);
    });

}

// Opens script
function openRoteiro(id) {
    sessionStorage.setItem('id_roteiro', id);
    
    // Sends the variable to the server by POST
    fetch('/guardarRoteiro', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ id_roteiro: id })
    })
    .then(response => response.json())
    .then(data => {

        if (data.success) {
            window.location.href = "/abrirRoteiro";
        }
    });
}