//----------------------------------------------------------Login and register----------------------------------------------------------

// Function that allows the user to press enter to submit the form
document.addEventListener("keydown", function(e) {
    if (e.key === "Enter") {
        e.preventDefault();

        if (sessionStorage.getItem('pageBegin') === '5') {

            if (nameinp.value && nameinp.value.trim() !== "") {

                if (emailinp.value && emailinp.value.trim() !== "") {

                    if(pass.value && pass.value.trim() !== "") {
                        
                        if(document.getElementById('firstCode').value.trim() !== "") {
                            submit()
                        }
                        else {
                            mostrarConfirm();
                        }
                    }
                    else {
                        mostrarSenha();
                    }
                } else {
                    mostrarEmail();
                }
            }
        }
        else if(sessionStorage.getItem('pageBegin') === '4') {
            FazerLogin();
        }
    }
});

//----------------------------------------------------------Password validation visuals----------------------------------------------------------

var pass = document.getElementById('pass');
var req1 = document.getElementById('req1');
var req2 = document.getElementById('req2');
var req3 = document.getElementById('req3');

pass.addEventListener('input', function() {
    // Checking the password length (minimum of 8 caracters)
    if (pass.value.trim().length >= 8) {
        req1.src = 'image/SenhaAchieve.svg';
        document.getElementById('reqSenha1').style.color = "#2E945C";
    } else {
        req1.src = 'image/SenhaNone.svg';
        document.getElementById('reqSenha1').style.color = "#8C2E2E";
    }

    const temMaiuscula = /[A-Z]/.test(pass.value); // Checks if it has capital letters
    const temMinuscula = /[a-z]/.test(pass.value); // Checks if it has lowercase letters.

    if(temMaiuscula && temMinuscula) {
        req2.src = 'image/SenhaAchieve.svg';
        document.getElementById('reqSenha2').style.color = "#2E945C";
    } else {
        req2.src = 'image/SenhaNone.svg';
        document.getElementById('reqSenha2').style.color = "#8C2E2E";
    }

    const temNumero = /\d/.test(pass.value); // Checks if it has numbers

    if(temNumero) {
        req3.src = 'image/SenhaAchieve.svg';
        document.getElementById('reqSenha3').style.color = "#2E945C";
    } else {
        req3.src = 'image/SenhaNone.svg';
        document.getElementById('reqSenha3').style.color = "#8C2E2E";
    }
});

//----------------------------------------------------------Password validation----------------------------------------------------------

var new_pass = document.getElementById('new_pass');
var req1n = document.getElementById('req1n');
var req2n = document.getElementById('req2n');
var req3n = document.getElementById('req3n');

new_pass.addEventListener('input', function() {
    // Checks password length (minimum of 8 caracters)
    if (new_pass.value.length >= 8) {
        req1n.src = 'image/SenhaAchieve.svg';
        document.getElementById('reqSenha1n').style.color = "#2E945C";
    } else {
        req1n.src = 'image/SenhaNone.svg';
        document.getElementById('reqSenha1n').style.color = "#8C2E2E";
    }

    const temMaiuscula = /[A-Z]/.test(new_pass.value); // Checks if it has capital letters
    const temMinuscula = /[a-z]/.test(new_pass.value); // Checks if it has lowcase letters

    if(temMaiuscula && temMinuscula) {
        req2n.src = 'image/SenhaAchieve.svg';
        document.getElementById('reqSenha2n').style.color = "#2E945C";
    } else {
        req2n.src = 'image/SenhaNone.svg';
        document.getElementById('reqSenha2n').style.color = "#8C2E2E";
    }

    const temNumero = /\d/.test(new_pass.value); // Checks if it has numbers

    if(temNumero) {
        req3n.src = 'image/SenhaAchieve.svg';
        document.getElementById('reqSenha3n').style.color = "#2E945C";
    } else {
        req3n.src = 'image/SenhaNone.svg';
        document.getElementById('reqSenha3n').style.color = "#8C2E2E";
    }
});

