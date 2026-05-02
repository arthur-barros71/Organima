<!--Cadastro-->
<div class="cad">
    <div class="container register name active">

        <!--Cadastro - Nome-->
        <div class="form">
            <div class="title">
                <p class="titleText font titlefont">Criar conta</p>
                <p class="subtitleText font fontcorpo">Insira seu email para começar a criação da sua conta</p>
            </div>
            <form action="/registrar" method="post">
                @csrf
                <div class="resp">
                    <p class="inpName nametext font fontcorpo">Nome</p>
                    <input type="text" id="nameinp" class="inp" maxlength="45">
                </div>
                <p onclick="mostrarEmail()" id="proxEmail" class="confirm button-normal fontcorpo">Próximo</p>
                <p onclick="login()" class="link fontcorpo">Já tem uma conta? Faça login</p>
            </form>
        </div>
    </div>

    <div class="container register email hidden">

        <!--Cadastro - Email-->
        <div class="form">
            <div class="title">
                <p class="titleText font titlefont">Criar conta</p>
                <p class="subtitleText font fontcorpo">Digite seu nome para prosseguir</p>
            </div>
            <form action="/registrar" method="post">
                @csrf
                <div class="resp">
                    <p class="inpName emailtext font fontcorpo">Email</p>
                    <input type="email" id="emailinp" class="inp" name="emailinp" maxlength="45">
                </div>
                <p onclick="mostrarSenha()" id="proxSenha" class="confirm button-normal fontcorpo">Próximo</p>
                <p onclick="mostrarName()" class="link fontcorpo">Voltar para o anterior</p>
            </form>
        </div>
    </div>

    <!--Cadastro - Senha-->
    <div class="container register senha hidden">
        <div class="form">
            <div class="title">
                <p class="titleText font titlefont">Criar conta</p>
                <p class="subtitleText font fontcorpo">Quase lá! Crie uma senha para sua conta</p>
            </div>
            <div>
                @csrf
                <div class="resp">
                    <p class="inpName passtext font fontcorpo">Senha</p>

                    <div class="inpSenha">
                        <input type="password" id="pass" class="inp">
                        <img src="Image/passHide.svg" id="passToggle" onclick="toggleSenha('pass', 'passToggle')">
                    </div>

                    <div class="require">
                        <p class="passtext font fontcorpo" style="font-size: 17px; margin-top: 4vh">A senha deve conter:
                        </p>
                        <div class="req fontcorpo">
                            <img src="image/SenhaNone.svg" id="req1">
                            <p id="reqSenha1">No mínimo 6 caracteres</p>
                        </div>
                        <div class="req fontcorpo">
                            <img src="image/SenhaNone.svg" id="req2">
                            <p id="reqSenha2">Letras maiúsculas e minúsculas</p>
                        </div>
                        <div class="req fontcorpo">
                            <img src="image/SenhaNone.svg" id="req3">
                            <p id="reqSenha3">Ao menos um número</p>
                        </div>
                    </div>
                </div>
                <p onclick="mostrarConfirm()" id="proxConfirm" class="confirm button-normal fontcorpo">Criar conta</p>
                <p onclick="mostrarEmail()" class="link fontcorpo">Voltar para o anterior</p>
            </div>
        </div>
    </div>

    {{-- Confirmação - Email --}}
    <div class="container register confirme hidden">
        <div class="form">
            <div class="title">
                <p class="titleText font titlefont" style="text-align: center; font-size: 40px">Só mais alguns passos...
                </p>
                <p class="subtitle font fontcorpo" style="text-align: center; font-size: 25px; margin: 2vh 0;">
                    <span>Quase Lá! </span>Digite o código que enviamos para o seu email para confirmar que é você.</p>
            </div>

            <p class="codeExpiraText fontcorpo" id="codeExpiraText">O código expira em: <span
                    id="codeExpireTime">15:00</span></p>

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
                    <p onclick="submit()" id="proxCad" class="confirm button-normal fontcorpo btn_code">Verificar
                        código</p>
                </div>
                <p class="subtitle font fontcorpo" style="text-align: center">Não recebeu o código? Clique aqui para
                    <span onclick="reenviarCodigo()" id="resend_code"> reenviar o código</span></p>
            </div>
        </div>
    </div>

    <!--Formulário completo oculto-->
    <form id="completeForm" action="/registrar" method="POST" style="display: none;">
        @csrf
        <input type="text" name="nm_usuario" id="hiddenName">
        <input type="email" name="ds_email" id="hiddenEmail">
        <input type="password" name="cd_senha" id="hiddenPassword">
        <button type="submit fontcorpo">Criar conta</button>
    </form>
</div>
