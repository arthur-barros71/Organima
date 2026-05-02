//-----------------------------------------------------Page functions-----------------------------------------------------

// Gets the bar from each page
var page1_bar = document.getElementById("page1_bar");
var page2_bar = document.getElementById("page2_bar");
var page3_bar = document.getElementById("page3_bar");

// Gets each page
var page1 = document.getElementById("page1");
var page2 = document.getElementById("page2");
var page3 = document.getElementById("page3");

document.addEventListener('DOMContentLoaded', function() {
    // Verifies if the variable already exists on sessionStorage
    let page = sessionStorage.getItem('page');  // Stores the 'page' value

    if (page) {
        // If it exists, use the stored value
        setPage(page);
    } else {
        // If it doesn't exist, use a default value
        page = '1';
        sessionStorage.setItem('page', page);  // Stores in sessionStorage
        setPage(page);
    }
});

// Sets the pages' start animation
setTimeout(function() {
    page1.style.transition = "all ease-in-out 0.5s";
    page2.style.transition = "all ease-in-out 0.5s";
    page3.style.transition = "all ease-in-out 0.5s";
    page1_bar.style.transition = "all linear 0.4s";
    page2_bar.style.transition = "all linear 0.4s";
    page3_bar.style.transition = "all linear 0.4s";
}, 600);

function changePage(pageNum) {
    var page = pageNum;
    sessionStorage.setItem('page', page); // Updates the value in sessionStorage
    setPage(page);
}

// Gets the page icons' paths from the sideBar
const homePaths = document.querySelectorAll('#homeIcon path');
const projPaths = document.querySelectorAll('#projIcon path');
const rotPaths = document.querySelectorAll('#rotIcon path');

var isChangingPage = false; // Variable to know if the page is currently being changed

// Changes the page with the animation
function setPage(pageNum) {

    if(isChangingPage) {
        return;
    }
    isChangingPage = true;

    document.querySelectorAll('.search_bar').forEach(input => {
        input.value = '';
    });

    document.getElementById('noResultRot').style.display = "none";
    document.getElementById('noResultProj').style.display = "none";

    const emptyItems = document.querySelectorAll('.emptyItems');

    if (emptyItems.length > 0) {
        emptyItems.forEach(item => {
            item.style.display = "flex";
        });
    }

    page1.classList.add("PageTransition");
    page2.classList.add("PageTransition");
    page3.classList.add("PageTransition");

    page1_bar.classList.remove("PageLineSelected");
    page2_bar.classList.remove("PageLineSelected");
    page3_bar.classList.remove("PageLineSelected");

    page1.classList.remove("PageActive");
    page2.classList.remove("PageActive");
    page3.classList.remove("PageActive");

    page1.classList.add("PageDesactive");
    page2.classList.add("PageDesactive");
    page3.classList.add("PageDesactive");

    homePaths.forEach(path => {
        path.setAttribute('fill', 'none');
        path.setAttribute('stroke', '#fdfdfd');
        path.setAttribute('stroke-width', '-3');
    });
      
    projPaths.forEach(path => {
        path.setAttribute('fill', 'none');
        path.setAttribute('stroke', '#fdfdfd');
        path.setAttribute('stroke-width', '-3');
    });
      
    rotPaths.forEach(path => {
        path.setAttribute('fill', 'none');
        path.setAttribute('stroke', '#fdfdfd');
        path.setAttribute('stroke-width', '-3');
    });      

    if (pageNum == 1) {
        page1_bar.classList.add("PageLineSelected");
        homePaths.forEach(path => {
            path.setAttribute('fill', '#fdfdfd');
            path.setAttribute('stroke', 'none');
            path.setAttribute('stroke-width', '0');
        });
    } else if (pageNum == 2) {
        page2_bar.classList.add("PageLineSelected");
        projPaths.forEach(path => {
            path.setAttribute('fill', '#fdfdfd');
            path.setAttribute('stroke', 'none');
            path.setAttribute('stroke-width', '0');
        });
    } else if (pageNum == 3) {
        page3_bar.classList.add("PageLineSelected");
        rotPaths.forEach(path => {
            path.setAttribute('fill', '#fdfdfd');
            path.setAttribute('stroke', 'none');
            path.setAttribute('stroke-width', '0');
        });
    }

    setTimeout(function() {
        if (pageNum == 1) {
            page1.classList.add("PageActive");
            page1.classList.remove("PageDesactive");
        } else if (pageNum == 2) {
            page2.classList.add("PageActive");
            page2.classList.remove("PageDesactive");
        } else if (pageNum == 3) {
            page3.classList.add("PageActive");
            page3.classList.remove("PageDesactive");
        }
        isChangingPage = false;
    }, 700);
}

//-----------------------------------------------------Project animations-----------------------------------------------------

// Create project's button animation
var create = document.getElementById("CreateBtn");
var createText = document.getElementById("CreateText");

function CreateBtnAnimation() {
    createText.classList.add("CreateTextAppear");
}

function CreateBtnAnimationOff() {
    createText.classList.remove("CreateTextAppear");
}