document.getElementById('pass').addEventListener('keydown', function(event) {
    var regex = /^[a-zA-Z0-9]$/;
  
    if (event.key === '`' && event.shiftKey) {
      event.preventDefault();
    }
  
    if (event.key === "Backspace") {
      return;
    }
  
    if (!regex.test(event.key)) {
      event.preventDefault();
    }
  }
);
  
document.getElementById('pass').addEventListener('paste', function(event) {
    var pasteData = event.clipboardData.getData('text');
    var regex = /^[a-zA-Z0-9]*$/;
  
    if (!regex.test(pasteData)) {
      event.preventDefault();
    }
  }
);  

//----------------------------------------------------------Switch from register to login----------------------------------------------------------

var register = document.getElementsByClassName("register")[0];
var logincont = document.getElementsByClassName("login")[0];
var imag = document.getElementById("image");

register.style.transition = "none";
logincont.style.transition = "none";
register.style.transform = "translate(0)";
logincont.style.transform = "translate(100vw)";
imag.style.transform = "translate(52vw)";

setTimeout(function() {
    register.style.transition = "all ease-in-out 0.5s";
    logincont.style.transition = "all ease-in-out 0.5s";
}, 1000);

function cadastrar() {
    register.style.transform = "translate(0)";
    logincont.style.transform = "translate(100vw)";
    imag.style.transform = "translate(52vw)";

    sessionStorage.setItem('pageBegin', 5);

    setTimeout(function() {
        document.getElementById('loginemail').value = "";
        document.getElementById('loginpass').value = "";
    }, 300);
}

function login() {
    register.style.transform = "translate(-50vw)";
    logincont.style.transform = "translate(50vw)";
    imag.style.transform = "translate(0vw)";   

    sessionStorage.setItem('pageBegin', 4);

    setTimeout(function() {
        document.getElementById('nameinp').value = null;
    }, 300);
}

//----------------------------------------------------------Hide/show password----------------------------------------------------------

function toggleSenha(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    const isPassword = input.type === 'password';

    input.type = isPassword ? 'text' : 'password';
    icon.src = isPassword ? 'Image/passShow.svg' : 'Image/passHide.svg';
}

//----------------------------------------------------------Pass register stages----------------------------------------------------------

var name_ = document.querySelector(".register.name");
var email_ = document.querySelector(".register.email");
var senha_ = document.querySelector(".register.senha");
var confirm_ = document.querySelector(".register.confirme");

var confirm2_ = document.querySelector(".login.confirme");
var login_ = document.querySelector(".login.emsen");
var forgot_ = document.querySelector(".login.forgot");
var nova_ = document.querySelector(".login.nova");

// Only the name is visible at the start
name_.classList.add("active");
name_.classList.remove("hidden");

email_.classList.add("hidden");
email_.classList.remove("active");

senha_.classList.add("hidden");
senha_.classList.remove("active");

confirm_.classList.add("hidden");
confirm_.classList.remove("active");

confirm2_.classList.add("hidden");
confirm2_.classList.remove("active");

login_.classList.add("active");
login_.classList.remove("hidden");

nova_.classList.add("hidden");
nova_.classList.remove("active");

var emailinp = document.getElementById("emailinp");
var nameinp = document.getElementById("nameinp");

var emailtext = document.getElementsByClassName("emailtext")[0];
var nametext = document.getElementsByClassName("nametext")[0];
var passtext = document.getElementsByClassName("passtext")[0];

// Show olny the email section
function mostrarEmail() {
    if(nameinp.value != "" && nameinp.value != null) {
        name_.classList.add("hidden");
        name_.classList.remove("active");
        
        email_.classList.add("active");
        email_.classList.remove("hidden");
    
        senha_.classList.add("hidden");
        senha_.classList.remove("active");

        confirm_.classList.add("hidden");
        confirm_.classList.remove("active");
        confirm_.style.width = "100%";
        imag.style.transform = "translate(52vw)";

        pass.value = null;
        emailinp.focus();
    }
    else {
        nametext.innerHTML = "Nome<span class='red-text'>*</span>"
    }
}

