//---------------------------------------------------Preventing the use of Enter----------------------------------------------------

document.addEventListener('DOMContentLoaded', function() {
    // Select all forms
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        // Add the keydown event to the form
        form.addEventListener('keydown', function(event) {
            // Check if the pressed key is Enter (code 13)
            if (event.key === 'Enter') {
                // Prevent the default behavior (form submission)
                event.preventDefault();
            }
        });
    });
});

//-----------------------------------------------------------Scene creation-----------------------------------------------------------

// Open scene creation modal
const createCenaModal = document.getElementById("ModalCreateCena");
const createCenaModalFade = document.getElementById("ModalCreateCenaFade");

// Global variables
let tempCenaDiv   = null;
let onInputHandler= null;
let onColorHandler= null;

function OpenCreateCenaModal() {
  // Opening animations
  createCenaModalFade.style.backgroundColor = "rgba(0,0,0,0.7)";
  createCenaModalFade.style.pointerEvents = "all";
  createCenaModal.style.transform = "translateY(0)";
  createCenaModal.style.opacity = 1;
  createCenaModal.style.pointerEvents = "all";

  document.getElementById('frameInicial').value = AtualFrame;
  document.getElementById('frameFinal').value = AtualFrame + 10;

  document.getElementById('nm_cena').value = null;
  document.getElementById('ds_cena').value = null;
}

function CloseModalCreateCena() {
  // Closing animations
  createCenaModalFade.style.backgroundColor = "rgba(0,0,0,0)";
  createCenaModalFade.style.pointerEvents = "none";
  createCenaModal.style.transform = "translateY(20vh)";
  createCenaModal.style.opacity = 0;
  createCenaModal.style.pointerEvents = "none";
}

const colorInput = document.getElementById('ds_cor');

// Set initial color on page load
colorInput.style.backgroundColor = colorInput.value;

// Update background color whenever the value changes
colorInput.addEventListener('input', function () {
    this.style.backgroundColor = this.value;
});

function criarCena() {
    const form = document.getElementById("createCenaForm");
    const url = form.action;
    const formData = new FormData(form);

    const nm_cena = document.getElementById("nm_cena");
    const ds_cena = document.getElementById("ds_cena");

    if (nm_cena.value?.trim() && ds_cena.value?.trim()) {

        if (nm_cena.value.length < 3 || nm_cena.value.length > 20) {
            showNotification("info", "Scene name must be between 3 and 20 characters.");
            return;
        }

        if (ds_cena.value.length < 3 || ds_cena.value.length > 500) {
            showNotification("info", "Scene description must be between 3 and 500 characters.");
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
            const data = await response.json();

            if (!response.ok) {
                if (data.errors) {
                    showNotification("error", "Error creating scene.");
                }
            } else {
                CloseModalCreateCena();
                updateTimelineAndSummary(nm_cena.value);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification("error", "Unexpected error while creating the scene.");
        });

    } else {
        showNotification("info", "Please fill in all fields.");
    }
}

// Function to display notifications
function showNotification(type, message) {
    if (document.getElementsByClassName('notification').length === 0) {
        createNotfy(type, message);
    }
}