// Project's status animation
function AnimaStats(id) {
    var stats_cont = document.getElementById(id);
    if (stats_cont) {
        stats_cont.classList.add("proj_statActive");
    }
}

function AnimaOffStats(id) {
    var stats_cont = document.getElementById(id);
    if (stats_cont) {
        stats_cont.classList.remove("proj_statActive");
    }
}

// Stops the use of 'enter'
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("form").forEach(form => {
        form.addEventListener("submit", function (event) {
            if (document.activeElement.tagName === "INPUT") {
                event.preventDefault(); // Bloqueia envio pelo Enter
            }
        });
    });
});

//-----------------------------------------------------Script animations-----------------------------------------------------

// Create script's button animation
var createR = document.getElementById("CreateRotBtn");
var createRText = document.getElementById("CreateRotText");

function CreateRotBtnAnimation() {
    createRText.classList.add("CreateRotTextAppear");
}

function CreateRotBtnAnimationOff() {
    createRText.classList.remove("CreateRotTextAppear");
}

//-----------------------------------------------------Open project-----------------------------------------------------

function OpenProject(id) {
    sessionStorage.setItem('id_projeto', id); // Saves the project ID in sessionStorage
    
    // Sends the variable to the server by POST (Laravel)
    fetch('/guardarProjeto', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ id_projeto: id }) // Sending the project ID
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = "/abrirProjeto"; // Redirects the user to the project page
        } else {
            console.log("Erro ao salvar o ID do projeto:", data.message);
        }
    })
    .catch(error => {
        console.error('Erro de rede:', error);
    });
}

//-----------------------------------------------------Open script-----------------------------------------------------

function OpenScript(id) {
    sessionStorage.setItem('id_roteiro', id); // Saves the script ID in sessionStorage
    
    // Sends the variable to the server by POST (Laravel)
    fetch('/guardarRoteiro', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ id_roteiro: id }) // Sending the script ID
    })
    .then(response => response.json())
    .then(data => {

        if (data.success) {
            window.location.href = "/abrirRoteiro"; // Redirects the user to the script page
        } else {
            console.log("Erro ao salvar o ID do projeto:", data.message);
        }
    })
    .catch(error => {
        console.error('Erro de rede:', error);
    });
}

//-----------------------------------------------------Project proprierties modal-----------------------------------------------------

var modalPropsOpenned;
var modalPropsButtonSelected;

document.addEventListener('DOMContentLoaded', function() {   

    // Closes the modal if the user clicks outside it
    function hideProprierties(event) {
        if(modalPropsOpenned) {
            if (!modalPropsOpenned.contains(event.target) && !event.target.closest('.' + modalPropsButtonSelected)) {

                // Removes the class that activates the modal
                modalPropsOpenned.classList.remove("modal_proj_open_active");
            }
        }        
    }

    // Adds the click event that closes the modal
    document.addEventListener('click', hideProprierties);
});

// Function that closes the modal without the user's click
function hideProprierties1() {
    modalPropsOpenned.classList.remove("modal_proj_open_active");
}

// Function that opens the modal
function ShowProprierties(id, id_proj) {
    var modalProps = document.getElementById(id);
    modalPropsOpenned = modalProps;
    modalPropsButtonSelected = id_proj;
    modalProps.classList.add("modal_proj_open_active");
}

//-----------------------------------------------------Script proprierties modal-----------------------------------------------------

var modalRotPropsOpenned;
var modalRotPropsButtonSelected;

document.addEventListener('DOMContentLoaded', function() {   

    // Closes the modal if the user clicks outside it
    function hideRotProprierties(event) {
        if(modalRotPropsOpenned) {
            if (!modalRotPropsOpenned.contains(event.target) && !event.target.closest('.' + modalRotPropsButtonSelected)) {
                // Removes the class that activates the modal
                modalRotPropsOpenned.classList.remove("modal_proj_open_active");
            }
        }        
    }

    // Adds the click event that closes the modal
    document.addEventListener('click', hideRotProprierties);
});

// Function that closes the modal without the user's click
function hideRotProprierties1() {
    modalRotPropsOpenned.classList.remove("modal_proj_open_active");
}

// Function that opens the modal
function openModalRotProps(id, id_proj) {
    var modalRotProps = document.getElementById(id);
    modalRotPropsOpenned = modalRotProps;
    modalRotPropsButtonSelected = id_proj;
    modalRotProps.classList.add("modal_proj_open_active");
}

//-----------------------------------------------------Project and script visualization-----------------------------------------------------

var projVisu1 = document.getElementsByClassName("projini")[0];
var rotVisu = document.getElementsByClassName("")[0];
var isChanging1 = false;

// Object that stores the position of each carousel
var projPositions1 = {};

