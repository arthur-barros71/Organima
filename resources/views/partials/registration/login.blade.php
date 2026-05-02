<!--Login-->
<div class="container login emsen active">
    <div class="form">
        <div class="title">
            <p class="titleText font titlefont">Login</p>
            <p class="subtitleText font fontcorpo">Bem-vindo de volta a Organima</p>
        </div>
        <form action="/entrar" method="post" id="FormLogin">
            @csrf
            <div class="resp">

                <p class="inpName font fontcorpo">Email</p>
                <input type="email" name="ds_email" id="loginemail" class="inp">

                <p class="inpName font top fontcorpo">Senha</p>
                <div class="inpSenha">
                    <input type="password" id="loginpass" name="cd_senha" class="inp">
                    <img src="Image/passHide.svg" id="loginpassToggle"
                        onclick="toggleSenha('loginpass', 'loginpassToggle')">
                </div>

                <p onclick="mostrarEsquecer()" class="link fontcorpo">Esqueci a senha...</p>

            </div>
            <p id="btnLogin" class="confirm button-normal fontcorpo" onclick="FazerLogin()">Fazer login</p>
            <p onclick="cadastrar()" class="link fontcorpo">Não tem uma conta? Crie uma!</p>
        </form>
    </div>
</div>

<div class="container login forgot hidden">
    <div class="form">
        <div class="title">
            <p class="titleText font titlefont">Esqueceu a senha?</p>
            <p class="subtitleText font fontcorpo"><span style="font-weight: bold">Redefina sua senha.</span> Digite o
                seu e-mail para que enviemos um código para confirmar que é você</p>
        </div>
        <form id="FormForgot">

            <div class="resp">

                <p class="inpName font fontcorpo">Email</p>
                <input type="email" name="ds_email" id="loginemail_r" class="inp">

            </div>
            <p class="confirm button-normal fontcorpo" onclick="verificarEmail()">Verificar email</p>
            <p onclick="cadastrar()" class="link fontcorpo">Voltar para anterior</p>
        </form>
    </div>
</div>

<div class="container login confirme hidden">
    <div class="form">
        <div class="title">
            <p class="titleText font titlefont" style="text-align: center; font-size: 40px">Esqueceu a senha?</p>
            <p class="subtitle font fontcorpo" style="text-align: center; font-size: 25px; margin: 2vh 0;">
                <span>Redefina a sua senha. </span>Digite o código que enviamos para o seu email para confirmar que é
                você.</p>
        </div>
        <div id="code_sent_container">
            <img src="image/CodeSent.gif" alt="Avião de papel sendo lançado" id="code_sent">
        </div>

        <div>
            @csrf
            <div id="verification_container">
                <div class="code-input" id="verificationCode">
                    <input type="text" id="firstCode" maxlength="1" />
                    <input type="text" maxlength="1" />
                    <input type="text" maxlength="1" />
                    <input type="text" maxlength="1" />
                    <input type="text" maxlength="1" />
                </div>
            </div>
            <div style="display: flex; width: 100%; justify-content: center; margin-top: 2vh">
                <p onclick="verificaCodigo()" class="confirm button-normal fontcorpo btn_code">Verificar código</p>
            </div>
            <p class="subtitle font fontcorpo" style="text-align: center">Não recebeu o código? Clique aqui para <span
                    onclick="reenviarCodigo()" id="resend_code"> reenviar o código</span></p>
        </div>
    </div>
</div>

<div class="container login nova hidden">
    <div class="form">
        <div class="title">
            <p class="titleText font titlefont">Nova Senha</p>
            <p class="subtitleText font fontcorpo">Digite sua nova senha</p>
        </div>
        <form id="FormForgot">
            @csrf

            <p class="inpName font top fontcorpo">Senha</p>
            <div class="inpSenha">
                <input type="password" id="new_pass" class="inp" name="cd_senha">
                <img src="Image/passHide.svg" id="new_passToggle" onclick="toggleSenha('new_pass', 'new_passToggle')">
            </div>

            <div class="require">
                <p class="passtext font fontcorpo" style="font-size: 17px; margin-top: 4vh">A senha deve conter:</p>
                <div class="req fontcorpo">
                    <img src="image/SenhaNone.svg" id="req1n">
                    <p id="reqSenha1n">No mínimo 6 caracteres</p>
                </div>
                <div class="req fontcorpo">
                    <img src="image/SenhaNone.svg" id="req2n">
                    <p id="reqSenha2n">Letras maiúsculas e minúsculas</p>
                </div>
                <div class="req fontcorpo">
                    <img src="image/SenhaNone.svg" id="req3n">
                    <p id="reqSenha3n">Ao menos um número</p>
                </div>
            </div>


            <p class="confirm button-normal fontcorpo" onclick="atualizarSenha()">Atualizar Senha</p>
        </form>
    </div>
</div>