// Function to update the timeline and side summary
function updateTimelineAndSummary(nm_cena) {
    fetch('/ultimacena')
        .then(response => response.json())
        .then(data => {
            if (!data.id || !data.nm_cor || !data.nm_cena_roteiro) {
                console.error("Incomplete response when creating scene:", data);
                return;
            }

            const cenaId = data.id;
            const cenaNome = data.nm_cena_roteiro;
            const cenaCor = data.nm_cor;

            // === 1. Create item in the summary ===
            const newCena = document.createElement('div');
            newCena.classList.add('cena');
            newCena.setAttribute('data-id', cenaId);
            newCena.setAttribute('onclick', `setCena(${cenaId}, '${cenaNome.replace(/'/g, "\\'")}')`);

            const header = document.createElement('div');
            header.style.display = "flex";
            header.style.justifyContent = "space-between";

            const title = document.createElement('p');
            title.classList.add('cena_title');
            title.textContent = cenaNome;

            const icon = document.createElement('img');
            icon.src = "Image/EditBlack.svg";

            header.appendChild(title);
            header.appendChild(icon);

            const barra = document.createElement('div');
            barra.classList.add('cenaColor');
            barra.style.backgroundColor = cenaCor;
            barra.style.width = "10%";

            newCena.appendChild(header);
            newCena.appendChild(barra);

            const summaryDiv = document.getElementById('Summary');
            summaryDiv.insertBefore(newCena, document.getElementById('add_cena'));

            // === 2. Create the corresponding new .textArea ===
            const novaTextArea = document.createElement('div');
            novaTextArea.classList.add('textArea');
            novaTextArea.setAttribute('contenteditable', 'true');
            novaTextArea.setAttribute('data-idCena', cenaId);
            novaTextArea.style.display = "none"; // starts hidden
            novaTextArea.innerHTML = "<p></p>"; // initial empty content

            const scrollTextArea = document.getElementById('scrollTextArea');
            scrollTextArea.appendChild(novaTextArea);

            // === 3. Update style (scroll in summary) ===
            atualizarEstiloCenas();
        })
        .catch(error => {
            console.error("Error fetching new scene:", error);
        });
}


function atualizarEstiloCenas() {
    const summary = document.getElementById('Summary');
    if (!summary) return;

    const cenas = summary.querySelectorAll('.cena');

    if (cenas.length > 5) {
        cenas.forEach(cena => cena.classList.add('cena_scroll'));
    } else {
        cenas.forEach(cena => cena.classList.remove('cena_scroll'));
    }
}

document.addEventListener('DOMContentLoaded', function () {
    atualizarEstiloCenas();
});

//-----------------------------------------------------------Scene editing-----------------------------------------------------------

// Open scene editing modal
const editCenaModal = document.getElementById("ModalEditCena");
const editCenaModalFade = document.getElementById("ModalEditCenaFade");

var idCenaEdit;

function OpenEditCenaModal(id, name, ds, cor) {
  // Opening animations
  editCenaModalFade.style.backgroundColor = "rgba(0,0,0,0.7)";
  editCenaModalFade.style.pointerEvents = "all";
  editCenaModal.style.transform = "translateY(0)";
  editCenaModal.style.opacity = 1;
  editCenaModal.style.pointerEvents = "all";

  document.getElementById('nm_cena_edit').value = name;
  document.getElementById('ds_cena_edit').value = ds;
  document.getElementById('ds_cor_edit').value = cor;

  idCenaEdit = id;

  // Update the color input background when opening the modal
  const colorInput = document.getElementById('ds_cor_edit');
  if (colorInput) {
    colorInput.style.backgroundColor = colorInput.value;

    // Add listener only once (avoids duplicates if opened multiple times)
    if (!colorInput.dataset.listenerAdded) {
      colorInput.addEventListener('input', function () {
        this.style.backgroundColor = this.value;
      });
      colorInput.dataset.listenerAdded = true;
    }
  }
}

function CloseModalEditCena() {

    editCenaModalFade.style.backgroundColor = "rgba(0,0,0,0)";
    editCenaModalFade.style.pointerEvents = "none";
    editCenaModal.style.transform = "translateY(20vh)";
    editCenaModal.style.opacity = 0;
    editCenaModal.style.pointerEvents = "none";

    setTimeout(function() {
        document.getElementById('nm_cena_edit').value = null;
        document.getElementById('ds_cena_edit').value = null;
        document.getElementById('ds_cor_edit').value = "#000000";
    }, 300);

    idCenaEdit = null;

}

