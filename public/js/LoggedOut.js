//----------------------------------------------------------Define initial page----------------------------------------------------------

document.addEventListener('DOMContentLoaded', function() {
    let pageBegin = sessionStorage.getItem('pageBegin');

    if (pageBegin) {
        ChangePageBegin(pageBegin);
    } else {
        pageBegin = '1';
        sessionStorage.setItem('pageBegin', pageBegin);
        ChangePageBegin(pageBegin);
    }
});

//----------------------------------------------------------Go to page----------------------------------------------------------

var LogPage = document.getElementById("login");
var CadPage = document.getElementById("cad");
var LogCad = document.querySelector(".center");
var BeginPage = document.querySelector(".centerBegin");

function ChangePageBegin(page, scroll) {
    pageBegin = page;
    sessionStorage.setItem('pageBegin', page);
    SetPageBegin(page);
    resetElements();

    // Verifies if the page state is already in the history before adding
    if (window.location.search !== '?page=' + page) {
        history.pushState({ pageBegin: page }, '', '?page=' + page);
    }

    // if there's no 'scroll' parameter, rolls until the top of '.centerbegin'
    if (scroll == null) {
        
        const centerBegin = document.querySelector('.centerBegin');

        if(centerBegin) {
            centerBegin.scrollTop = 0;
        }

    } else {
        const scrollElement = document.querySelector(scroll);

        if (scrollElement) {
            const centerBegin = document.querySelector('.centerBegin');

            const elementPosition = scrollElement.offsetTop;

            centerBegin.scrollTo({
                top: elementPosition - 150,
                behavior: 'smooth'
            });
        } else {
            console.warn("Elemento para scroll não encontrado:", scroll);
        }
    }
}

// Switch page animation
var BeginPageLine = document.getElementById("BeginPageLine");

if(BeginPageLine) {
    BeginPageLine.style.transition = "none";
}

setTimeout(function() {
    if(BeginPageLine) {
        BeginPageLine.style.transition = "all ease-in-out 0.3s";
    }    
}, 500);

// Configuring login and register
var register = document.getElementsByClassName("register")[0];
var logincont = document.getElementsByClassName("login")[0];
var imag = document.getElementById("image");

if(LogCad) {
    LogCad.style.display = "none";
}

if(BeginPage) {
    BeginPage.style.display = "none";
}

// Getting the page IDs
var PageIni = document.querySelector(".PageIni");
var PageSobreNos = document.querySelector(".PageSobreNos");
var PageContato = document.querySelector(".PageContato");

function SetPageBegin(page) {
    if(page == 1 || page == 2 || page == 3) {

        if(LogCad) {
            LogCad.style.display = "none";
        }

        if(BeginPage) {
            BeginPage.style.display = "flex";
        }      

        if (page == 1 && BeginPageLine && PageSobreNos) {
            BeginPageLine.style.transform = "translateX(0)";
            BeginPageLine.style.width = "28%";
            PageSobreNos.style.opacity = 0;
            PageContato.style.opacity = 0;

            setTimeout(function() {
                PageIni.style.display = "block";
                PageSobreNos.style.display = "none";
                PageContato.style.display = "none";

                setTimeout(function() {
                    PageIni.style.opacity = 100;
                }, 100);
                
            }, 500);

        }
        else if (page == 2) {
            BeginPageLine.style.transform = "translateX(182%)";
            BeginPageLine.style.width = "20%";
            PageIni.style.opacity = 0;
            PageContato.style.opacity = 0;

            setTimeout(function() {
                PageSobreNos.style.display = "block";
                PageIni.style.display = "none";
                PageContato.style.display = "none";

                setTimeout(function() {
                    PageSobreNos.style.opacity = 100;
                }, 100);
                
            }, 500);

        }
        else if (page == 3) {
            BeginPageLine.style.transform = "translateX(187%)";
            BeginPageLine.style.width = "35%";
            PageIni.style.opacity = 0;
            PageSobreNos.style.opacity = 0;

            setTimeout(function() {
                PageContato.style.display = "block";
                PageIni.style.display = "none";
                PageSobreNos.style.display = "none";

                setTimeout(function() {
                    PageContato.style.opacity = 100;
                }, 100);
                
            }, 500);

        }
    }
    else {
        LogCad.style.display = "block";
        BeginPage.style.display = "none";

        // Removes temporary transition
        register.style.transition = "none";
        logincont.style.transition = "none";

        if (page == 4) {
            register.style.transform = "translate(-50vw)";
            logincont.style.transform = "translate(50vw)";
            imag.style.transform = "translate(0vw)";

            setTimeout(function() {
                register.style.transition = "all ease-in-out 0.5s";
                logincont.style.transition = "all ease-in-out 0.5s";
            }, 500);
        }
        else if (page == 5) {
            register.style.transform = "translate(0)";
            logincont.style.transform = "translate(100vw)";
            imag.style.transform = "translate(52vw)";
            resetElements()

            setTimeout(function() {
                register.style.transition = "all ease-in-out 0.5s";
                logincont.style.transition = "all ease-in-out 0.5s";
            }, 500);
        }
    }
}

