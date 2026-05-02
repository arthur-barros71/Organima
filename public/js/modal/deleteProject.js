//-----------------------------------------------------Delete project modal-----------------------------------------------------

var modalDeleteFade = document.getElementById("ModalDeleteProjFade");
var modalDelete = document.getElementById("ModalDeleteProj");

var idp;
var proj;
var iniproj;

function excluirProjModal(idpr, projt, iniprojt) {
    modalDeleteFade.style.pointerEvents = "all";
    modalDeleteFade.style.backgroundColor = "rgb(0, 0, 0, 0.2)";
    modalDelete.style.transform = "translateY(0)";
    modalDelete.style.opacity = 1;

    idp = idpr;
    proj = projt;
    iniproj = iniprojt;

    hideProprierties1();
}

function closeExcluirProjModal() {
    modalDeleteFade.style.pointerEvents = "none";
    modalDeleteFade.style.backgroundColor = "rgb(0, 0, 0, 0)";
    modalDelete.style.transform = "translateY(20vh)";
    modalDelete.style.opacity = 0;

    idp = null;
    proj = null;
    iniproj = null
}

//-----------------------------------------------------Delete project-----------------------------------------------------
function excluirProj() {

    var proj_id = idp;

    $.ajax({
        url: '/deletarProjeto',
        method: 'POST',
        data: {
            id_projeto: proj_id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {

            if (response.success) {
                
                document.getElementById(proj).remove();
                document.getElementById(iniproj).remove();

                const el = document.getElementById("proj_qtd");
                const valor = parseInt(el.innerHTML) - 1;
                el.innerHTML = valor.toString().padStart(2, '0');

                const el1 = document.getElementById("proj_qtd1");
                const valor1 = parseInt(el.innerHTML) - 1;
                el1.innerHTML = valor1.toString().padStart(2, '0');

                if(valor == 0) {
                    let yourProjsDiv = document.getElementById('your_projs');
        
                    let emptyDiv = document.createElement('div');
                    emptyDiv.className = 'emptyItems fontcorpo';
                    emptyDiv.id = 'emptyProjectsMsg'; // Adds an ID for future control

                    let p = document.createElement('p');
                    p.innerText = 'Não há nenhum projeto atribuído a você.';

                    emptyDiv.appendChild(p);
                    yourProjsDiv.appendChild(emptyDiv);

                    let recentProjsDiv = document.getElementById('recent_projs');
        
                    let emptyDiv1 = document.createElement('div');
                    emptyDiv1.className = 'EmptyCenter fontcorpo';
                    emptyDiv1.id = 'emptyRecentProjectsMsg'; // Adds an ID for future control

                    let p1 = document.createElement('p');
                    p1.innerText = 'Não há nenhum projeto atribuído a você';

                    emptyDiv1.appendChild(p1);
                    recentProjsDiv.appendChild(emptyDiv1);
                }

                atualizarProgresso();

                if (document.getElementsByClassName('notification').length === 0) {
                    createNotfy("success", "Projeto deletado com sucesso.");
                }

                closeExcluirProjModal();

            } else {
                
                if (document.getElementsByClassName('notification').length === 0) {
                    createNotfy("error", "Erro ao deletar projeto.");
                }

                closeExcluirProjModal();

            }
        },
        error: function(xhr, status, error) {
            console.log(xhr, status, error);
            if (document.getElementsByClassName('notification').length === 0) {
                createNotfy("error", "Erro ao deletar projeto.");

                modalDeleteFade.style.pointerEvents = "none";
                modalDeleteFade.style.backgroundColor = "rgb(0, 0, 0, 0)";
                modalDelete.style.transform = "translateY(20vh)";
                modalDelete.style.opacity = 0;

                idp = null;
                proj = null;
            }
        }
    });
}