//-----------------------------------------------------Edit profile-----------------------------------------------------

var MdPrflCmplt = document.getElementById("ModalProfileComplete");
var MdPrflCmpltFade = document.getElementById("ModalProfileCompleteFade");

function OpenModalProfileComplete() {
    MdPrflCmplt.classList.add('ModalProfileCompleteActive');
    MdPrflCmpltFade.classList.add('ModalProfileFadeActive');
    closeProfileModal1();
}

function CloseModalProfileComplete() {
    MdPrflCmplt.classList.remove('ModalProfileCompleteActive');
    MdPrflCmpltFade.classList.remove('ModalProfileFadeActive');
}

//-----------------------------------------------------Recovery email-----------------------------------------------------

var email = document.getElementById("recpEmail");
var email_img = document.getElementById("recpEmail_Img");
var email_img_cancel = document.getElementById("recpEmail_Img_cancel");
var userEmail = email.value;
var EmailForm = document.getElementById("recpEmailForm");

// Function to change recovery email
function alterRecpEmail(em) {

    let valorInicial = userEmail;

    if (email_img.src.includes("Edit.svg")) {
        email.removeAttribute("readonly");

        if (email.value == "adicionar email") {
            email.value = '';
        }
        email.focus();
        email_img_cancel.style.opacity = 1;

        setTimeout(function() {
            email_img.src = "image/Check.png";
        }, 100);

    } else if (email_img.src.includes("Check.png")) {

        email_img_cancel.style.opacity = 0;
        userEmail = email.value;
        email.setAttribute("readonly", "true");
        email_img.src = "image/Edit.svg";

        if(em == userEmail) {
            if (document.getElementsByClassName('notification').length === 0) {
                createNotfy("error", 'O email de verificação não pode ser igual ao seu email principal.');
            }
            email.value = valorInicial;
            email.focus();
            return;
        }

        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailRegex.test(userEmail)) {
            if (document.getElementsByClassName('notification').length === 0) {
                createNotfy("error", "Por favor, insira um e-mail válido.");
            }
            email.value = valorInicial;
            email.focus();
            return;
        }

        $.ajax({
            url: '/alterarEmailRecuperacao',
            method: 'POST',
            data: {
                ds_email_recuperacao: email.value
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    if (document.getElementsByClassName('notification').length === 0) {
                        createNotfy("success", response.message);
                    }
                } else {
                    if (document.getElementsByClassName('notification').length === 0) {
                        createNotfy("error", response.message);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr, status, error);
                if (document.getElementsByClassName('notification').length === 0) {
                    createNotfy("error", "Ocorreu um erro ao atualizar o email de recuperação.");
                }
            }
        });
    }
}

// Function to cancel the recovery email's change
function alterRecpEmailCancel() {
    if (email_img_cancel.style.opacity == 1) {
        email_img_cancel.style.opacity = 0;
        email.value = userEmail;
        email.setAttribute("readonly", "true");
        email_img.src = "image/Edit.svg";
    }
}

//-----------------------------------------------------Name-----------------------------------------------------

var UpdateName = document.getElementById("update_name");
var name_img = document.getElementById("name_Img");
var name_img_cancel = document.getElementById("name_Img_cancel");
var userName = document.getElementById("update_name").value;
var NameForm = document.getElementById("NameForm");

var NameBar = document.getElementById("NameBar");
var ModalName = document.getElementById("ModalName");

//Function to change the user's name
function alterName() {
    userName = UpdateName.value;

    if (name_img.src.includes("Edit.svg")) {
        UpdateName.removeAttribute("readonly");
        if(UpdateName.value == "adicionar número") {
            UpdateName.value = '';
        }
        UpdateName.focus();
        name_img_cancel.style.opacity = 1;

        setTimeout(function() {
            name_img.src = "image/Check.png";
        }, 100);

    } else if (name_img.src.includes("Check.png")) {
        name_img_cancel.style.opacity = 0;
        UpdateName.value = userName;
        UpdateName.setAttribute("readonly", "true");
        name_img.src = "image/Edit.svg";

        $.ajax({
            url: '/alterarNome',
            method: 'POST',
            data: {
                nm_usuario: UpdateName.value
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    if (document.getElementsByClassName('notification').length === 0) {
                        createNotfy("success", response.message);
                    }
                    NameBar.innerHTML = UpdateName.value; // Adds the new name to the sidebar
                    ModalName.innerHTML = UpdateName.value; // Adds the new name to the profile options modal

                } else {
                    if (document.getElementsByClassName('notification').length === 0) {
                        createNotfy("error", response.message);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr, status, error);
                if (document.getElementsByClassName('notification').length === 0) {
                    createNotfy("error", "Ocorreu um erro ao atualizar o nome de usuário.");
                }
            }
        });
    }
}

// Function to cancel the user's name change
function alterNameCancel() {
    if (name_img_cancel.style.opacity == 1) {
        name_img_cancel.style.opacity = 0;
        UpdateName.value = userName;
        UpdateName.setAttribute("readonly", "true");
        name_img.src = "image/Edit.svg";
    }
}

