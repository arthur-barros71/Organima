//---------------------------------------------Share project--------------------------------------------------------

function openModalShareProj() {
    document.getElementById('ModalShareProjectFade').classList.add('ModalShareProjectFadeActive');
    document.getElementById('ModalShareProject').classList.add('ModalShareProjectActive');
}

function closeModalShareProj() {
    document.getElementById('ModalShareProjectFade').classList.remove('ModalShareProjectFadeActive');
    document.getElementById('ModalShareProject').classList.remove('ModalShareProjectActive');
}

function ShareProj() {

    var shareProjBtn = document.getElementById('shareProjBtn');
    shareProjBtn.disabled = true;

    if(document.getElementById('ds_email').value.trim() == "") {
        if (document.getElementsByClassName('notification').length === 0) {
            createNotfy("error", "Preencha todos os campos.");
        }
        document.getElementById('ds_email').focus();
        shareProjBtn.disabled = false;
        return;
    }

    const email = document.getElementById('ds_email').value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(email)) {
        if (document.getElementsByClassName('notification').length === 0) {
            createNotfy("error", "Digite um e-mail válido.");
        }
        document.getElementById('ds_email').focus();
        shareProjBtn.disabled = false;
        return;
    }

    fetch(`/compartilharProjeto/${document.getElementById("proj_id").textContent.trim()}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ ds_email: document.getElementById('ds_email').value, id_cargo: document.getElementById('id_cargo').value })
    })    
    .then(response => response.json())
    .then(data => {

        if (data.success) {
            if (document.getElementsByClassName('notification').length === 0) {
                createNotfy("success", "Projeto compartilhado com sucesso.");
            }
    
            shareProjBtn.disabled = false;
    
            const container = document.getElementById("colab");
    
            const extensoes = ['.webp', '.png', '.jpg', '.jpeg', '.jfif'];
            const imagemPadrao = 'image/ProfileImg.svg';
    
            // Function to find the first existing image
            const encontrarImagem = async (id) => {
                for (const ext of extensoes) {
                    const url = `storage/user_${id}/profile${ext}`;
                    try {
                        const response = await fetch(url, { method: 'HEAD' });
                        if (response.ok) {
                            return url;
                        }
                    } catch (e) {
                        // Ignores
                    }
                }
                return imagemPadrao;
            };
    
            // Searchs and adds the element
            encontrarImagem(data.id).then(imagem => {
                const userDiv = document.createElement("div");
                userDiv.classList.add("colabUser");
    
                userDiv.innerHTML = `
                    <div class="colabInfo">
                        <div class="colabImg">
                            <img src="${imagem}" alt="Foto de ${data.nome}">
                        </div>
                        <div class="colabTexts">
                            <p>${data.nome}</p>
                            <p style="font-size: 13px">${data.email}</p>
                        </div>
                    </div>
                    <select>
                        <option value="1" ${data.cargo === 1 ? 'selected' : ''}>Leitor</option>
                        <option value="2" ${data.cargo === 2 ? 'selected' : ''}>Comentador</option>
                        <option value="3" ${data.cargo === 3 ? 'selected' : ''}>Editor</option>
                    </select>
                `;
    
                container.appendChild(userDiv);
            })
        }
        else {
            if(data.message == "Usuário já é contribuidor deste projeto.") {
                if (document.getElementsByClassName('notification').length === 0) {
                    createNotfy("error", "O usuário indicado já está no projeto.");
                }
                shareProjBtn.disabled = false;
                return;
            }
            else if(data.message == "Usuário não encontrado.") {
                if (document.getElementsByClassName('notification').length === 0) {
                    createNotfy("error", "O usuário não foi encontado.");
                }
                shareProjBtn.disabled = false;
                document.getElementById('ds_email').focus();
                return;
            }
            else {
                if (document.getElementsByClassName('notification').length === 0) {
                    createNotfy("error", "Erro no compartilhamento do projeto.");
                }
                shareProjBtn.disabled = false;
                return;
            }
        }
    
    })
    
    .catch(error => {
        console.error('Erro de rede:', error);
        shareProjBtn.disabled = false;
    });
}

function salvarNovoCargo(id) {
    const projetoId = document.getElementById("proj_id").textContent.trim();
    const cargo = document.getElementById('cargo_' + id).value;

    fetch(`/salvarNovoCargo/${projetoId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            id_usuario: id,
            id_cargo: cargo
        })
    })
    .then(response => response.json().then(data => ({ ok: response.ok, data })))
    .then(({ ok, data }) => {
        if (ok && data.success) {
            
        } else {
            if (document.getElementsByClassName('notification').length === 0) {
                createNotfy("error", data.message || "Erro ao alterar a permissão.");
            }
        }
    })
    .catch(error => {
        console.error('Erro de rede:', error);
        createNotfy("error", "Erro de rede ao alterar a permissão.");
    });
}