function editarCena() {

    if (!idCenaEdit) {
        return;
    }

    const form = document.getElementById("editCenaForm");
    const url = "/editarCenaRoteiro/" + idCenaEdit;
    const formData = new FormData(form);

    const nm_cena = document.getElementById("nm_cena_edit");
    const ds_cena = document.getElementById("ds_cena_edit");

    if (nm_cena.value?.trim() && ds_cena.value?.trim()) {

        if (nm_cena.value.length < 3 || nm_cena.value.length > 20) {
            showNotification("info", "Scene name must be between 3 and 20 characters.");
            return;
        }

        if (ds_cena.value.length < 3 || ds_cena.value.length > 500) {
            showNotification("info", "Scene description must be between 3 and 500 characters.");
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
            const data = await response.json();

            if (!response.ok) {
                if (data.errors) {
                    showNotification("error", "Error editing scene.");
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

function deleteCena() {

    const url = "/deletarCenaRoteiro/" + idCenaEdit;

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
    })
    .then(async response => {
        const data = await response.json();

        if (!response.ok) {
            if (data.errors) {
                showNotification("error", "Error editing scene.");
            }
        } else {

            location.reload();
            
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification("error", "Unexpected error while editing the scene.");
    });

}

//---------------------------------------Select scene---------------------------------------

function setCena(id, name) {
    document.getElementById('cenaAtual').textContent = "Current scene: " + name;    

    let cenas = document.querySelectorAll('.cena');
    let cenasText = document.querySelectorAll('.textArea');

    // Hide all texts
    cenasText.forEach(cena => cena.style.display = "none");

    // Shrink all scene bars to 10%
    cenas.forEach(cena => {
        let barra = cena.querySelector('.cenaColor');
        if (barra) barra.style.width = "10%";

        // If this is the selected scene, expand to 100%
        if (cena.getAttribute('data-id') == id) {
            if (barra) barra.style.width = "100%";
        }
    });

    // Show the text corresponding to the selected scene
    cenasText.forEach(function(cena) {
        if (cena.getAttribute('data-idCena') == id) {
            cena.style.display = "block";
            cena.focus();
            window.idCena = cena.getAttribute('data-idCena');

            // Count words and characters
            const text = cena.innerText || cena.textContent;
            const wordCount = text.trim().split(/\s+/).filter(word => word.length > 0).length;
            const charCount = text.replace(/\s/g, '').length;

            document.getElementById("qt_words_cena").textContent = wordCount;
            document.getElementById("qt_char_cena").textContent = charCount;
        }
    });

    // Send update to backend
    if (idCena != null) {
        const formData = new FormData();
        formData.append('id_cena_roteiro', idCena);

        fetch('/alterarCena', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: formData
        })
        .then(response => response.json());
    }
}

// Share animation
let shareVisible = false; // global flag

function ShareAppear() {
    if (shareVisible) return; // prevents repeated execution
    shareVisible = true;

    const ShareTextBtn = document.getElementById("ShareTextBtn");
    const addShare = document.getElementById("addShare");
    const users = document.querySelectorAll(".UserShare"); 

    ShareTextBtn.style.width = "40%";
    addShare.style.left = "5%";

    users.forEach((el) => {
        const left = parseFloat(el.style.left) || 0;
        el.style.left = (left + 5) + "%";
    });
}

function ShareOut() {
    if (!shareVisible) return;
    shareVisible = false;

    const ShareTextBtn = document.getElementById("ShareTextBtn");
    const addShare = document.getElementById("addShare");
    const users = document.querySelectorAll(".UserShare"); 

    ShareTextBtn.style.width = "0";
    addShare.style.left = "0";

    users.forEach((el) => {
        const left = parseFloat(el.style.left) || 0;
        el.style.left = (left - 5) + "%";
    });
}

//---------------------------------------------Share modal--------------------------------------------------------

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
            createNotfy("error", "Please fill in all fields.");
        }
        document.getElementById('ds_email').focus();
        shareProjBtn.disabled = false;
        return;
    }

    const email = document.getElementById('ds_email').value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(email)) {
        if (document.getElementsByClassName('notification').length === 0) {
            createNotfy("error", "Please enter a valid email.");
        }
        document.getElementById('ds_email').focus();
        shareProjBtn.disabled = false;
        return;
    }

    fetch(`/compartilharRoteiro/${document.getElementById("script_id").textContent.trim()}`, {
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
                createNotfy("success", "Script shared successfully.");
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
                        // Ignore error
                    }
                }
                return imagemPadrao;
            };
    
            // Fetch and create the element
            encontrarImagem(data.id).then(imagem => {
                const userDiv = document.createElement("div");
                userDiv.classList.add("colabUser");
    
                userDiv.innerHTML = `
                    <div class="colabInfo">
                        <div class="colabImg">
                            <img src="${imagem}" alt="Photo of ${data.nome}">
                        </div>
                        <div class="colabTexts">
                            <p>${data.nome}</p>
                            <p style="font-size: 13px">${data.email}</p>
                        </div>
                    </div>
                    <select>
                        <option value="1" ${data.cargo === 1 ? 'selected' : ''}>Reader</option>
                        <option value="2" ${data.cargo === 2 ? 'selected' : ''}>Commenter</option>
                        <option value="3" ${data.cargo === 3 ? 'selected' : ''}>Editor</option>
                    </select>
                `;
    
                container.appendChild(userDiv);
            })
        }
        else {
            if(data.message == "Usuário já é contribuidor deste roteiro.") {
                if (document.getElementsByClassName('notification').length === 0) {
                    createNotfy("error", "This user is already in the script.");
                }
                shareProjBtn.disabled = false;
                return;
            }
            else if(data.message == "Usuário não encontrado.") {
                if (document.getElementsByClassName('notification').length === 0) {
                    createNotfy("error", "User not found.");
                }
                shareProjBtn.disabled = false;
                document.getElementById('ds_email').focus();
                return;
            }
            else {
                if (document.getElementsByClassName('notification').length === 0) {
                    createNotfy("error", "Error sharing the script.");
                }
                shareProjBtn.disabled = false;
                return;
            }
        }
    
    })
    
    .catch(error => {
        console.error('Network error:', error);
        shareProjBtn.disabled = false;
    });
}