// Show only the name section
function mostrarName() {
    email_.classList.add("hidden");
    email_.classList.remove("active");
    
    name_.classList.add("active");
    name_.classList.remove("hidden");
    
    senha_.classList.add("hidden");
    senha_.classList.remove("active");

    confirm_.classList.add("hidden");
    confirm_.classList.remove("active");
    confirm_.style.width = "100%";
    imag.style.transform = "translate(52vw)";

    pass.value = null;
    emailinp.value = null;

    nameinp.focus();
}

async function reenviarCodigo() {
    const ds_email = document.getElementById("emailinp").value;

    try {

    const codeResponse = await fetch('/enviarCodigo', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ ds_email })
    });

    const codeData = await codeResponse.json();

    if (!codeResponse.ok || !codeData.success) {
        if (existingNotification.length === 0) {
            createNotfy("error", "Falha ao enviar o código");
        }   
        throw new Error(codeData.message || "Falha ao enviar código");
    }
    else {
        inputs.forEach(input => {
            input.disabled = false;
        });

        document.getElementById('codeExpiraText').innerHTML = "O código expira em: <span id='codeExpireTime'>15:00</span>";
        document.getElementById('codeExpiraText').style.color = "#14141B";

        clearInterval(expirationIntervalId);
        expirationIntervalId = null;

        expirationTime = 900;
        calcExpirationTime();
    }

    }
    catch (error) {
        console.error("Erro:", error);
        alert("Ocorreu um erro ao enviar o código. Por favor, tente novamente.");
    }
}

var expirationTime;

async function mostrarConfirm() { 
    const ds_email = document.getElementById("emailinp").value;

    const password = document.getElementById("pass").value;

    document.getElementById('proxConfirm').classList.add('btnDisabled');

    // Checks password strenght
    const hasUpper = /[A-Z]/.test(password);
    const hasLower = /[a-z]/.test(password);
    const hasNumber = /\d/.test(password);
    
    if (password.length < 8 || !hasUpper || !hasLower || !hasNumber) {
        if (existingNotification.length === 0) {
            createNotfy("error", "A senha não atende aos requisitos mínimos");
        }   
        document.getElementById('proxConfirm').classList.remove('btnDisabled');
        return;
    }

    try {

        const codeResponse = await fetch('/enviarCodigo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ ds_email })
        });

        const codeData = await codeResponse.json();

        if (!codeResponse.ok || !codeData.success) {
            if (existingNotification.length === 0) {
                createNotfy("error", "Falha ao enviar o código");
            }
            document.getElementById('proxConfirm').classList.remove('btnDisabled');
            throw new Error(codeData.message || "Falha ao enviar código");
        }

        document.getElementById('proxConfirm').classList.remove('btnDisabled');

        // The screen will only change if the code was successfully sent
        email_.classList.add("hidden");
        email_.classList.remove("active");
        
        name_.classList.add("hidden");
        name_.classList.remove("active");
        
        senha_.classList.add("hidden");
        senha_.classList.remove("active");

        confirm_.classList.add("active");
        confirm_.classList.remove("hidden");
        confirm_.style.width = "200%";
        imag.style.transform = "translate(100vw)";

        // Stores the email to use it in the verification
        localStorage.setItem('verificationEmail', ds_email);

        document.getElementById('firstCode').focus();

        expirationTime = 900;
        calcExpirationTime();

    }
    catch (error) {
        console.error("Erro:", error);
        alert("Ocorreu um erro ao enviar o código. Por favor, tente novamente.");
        document.getElementById('proxConfirm').classList.remove('btnDisabled');
    }

}

const inputs = document.querySelectorAll('#verificationCode input');

var expirationIntervalId;

function calcExpirationTime() {

    expirationIntervalId = setInterval(() => {
        if (expirationTime > 0) {
            expirationTime -= 1;
            
            let minutes = Math.floor(expirationTime / 60);
            let seconds = expirationTime % 60;
            let formattedTime = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

            document.getElementById('codeExpireTime').textContent = formattedTime;

            if(expirationTime <= 5) {
                document.getElementById('codeExpireTime').style.color = "#D53E41";
            }

        } else {
            clearInterval(expirationIntervalId);

            document.getElementById('codeExpiraText').textContent = "Código expirado";
            document.getElementById('codeExpiraText').style.color = "#D53E41";

            inputs.forEach(input => {
                input.disabled = true;
                input.value = null;
            });

        }
    }, 1000);

}