//-----------------------------------------------------Phone number-----------------------------------------------------

document.addEventListener('DOMContentLoaded', function () {
    const telInput = document.getElementById('tel');
  
    function aplicarMascaraTelefone(valor) {
        const somenteNumeros = valor.replace(/\D/g, "");
  
        if (somenteNumeros.length <= 10) {
            // Format: (99) 9999-9999
            return somenteNumeros.replace(/(\d{2})(\d{4})(\d{0,4})/, "($1) $2-$3");
        } else {
            // Format: (99) 99999-9999
            return somenteNumeros.replace(/(\d{2})(\d{5})(\d{0,4})/, "($1) $2-$3");
        }
    }
  
    // If the field already has value
    if (telInput.value && !isNaN(telInput.value.replace(/\D/g, ''))) {
        telInput.value = aplicarMascaraTelefone(telInput.value);
    }
  
    // If at any point it becomes editable
    telInput.addEventListener('input', function () {
        telInput.value = aplicarMascaraTelefone(telInput.value);
    });
  }
);

//-----------------------------------------------------Profile image-----------------------------------------------------

// Import profile image  
const uploadUrl = document.querySelector('meta[name="profile-image-upload-url"]').getAttribute('content');

var isModalProfileImageOpen = false;

function openProfileImageModal() {
    document.getElementById('profileImageModal').style.display = "block";
    isModalProfileImageOpen = true;

    document.getElementById('modal_prfl_cmplt_img').style.filter = 'blur(0) brightness(1)';
    document.getElementById('modal_prfl_cmplt_img').style.cursor = "default";
}

function CloseModalProfileImage() {
    document.getElementById('profileImageModal').style.display = "none";
    isModalProfileImageOpen = false;
}

function checkModalProfileImage() {

    if(isModalProfileImageOpen == true) {
        document.getElementById('modal_prfl_cmplt_img').style.filter = 'blur(0) brightness(1)';
        document.getElementById('modal_prfl_cmplt_img').style.cursor = "default";
    }
    else {
        document.getElementById('modal_prfl_cmplt_img').style.filter = 'blur(1px) brightness(0.6)';
        document.getElementById('modal_prfl_cmplt_img').style.cursor = "pointer";
    }
}

function checkModalProfileImage2() {
    document.getElementById('modal_prfl_cmplt_img').style.filter = 'blur(0) brightness(1)';
    document.getElementById('modal_prfl_cmplt_img').style.cursor = "default";
}

document.getElementById('setImgProfile').addEventListener('click', function () {
  document.getElementById('importProfileImg').click();
});

function changeDefaultImg(num) {

    const formData = new FormData();
    formData.append('imgNum', num);

    fetch("/escolherImagemPadrão", {
      method: "POST",
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {  
        // Builds the public path to show the image
        const imageUrl = `/storage/${data.path}?v=${Date.now()}`;
  
        // Update the srcs of the profile images
        document.getElementById('profileImgBar').src = imageUrl;
        document.getElementById('modalProfileImg').src = imageUrl;
        document.getElementById('modal_prfl_img').src = imageUrl;
        document.getElementById('profileImageChoose').src = 'Image/ProfileCustom.svg';

        document.querySelectorAll('.profileImageOpt').forEach(el => {
            el.style.border = '3px #4A4A5F solid';
        });

        document.getElementById('profileImageOpt_' + num).style.border = "3px solid #fdfdfd";
    })
    .catch(error => console.error('Erro ao escolher imagem:', error));

}

document.getElementById('importProfileImg').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
  
    const formData = new FormData();
    formData.append('profile_image', file);
  
    fetch(uploadUrl, {
      method: "POST",
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: formData
    })
    .then(response => {
      if (!response.ok) {
        return Promise.reject(response);
      }
      return response.json();
    })
    .then(data => {  
        // Builds the public path to show the image
        const imageUrl = `/storage/${data.path}?v=${Date.now()}`;
  
        // Update the srcs of the profile images
        document.getElementById('profileImgBar').src = imageUrl;
        document.getElementById('modalProfileImg').src = imageUrl;
        document.getElementById('modal_prfl_img').src = imageUrl;
        document.getElementById('profileImageChoose').src = imageUrl;

        document.querySelectorAll('.profileImageOpt').forEach(el => {
            el.style.border = '3px #4A4A5F solid';
        });

        document.getElementById('setImgProfile').style.border = "3px solid #fdfdfd";
    })
    .catch(error => console.error('Erro ao enviar imagem:', error));
});

//-----------------------------------------------------Disable account-----------------------------------------------------

function desativarConta() {
    const token = document.querySelector('meta[name="csrf-token"]').content;
        
    fetch('/desativarConta', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/';
        } else {
            alert(data.message || 'Ocorreu um erro ao desativar a conta');
        }
    })
    createNotfy("info", "A sua conta foi desativada")
    .catch(error => {
        console.error('Error:', error);
        alert('Ocorreu um erro ao processar sua solicitação');
    });
}