function salvarNovoCargo(id) {
    const roteiroId = document.getElementById("script_id").textContent.trim();
    const cargo = document.getElementById('cargo_' + id).value;

    fetch(`/salvarNovoCargoRoteiro/${roteiroId}`, {
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
                createNotfy("error", data.message || "Error changing permission.");
            }
        }
    })
    .catch(error => {
        console.error('Network error:', error);
        createNotfy("error", "Network error while changing permission.");
    });
}

// Options modal
document.addEventListener('DOMContentLoaded', function() {

    var ModalRotOpt = document.getElementById("modal_opts");

    function OpenModalRotOpt() {
        ModalRotOpt.style.height = "14vh";
        ModalRotOpt.style.padding = "1% 1% 2.2% 1%";
    }

    // Close the modal if the click is outside it
    function closeModalRotOpt(event) {
        if (!ModalRotOpt.contains(event.target) && !event.target.closest('.ModalPorjOptBtn')) {
            ModalRotOpt.style.height = "0";
            ModalRotOpt.style.padding = "0 1% 0 1%";
        }
    }

    // Add click event to open the modal
    var profileButton = document.querySelector('.ModalPorjOptBtn');
    if (profileButton) {
        profileButton.onclick = OpenModalRotOpt;
    }

    // Add click event to document to close the modal
    document.addEventListener('click', closeModalRotOpt);
});

function closeModalRotOpt2() {
    document.getElementById("modal_opts").style.height = "0";
    document.getElementById("modal_opts").style.padding = "0 1% 0 1%";
}

// Open properties modal
var ModalPropsFade = document.getElementById("ModalPropsFade");
var ModalProps = document.getElementById("ModalProps");