inputs.forEach((input, i) => {
    input.addEventListener('input', () => {
      input.value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');

      if (input.value && i < inputs.length - 1) {
        inputs[i + 1].focus();
      }
    });

    input.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace' && !input.value && i > 0) {
        inputs[i - 1].focus();
      }
    });
  });

  window.addEventListener('DOMContentLoaded', () => {
    inputs[0].focus();
  }
);

function getVerificationCode() {
    return Array.from(document.querySelectorAll('#verificationCode input'))
      .map(input => input.value)
      .join('');
}  

// Shows only the password section
function mostrarSenha() {
    const email = document.getElementById("emailinp").value;
    const regexEmail = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    document.getElementById('proxSenha').classList.add('btnDisabled');

    // Checks if the email has a valid format
    if (email !== "" && email !== null && regexEmail.test(email)) {
        // Send the AJAX request to check if the email exists in the database
        $.ajax({
            url: '/verificarEmail',
            method: 'POST',
            data: {
                ds_email: email
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Checks if the email already exists
                if (response.exists) {
                    // If the email exists, sends a warning
                    if (document.getElementsByClassName('notification').length === 0) {
                        createNotfy("error", "Este e-mail já está cadastrado.");
                    }
                    document.getElementById('proxSenha').classList.remove('btnDisabled');
                } else {
                    // If the email doesn't exist, shows the password section
                    email_.classList.add("hidden");
                    email_.classList.remove("active");
        
                    name_.classList.add("hidden");
                    name_.classList.remove("active");
        
                    senha_.classList.add("active");
                    senha_.classList.remove("hidden");

                    confirm_.classList.add("hidden");
                    confirm_.classList.remove("active");

                    document.getElementById('pass').focus();

                    document.getElementById('proxSenha').classList.remove('btnDisabled');
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr, status, error);
                createNotfy("error", "Ocorreu um erro ao verificar o e-mail.");
                document.getElementById('proxSenha').classList.remove('btnDisabled');
            }            
        });        
    } else if (email === "" || email === null) {
        // Empty email message
        emailtext.innerHTML = "Email<span class='red-text'>*</span>";

        document.getElementById('proxSenha').classList.remove('btnDisabled');
    } else {
        // Invalid email message
        emailtext.innerHTML = "Email<span class='red-text'>*</span>";

        // Checks if there is already a warning
        var existingNotification = document.getElementsByClassName('notification');
       
        if (existingNotification.length === 0) {
            createNotfy("info", "Email inválido");  // Shows the warning only if it doesn't have any yet
        }
        document.getElementById('proxSenha').classList.remove('btnDisabled');
    }
}

//----------------------------------------------------------Create account----------------------------------------------------------

async function submit() {
    const ds_email = document.getElementById("emailinp").value;
    const name = document.getElementById("nameinp").value;
    const password = document.getElementById("pass").value;
    const code = getVerificationCode().toUpperCase();

    document.getElementById('proxCad').classList.add('btnDisabled');
    
    // Basic validation
    if (!code) {
        createNotfy("info", "Preencha todos os campos");
        document.getElementById('proxCad').classList.remove('btnDisabled');
        return;
    }
    
    if (code.length !== 5) {
        createNotfy("info", "O código deve ter 5 caracteres");
        document.getElementById('proxCad').classList.remove('btnDisabled');
        return;
    }

    // Checks password strenght
    const hasUpper = /[A-Z]/.test(password);
    const hasLower = /[a-z]/.test(password);
    const hasNumber = /\d/.test(password);
    
    if (password.length < 8 || !hasUpper || !hasLower || !hasNumber) {
        createNotfy("error", "A senha não atende aos requisitos mínimos");
        document.getElementById('proxCad').classList.remove('btnDisabled');
        return;
    }

    try {
        // Checks the code
        const verificationResponse = await fetch('/verificarCodigo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ ds_email, code })
        });

        const verificationData = await verificationResponse.json();

        if (!verificationResponse.ok || !verificationData.success) {
            createNotfy("error", "Código Inválido");
            document.getElementById('proxCad').classList.remove('btnDisabled');
            throw new Error("Verificação falhou");
        }

        // If the code is correct, prepares and sends the form
        document.getElementById('hiddenName').value = name;
        document.getElementById('hiddenEmail').value = ds_email;
        document.getElementById('hiddenPassword').value = password;
        
        // Sends the complete form
        document.getElementById('completeForm').submit();

    } catch (error) {
        console.error("Erro:", error);
    }
}

