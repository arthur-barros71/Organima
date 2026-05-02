<div class="PageContato">
    <section class="Intro">

        <div class="rowBeginCenter2">

            <div class="secSobre">

                <p class="ContBigTitle titlefont">Formulário de Contato</p>
                <p class="ContText fontcorpo">Preencha as informações a baixo para entrar em contato com a equipe de
                    suporte ou dar um feedback!</p>

            </div>

            <img class="ContImg" src="image/Ilustração Fale Conosco.png">

        </div>

    </section>

    <section class="Enviar">

        <form action="/contato" method="post" class="BeginForm">
            @csrf

            <p class="inpText fontcorpo">Nome<span class="req">*</span></p>
            <input class="input" name="nome" id="namesend" type="text" required>

            <p class="inpText fontcorpo">Email<span class="req">*</span></p>
            <input class="input" name="email" id="emailsend" type="text" required>

            <p class="inpText fontcorpo">Mensagem<span class="req">*</span></p>
            <input class="inputMessage" name="mensagem" id="messagesend" type="text" required>

            <div class="centerCtt">

                <div class="btns">

                    <input class="Send" name="Enviar" value="Enviar" type="submit">

                    <button class="Cancel" onclick="limpar()">Cancelar</button>

                </div>

            </div>

        </form>

    </section>
</div>