function OpenModalProps() {
    ModalProps.style.display = "flex";
    ModalPropsFade.style.backgroundColor = "rgb(0, 0, 0, 0.5)";
    ModalPropsFade.style.pointerEvents = "all";
    ModalProps.style.transform = "translateY(0)";
    ModalProps.style.opacity = 1;
    ModalProps.style.pointerEvents = "all";

    var ModalPorjOpt = document.getElementById("modal_opts");
    ModalPorjOpt.style.height = "0";
    ModalPorjOpt.style.padding = "0 2% 0 1%";

    document.getElementById('qt_char').textContent = contarCaracteres();
    document.getElementById('qt_palavras').textContent = contarPalavras();

    closeModalRotOpt2()
}

function getTextoSemHtml() {
    const textAreas = document.querySelectorAll('.textArea');
    let textoCompleto = "";
    textAreas.forEach(div => {
        textoCompleto += div.innerHTML + " ";
    });

    return textoCompleto
        .replace(/<[^>]*>/g, ' ')         // Remove HTML tags
        .replace(/&[^;\s]+;/g, ' ')       // Remove HTML entities
        .replace(/\s+/g, ' ')             // Normalize multiple spaces
        .trim();
}

function contarPalavras() {
    const texto = getTextoSemHtml();
    return texto ? texto.split(" ").length : 0;
}

function contarCaracteres() {
    const texto = getTextoSemHtml();
    return texto.length;
}

function CloseModalProps() {
    ModalPropsFade.style.backgroundColor = "rgb(0, 0, 0, 0)";
    ModalPropsFade.style.pointerEvents = "none";
    ModalProps.style.transform = "translateY(20vh)";
    ModalProps.style.opacity = 0;
    ModalProps.style.pointerEvents = "none";
}

function CloseRot() {
    let CloseRotForm = document.getElementById("CloseProjForm");
    CloseRotForm.submit();
}

// Open / Close script editing modal
var MdEditRot = document.getElementById("ModalEditRot");
var MdEditRotFade = document.getElementById("ModalEditRotFade");

var nm_rot;
var ds_rot;

function OpenModalEditRot() {
    MdEditRot.classList.add('ModalEditRotActive');
    MdEditRotFade.classList.add('ModalEditRotFadeActive');

    nm_rot = document.getElementById('Rot_name').value;
    ds_rot = document.getElementById('desc_Rot').textContent;

    closeModalRotOpt2()
}

function CloseModalEditRot() {
    MdEditRot.classList.remove('ModalEditRotActive');
    MdEditRotFade.classList.remove('ModalEditRotFadeActive');

    document.getElementById('Rot_name').value = nm_rot;
    document.getElementById('desc_Rot').textContent = ds_rot;
}

// Edit script block
var RotForm = document.getElementById("FormEditRot");
var Rot_name = document.getElementById("Rot_name");
var Rot_desc = document.getElementById("desc_Rot");

function EditRot() {

    document.getElementById('editRot_btn').disabled = true;

    if (Rot_name.value.length >= 3 && Rot_name.value.length <= 45) {

        if (Rot_desc.value.length >= 3 && Rot_desc.value.length <= 500) {
            
            RotForm.submit();
            
        } else {
            if (document.getElementsByClassName('notification').length === 0) {
                createNotfy("error", "Please select a status for the script block.");
            }
            status_Rot_id.focus();
            document.getElementById('editRot_btn').disabled = false;
        }
    } else {
        if (document.getElementsByClassName('notification').length === 0) {
            createNotfy("error", "The script block name must be between 3 and 45 characters.");
        }
        Rot_name.focus();
        document.getElementById('editRot_btn').disabled = false;
    }
}

//----------------------------------------------Comments-----------------------------------------------------------

document.addEventListener('DOMContentLoaded', function() {

    window.Pusher = Pusher;

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '6558ceb09b2f343f71b5',
        cluster: 'sa1',
        forceTLS: true,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            withCredentials: true
        }
    });
  }
);

function atualizarIconSend() {
    const input = document.getElementById('commentInput').value.trim();
    const img = document.getElementById('sendCommentImg');

    if (input !== '') {
        img.src = "Image/SendComment.svg";
    } else {
        img.src = "Image/SendCommentDesactivated.svg";
    }
}