//----------------------------------------------------------Login----------------------------------------------------------

function FazerLogin() {
    var FormLogin = document.getElementById("FormLogin");
    var email = document.getElementById("loginemail");
    var loginpassword = document.getElementById("loginpass");

    document.getElementById('btnLogin').classList.add('btnDisabled');

    if (email.value !== "" && loginpassword.value !== "") {

        const regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        if (regex.test(email.value)) {

            $.ajax({
                url: '/entrar',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    ds_email: email.value,
                    cd_senha: loginpassword.value
                }),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {

                    if (response.success) {

                        sessionStorage.removeItem('page');
                        sessionStorage.removeItem('pageBegin');

                        window.location.href = "/";

                    } else {

                        var existingNotification = document.getElementsByClassName('notification');

                        if (response.reason === 'account_suspended') {
                            if (existingNotification.length === 0) {
                                createNotfy("error", response.message);
                            }

                            setTimeout(function() {
                                document.getElementById('loginpass').value = "";
                                document.getElementById('loginemail').value = "";
                            }, 300);

                            document.getElementById('btnLogin').classList.remove('btnDisabled');

                            document.getElementById('loginemail').focus();

                            return;
                        }
                        else if (response.reason === 'invalid_credentials') {
                            if (existingNotification.length === 0) {
                                createNotfy("error", response.message);
                            }
                        }
                        else {
                            if (existingNotification.length === 0) {
                                createNotfy("error", "Erro desconhecido.");
                            }
                        }

                        setTimeout(function() {
                            document.getElementById('loginpass').value = "";
                        }, 300);

                        document.getElementById('btnLogin').classList.remove('btnDisabled');

                        document.getElementById('loginpass').focus();
                    }
                },
                error: function(xhr, status, error) {
                    var existingNotification = document.getElementsByClassName('notification');
                    if (existingNotification.length === 0) {
                        createNotfy("error", "Ocorreu um erro ao realizar o login.");
                    }
                    document.getElementById('btnLogin').classList.remove('btnDisabled');
                }
            });

        } else {

            var existingNotification = document.getElementsByClassName('notification');

            if (existingNotification.length === 0) {
                createNotfy("error", "Email inválido.");
            }
            document.getElementById('btnLogin').classList.remove('btnDisabled');
        }

    } else {

        var existingNotification = document.getElementsByClassName('notification');

        if (existingNotification.length === 0) {
            createNotfy("info", "Preencha todos os campos.");
        }
        document.getElementById('btnLogin').classList.remove('btnDisabled');
    }
}

function mostrarEsquecer(){
    login_.classList.add("hidden");
    login_.classList.remove("active");

    forgot_.classList.add("active");
    forgot_.classList.remove("hidden");

    forgot_.style.transform = "translate(50vw)";

    // imag.style.display = "none";
}