// Generic function to advance pages on any carousel
function PassRecentProjPage(id, projnum, retid, nextid, nextImg, prevImg) {
    var projCont1 = document.getElementById(id);
    var RetProj1 = document.getElementById(retid);
    var NextProj1 = document.getElementById(nextid);
    var nextImg1 = document.getElementById(nextImg);
    var prevImg1 = document.getElementById(prevImg);

    if (!projPositions1[id]) projPositions1[id] = 4; // Defines the initial position if it doesn't exist

    if (!isChanging1 && projPositions1[id] < projnum) {
        projCont1.scrollBy({ left: projVisu1.clientWidth, behavior: 'smooth' });
        projPositions1[id]++;

        RetProj1.classList.add("projGradientVisible");
        RetProj1.style.pointerEvents = "all";
        prevImg1.style.pointerEvents = "all";

        isChanging1 = true;
        setTimeout(() => isChanging1 = false, 700);

        if (projPositions1[id] == projnum) {
            NextProj1.classList.remove("projGradientVisible");
            NextProj1.style.pointerEvents = "none";
            nextImg1.style.pointerEvents = "none";
        }
    }
}

// Generic function to rewind pages on any carousel
function returnRecentProjPage(id, retid, nextid, nextImg, prevImg) {
    var projCont1 = document.getElementById(id);
    var RetProj1 = document.getElementById(retid);
    var NextProj1 = document.getElementById(nextid);
    var nextImg1 = document.getElementById(nextImg);
    var prevImg1 = document.getElementById(prevImg);

    if (!projPositions1[id]) projPositions1[id] = 4; // Defines the initial position if it doesn't exist

    if (!isChanging1 && projPositions1[id] > 4) {
        projCont1.scrollBy({ left: -projVisu1.clientWidth, behavior: 'smooth' });
        projPositions1[id]--;

        isChanging1 = true;
        setTimeout(() => isChanging1 = false, 700);

        if (projPositions1[id] == 4) {
            RetProj1.classList.remove("projGradientVisible");
            RetProj1.style.pointerEvents = "none";
            prevImg1.style.pointerEvents = "none";
        }

        NextProj1.classList.add("projGradientVisible");
        NextProj1.style.pointerEvents = "all";
        nextImg1.style.pointerEvents = "all";
    }
}

// Function for filtering the projects
function filterProjects() {
    let input = document.getElementById('searchInput').value.toLowerCase();
    let projects = document.getElementsByClassName('proj');

    let visibleCount = 0;

    for (let i = 0; i < projects.length; i++) {
        let projectName = projects[i].getElementsByClassName('projName')[0].innerText.toLowerCase();

        if (projectName.includes(input)) {
            projects[i].style.display = '';
            visibleCount++;
        } else {
            projects[i].style.display = 'none';
        }
    }

    const emptyItems = document.querySelectorAll('.emptyItems');

    if (visibleCount === 0 && input.length > 0) {
        document.getElementById('noResultProj').style.display = "flex";

        if (emptyItems.length > 0) {
            emptyItems.forEach(item => {
                item.style.display = "none";
            });
        }
    } 
    else {
        document.getElementById('noResultProj').style.display = "none";

        if (emptyItems.length > 0) {
            emptyItems.forEach(item => {
                item.style.display = "flex";
            });
        }
    }
}

// Function for filtering the scripts
function filterScripts() {
    let input = document.getElementById('searchInputRot').value.toLowerCase();
    let projects = document.getElementsByClassName('rot');

    let visibleCount = 0;

    for (let i = 0; i < projects.length; i++) {
        let projectName = projects[i].getElementsByClassName('rotName')[0].innerText.toLowerCase();

        if (projectName.includes(input)) {
            projects[i].style.display = '';
            visibleCount++;
        } else {
            projects[i].style.display = 'none';
        }
    }

    const emptyItems = document.querySelectorAll('.emptyItems');

    if (visibleCount === 0 && input.length > 0) {
        document.getElementById('noResultRot').style.display = "flex";

        if (emptyItems.length > 0) {
            emptyItems.forEach(item => {
                item.style.display = "none";
            });
        }
    } 
    else {
        document.getElementById('noResultRot').style.display = "none";

        if (emptyItems.length > 0) {
            emptyItems.forEach(item => {
                item.style.display = "flex";
            });
        }
    }
}

//-----------------------------------------------------Logout-----------------------------------------------------

function logout() {
    sessionStorage.removeItem('page');
    sessionStorage.removeItem('pageBegin');
    document.getElementById('logoutForm').submit();
}

//-----------------------------------------------------Update progress-----------------------------------------------------

function atualizarProgresso() {
    const itens = document.querySelectorAll('.progressoProjeto');

    let soma = 0;
    itens.forEach(el => {
        const texto = el.textContent.trim();
        const valor = parseFloat(texto.replace('%', '')) || 0;
        soma += valor;
    });

    const media = itens.length > 0 ? soma / itens.length : 0;

    let cor = '#FF0000';
    if (media >= 100) {
        cor = '#3ED582';
    } else if (media >= 45) {
        cor = '#D5B73E';
    }

    const mediaArredondada = Math.round(media * 100) / 100;

    const statusGeralEl = document.getElementById('statusGeral');
    statusGeralEl.textContent = mediaArredondada + '%';
    statusGeralEl.style.color = cor;

    const statusProjEl = document.getElementById('statusProj');
    statusProjEl.textContent = mediaArredondada + '%';
    statusProjEl.style.color = cor;
}