document.addEventListener('DOMContentLoaded', function () {

    document.getElementById('CommentsCont').scrollTop = document.getElementById('CommentsCont').scrollHeight;

    const roteiroId = document.getElementById("script_id").textContent.trim();
    
    // Check the Pusher connection
    Echo.connector.pusher.connection.bind('connected', function () {
        console.log("Pusher connected successfully!");
        console.log('Pusher connection:', Echo.connector.pusher.connection);

        const sendImg = document.getElementById('sendCommentImg');
        sendImg.addEventListener('click', checarIC);

        const chatChannel = window.Echo.private('chat_rot.' + roteiroId);

        chatChannel.listen('NovoComentarioRoteiro', (event) => {
            console.log('Event received:', event);

            recarregarComentarios(roteiroId);

            fetch(`/salvarComentarioRoteiro/${roteiroId}`, {
                 method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    ds_comentario: event.ds_comentario,
                    id: event.id,
                    id_usuario: event.id_usuario,
                })
            })
            .then(response => response.json())
            .catch(error => console.error('Error saving comment:', error));                
        })
        .error(err => {
            console.error('Error listening to event:', err);
        });

        chatChannel.listen('NovoErroRoteiro', (event) => {

            recarregarComentariosErro(roteiroId);

            fetch(`/salvarErroRoteiro/${roteiroId}`, {
                 method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    ds_erro: event.ds_erro,
                    id: event.id,
                    id_usuario: event.id_usuario,
                })
            })
            .then(response => response.json())
            .catch(error => console.error('Error saving error:', error));                
        })
        .error(err => {
            console.error('Error listening to event:', err);
        });

        console.log('Connected to channel chat_rot.' + roteiroId);
    });    

    // Bind error event in case of connection failure
    Echo.connector.pusher.connection.bind('error', function(err) {
        console.log("Pusher connection error:", err);
    });
});

function recarregarComentarios(roteiroId) {
    fetch(`/comentariosRoteiro/${roteiroId}`)
        .then(response => {
            if (!response.ok) throw new Error("Error loading comments.");
            return response.text();
        })
        .then(html => {
            const cont = document.getElementById('CommentsCont');
            cont.innerHTML = html;
            cont.scrollTop = cont.scrollHeight;
        })
        .catch(error => {
            console.error("Error loading comments:", error);
        });
}

function recarregarComentariosErro(roteiroId) {

    fetch(`/comentariosErroRoteiro/${roteiroId}`)
        .then(response => {
            if (!response.ok) throw new Error("Error loading errors.");
            return response.text();
        })
        .then(html => {
            const cont = document.getElementById('ErrorsCont');
            cont.innerHTML = html;

            const commentConts = Array.from(document.querySelectorAll('.comment_cont'));
            const consertoMargins = Array.from(document.querySelectorAll('.consertoMargin'));

            // Sort comment_conts by data-dt_erro (optional, to ensure visual order)
            commentConts.sort((a, b) => new Date(a.dataset.dt_erro) - new Date(b.dataset.dt_erro));

            consertoMargins.forEach(conserto => {
                const consertoDate = new Date(conserto.dataset.dt);

                // Filter comment_conts with an earlier date
                const previousComment = commentConts
                    .filter(comment => new Date(comment.dataset.dt_erro) <= consertoDate)
                    .reduce((closest, current) => {
                        const currentDt = new Date(current.dataset.dt_erro);
                        const closestDt = closest ? new Date(closest.dataset.dt_erro) : new Date(0);
                        return currentDt > closestDt ? current : closest;
                    }, null);

                // If a previous comment_cont was found, insert after it
                if (previousComment) {
                    previousComment.parentNode.insertBefore(conserto, previousComment.nextSibling);
                }
            });

            cont.scrollTop = cont.scrollHeight;
        })
        .catch(error => {
            console.error("Error loading error comments:", error);
        });
}

