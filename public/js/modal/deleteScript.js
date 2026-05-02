//-----------------------------------------------------Delete script modal-----------------------------------------------------

var modalDeleteRotFade = document.getElementById("ModalDeleteRotFade");
var modalDeleteRot = document.getElementById("ModalDeleteRot");

var idr;
var Rot;
var inirot;

function excluirRotModal(idpr, rott, inirott) {
    modalDeleteRotFade.style.pointerEvents = "all";
    modalDeleteRotFade.style.backgroundColor = "rgb(0, 0, 0, 0.2)";
    modalDeleteRot.style.transform = "translateY(0)";
    modalDeleteRot.style.opacity = 1;

    idr = idpr;
    Rot = rott;
    inirot = inirott;

    hideRotProprierties1();
}

function closeExcluirRotModal() {
    modalDeleteRotFade.style.pointerEvents = "none";
    modalDeleteRotFade.style.backgroundColor = "rgb(0, 0, 0, 0)";
    modalDeleteRot.style.transform = "translateY(20vh)";
    modalDeleteRot.style.opacity = 0;

    idr = null;
    Rot = null;
    inirot = null;
}

//-----------------------------------------------------Delete script-----------------------------------------------------

function excluirRot() {

    var rot_id = idr;

    $.ajax({
        url: '/deletarRoteiro',
        method: 'POST',
        data: {
            id: rot_id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {

            if (response.success) {
                
                document.getElementById(Rot).remove();
                document.getElementById(inirot).remove();

                const el = document.getElementById("rot_qtd");
                const el1 = document.getElementById("rot_qtd1");

                const valor = parseInt(el.innerHTML) - 1;
                
                el.innerHTML = valor.toString().padStart(2, '0');
                el1.innerHTML = valor.toString().padStart(2, '0');

                if(valor == 0) {
                    let blocksDiv = document.getElementById('your_blocks');
        
                    let emptyDiv = document.createElement('div');
                    emptyDiv.className = 'emptyItems fontcorpo';
                    emptyDiv.id = 'emptyBlocksMsg'; // Adds an ID for future control

                    let p = document.createElement('p');
                    p.innerText = 'Não há nenhum bloco de roteiro atribuído a você.';

                    emptyDiv.appendChild(p);
                    blocksDiv.appendChild(emptyDiv);

                    let recentBlocksDiv = document.getElementById('recent_blocks');
        
                    let emptyDiv1 = document.createElement('div');
                    emptyDiv1.className = 'EmptyCenter fontcorpo';
                    emptyDiv1.id = 'emptyRecentBlocksMsg'; // Adds an ID for future control

                    let p1 = document.createElement('p');
                    p1.innerText = 'Não há nenhum bloco de roteiro atribuído a você.';

                    emptyDiv1.appendChild(p1);
                    recentBlocksDiv.appendChild(emptyDiv1);
                }

                atualizarProgresso();

                if (document.getElementsByClassName('notification').length === 0) {
                    createNotfy("success", "Bloco de roteiro deletado com sucesso.");
                }

                closeExcluirRotModal();

            } else {
                
                if (document.getElementsByClassName('notification').length === 0) {
                    createNotfy("error", "Erro ao deletar bloco de roteiro.");
                }

                closeExcluirRotModal();

            }
        },
        error: function(xhr, status, error) {
            console.log(xhr, status, error);
            if (document.getElementsByClassName('notification').length === 0) {
                createNotfy("error", "Erro ao deletar bloco de roteiro.");

                modalDeleteRotFade.style.pointerEvents = "none";
                modalDeleteRotFade.style.backgroundColor = "rgb(0, 0, 0, 0)";
                modalDeleteRot.style.transform = "translateY(20vh)";
                modalDeleteRot.style.opacity = 0;

                idp = null;
                proj = null;
            }
        }
    });
}