function mostrarNovaSenha() {
    login_.classList.add("hidden");
    login_.classList.remove("active");

    forgot_.classList.add("hidden");
    forgot_.classList.remove("active");

    confirm2_.classList.add("hidden");
    confirm2_.classList.remove("active");

    nova_.classList.add("active");
    nova_.classList.remove("hidden");

    nova_.style.transform = "translate(50vw)";
    imag.style.transform = "translate(0vw)";
}

 async function verificarEmail(){
    var ds_email = document.getElementById("loginemail_r").value;

    if (ds_email != "" && ds_email != null) {
        const regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        if (regex.test(ds_email)) {

            $.ajax({
                url: '/verificarEmail',
                method: 'POST',
                data: {
                    ds_email: ds_email
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: async function(response) {
                    // Checks if the email already exists
                    if (response.exists) {
                        localStorage.setItem("emailRecuperacao", ds_email);

                        forgot_.classList.add("hidden");
                        forgot_.classList.remove("active");

                        confirm2_.classList.add("active");
                        confirm2_.classList.remove("hidden");
                        confirm2_.style.width = "200%";
                        imag.style.transform = "translate(-100vw)"

                        try {

                            const codeResponse = await fetch('/enviarCodigo', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({ ds_email })
                            });
                        
                            const codeData = await codeResponse.json();
                        
                            if (!codeResponse.ok || !codeData.success) {
                                if (existingNotification.length === 0) {
                                    createNotfy("error", "Falha ao enviar o código");
                                }   
                                throw new Error(codeData.message || "Falha ao enviar código");
                            }
                        
                            }
                            catch (error) {
                                console.error("Erro:", error);
                                alert("Ocorreu um erro ao enviar o código. Por favor, tente novamente.");
                            }
                        
                       
                    } else {
                        
                        // If the email doesn't exist in the database, shows a warning
                        if (document.getElementsByClassName('notification').length === 0) {
                            createNotfy("error", "Email não existe.");
                        }

                    }
                },
                error: function(xhr, status, error) {
                    createNotfy("error", "Ocorreu um erro ao verificar.");
                }            
            });  

        } 
        else {
            var existingNotification = document.getElementsByClassName('notification');

            if (existingNotification.length === 0) {
                createNotfy("error", "Email inválido.");
            }
        }
    }
    else {
        var existingNotification = document.getElementsByClassName('notification');

        if (existingNotification.length === 0) {
            createNotfy("info", "Preencha todos os campos.");
        }
    }
}

async function verificaCodigo(){

    const ds_email = document.getElementById("loginemail_r").value;
    const code = getVerificationCode().toUpperCase();

    if (!code) {
        createNotfy("info", "Preencha todos os campos");
        return;
    }
    
    if (code.length !== 5) {
        createNotfy("info", "O código deve ter 5 caracteres");
        return;
    }

    try {
        // Checks the code
        const verificationResponse = await fetch('/verificarCodigo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ ds_email, code })
        });

        const verificationData = await verificationResponse.json();

        if (!verificationResponse.ok || !verificationData.success) {
            createNotfy("error", "Código Inválido");
            throw new Error("Verificação falhou");
        }

        // If the code is correct, prepares and sends the form
        mostrarNovaSenha();
        

    } catch (error) {
        console.error("Erro:", error);
    }

}

async function atualizarSenha(){
    const password = document.getElementById("new_pass").value;
    const ds_email = localStorage.getItem("emailRecuperacao");

    // Checks password strenght 
    const hasUpper = /[A-Z]/.test(password);
    const hasLower = /[a-z]/.test(password);
    const hasNumber = /\d/.test(password);
    

    if(password != null || password != ""){
        if (password.length < 8 || !hasUpper || !hasLower || !hasNumber) {
            if (existingNotification.length === 0) {
                createNotfy("info", "A senha não atende aos requisitos mínimos");
            }   
            return;
        }
    }
    else {
        if(existingNotification.length === 0){
            createNotfy("info", "preencha todos os campos");
        }
        return;
    }
    

    try {
        // Configure the CSRF header
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Makes the request
        const response = await fetch('/atualizar_senha', {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            
            body: JSON.stringify({
                ds_email: ds_email,
                cd_senha: password
            })

        });

        const result = await response.json();

        if (response.ok) {
            createNotfy("success", "Senha atualizada com sucesso");
            confirm2_.classList.add("hidden");
            confirm2_.classList.remove("active");

            login_.classList.add("active");
            login_.classList.remove("hidden");

            nova_.classList.add("hidden");
            nova_.classList.remove("active");
                        
        } else {
            if (result.errors) {
                let errorMessage = '';
                for (const [key, value] of Object.entries(result.errors)) {
                    errorMessage += `${value}\n`;
                }
                alert(errorMessage);
            } else {
                alert(result.message || 'Erro ao atualizar senha');
            }
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('Ocorreu um erro ao conectar com o servidor');
    }

}