function recarregarComentariosErroCorrecao(roteiroId) {
    fetch(`/comentariosErroRoteiro/${roteiroId}`)
        .then(response => {
            if (!response.ok) throw new Error("Error loading errors.");
            return response.text();
        })
        .then(html => {
            const cont = document.getElementById('ErrorsCont');
            cont.innerHTML = html;
            
            const commentConts = Array.from(document.querySelectorAll('.comment_cont'));
            const consertoMargins = Array.from(document.querySelectorAll('.consertoMargin'));

            // Sort comment_conts by data-dt_erro (optional, to ensure visual order)
            commentConts.sort((a, b) => new Date(a.dataset.dt_erro) - new Date(b.dataset.dt_erro));

            consertoMargins.forEach(conserto => {
                const consertoDate = new Date(conserto.dataset.dt);

                // Filter comment_conts with an earlier date
                const previousComment = commentConts
                    .filter(comment => new Date(comment.dataset.dt_erro) <= consertoDate)
                    .reduce((closest, current) => {
                        const currentDt = new Date(current.dataset.dt_erro);
                        const closestDt = closest ? new Date(closest.dataset.dt_erro) : new Date(0);
                        return currentDt > closestDt ? current : closest;
                    }, null);

                // If a previous comment_cont was found, insert after it
                if (previousComment) {
                    previousComment.parentNode.insertBefore(conserto, previousComment.nextSibling);
                }
            });
        })
        .catch(error => {
            console.error("Error loading error comments:", error);
        });
}

// Sort fix notifications
document.addEventListener('DOMContentLoaded', function() {

    const commentConts = Array.from(document.querySelectorAll('.comment_cont'));
    const consertoMargins = Array.from(document.querySelectorAll('.consertoMargin'));

    // Sort comment_conts by data-dt_erro (optional, to ensure visual order)
    commentConts.sort((a, b) => new Date(a.dataset.dt_erro) - new Date(b.dataset.dt_erro));

    consertoMargins.forEach(conserto => {
        const consertoDate = new Date(conserto.dataset.dt);

        // Filter comment_conts with an earlier date
        const previousComment = commentConts
            .filter(comment => new Date(comment.dataset.dt_erro) <= consertoDate)
            .reduce((closest, current) => {
                const currentDt = new Date(current.dataset.dt_erro);
                const closestDt = closest ? new Date(closest.dataset.dt_erro) : new Date(0);
                return currentDt > closestDt ? current : closest;
            }, null);

        // If a previous comment_cont was found, insert after it
        if (previousComment) {
            previousComment.parentNode.insertBefore(conserto, previousComment.nextSibling);
        }
    });
});

function checarIC() {
    const commentsCont = document.getElementById('CommentsCont');

    const display = window.getComputedStyle(commentsCont).display;

    if (display === "block") {
        EnviarComentario();
    } else {
        EnviarErro();
    }
}