// Function that goes back to pageBegin when the user clicks on the return button of the browser
window.addEventListener('popstate', function(event) {
    if (event.state && event.state.pageBegin) {
        let pageBegin = event.state.pageBegin;
        SetPageBegin(pageBegin);
    }
});

//----------------------------------------------------------Page animations----------------------------------------------------------

// Scroll animations
document.addEventListener('DOMContentLoaded', () => {
    // List of selectors for the elements being observed
    const elementsToObserve = [
        '.logo-sobre',
        '.logo-sobre-nos',
        '.BigTitle',
        '.Text',
        '.TextIni',
        '.importVideoImg',
        '.errorIdentify',
        '.contQuali',
        '.SobreBigTitle',
        '.SobreText',
        '.Obj',
        '.BigTitleQuest',
        '.Quest',
        '.TextQuest',
        '.Quest1',
        '.TextQuest1',
        '.vis',
        '.val',
        '.ODS',
        '.vis1',
        '.val1',
        '.ContBigTitle',
        '.ContText',
        '.ContImg',
    ];

    // Callback of IntersectionObserver
    const handleIntersect = (entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('scrolled'); // Start animation
            }
        });
    };

    // Configuration of IntersectionObserver
    const observerOptions = {
        root: null, // Observes in relation to the viewport
        threshold: 0.3 // Activates when 30% of the element is visible
    };

    const observer = new IntersectionObserver(handleIntersect, observerOptions);

    // Observes each element present on the list
    elementsToObserve.forEach((selector) => {
        const element = document.querySelector(selector);
        if (element) {
            observer.observe(element);
        }
    });
});

// Function to return to the original state (before scrolling)
function resetElements() {
    const elementsToObserve = [
        '.logo-sobre',
        '.logo-sobre-nos',
        '.BigTitle',
        '.Text',
        '.importVideoImg',
        '.errorIdentify',
        '.contQuali',
        '.SobreBigTitle',
        '.SobreText',
        '.Obj',
        '.BigTitleQuest',
        '.Quest',
        '.TextQuest',
        '.Quest1',
        '.TextQuest1',
        '.vis',
        '.val',
        '.ODS',
        '.vis1',
        '.val1',
        '.ContBigTitle',
        '.ContText',
        '.ContImg',
    ];

    elementsToObserve.forEach((selector) => {
        const element = document.querySelector(selector);
        if (element) {
            element.classList.remove('scrolled'); // Removes the '.scrolled' class
        }
    });
}

// Header animation
document.addEventListener('DOMContentLoaded', function() {
    let lastScrollTop = 0;
    const header = document.querySelector('.header');
    const centerBegin = document.querySelector('.centerBegin');

    if(header) {
        header.style.boxShadow = "none"; 
    }

    if(centerBegin) {
        centerBegin.addEventListener('scroll', function() {
            let currentScroll = centerBegin.scrollTop;
    
            if (currentScroll < 20) {
                header.style.boxShadow = "none";
            } 
            else if (currentScroll > lastScrollTop) {
                header.classList.add('hiddenHeader');
                header.style.boxShadow = "0px 0.2vh 10px rgba(0, 0, 0, 0.1)";
            } 
            else if (currentScroll < lastScrollTop) {
                header.classList.remove('hiddenHeader');
            }
    
            // Updates the scroll position
            lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
        });
    }    
});

//----------------------------------------------------------Contact----------------------------------------------------------

function limpar() {
    var name = document.getElementById('name');
    var email = document.getElementById('email');
    var message = document.getElementById('message');
   
    name.value = ''; 
    email.value = ''; 
    message.value = '';
} 


// Function to send email
function EnviarEmail() {

    const email = document.getElementById("emailsend").value;
    const name = document.getElementById("namesend").value;
    const message = document.getElementById("messagesend").value;

    // Verify if the email has a valid format
    if (email !== "" && email !== null && regexEmail.test(email)) {
        // Send the AJAX request to the server
        $.ajax({
            url: '/contato',
            method: 'POST',
            data: {
                email: email,
                nome: name,
                mensagem: message,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (document.getElementsByClassName('notification').length === 0) {
                    createNotfy("success", "Email enviado com sucesso.");
                }
            },
            error: function(xhr, status, error) {
                console.log("Erro AJAX: ", xhr, status, error);
                createNotfy("error", "Erro ao enviar e-mail.");
            }
        });
    } else {
        console.log("Email inválido");
    }
}