function corrigirErro(idErro, id, idCont, idRadius, idIcon, idTxt) {
    
    let inp = document.getElementById(id).checked;

    if(inp == true) {
        let inp = document.getElementById(id).disabled = true;
        document.getElementById(idCont).style.backgroundColor = "#DCF8EA";
        document.getElementById(idCont).style.color = "#8FCDAC";

        document.getElementById(idRadius).style.borderBottomLeftRadius = "5px";
        document.getElementById(idRadius).style.borderTopLeftRadius = "5px";

        document.getElementById(idIcon).style.display = "none";

        document.getElementById(idTxt).style.textDecoration = "line-through";
        document.getElementById(idTxt).style.fontStyle = "italic";
        document.getElementById(idTxt).style.opacity = 0.54;

        fetch(`/corrigirErroRoteiro/${idErro}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
        })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (ok && data.success) {

                recarregarComentariosErroCorrecao(document.getElementById("script_id").textContent.trim());

            } else {
                if (document.getElementsByClassName('notification').length === 0) {
                    createNotfy("error", data.message || "An error occurred while fixing the error.");
                }
            }
        })
        .catch(error => {
            console.error('Network error:', error);
            createNotfy("error", "Network error while fixing error.");
        });
    }
    else {
        document.getElementById(idCont).style.backgroundColor = "#FADBDB";
        document.getElementById(idCont).style.color = "#D58888";

        document.getElementById(idRadius).style.borderBottomLeftRadius = "10px";
        document.getElementById(idRadius).style.borderTopLeftRadius = "10px";

        document.getElementById(idIcon).style.display = "block";

        document.getElementById(idTxt).style.textDecoration = "none";
        document.getElementById(idTxt).style.fontStyle = "normal";
        document.getElementById(idTxt).style.opacity = 1;
    }

}

function EnviarComentario() {

    let ds_comentario = document.getElementById('commentInput').value

    if(ds_comentario == null || ds_comentario == '') {
        return;
    }

    document.getElementById('commentInput').value = null;
    document.getElementById('commentInput').focus();

    document.getElementById('sendCommentImg').src = "Image/SendCommentDesactivated.svg";

    fetch(`/enviarComentarioRoteiro/${document.getElementById("script_id").textContent.trim()}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            ds_comentario: ds_comentario,
        })
    })
    .then(response => response.json().then(data => ({ ok: response.ok, data })))
    .then(({ ok, data }) => {
        if (ok && data.success) {

        } else {
            if (document.getElementsByClassName('notification').length === 0) {
                createNotfy("error", data.message || "An error occurred while sending the comment.");
            }
        }
    })
    .catch(error => {
        console.error('Network error:', error);
        createNotfy("error", "Network error while sending comment.");
    });
}

function EnviarErro() {

    let ds_erro = document.getElementById('commentInput').value

    if(ds_erro == null || ds_erro == '') {
        return;
    }

    document.getElementById('commentInput').value = null;
    document.getElementById('commentInput').focus();

    document.getElementById('sendCommentImg').src = "Image/SendCommentDesactivated.svg";

    fetch(`/enviarErroRoteiro/${document.getElementById("script_id").textContent.trim()}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            ds_erro: ds_erro,
        })
    })
    .then(response => response.json().then(data => ({ ok: response.ok, data })))
    .then(({ ok, data }) => {
        if (ok && data.success) {

        } else {
            if (document.getElementsByClassName('notification').length === 0) {
                createNotfy("error", data.message || "An error occurred while sending the comment.");
            }
        }
    })
    .catch(error => {
        console.error('Network error:', error);
        createNotfy("error", "Network error while sending comment.");
    });
}

function showComments(ic) {

    if(ic == 1) {
        document.getElementById('ic1').style.backgroundColor = "#E6E6E6";
        document.getElementById('ic1').style.color = "#14141B";
        document.getElementById('CommentsCont').style.display = "block";
        document.getElementById('CommentsCont').scrollTop = document.getElementById('CommentsCont').scrollHeight;

        document.getElementById('ic0').style.backgroundColor = "transparent";
        document.getElementById('ic0').style.color = "#8A8A94";
        document.getElementById('ErrorsCont').style.display = "none";
        
        document.getElementById('commentInput').placeholder = "Type your comment...";
    }
    else {
        document.getElementById('ic1').style.backgroundColor = "transparent";
        document.getElementById('ic1').style.color = "#8A8A94";
        document.getElementById('CommentsCont').style.display = "none";

        document.getElementById('ic0').style.backgroundColor = "#E6E6E6";
        document.getElementById('ic0').style.color = "#14141B";
        document.getElementById('ErrorsCont').style.display = "block";
        document.getElementById('ErrorsCont').scrollTop = document.getElementById('ErrorsCont').scrollHeight;

        document.getElementById('commentInput').placeholder = "Describe the error found...";
    }

}

document.getElementById('commentInput').addEventListener('keydown', function(event) {
    
    if (event.key === 'Enter') {
        
        checarIC();

    }
});