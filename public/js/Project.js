//---------------------------------------Preventing the use of Enter-----------------------------------------

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

//----------------------------------------------------------Fullscreen----------------------------------------------------------

var fullscreen = 0;

function enterCustomFullscreen() {
    if (fullscreen == 1) {
        exitCustomFullscreen();
    }
    else {
        const wrapper = document.getElementById("frameWrapper");
        const visu = document.querySelector(".Visu");
        const player = document.getElementById("player");
        const playCenter = document.getElementById("playCenter");
    
        player.style.color = "white";
        player.style.backgroundColor = "black";
        playCenter.classList.add("playCenterFullscreen");
    
        wrapper.classList.add("fullscreen");
        visu.classList.add("fullscreen-mode");
    
        document.getElementById("frameWrapper").classList.add("fullscreen");
        document.querySelector(".Visu").classList.add("fullscreen-mode");
        document.getElementById("exitFullscreenBtn").style.display = "block";
        document.getElementById("exitFullscreenBtn").style.width = "3%";
    
        const container = document.getElementById('fullscreenContainer');
        container.classList.add('fullscreen');
        document.body.style.overflow = 'hidden';

        document.getElementById('playPrev').style.display = "none";
        document.getElementById('playNext').style.display = "none";

        document.getElementById('playLeft').style.display = "none";
        document.getElementById('playRight').style.fontSize = "15px";

        let input_row = document.getElementById('input-row');
        let play = document.getElementById('play');
        let Visu_Scroll = document.getElementById('Visu_Scroll');
        input_row.insertBefore(play, Visu_Scroll);

        let playRight = document.getElementById('playRight');
        let exitFullscreenBtn = document.getElementById('exitFullscreenBtn');
        input_row.insertBefore(playRight, exitFullscreenBtn);

        playRight.style.width = "13%";
        playRight.style.marginRight = "1%";
        playRight.style.color = "#FDFDFD";

        player.style.display = "none";

        play.style.width = "2%";
        play.style.filter = "invert()";
        play.style.cursor = "pointer";
        play.style.marginRight = "1%";
        play.style.marginLeft = "0";

        const style = document.createElement('style');
        style.innerHTML = `.mili {
            font-size: 10px;
        }`;
        document.head.appendChild(style);

        document.getElementById('visu_inputs').style.marginBottom = "1vh";
        document.getElementById('frameWrapper').style.marginTop = "7vh";
        document.getElementById('frameWrapper').style.marginBottom = "0";

        document.getElementById('proporcao').style.top = "3vh";
        document.getElementById('proporcao').style.left = "2%";
    
        fullscreen = 1;

        setProporcao(document.getElementById('proporcaoValor').textContent);
    }
}

function exitCustomFullscreen() {

    const wrapper = document.getElementById("frameWrapper");
    const visu = document.querySelector(".Visu");
    const player = document.getElementById("player");
    const playCenter = document.getElementById("playCenter");

    player.style.color = "black";
    player.style.backgroundColor = "white";
    playCenter.classList.remove("playCenterFullscreen");

    wrapper.classList.remove("fullscreen");
    visu.classList.remove("fullscreen-mode");

    document.getElementById("frameWrapper").classList.remove("fullscreen");
    document.querySelector(".Visu").classList.remove("fullscreen-mode");
    document.getElementById("exitFullscreenBtn").style.width = "4%";

    const container = document.getElementById('fullscreenContainer');
    container.classList.remove('fullscreen');
    document.body.style.overflow = 'auto';

    document.getElementById('playPrev').style.display = "block";
    document.getElementById('playNext').style.display = "block";

    document.getElementById('playLeft').style.display = "block";
    document.getElementById('playRight').style.fontSize = "20px";

    let playCenter1 = document.getElementById('playCenter');
    let play = document.getElementById('play');
    let playNext = document.getElementById('playNext');
    playCenter1.insertBefore(play, playNext);

    let playRight = document.getElementById('playRight');
    let exitFullscreenBtn = document.getElementById('exitFullscreenBtn');
    player.appendChild(playRight);

    playRight.style.width = "33.33%";
    playRight.style.marginRight = "0";
    playRight.style.color = "#14141b";

    player.style.display = "flex";

    play.style.width = "7%";
    play.style.filter = "none";
    play.style.cursor = "pointer";
    play.style.marginRight = "5%";
    play.style.marginLeft = "5%";

    document.getElementById('playNext').style.width = "7%";
    document.getElementById('playPrev').style.width = "7%";

    const style = document.createElement('style');
    style.innerHTML = `.mili {
        font-size: 17px;
    }`;
    document.head.appendChild(style);

    document.getElementById('visu_inputs').style.marginBottom = "0";
    document.getElementById('frameWrapper').style.marginTop = "3vh";
    document.getElementById('frameWrapper').style.marginBottom = "2vh";

    document.getElementById('proporcao').style.top = "12vh";
    document.getElementById('proporcao').style.left = "16%";

    fullscreen = 0;

    setProporcao(document.getElementById('proporcaoValor').textContent);
}

exitCustomFullscreen();

['fullscreenchange', 'webkitfullscreenchange', 'mozfullscreenchange', 'MSFullscreenChange'].forEach(eventType => {
    document.addEventListener(eventType, () => {
        const isFullscreen =
            document.fullscreenElement ||
            document.webkitFullscreenElement ||
            document.mozFullScreenElement ||
            document.msFullscreenElement;

        const proporcaoEl = document.getElementById('proporcaoValor');
        if (proporcaoEl) {
            const valor = proporcaoEl.textContent.trim();
            setProporcao(valor);
        } else {
            console.warn("Elemento proporcaoValor não encontrado.");
        }
    });
});

// Allows 'space' to play the animation/video and 'escape' to exit fullscreen
document.addEventListener('keydown', function(event) {

    if (event.code === 'Space') {
        if(fullscreen == 1) {
            playIcon();
        }
    }

    if (event.code === 'Escape') {
        if(fullscreen == 1) {
            exitCustomFullscreen();
        }
      }

  });

//----------------------------------------------------------Share button----------------------------------------------------------

let shareVisible = false; // global flag

function ShareAppear() {
    if (shareVisible) return; // Prevents repeated execution
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

//-------------------------------------------------------Cursor------------------------------------------------------------------

const cursor = document.querySelector(".cursor");
const frames = document.querySelector(".Frames");
var time = document.querySelector(".time");

let isDragging = false;
let offsetX = 0;
let scrollInterval = null;

// Function to adjust the cursor's initial position inside the frames
function setInitialPosition() {
    let framesRect = frames.getBoundingClientRect();
    let minX = framesRect.width * 0.165;
    cursor.style.left = minX + "px";
}

// Function that checks if the cursor is in its initial or final 5% to allow scrolling
function shouldScroll(e) {
    let framesRect = frames.getBoundingClientRect();
    let cursorRect = cursor.getBoundingClientRect();

    let minX = framesRect.left + framesRect.width * 0.05;
    let maxX = framesRect.left + framesRect.width * 0.95;

    // Checks if the cursor is inside the div's initial or final 5%
    return (e.clientX <= minX || e.clientX >= maxX);
}

// Function that determines which direction to scroll
function determineScrollDirection(e) {
    let framesRect = frames.getBoundingClientRect();

    let minX = framesRect.left + framesRect.width * 0.05; // 5% from the start of Frames
    let maxX = framesRect.left + framesRect.width * 0.95; // 95% of Frames width

    // If the cursor is below 5%, scroll left
    if (e.clientX <= minX) {
        return 'left';
    }

    // If the cursor is above 95%, scroll right
    if (e.clientX >= maxX) {
        return 'right';
    }

    return 'none';
}

// Function that moves the scroll of the divs
function moveScroll(direction) {
    // Selects the .audio_editor div
    const audioEditor = document.querySelector('.audio_editor');
    const timeView = document.getElementById('timeView');
    const timelineCena = document.getElementById('timelineWrapper');
    
    if (direction === 'left') {

        frames.scrollLeft -= 10; // Moves left
        time.scrollLeft -= 10;   // Moves the .time div left as well
        audioEditor.scrollLeft -= 10;

        if (audioEditor) {
            audioEditor.scrollLeft = document.getElementById('Frames').scrollLeft;
            timeView.scrollLeft -= 10;
            timelineCena.scrollLeft = document.getElementById('Frames').scrollLeft;
        }
    } else if (direction === 'right') {
        frames.scrollLeft += 10; // Moves right
        time.scrollLeft += 10;   // Moves the .time div right as well
        audioEditor.scrollLeft += 10;
        if (audioEditor) {
            audioEditor.scrollLeft = document.getElementById('Frames').scrollLeft;
            timeView.scrollLeft += 10;
            timelineCena.scrollLeft = document.getElementById('Frames').scrollLeft;
        }
    }
}

// Function that starts the scroll loop
function startScrollLoop(direction) {
    // If an interval already exists, don't start another one
    if (scrollInterval !== null) return;

    // Starts a continuous scroll loop
    scrollInterval = setInterval(() => {
        moveScroll(direction);
    }, 10); // Moves scroll every 10ms
}

// Function that stops the scroll loop
function stopScrollLoop() {
    clearInterval(scrollInterval);
    scrollInterval = null;
}

// Click event on the cursor to start dragging
cursor.addEventListener("mousedown", (e) => {
    isDragging = true;
    let cursorRect = cursor.getBoundingClientRect();
    offsetX = e.clientX - cursorRect.left;
    document.body.style.userSelect = "none"; // Prevents text selection
});

// Mouse move event to drag the cursor
document.addEventListener("mousemove", (e) => {
    if (!isDragging) return;

    if(play.src.endsWith("PlayerPause.svg")) {
        playIcon();
    }

    let framesRect = frames.getBoundingClientRect();
    let minX = framesRect.width * 0.1;
    let maxX = framesRect.width * 1.1;

    let newX = e.clientX - offsetX;

    // Ensures the cursor stays within the 10% and 90% bounds of Frames
    newX = Math.max(minX, Math.min(maxX, newX));

    cursor.style.left = newX + "px";

    // Checks the scroll direction
    let scrollDirection = determineScrollDirection(e);
    if (scrollDirection === 'left') {
        startScrollLoop('left');
    } else if (scrollDirection === 'right') {
        startScrollLoop('right');
    } else {
        stopScrollLoop(); // Stops scrolling if the cursor leaves the bounds
    }
});

// When the user releases the mouse button
document.addEventListener("mouseup", () => {
    isDragging = false;
    
    if(!isPlaying) {
        UpdateAudioPosition();
    }
    
    document.body.style.userSelect = ""; // Restores normal selection
    stopScrollLoop(); // Stops the loop when the mouse is released
});

// Ensures the cursor starts within bounds
window.addEventListener("load", setInitialPosition);

// Checks frame count
var frames_view = document.getElementById("Frames");

var quantidade = frames_view.children.length - 1;
if(quantidade == 0) {
    document.getElementById("export_vd_button").style.color = "#acacac";
    document.getElementById("export_vd_button").style.cursor = "default";
    document.getElementById("export_vd_button").removeAttribute("onclick");
}
document.getElementById("qtd_frames").textContent = quantidade;

var Visu_Scroll = document.getElementById("Visu_Scroll");
Visu_Scroll.max = quantidade;

// Checks selected frame
var frame = 0;

// Function to check overlap
function verificaSobreposicao(elemento1, elemento2) {
    const rect1 = elemento1.getBoundingClientRect();
    const rect2 = elemento2.getBoundingClientRect();

    return !(rect1.right < rect2.left || 
             rect1.left > rect2.right || 
             rect1.bottom < rect2.top || 
             rect1.top > rect2.bottom);
}

// Function to move the cursor and check overlap
document.addEventListener('mousemove', function(e) {
    const cursor = document.getElementById('cursor');
    const frames = document.querySelectorAll('.frame');

    // Checks overlap between the cursor and the frames
    frames.forEach(function(frame, index) {
        if (verificaSobreposicao(cursor, frame)) {
            frame = index + 1;

            setFrameAtual(frame);
        }
    });

});

//----------------------------------------------------------Frames----------------------------------------------------------

let shiftPressed = false;
let lastSelectedId = null;

document.addEventListener("keydown", function(event) {
    if (event.key === "Shift") {
        shiftPressed = true;
    }
});

document.addEventListener("keyup", function(event) {
    if (event.key === "Shift") {
        shiftPressed = false;
    }
});

// Function to select frames
let selectedFrames = []; // List of selected frames

function SelectFrame(frameId) {
    var frameSelect = document.getElementById(frameId);
    var other_frames = document.querySelectorAll(".frame");
    var cursor = document.querySelector(".cursor");
    var timeline = document.querySelector(".timeline");

    let idNumber = parseInt(frameId.replace("frame_", ""), 10);

    if (!shiftPressed || lastSelectedId === null) {
        let frames = document.querySelectorAll(".frame");
        frames.forEach(element => {
            element.style.boxShadow = "none";  // Removes the border and the shadow
        });

        let imgs = document.querySelectorAll(".frame img");
        imgs.forEach(element => {
            element.style.border = "none";
        });

        selectedFrames = [idNumber];
        lastSelectedId = idNumber;

        // Replacing the border for box-shadow
        frameSelect.style.boxShadow = "0 0 0 1px white, 0 0 0 4px #3E52D5";

    } else {
        let minId = Math.min(lastSelectedId, idNumber);
        let maxId = Math.max(lastSelectedId, idNumber);

        other_frames.forEach(element => {
            let elementId = parseInt(element.id.replace("frame_", ""), 10);
            if (!isNaN(elementId) && elementId >= minId && elementId <= maxId) {
                element.style.boxShadow = "0 0 0 1px white, 0 0 0 4px #3E52D5"; // Adds the external "border"
                if (!selectedFrames.includes(elementId)) {
                    selectedFrames.push(elementId);
                }
            } else {
                element.style.boxShadow = "none";  // Removes the external "border"
            }
        });
    }

    var rectFrame = frameSelect.getBoundingClientRect();
    var rectTimeline = timeline.getBoundingClientRect();
    var margemEsquerda = timeline.offsetWidth * 0.16;
    var novaPosicao = rectFrame.left - rectTimeline.left + margemEsquerda;
    cursor.style.left = novaPosicao + "px";

    const frameOptMargin = document.getElementById('frameOptMargin');
    if (frameOptMargin) {
        let marginOpt = document.getElementById('frameOpt').offsetWidth * 0.4;
        let posicaoOpt = novaPosicao - marginOpt;
        frameOptMargin.style.transition = "top 0.3s ease-in-out, opacity 0.3s ease-in-out";
        frameOptMargin.style.left = posicaoOpt + "px";
        frameOptMargin.style.top = "3vh";
        frameOptMargin.style.opacity = 1;
    }

    if (selectedFrames.length === 0) {
        document.getElementById('substBtn').style.filter = "brightness(0.5)";
        document.getElementById('substBtn').style.cursor = "default";

        document.getElementById('expBtn').style.filter = "brightness(0.5)";
        document.getElementById('expBtn').style.cursor = "default";

        document.getElementById('delBtn').style.filter = "brightness(0.5)";
        document.getElementById('delBtn').style.cursor = "default";

        document.getElementById('moveBtn').style.filter = "brightness(0.5)";
        document.getElementById('moveBtn').style.cursor = "default";
    }
    else if(selectedFrames.length == 1) {
        document.getElementById('substBtn').style.filter = "brightness(1)";
        document.getElementById('substBtn').style.cursor = "pointer";

        document.getElementById('expBtn').style.filter = "brightness(1)";
        document.getElementById('expBtn').style.cursor = "pointer";

        document.getElementById('delBtn').style.filter = "brightness(1)";
        document.getElementById('delBtn').style.cursor = "pointer";

        document.getElementById('moveBtn').style.filter = "brightness(1)";
        document.getElementById('moveBtn').style.cursor = "pointer";
    }
    else {
        document.getElementById('substBtn').style.filter = "brightness(0.5)";
        document.getElementById('substBtn').style.cursor = "default";

        document.getElementById('expBtn').style.filter = "brightness(1)";
        document.getElementById('expBtn').style.cursor = "pointer";

        document.getElementById('delBtn').style.filter = "brightness(1)";
        document.getElementById('delBtn').style.cursor = "pointer";

        document.getElementById('moveBtn').style.filter = "brightness(0.5)";
        document.getElementById('moveBtn').style.cursor = "default";
    }

    UpdateAudioPosition();
}

// Function to deselect all frames
function DeselectAllFrames() {
    let frames = document.querySelectorAll(".frame");
    frames.forEach(element => {
        element.style.boxShadow = "none";  // Removes the external "border"
        let img = element.querySelector("img");
        if (img) {
            img.style.border = "none"; // Removes the image's border
        }
    });

    selectedFrames = [];

    const frameOptMargin = document.getElementById('frameOptMargin');
    if (frameOptMargin) {
        frameOptMargin.style.transition = "top 0.3s ease-in-out, opacity 0.3s ease-in-out";
        frameOptMargin.style.top = "4vh";
        frameOptMargin.style.opacity = 0;
    }

    setTimeout(() => {
        document.getElementById('moveCont').style.display = "none";
        
        document.getElementById('moveIconFundo').style.fill = "#fdfdfd";
        document.getElementById('moveIconFundo').style.opacity = 0.26;
        document.getElementById('moveIcon').style.fill = "#616187";
    }, 300);
    
}

function showFrameBtns(id) {
    document.getElementById(id).style.filter = "brightness(0.9)";
}

function hideFramesBtns(id) {
    document.getElementById(id).style.filter = "brightness(1)";
}

document.addEventListener("mousedown", function(event) {

    if (event.button !== 0) return;
    if (event.target.closest(".frame")) return;
    if (event.target.closest("#frameOpt")) return;
    if (event.target.closest("#moveCont")) return;

    DeselectAllFrames();
});

function showMoverFrame() {
    if(selectedFrames.length !== 1) {
        return;
    }

    let cont = document.getElementById('moveCont');
    const frameOptMargin = document.getElementById('frameOptMargin');

    if(cont.style.display == "flex") {
        cont.style.display = "none";
        frameOptMargin.style.transition = "opacity 0.3s ease-in-out";
        frameOptMargin.style.top = "3vh";

        document.getElementById('moveIconFundo').style.fill = "#fdfdfd";
        document.getElementById('moveIconFundo').style.opacity = 0.26;
        document.getElementById('moveIcon').style.fill = "#616187";
    }
    else {
        cont.style.display = "flex";
        frameOptMargin.style.transition = "opacity 0.3s ease-in-out";
        frameOptMargin.style.top = "0.5vh";

        document.getElementById('moveFrameInput').value = Visu_Scroll.value;
        document.getElementById('moveFrameInput').max = Visu_Scroll.max;

        document.getElementById('moveIconFundo').style.fill = "#616187";
        document.getElementById('moveIconFundo').style.opacity = 1;
        document.getElementById('moveIcon').style.fill = "#fdfdfd";
    }
}

function moverFrame() {
    if (selectedFrames.length !== 1) {
        return;
    }

    const toFrameInput = document.getElementById('moveFrameInput');
    let toFrame = parseInt(toFrameInput.value, 10);
    const proj_id = document.getElementById("proj_id").textContent.trim();
    const frameAtual = selectedFrames[0];
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

    const maxFrame = parseInt(toFrameInput.max, 10);
    if (toFrame > maxFrame) {
        toFrame = maxFrame;
        toFrameInput.value = maxFrame;
    }

    if (frameAtual === toFrame) {
        return;
    }

    const formData = new FormData();
    formData.append("frameAtual", frameAtual);
    formData.append("toFrame", toFrame);

    fetch(`/moverFrame/${proj_id}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken
        },
        body: formData
    })
    .then(resp => {
        if (!resp.ok) {
            return resp.text().then(txt => { 
                throw new Error(txt || `Erro ${resp.status}`); 
            });
        }
        return resp.json();
    })
    .then(data => {
        if (data.success) {
            const start = Math.min(frameAtual, toFrame);
            const end   = Math.max(frameAtual, toFrame);

            for (let i = start; i <= end; i++) {
                const imgElem = document.getElementById(`frame_img_${i}`);
                if (imgElem) {
    
                    const pad = String(i - 1).padStart(4, '0');
                    const newUrl = `/storage/proj_${proj_id}/frame_${pad}.jpg?t=${Date.now()}`;
                    imgElem.src = newUrl;
                }
            }

            selectedFrames = [toFrame];

            setFrameAtual(end);
            UpdateFrame();

            DeselectAllFrames();

            setTimeout(() => {
                SelectFrame('frame_' + toFrameInput.value);
            }, 300);            
        }
    })
    .catch(error => {
        console.error("Erro ao mover frame:", error);
    });
}

document.addEventListener("DOMContentLoaded", function() {
    const frame = document.querySelector(".frame");
    const frameOpt = document.getElementById("frameOpt");

    if (frame && frameOpt) {
        frameOpt.style.width = frame.offsetWidth + "px";
        document.getElementById('moveCont').style.width = frame.offsetWidth + "px";
    }
});

// Delete frames
function deleteSelectedFrames() {
    if (selectedFrames.length === 0) {
        return;
    }

    var proj_id = document.getElementById("proj_id").textContent;

    fetch(`/deletarFrames/${proj_id}`, {
        method: "DELETE",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
        },
        body: JSON.stringify({ frames: selectedFrames })
    })
    .then(response => response.json())
    .then(data => {
        // Removes all the old frames
        let frameContainer = document.getElementById("Frames");
        frameContainer.innerHTML = "";

        // Inserts all the new frames in the correct order
        data.new_paths.forEach((path, index) => {
            let frameId = "frame_" + (index + 1);

            // Adds the frame's div
            let frameDiv = document.createElement("div");
            frameDiv.classList.add("frame");
            frameDiv.id = frameId;
            frameDiv.onclick = function() { SelectFrame(frameId); };

            // Adds the image inside the div
            let img = document.createElement("img");
            img.src = path + '?t=' + new Date().getTime(); // Adds an unique timestamp in the URL to avoid cache

            frameDiv.appendChild(img);
            frameContainer.appendChild(frameDiv); // Adds directly in the correct order
        });

        // Adds the image "AddFrame.svg" at the end of the div
        let addFrameImg = document.createElement("img");
        addFrameImg.src = "Image/AddFrame.svg";
        addFrameImg.onclick = importImages;
        frameContainer.appendChild(addFrameImg);

        Visu_Scroll.max = frames_view.children.length - 1;
        quantidade = frames_view.children.length - 1;
        UpdateFPS();

        selectedFrames = []; // Cleans the selection after deleting
        
        DeselectAllFrames();

    })
    .catch(error => console.error("Erro ao deletar frames:", error));
}

function exportSelectedFrames() {
    if (selectedFrames.length === 0) {
        return;
    }

    const proj_id = document.getElementById("proj_id").textContent.trim();
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    fetch(`/exportarFramesSelecionados/${proj_id}`, {
        method: "POST", 
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify({ frames: selectedFrames })
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error(text || `Erro ${response.status}`);
            });
        }
        return response.blob().then(blob => ({ blob, response }));
    })
    .then(({ blob, response }) => {

        let filename = "download";
        const contentDisp = response.headers.get("Content-Disposition");
        if (contentDisp) {
            const match = /filename="?([^"]+)"?/.exec(contentDisp);
            if (match && match[1]) {
                filename = match[1];
            }
        }

        // Adds a temporary URL to force the download
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    })
    .catch(error => {
        console.error("Erro ao exportar frames:", error);
    });
}

function substituirFrameInput() {
    if (selectedFrames.length !== 1) {
        return;
    }
    document.getElementById('substFrame').click();
}

function substituirFrame() {
    if (selectedFrames.length !== 1) {
        return;
    }

    const input = document.getElementById('substFrame');
    if (input.files.length === 0) {
        alert("Selecione uma imagem para substituir o frame.");
        return;
    }
    const file = input.files[0];

    const proj_id = document.getElementById("proj_id").textContent.trim();
    const frameId = selectedFrames[0];
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const formData = new FormData();
    formData.append("frame", file);
    formData.append("frames[]", frameId);

    fetch(`/substituirFrame/${proj_id}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken
        },
        body: formData
    })
    .then(resp => {
        if (!resp.ok) {
            return resp.text().then(txt => { 
                throw new Error(txt || `Erro ${resp.status}`); 
            });
        }
        return resp.json();
    })
    .then(data => {

        if (data.success) {
            const imgElem = document.getElementById(`frame_img_${frameId}`);
            if (imgElem) {
                imgElem.src = data.url + "?t=" + Date.now();
            }

            input.value = "";
        }
    })
    .catch(error => {
        console.error("Erro ao substituir frame:", error);
        input.value = "";
    });
}

document.addEventListener("keydown", function (event) {
    if (event.key === "Delete") {
        deleteSelectedFrames(); // Calls the delete function
    }
});

//----------------------------------------------------------FPS----------------------------------------------------------

// Update FPS
var maxTime = 0;

let debounceTimer = null;

window.onload = () => {
    UpdateFPS();
};

function UpdateFPS() {

    if(play.src.endsWith("PlayerPause.svg")) {
        playIcon();
    }

    var FPS = document.getElementById("FPS");
    var FPS_Select = document.getElementById("FPS_Select");
    var MaxTime = document.getElementById("MaxTime");

    FPS.textContent = FPS_Select.value;

    maxTime = quantidade / FPS_Select.value;

    var hours = Math.floor(maxTime / 3600);
    var minutes = Math.floor((maxTime % 3600) / 60);
    var seconds = Math.floor(maxTime % 60);
    var milseconds = Math.floor((maxTime % 1) * 100);

    hours = hours.toString().padStart(2, "0");
    minutes = minutes.toString().padStart(2, "0");
    seconds = seconds.toString().padStart(2, "0");
    milseconds = milseconds.toString().padStart(2, "0");

    MaxTime.innerHTML = hours + ":" + minutes + ":" + seconds + "," + "<span class='mili'>" + milseconds + "</span>";
    document.getElementById("ProjTime").innerHTML = hours + ":" + minutes + ":" + seconds + "," + "<span class='mili'>" + milseconds + "</span>";

    setFrameAtual(frame); // Updates normally

    if (debounceTimer) {
        clearTimeout(debounceTimer); // Cancels the previous timeout
    }

    debounceTimer = setTimeout(() => {
        var proj_id = document.getElementById("proj_id").textContent;

        fetch(`/atualizarFPS/${proj_id}`, {
            method: "PUT",
            headers: { 
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
            },
            body: JSON.stringify({ ds_fps: FPS_Select.value })
        })
        .then(response => response.json())
        .catch(error => console.error("Erro ao atualizar FPS:", error));
    }, 500); // The save only happens after 500s without changes

    setTimeValues();
}

function showOnion() {
    if(document.getElementById('isOnion').checked == true) {
        document.getElementById('onionConfig').style.height = "9vh";
        document.getElementById('onionConfig').style.borderTop = "2px solid #E3E3E3";
    }
    else {
        document.getElementById('onionConfig').style.height = "0";
        document.getElementById('onionConfig').style.borderTop = "none";
    }

    UpdateFrame();
}

const layersState = {
  prev3: false,
  prev2: false,
  prev1: true,
  post1: true,
  post2: false,
  post3: false,
};

function activeOnion(num, idNum, id) {
  const isPrevious = num < 0;
  const colorInputId = isPrevious ? 'prevFrameColor' : 'postFrameColor';
  const color = document.getElementById(colorInputId).value;
  const layerKey = `${isPrevious ? 'prev' : 'post'}${Math.abs(num)}`;

  // Toggle the state in layersState
  layersState[layerKey] = !layersState[layerKey];

  // Sync the corresponding global variable
  switch (layerKey) {
    case 'prev1':
      prev1Enabled = layersState.prev1;
      break;
    case 'prev2':
      prev2Enabled = layersState.prev2;
      break;
    case 'prev3':
      prev3Enabled = layersState.prev3;
      break;
    case 'post1':
      post1Enabled = layersState.post1;
      break;
    case 'post2':
      post2Enabled = layersState.post2;
      break;
    case 'post3':
      post3Enabled = layersState.post3;
      break;
  }

  const btn = document.getElementById(idNum);
  const bar = document.getElementById(id);

  if (layersState[layerKey]) {
    btn.style.backgroundColor = color;
    bar.style.backgroundColor = color;
  } else {
    btn.style.backgroundColor = '';
    bar.style.backgroundColor = '';
  }

  UpdateFrame();
}

function setOnionColor(which) {
    if(which == "prev") {
        prevFrameColor = document.getElementById('prevFrameColor').value;
    }
    else if(which == "post") {
        postFrameColor = document.getElementById('postFrameColor').value;
    }
    UpdateFrame();
}

let prevFrameColor = "#3ED582";
let postFrameColor = "#D53E41";
let prev1Enabled = true;
let prev2Enabled = false;
let prev3Enabled = false;
let post1Enabled = true;
let post2Enabled = false;
let post3Enabled = false;
const imageDataCache = {}

function carregarImagemComoCanvas(src) {
    return new Promise(resolve => {
        if (imageDataCache[src]) {
            resolve(imageDataCache[src])
            return
        }
        const img = new Image()
        img.crossOrigin = "anonymous"
        img.onload = () => {
            const temp = document.createElement("canvas")
            temp.width = img.width
            temp.height = img.height
            const tctx = temp.getContext("2d")
            tctx.drawImage(img, 0, 0)
            const imgData = tctx.getImageData(0, 0, img.width, img.height)
            imageDataCache[src] = { data: imgData, width: img.width, height: img.height }
            resolve(imageDataCache[src])
        }
        img.src = src
    })
}

function hexToRgb(hex) {
    const h = hex.replace("#", "")
    const bigint = parseInt(h.length === 3 ? h.split("").map(c => c + c).join("") : h, 16)
    return { r: (bigint >> 16) & 255, g: (bigint >> 8) & 255, b: bigint & 255 }
}

async function atualizarFramesComDiferenca(frameAtual) {
    const frameAtualEl = document.getElementById("frame_" + frameAtual)
    if (!frameAtualEl) return
    const imgAtualEl = frameAtualEl.querySelector("img")
    if (!imgAtualEl) return
    const srcAtual = imgAtualEl.src

    const frameVisu = document.getElementById("FrameVisu")
    frameVisu.src = srcAtual

    await new Promise(resolve => {
        if (frameVisu.complete && frameVisu.naturalWidth) {
            resolve()
        } else {
            frameVisu.onload = () => resolve()
        }
    })

    const wrapper = frameVisu.parentElement
    const wrapperRect = wrapper.getBoundingClientRect()
    const naturalW = frameVisu.naturalWidth
    const naturalH = frameVisu.naturalHeight
    const contW = wrapperRect.width
    const contH = wrapperRect.height
    const scale = Math.min(contW / naturalW, contH / naturalH)
    const dispW = naturalW * scale
    const dispH = naturalH * scale
    const offsetLeft = (contW - dispW) / 2
    const offsetTop = (contH - dispH) / 2

    document.querySelectorAll('.diffCanvas').forEach(canvas => {
        canvas.style.position = 'absolute'
        canvas.style.left = `${offsetLeft}px`
        canvas.style.top = `${offsetTop}px`
        canvas.style.width = `${dispW}px`
        canvas.style.height = `${dispH}px`
    })

    if (!document.getElementById("isOnion").checked) {
        document.querySelectorAll('.diffCanvas').forEach(c => c.style.display = 'none')
        return
    }

    let imgDataAtual
    try {
        imgDataAtual = await carregarImagemComoCanvas(srcAtual)
    } catch {
        return
    }

    const prevRgb = hexToRgb(prevFrameColor)
    const postRgb = hexToRgb(postFrameColor)

    for (let i = 1; i <= 3; i++) {
        const alpha = i === 1 ? 0.8 : i === 2 ? 0.5 : 0.2

        const anteriorEl = document.getElementById("frame_" + (frameAtual - i))
        const canvasPrev = document.getElementById("canvasPrev" + i)
        if (anteriorEl && anteriorEl.querySelector("img") && canvasPrev) {
            const srcAnterior = anteriorEl.querySelector("img").src
            let imgDataAnterior
            try {
                imgDataAnterior = await carregarImagemComoCanvas(srcAnterior)
            } catch {
                canvasPrev.style.display = 'none'
                continue
            }
            if ((i === 1 && prev1Enabled) || (i === 2 && prev2Enabled) || (i === 3 && prev3Enabled)) {
                desenharDiferenca(canvasPrev, imgDataAnterior, imgDataAtual, dispW, dispH, prevRgb, alpha)
            } else {
                canvasPrev.style.display = 'none'
            }
        } else if (canvasPrev) {
            canvasPrev.style.display = 'none'
        }

        const posteriorEl = document.getElementById("frame_" + (frameAtual + i))
        const canvasPost = document.getElementById("canvasPost" + i)
        if (posteriorEl && posteriorEl.querySelector("img") && canvasPost) {
            const srcPosterior = posteriorEl.querySelector("img").src
            let imgDataPosterior
            try {
                imgDataPosterior = await carregarImagemComoCanvas(srcPosterior)
            } catch {
                canvasPost.style.display = 'none'
                continue
            }
            if ((i === 1 && post1Enabled) || (i === 2 && post2Enabled) || (i === 3 && post3Enabled)) {
                desenharDiferenca(canvasPost, imgDataPosterior, imgDataAtual, dispW, dispH, postRgb, alpha)
            } else {
                canvasPost.style.display = 'none'
            }
        } else if (canvasPost) {
            canvasPost.style.display = 'none'
        }
    }
}

function desenharDiferenca(canvas, srcData1, srcData2, dispW, dispH, colorRgb, alpha) {
    const off = document.createElement("canvas")
    off.width = srcData1.width
    off.height = srcData1.height
    const octx = off.getContext("2d")
    const diffImage = octx.createImageData(srcData1.width, srcData1.height)
    const d1 = srcData1.data.data
    const d2 = srcData2.data.data
    const dd = diffImage.data
    const aByte = Math.floor(alpha * 255)
    for (let i = 0; i < dd.length; i += 4) {
        const aA = d1[i + 3], aB = d2[i + 3]
        if (aA === 0 && aB === 0) {
            dd[i + 3] = 0
            continue
        }
        const rA = d1[i], gA = d1[i + 1], bA = d1[i + 2]
        const rB = d2[i], gB = d2[i + 1], bB = d2[i + 2]
        const diffv = Math.abs(rA - rB) + Math.abs(gA - gB) + Math.abs(bA - bB)
        if (diffv > 50) {
            dd[i] = colorRgb.r
            dd[i + 1] = colorRgb.g
            dd[i + 2] = colorRgb.b
            dd[i + 3] = aByte
        } else {
            dd[i + 3] = 0
        }
    }
    octx.putImageData(diffImage, 0, 0)
    canvas.width = dispW
    canvas.height = dispH
    const ctx = canvas.getContext("2d")
    ctx.clearRect(0, 0, dispW, dispH)
    ctx.drawImage(off, 0, 0, off.width, off.height, 0, 0, dispW, dispH)
    canvas.style.display = 'block'
}

let AtualFrame

function setFrameAtual(frameAtual) {
    AtualFrame = frameAtual
    const frameCountEl = document.getElementById("frame_count")
    const timeEl = document.getElementById("time")
    const frameVisuEl = document.getElementById("FrameVisu")
    const visuScrollEl = document.getElementById("Visu_Scroll")
    const fpsSelect = document.getElementById("FPS_Select")

    frameCountEl.textContent = "Frame: " + frameAtual
    visuScrollEl.value = frameAtual

    const frameTime = frameAtual / parseFloat(fpsSelect.value)
    const hoursFrame = String(Math.floor(frameTime / 3600)).padStart(2, "00")
    const minutesFrame = String(Math.floor((frameTime % 3600) / 60)).padStart(2, "00")
    const secondsFrame = String(Math.floor(frameTime % 60)).padStart(2, "00")
    const milsecondsFrame = String(Math.floor((frameTime % 1) * 100)).padStart(2, "00")
    timeEl.innerHTML = `${hoursFrame}:${minutesFrame}:${secondsFrame},<span class='mili'>${milsecondsFrame}</span>`

    const frameElement = document.getElementById("frame_" + frameAtual)
    if (frameElement) {
        const imgElement = frameElement.querySelector("img")
        if (imgElement) {
            frameVisuEl.src = imgElement.src
            if (document.getElementById("isOnion").checked) {
                atualizarFramesComDiferenca(frameAtual)
            } else {
                document.querySelectorAll('.diffCanvas').forEach(c => c.style.display = 'none')
            }
        } else {
            document.querySelectorAll('.diffCanvas').forEach(c => c.style.display = 'none')
        }
    } else {
        frameVisuEl.src = "Image/FrameTeste.png"
        document.querySelectorAll('.diffCanvas').forEach(c => c.style.display = 'none')
    }

    document.querySelectorAll('.cenaLine').forEach(element => {
        const frameInicial = parseInt(element.dataset.frameInicial, 10)
        const frameFinal = parseInt(element.dataset.frameFinal, 10)
        element.style.width = (frameInicial <= frameAtual && frameFinal >= frameAtual) ? "100%" : "10%"
    })
}

function UpdateFrame() {
    setFrameAtual(Visu_Scroll.value);
    var frame = document.getElementById("frame_" + (Visu_Scroll.value));
    var cursor = document.querySelector(".cursor");
    var timeline = document.querySelector(".timeline");

    frame.scrollIntoView({
        block: 'nearest',     // Does not move vertically
        inline: 'center'      // Aligns the element to the center horizontally
    });

    // Gets the coordinates of the selected frame relative to the timeline
    var rectFrame = frame.getBoundingClientRect();
    var rectTimeline = timeline.getBoundingClientRect();

    // Calculates the correct position taking into account the 10% margin of the timeline
    var leftMargin = timeline.offsetWidth * 0.16; // 10% of the timeline width
    var newPosition = rectFrame.left - rectTimeline.left + leftMargin;

    // Moves the cursor to the center of the selected frame (X axis only)
    cursor.style.left = newPosition + "px";

    document.getElementById("FrameVisu").src = document.getElementById("frame_" + (Visu_Scroll.value)).querySelector("img").src;

    // Updates the .audio_editor div scroll to follow the timeline scroll
    var audioEditor = document.querySelector('.audio_editor');
    var timeView = document.getElementById('timeView');
    var timelineCena = document.getElementById("timelineWrapper");

    if (audioEditor) {
        audioEditor.scrollLeft = document.getElementById('Frames').scrollLeft;
        timeView.scrollLeft = document.getElementById('Frames').scrollLeft;
        timelineCena.scrollLeft = document.getElementById('Frames').scrollLeft;
    }
}

var play = document.getElementById("play");
var audioContext = new (window.AudioContext || window.webkitAudioContext)(); // Creates the audio context
var audioBuffer;  // Where the audio will be stored
var rangeInput = document.getElementById('Audio_Scroll');  // Reference to the range input
var sourceNode;  // The source node for audio playback
var isPlaying = false;  // Flag to control playback
var startTime = 0;  // Stores the time when playback started
var pauseTime = 0;  // Stores the pause time

const fps = parseInt(document.getElementById("FPS_Select").value, 10);

var audioElement = document.querySelector('.audio_player');  // Select the audio element

// Canvas for sound wave
var canvas = document.getElementById('waveCanvas');

// Function to hide the loading screen
function hideLoading() {
    document.getElementById('loading').style.display = 'none';
}

// Check if the canvas exists and, if so, wait for the drawWaveform function to finish
if (canvas) {
    var ctx = canvas.getContext('2d');

    // Load the audio when the DOM is ready
    loadAudio(audioElement.querySelector('source').src);  // Passes the src to the audio loading function
}

//----------------------------------------------------------Audio----------------------------------------------------------

// Load the audio
var timeAudio;

function loadAudio(url) {
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error loading audio: ${response.statusText}`);
            }
            return response.arrayBuffer();  // Gets the audio file as a buffer array
        })
        .then(data => audioContext.decodeAudioData(data))  // Decodes the audio
        .then(buffer => {
            audioBuffer = buffer;  // Stores the decoded audio
            timeAudio = audioBuffer.duration;  // Assigns the total audio duration to the variable
            setVolume(parseFloat(document.getElementById("Vol_Select").value))
            drawWaveform();  // Draws the sound wave
        })
        .catch(error => console.error(error));  // Displays errors in the console, if any
}

function playAudio() {
    if (!audioBuffer) {
        console.error("Audio not loaded.");
        return;
    }

    if (sourceNode) {
        return; // If already playing, do nothing
    }

    // Ensure the AudioContext is active
    if (audioContext.state === "suspended") {
        audioContext.resume().then(() => {
            startAudio();  // Calls the function that starts audio playback
        });
    } else {
        startAudio(); // If already active, start playback
    }
}

function startAudio() {
    let currentTime = audioContext.currentTime;

    sourceNode = audioContext.createBufferSource();
    sourceNode.buffer = audioBuffer;

    // Creating the gain node
    gainNode = audioContext.createGain();
    gainNode.gain.setValueAtTime(volume, currentTime);  // Applies volume at the start of playback

    sourceNode.connect(gainNode);
    gainNode.connect(audioContext.destination);

    let startOffset = pauseTime;

    startTime = currentTime;

    sourceNode.start(currentTime, startOffset);

    sourceNode.onended = function () {
        sourceNode = null;
    };
}

// Function to pause the audio
function pauseAudio() {
    if (!sourceNode) return;

    pauseTime += audioContext.currentTime - startTime;

    sourceNode.stop();
    sourceNode = null;
}

// Updates the progress bar
function updateRange() {
    if (sourceNode && isPlaying) {
        let elapsed = audioContext.currentTime - startTime;
        let progress = (elapsed / audioBuffer.duration) * 100;
        rangeInput.value = Math.min(progress, rangeInput.max);
    }
}

function drawWaveform() {
    if (!audioBuffer) return;

    const fps = parseInt(document.getElementById("FPS_Select").value, 10);
    const frameElement = document.querySelector('.frame');
    if (!frameElement) return;

    const style = window.getComputedStyle(frameElement);
    const frameWidth = frameElement.getBoundingClientRect().width;
    const marginRight = parseFloat(style.marginRight) || 0;
    const effectiveFrameWidth = frameWidth + marginRight;

    const totalFrames = Math.floor(timeAudio * fps) + 1;
    const totalWidth = (totalFrames * effectiveFrameWidth) + marginRight;
    const framesWidth = effectiveFrameWidth * (quantidade + 1) + marginRight;

    const waveDiv = document.querySelector(".waveDiv");
    if (!waveDiv) return;
    waveDiv.style.width = `${totalWidth}px`;
    waveDiv.innerHTML = "";

    const container = document.querySelector(".audio_editor");
    if (!container) return;

    const rawData = audioBuffer.numberOfChannels > 0
        ? audioBuffer.getChannelData(0)
        : [];
    const audioCanvasWidth = ((totalFrames - 1) * effectiveFrameWidth - marginRight) - 40;
    if (audioCanvasWidth === 0 || rawData.length === 0) return;

    const samplesPerPixel = rawData.length / audioCanvasWidth;
    const SECTION_WIDTH = Math.min(audioCanvasWidth, 5000);
    const numSections = Math.ceil(audioCanvasWidth / SECTION_WIDTH);
    const frag = document.createDocumentFragment();
    const dpr = window.devicePixelRatio || 1;

    // Creates the div with the .CanvasBorder class
    const canvasWrapper = document.createElement("div");
    canvasWrapper.classList.add("CanvasBorder");  // Adds the .CanvasBorder class

    for (let s = 0; s < numSections; s++) {
        const startX   = s * SECTION_WIDTH;
        const sectionW = (s === numSections - 1) ? audioCanvasWidth - startX : SECTION_WIDTH;

        const canvas = document.createElement("canvas");
        const H      = 50;
        canvas.width  = sectionW * dpr;
        canvas.height = H * dpr;
        canvas.style.width  = `${sectionW}px`;
        canvas.style.height = `${H}px`;

        const ctx = canvas.getContext("2d");
        ctx.scale(dpr, dpr);
        ctx.clearRect(0, 0, sectionW, H);
        ctx.strokeStyle = '#3ED582';
        ctx.lineWidth   = 2;

        const centerY = H / 2;
        const scaleY  = centerY * 0.9;
        ctx.beginPath();

        for (let x = startX; x < startX + sectionW; x++) {
            const sampleIdx = Math.floor(x * samplesPerPixel);
            const sample    = rawData[Math.min(sampleIdx, rawData.length - 1)];
            const cx        = x - startX;
            const cy        = centerY + sample * scaleY;
            if (cx === 0) ctx.moveTo(cx, cy);
            else          ctx.lineTo(cx, cy);
        }
        ctx.stroke();

        // Adds each canvas inside the .CanvasBorder div
        canvasWrapper.appendChild(canvas);
    }

    // Adds the div with all canvases to waveDiv (after .CanvasBorder)
    waveDiv.appendChild(canvasWrapper);

    // Adds the extra div after the .CanvasBorder div
    const remainder = (framesWidth - audioCanvasWidth);
    if (remainder > 0) {
        const divExtra = document.createElement("div");
        divExtra.style.width   = `${remainder}px`;
        divExtra.style.height  = `${container.offsetHeight}px`;
        divExtra.style.display = "inline-block";
        waveDiv.appendChild(divExtra);  // Adds after .CanvasBorder
    }

    checkSize();
    hideLoading();
}

function checkSize() {
    // Gets the .frame element to calculate each frame's width
    const frameElement = document.querySelector('.frame');
    if (!frameElement) {
        console.error("Element with class .frame not found.");
        return;
    }

    // Gets the frame width and right margin
    const style = window.getComputedStyle(frameElement);
    const frameWidth = frameElement.getBoundingClientRect().width;
    const marginRight = parseFloat(style.marginRight) || 0;

    const effectiveFrameWidth = frameWidth + marginRight;  // Total width of each frame (including margin)

    const totalFrames = quantidade;
    const totalFramesWidth = totalFrames * effectiveFrameWidth;

    const totalAudio = Math.floor(timeAudio * fps);
    const totalWidth = totalAudio * effectiveFrameWidth;

    // Checks if the canvas size is larger than the frames
    if (totalWidth > totalFramesWidth) {
        const framesContainer = document.getElementById('Frames');
        if (framesContainer) {
            const diff = totalWidth - totalFramesWidth;  // Difference between the canvas and the frames

            // Creates an extra div and appends it to the #Frames container
            const extraDiv = document.createElement("div");
            extraDiv.style.width = `${diff}px`;  // The extra div width is the difference
            extraDiv.style.height = "100%";  // Default height of 100%
            extraDiv.style.backgroundColor = "transparent";  // Can be adjusted as needed
            extraDiv.style.flexShrink = "0";  // Ensures the div does not shrink

            framesContainer.appendChild(extraDiv);
        }
    }
}

function UpdateAudioPosition() {
    if (!audioBuffer) return;

    // Ensures the AudioContext is active
    if (audioContext.state === "suspended") {
        audioContext.resume();
    }

    // Adjusts the conversion of the scroll value to percentage
    let scrollRatio = (Visu_Scroll.value - Visu_Scroll.min) / (Visu_Scroll.max - Visu_Scroll.min);
    rangeInput.value = (scrollRatio * 100).toFixed(2); // Ensures precision

    // Corrects the time calculation
    let rangeValue = parseFloat(rangeInput.value);
    let newTime = (rangeValue / 100) * audioBuffer.duration;

    if (sourceNode) {
        sourceNode.stop();
        sourceNode.disconnect();
        sourceNode = null;
    }

    // Avoids negative times and ensures newTime stays within bounds
    pauseTime = Math.max(0, Math.min(audioBuffer.duration, newTime));
}

var volume = 1;  // Initial volume (1 means maximum volume)

// Function to change the audio volume
function setVolume(value) {

    document.getElementById("Volume").textContent = Math.round(document.getElementById("Vol_Select").value * 100);

    if(play.src.endsWith("PlayerPause.svg")) {
        playIcon();
    }

    volume = value;
    if (sourceNode && sourceNode.context.state === "running") {
        const currentTime = audioContext.currentTime;
        gainNode.gain.setValueAtTime(volume, currentTime);
    }

    debounceTimer = setTimeout(() => {
        var proj_id = document.getElementById("proj_id").textContent;

        fetch(`/atualizarVolume/${proj_id}`, {
            method: "PUT",
            headers: { 
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
            },
            body: JSON.stringify({ qt_volume: document.getElementById("Vol_Select").value })
        })
        .then(response => response.json())
        .catch(error => console.error("Error updating Volume:", error));
    }, 500); // Saving only occurs after 500ms without changes
}

document.getElementById("Vol_Select").addEventListener("input", function(event) {
    setVolume(event.target.value);
});

// Updates the input range at each interval
setInterval(updateRange, 100);  // Updates the range every 100ms

if(canvas) {
    // Adds play/pause when the control button is clicked
    audioElement.addEventListener('play', playAudio);
    audioElement.addEventListener('pause', pauseAudio);
}

function onAudioLoaded() {
    UpdateAudioPosition();
    updateRange();
    drawWaveform();
}

//----------------------------------------------------------Video----------------------------------------------------------

var playInterval = null; // Stores the active setInterval

function playIcon() {

    if(quantidade > 0) {

        if (play.src.endsWith("PlayerPlay.svg")) {

            if(Visu_Scroll.value == quantidade) {
                playBack();
                UpdateAudioPosition();
                play.src = "Image/PlayerPause.svg";
                playView();
                playAudio();
                isPlaying = true;
            }
            else {
                play.src = "Image/PlayerPause.svg";
                playView();
                playAudio();
                isPlaying = true;
            }
            
        } else if (play.src.endsWith("PlayerPause.svg")) {
            play.src = "Image/PlayerPlay.svg";
            clearInterval(playInterval);
            playInterval = null;
            pauseAudio();
            isPlaying = false;
        }

    }
    
}

document.addEventListener('DOMContentLoaded', function() {

    if(canvas) {
        var audioSrc = audioElement.querySelector('source').src;  // Gets the src from the <source> inside <audio>
        loadAudio(audioSrc);  // Passes the src to the loadAudio function

        document.getElementById("Volume").textContent = Math.round(document.getElementById("Vol_Select").value * 100);
    }    
});

var FPS_Select1 = document.getElementById("FPS_Select");
var timePerFrame

function handleInput() {
    if(play.src.endsWith("PlayerPause.svg")) {
        playIcon();
    }
    UpdateAudioPosition();  // Calls the function to control the audio position
}

function calcularTempoPerFrame(qtd, time) {
    timePerFrame = (time / qtd) * 1000;
}

let lastTime = 0;
let accumulatedTime = 0;
let playing = false;

function playView() {
    
    if (playing) return; // Prevents multiple executions
    playing = true;
    lastTime = performance.now();
    
    function updateFrameTime(time) {
        if (!play.src.endsWith("PlayerPause.svg")) { 
            playing = false;
            return;
        }

        let deltaTime = time - lastTime; // Time since the last call
        lastTime = time;
        accumulatedTime += deltaTime;

        let timePerFrame = 1000 / parseFloat(FPS_Select1.value); // Time per frame
        let framesToAdvance = Math.floor(accumulatedTime / timePerFrame);
        
        if (framesToAdvance > 0) {
            let currentValue = Number(Visu_Scroll.value);
            let nextValue = Math.min(currentValue + framesToAdvance, quantidade);

            Visu_Scroll.value = nextValue;
            setFrameAtual(nextValue);
            UpdateFrame();

            accumulatedTime %= timePerFrame; // Keeps the residual time
        }

        if (Visu_Scroll.value < quantidade) {

            requestAnimationFrame(updateFrameTime);

        } else {

            if(document.getElementById("isRepeat").checked == false) {
                playing = false;
                playIcon();
            }
            else {

                playing = false;

                if(quantidade > 0) {
                    repeat();  
                }                            

            }
            
        }
    }

    requestAnimationFrame(updateFrameTime);
}

function setTimeValues() {
    var time_views = document.querySelectorAll(".time_view");

    if (time_views.length === 0) {
        console.warn("No .time_view found.");
        return;
    }

    var fps = parseFloat(document.getElementById("FPS_Select").value);
    if (isNaN(fps) || fps <= 0) {
        console.error("Invalid FPS.");
        return;
    }

    time_views.forEach(time_view => {
        let index = parseInt(time_view.getAttribute("index"), 10);
        if (isNaN(index)) {
            console.error("Invalid index for a .time_view:", time_view);
            return;
        }
    
        let timeInSeconds = index / fps;
        let isWholeSecond = Number.isInteger(timeInSeconds);
    
        let hours = Math.floor(timeInSeconds / 3600);
        let minutes = Math.floor((timeInSeconds % 3600) / 60);
        let seconds = Math.floor(timeInSeconds % 60);
        let milliseconds = Math.round((timeInSeconds % 1) * 1000);
    
        let timeElement = time_view.querySelector("p");
        if (timeElement) {
            if (isWholeSecond) {
                // If it's a whole number, show only hh:mm:ss
                timeElement.textContent = `${String(hours).padStart(2, "0")}:${String(minutes).padStart(2, "0")}:${String(seconds).padStart(2, "0")}`;
                timeElement.style.fontSize = "inherit";
                timeElement.style.color = "inherit";
            } else {
                // If it's not a whole number, show only the milliseconds
                timeElement.textContent = `.${String(milliseconds).padStart(3, "0")}`;
                timeElement.style.fontSize = "12px";
                timeElement.style.color = "#8C8C94";
            }
            timeElement.style.visibility = "visible";
        } else {
            console.warn("No <p> element found inside .time_view.");
        }
    });
}

function playEnd() {

    if(quantidade > 0) {
        if(play.src.endsWith("PlayerPause.svg")) {
            playIcon();
        }

        setFrameAtual(quantidade);
        UpdateFrame();
    }    
} 

function playBack() {

    if(quantidade > 0) {
        if(play.src.endsWith("PlayerPause.svg")) {
            playIcon();
        }

        setFrameAtual(1);
        UpdateFrame();
    }
    
} 

function repeat() {

    if(quantidade > 0) {

        let fps = parseInt(document.getElementById('Visu_Scroll').value);
        let tempo = 1000 / fps;
        playBack();
        document.getElementById("FrameVisu").src = document.getElementById("frame_" + (quantidade)).querySelector("img").src;
        UpdateAudioPosition();

        setTimeout(() => {
            document.getElementById("FrameVisu").src = document.getElementById("frame_" + (1)).querySelector("img").src;
            playIcon(); 
        }, tempo);

    }    
}

function showFrameNum() {
    let check = document.getElementById('isShowNum').checked;

    if(check == true) {
        document.querySelectorAll('.frameInfo').forEach(function(element) {
            element.style.opacity = 1;
        });  
    }
    else {
        document.querySelectorAll('.frameInfo').forEach(function(element) {
            element.style.opacity = 0;
        });
    }
}

//---------------------------------------Video proportion-----------------------------------------

// Change video proportion
function openProporcaoModal() {
    
    let proporcao = document.getElementById('proporcaoContainer');
    let img = document.getElementById('proporcaoImg');

    if(proporcao.style.display == "none") {
        proporcao.style.display = "block";
        img.src = "Image/SetaProporcaoAberta.svg"
    }
    else {
        proporcao.style.display = "none";
        img.src = "Image/SetaProporcao.svg"
    }

}

function setProporcao(prop) {

    if(prop == "16:9") {
        document.getElementById('proporcaoIcon').src = "Image/16_9.svg";

        const frameWrapper = document.getElementById('frameWrapper');
        const height = parseFloat(window.getComputedStyle(frameWrapper).height);  // Use getComputedStyle to get the actual height
        const width = (16 / 9) * height;
        frameWrapper.style.width = width + "px";
    }
    else if(prop == "16:10") {
        document.getElementById('proporcaoIcon').src = "Image/16_10.svg";

        const frameWrapper = document.getElementById('frameWrapper');
        const height = parseFloat(window.getComputedStyle(frameWrapper).height);  // Use getComputedStyle to get the actual height
        const width = (16 / 10) * height;
        frameWrapper.style.width = width + "px";
    }
    else if(prop == "9:16") {
        document.getElementById('proporcaoIcon').src = "Image/9_16.svg";

        const frameWrapper = document.getElementById('frameWrapper');
        const height = parseFloat(window.getComputedStyle(frameWrapper).height);  // Use getComputedStyle to get the actual height
        const width = (9 / 16) * height;
        frameWrapper.style.width = width + "px";
    }

}

function changeProporcao(prop) {

    document.getElementById("16:9").style.opacity = 0.3;
    document.getElementById("16:10").style.opacity = 0.3;
    document.getElementById("9:16").style.opacity = 0.3;

    document.getElementById(prop).style.opacity = 1;

    document.getElementById('proporcaoValor').textContent = prop;

    setProporcao(prop);

    const projetoId = document.getElementById("proj_id").textContent.trim();

    fetch(`/salvarProporcao/${projetoId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            nm_proporcao: prop
        })
    })
    .then(response => response.json().then(data => ({ ok: response.ok, data })))
    .then(({ ok, data }) => {
        if (ok && data.success) {
            
        } else {
            if (document.getElementsByClassName('notification').length === 0) {
                createNotfy("error", data.message || "Error saving video proportion.");
            }
        }
    })
    .catch(error => {
        console.error('Network error:', error);
        createNotfy("error", "Network error while saving video proportion.");
    });
}

setProporcao(document.getElementById('proporcaoValor').textContent);

//--------------------------------------------------Project options-------------------------------------------------------

var propsVideo = document.getElementById("videoProps");
var propsAudio = document.getElementById("audioProps");

var videoPropsBtn = document.getElementById("videoPropsBtn");
var audioPropsBtn = document.getElementById("audioPropsBtn");

function showPropsVideo() {
    propsVideo.style.display = "block";
    propsAudio.style.display = "none";

    videoPropsBtn.style.backgroundColor = "#3E52D5";
    videoPropsBtn.style.color = "#FDFDFD";
    audioPropsBtn.style.backgroundColor = "#FDFDFD";
    audioPropsBtn.style.color = "black";
}

function showPropsAudio() {
    propsVideo.style.display = "none";
    propsAudio.style.display = "block";

    videoPropsBtn.style.backgroundColor = "#FDFDFD";
    videoPropsBtn.style.color = "black";
    audioPropsBtn.style.backgroundColor = "#3E52D5";
    audioPropsBtn.style.color = "#FDFDFD";
}

function showPropsColor() {
    propsVideo.style.display = "none";
    propsAudio.style.display = "none";

    videoPropsBtn.style.backgroundColor = "#FDFDFD";
    videoPropsBtn.style.color = "black";
    audioPropsBtn.style.backgroundColor = "#FDFDFD";
    audioPropsBtn.style.color = "black";
}

showPropsVideo();

var retic = document.getElementById("retic");
let stepRetic = 0;
let reticInterval = null;

function startReticLoop() {
    if (reticInterval) return; // Prevents multiple loops from running

    reticInterval = setInterval(() => {
        stepRetic++;

        if (stepRetic == 1) {
            retic.textContent = ".";
        } else if (stepRetic == 2) {
            retic.textContent = "..";
        } else if (stepRetic == 3) {
            retic.textContent = "...";
            stepRetic = 0;
        }
    }, 500);
}

function stopReticLoop() {
    clearInterval(reticInterval);
    reticInterval = null;
    retic.textContent = "";
}

//-------------------------------------------------------Loading screen---------------------------------------------------------

// Check when the page and all resources are loaded
window.addEventListener('load', function() {
    const canvas = document.getElementById('waveCanvas');
    
    // Check if the canvas exists
    if (canvas) {
        return;
    } else {
        document.getElementById('loading').style.display = 'none';       
    }
});

//----------------------------------------------------------Notification----------------------------------------------------------

// Function to display notifications
function showNotification(type, message) {
    if (document.getElementsByClassName('notification').length === 0) {
        createNotfy(type, message);
    }
}

//----------------------------------------------------------Timeline and summary----------------------------------------------------------

// Função para atualizar a timeline e o sumário lateral
function updateTimelineAndSummary(nm_cena, frameInicial, frameFinal) {
    const frames = document.querySelectorAll('.frame');
    const frame = frames[0];
    if (!frame) return;

    const styles = window.getComputedStyle(frame);
    const width = parseFloat(styles.width);
    const marginLeft = parseFloat(styles.marginLeft);
    const marginRight = parseFloat(styles.marginRight);
    const totalFrameWidth = width + marginLeft + marginRight;

    // Criando a cena na timeline
    const quant = parseInt(frameFinal) - parseInt(frameInicial);
    const inicio = parseInt(frameInicial);
    const totalWidth = (totalFrameWidth * (quant + 1)) - marginRight;
    const totalLeft = totalFrameWidth * (inicio - 1) - marginLeft;

    let newCena = document.createElement('div');
    newCena.classList.add('cenaTime');
    newCena.style.backgroundColor = document.getElementById('ds_cor').value;
    newCena.setAttribute('quant', quant);
    newCena.setAttribute('inicio', inicio);
    newCena.style.width = `${totalWidth}px`;
    newCena.style.left = `${totalLeft}px`;

    document.getElementById('timelineCena').appendChild(newCena);

    //--------------------------------Sumário lateral------------------------------------
    // Cria o elemento da cena
    const newCenaLat = document.createElement('div');
    newCenaLat.classList.add('cena');

    // Cria o título da cena
    const newCenaTitle = document.createElement('p');
    newCenaTitle.classList.add('cena_title');
    newCenaTitle.textContent = nm_cena; // Define o nome da cena

    // Cria o progresso da cena
    const newCenaProgress = document.createElement('p');
    newCenaProgress.classList.add('cena_progress');
    newCenaProgress.innerHTML = `Progresso: <span class="cena_percent">0%</span>`; // Define o progresso

    // Adiciona o título e o progresso à nova cena
    newCenaLat.appendChild(newCenaTitle);
    newCenaLat.appendChild(newCenaProgress);

    newCenaLat.onclick = function() {
        gotoCena(inicio);  // A função gotoCena será chamada com o parâmetro 'inicio'
    };

    // Encontra o container onde as cenas serão adicionadas
    const summaryDiv = document.getElementById('Summary');

    summaryDiv.insertBefore(newCenaLat, document.getElementById('add_cena'));
}

const frameInicial = document.getElementById('frameInicial');
const frameFinal = document.getElementById('frameFinal');

let usuarioDigitandoFrameFinal = false;

// Detecta quando o usuário está digitando manualmente
frameFinal.addEventListener('focus', () => {
    usuarioDigitandoFrameFinal = true;
});

frameFinal.addEventListener('blur', () => {
    usuarioDigitandoFrameFinal = false;
    ajustarFrameFinal();
});

// Atualiza automaticamente o frame final (se o usuário não estiver digitando)
frameInicial.addEventListener('input', ajustarFrameFinal);

function ajustarFrameFinal() {
    const inicio = parseInt(frameInicial.value);
    const fimAtual = parseInt(frameFinal.value);

    if (!isNaN(inicio) && !usuarioDigitandoFrameFinal) {
        const novoFinal = inicio + 1;
        if (isNaN(fimAtual) || fimAtual <= inicio) {
            frameFinal.value = novoFinal;
        }
    }
}

//----------------------------------------------------------Scene----------------------------------------------------------

// Show scene toggle button
function showInfoCena(id, btn) {
    let showBtn = document.getElementById(id);
    let seta = document.getElementById(btn);

    if(showBtn.style.height != "2.2vh") {
        showBtn.style.height = "2.2vh";
        showBtn.style.marginTop = "0.7vh";

        seta.src = "Image/cena_setaShow.svg";
    }
    else {
        showBtn.style.height = "0vh";
        showBtn.style.marginTop = "0vh";

        seta.src = "Image/cena_seta.svg";
    }   

}

var ligarCena;

function openModalLigarCena(id) {
    ligarCena = id;

    document.getElementById('modalLigarCenaFade').style.backgroundColor = "rgb(0, 0, 0, 0.2)";
    document.getElementById('modalLigarCenaFade').style.pointerEvents = "all";

    document.getElementById('modalLigarCena').style.opacity = 1;
    document.getElementById('modalLigarCena').style.transform = 'translateY(0vh)';
}

function CloseModalLigarCena() {
    ligarCena = null;

    document.getElementById('modalLigarCenaFade').style.backgroundColor = "rgb(0, 0, 0, 0)";
    document.getElementById('modalLigarCenaFade').style.pointerEvents = "none";

    document.getElementById('modalLigarCena').style.opacity = 0;
    document.getElementById('modalLigarCena').style.transform = 'translateY(30vh)';
}

function hexToRgba(hex, alpha) {
    hex = hex.replace('#', '');

    if (hex.length === 3) {
        // Expand shorthand notation (#abc → #aabbcc)
        hex = hex.split('').map(char => char + char).join('');
    }

    const bigint = parseInt(hex, 16);
    const r = (bigint >> 16) & 255;
    const g = (bigint >> 8) & 255;
    const b = bigint & 255;

    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

var ligarCenaRot;

function setLigarCena(idRot, id) {
    ligarCenaRot = idRot;

    let cenaLigar = document.getElementById(id);

    document.querySelectorAll('.cenaLigar').forEach(function(cena) {
        cena.style.backgroundColor = "#FDFDFD";
    });

    const corHex = cenaLigar.getAttribute('data-corCena');
    const rgba = hexToRgba(corHex, 0.2);

    cenaLigar.style.backgroundColor = rgba;
}

function ligarCenaRoteiro() {
    fetch('/ligarCena', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            id: ligarCena,
            id_cena_roteiro: ligarCenaRot,
        })
    })
    .then(response => response.json())
    .then(data => {

        if (data.success) {
            CloseModalLigarCena();
            recarregarBlocoCenas();
        } else {
            createNotfy("error", data.message || "Error linking the scene.");
        }
    });
}

function desligarCena(id) {
    fetch('/desligarCena', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            id: id,
        })
    })
    .then(response => response.json())
    .then(data => {

        if (data.success) {
            recarregarBlocoCenas();
        }
    });
}

function recarregarBlocoCenas() {
    fetch('/cenasProjeto')
        .then(res => res.text())
        .then(html => {
            document.getElementById('Summary').innerHTML = html;
        }
    );
}

function contarCaracteresSemHTML(htmlString) {
    const div = document.createElement("div");
    div.innerHTML = htmlString;
    return div.innerText.length;
}

function contarPalavrasSemHTML(htmlString) {
    const div = document.createElement("div");
    div.innerHTML = htmlString;
    const texto = div.innerText.trim();
    if (texto === "") return 0;
    return texto.split(/\s+/).length;
}

var rotOpenID

function openModalVerCena(id, nome, desc, idrot, rot, prop, txt) {

    document.getElementById('modalVerCenaFade').style.backgroundColor = "rgb(0, 0, 0, 0.2)";
    document.getElementById('modalVerCenaFade').style.pointerEvents = "all";

    document.getElementById('modalVerCena').style.opacity = 1;
    document.getElementById('modalVerCena').style.transform = 'translateY(0vh)';

    document.getElementById('verCenaName').textContent = "View " + nome;
    document.getElementById('verCenaTitle').textContent = nome;
    document.getElementById('verCenaDesc').textContent = desc;
    document.getElementById('verCenaRoteiro').textContent = rot;
    document.getElementById('verCenaProprietario').textContent = prop;

    document.getElementById('verCenaCont').innerHTML = txt;

    const caracteres = contarCaracteresSemHTML(txt);
    document.getElementById('verCenaNumChar').textContent = caracteres;

    const palavras = contarPalavrasSemHTML(txt);
    document.getElementById('verCenaNumWords').textContent = palavras;

    rotOpenID = idrot;
}

function closeModalVerCena() {

    document.getElementById('modalVerCenaFade').style.backgroundColor = "rgb(0, 0, 0, 0)";
    document.getElementById('modalVerCenaFade').style.pointerEvents = "none";

    document.getElementById('modalVerCena').style.opacity = 0;
    document.getElementById('modalVerCena').style.transform = 'translateY(30vh)';

    rotOpenID = null;
}

function goRoteiro() {

    if(rotOpenID == null) {
        return;
    }

    sessionStorage.setItem('id_roteiro', rotOpenID);
    
    // Sends the variable to the server via POST (Laravel)
    fetch('/guardarRoteiro', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ id_roteiro: rotOpenID })
    })
    .then(response => response.json())
    .then(data => {

        if (data.success) {
            window.location.href = "/abrirRoteiro";
        }
    });
}

window.addEventListener('DOMContentLoaded', () => {
    const frames = document.querySelectorAll('.frame'); // Ensure these elements exist in the DOM
    const cenaTimes = document.querySelectorAll('.cenaTime');

    // Check if there are frames
    const frame = frames[0];  // Assumes all frames have the same width
    if (!frame) return;

    const styles = window.getComputedStyle(frame);
    const width = parseFloat(styles.width);
    const marginLeft = parseFloat(styles.marginLeft);
    const marginRight = parseFloat(styles.marginRight);
    const totalFrameWidth = width + marginLeft + marginRight;

    // Adjust scene widths
    cenaTimes.forEach(cena => {
        const quant = parseInt(cena.getAttribute('quant')) || 1;
        const totalWidth = (totalFrameWidth * (quant + 1)) - marginRight; // Calculates width based on quant
        cena.style.width = `${totalWidth}px`;  // Sets the calculated width
    });

    // Calculate the total width of timelineCena based on the number of frames
    const timelineCena = document.getElementById("timelineCena");
    if (timelineCena) {
        const totalTimelineWidth = totalFrameWidth * (frames.length + 1);  // Adjust totalWidth for all frames
        timelineCena.style.width = `${totalTimelineWidth}px`;
    }
});

document.addEventListener("DOMContentLoaded", () => {
    const cenas = document.querySelectorAll('.cenaTime');

    cenas.forEach(cena => {
        const frameId = cena.getAttribute('inicio');
        const frame = document.getElementById(`frame_${frameId}`);

        if (frame) {
            const offsetLeft = frame.offsetLeft;
            cena.style.left = offsetLeft + "px";
            cena.style.width = frame.offsetWidth + "px"; // Optional, or can be calculated based on range
        }
    });
});

function gotoCena(num) {
    setFrameAtual(num);
    UpdateFrame(num);
}

// Dual range input
let sliderOne = document.getElementById("frameInicial");
let sliderTwo = document.getElementById("frameFinal");
let displayValOne = document.getElementById("range1");
let displayValTwo = document.getElementById("range2");
let minGap = 0;
let sliderTrack = document.querySelector(".slider-track");
let sliderMaxValue = document.getElementById("frameInicial").max;

function slideOne() {
    if(parseInt(sliderTwo.value) - parseInt(sliderOne.value) <= minGap){
        sliderOne.value = parseInt(sliderTwo.value) - minGap;
    }
    displayValOne.textContent = sliderOne.value;
    fillColor();

    setFrameAtual(sliderOne.value);
    UpdateFrame();
}

function slideTwo() {
    if(parseInt(sliderTwo.value) - parseInt(sliderOne.value) <= minGap){
        sliderTwo.value = parseInt(sliderOne.value) + minGap;
    }
    displayValTwo.textContent = sliderTwo.value;
    fillColor();

    setFrameAtual(sliderTwo.value);
    UpdateFrame();
}

function fillColor() {
    let max = parseInt(sliderOne.max);
    let val1 = parseInt(sliderOne.value);
    let val2 = parseInt(sliderTwo.value);
    
    let percent1 = (val1 / max) * 100;
    let percent2 = (val2 / max) * 100;

    sliderTrack.style.background = `linear-gradient(to right, #dadae5 ${percent1}% , #5a6ce4 ${percent1}% , #5a6ce4 ${percent2}%, #dadae5 ${percent2}%)`;
}

if(frames_view.children.length - 1 <= 0) {
    this.document.getElementById("add_cena").disabled = true;
}

// Dual range input (edit)
let sliderOneEdit = document.getElementById("nr_frame_inicial_edit");
let sliderTwoEdit = document.getElementById("nr_frame_final_edit");
let displayValOneEdit = document.getElementById("range1Edit");
let displayValTwoEdit = document.getElementById("range2Edit");
let minGapEdit = 0;
let sliderTrackEdit = document.querySelector(".slider-trackEdit");
let sliderMaxValueEdit = document.getElementById("nr_frame_inicial_edit").max;

function slideOneEdit() {
    if(parseInt(sliderTwoEdit.value) - parseInt(sliderOneEdit.value) <= minGapEdit){
        sliderOneEdit.value = parseInt(sliderTwoEdit.value) - minGapEdit;
    }
    displayValOneEdit.textContent = sliderOneEdit.value;
    fillColorEdit();

    setFrameAtual(sliderOneEdit.value);
    UpdateFrame();

    updateEditCena(id_cena_edit);
}

function slideTwoEdit() {
    if(parseInt(sliderTwoEdit.value) - parseInt(sliderOneEdit.value) <= minGapEdit){
        sliderTwoEdit.value = parseInt(sliderOneEdit.value) + minGapEdit;
    }
    displayValTwoEdit.textContent = sliderTwoEdit.value;
    fillColorEdit();

    setFrameAtual(sliderTwoEdit.value);
    UpdateFrame();

    updateEditCena(id_cena_edit);
}

function fillColorEdit() {
    let max = parseInt(sliderOneEdit.max);
    let val1 = parseInt(sliderOneEdit.value);
    let val2 = parseInt(sliderTwoEdit.value);
    
    let percent1 = (val1 / max) * 100;
    let percent2 = (val2 / max) * 100;

    sliderTrackEdit.style.background = `linear-gradient(to right, #dadae5 ${percent1}% , #5a6ce4 ${percent1}% , #5a6ce4 ${percent2}%, #dadae5 ${percent2}%)`;
}

if(frames_view.children.length - 1 <= 0) {
    this.document.getElementById("add_cena").disabled = true;
}

function updateEditCena(id) {
  if (!document.getElementById(id)) return;
  const inicio = +document.getElementById("nr_frame_inicial_edit").value;
  const fim = +document.getElementById("nr_frame_final_edit").value;
  const quant = fim - inicio;

  document.getElementById(id).setAttribute("inicio", inicio);
  document.getElementById(id).setAttribute("quant",  quant);

  const frames = document.querySelectorAll(".frame");
  if (!frames.length) return;
  const f = frames[0];
  const cs= getComputedStyle(f);
  const w = parseFloat(cs.width);
  const ml= parseFloat(cs.marginLeft);
  const mr= parseFloat(cs.marginRight);
  const totalW = w + ml + mr;

  document.getElementById(id).style.left  = `${totalW * (inicio - 1)}px`;
  document.getElementById(id).style.width = `${(totalW * (quant + 1)) - mr}px`;
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

    const projetoId = document.getElementById("proj_id").textContent.trim();
    
    // Check the Pusher connection
    Echo.connector.pusher.connection.bind('connected', function () {
        console.log("Pusher connected successfully!");
        console.log('Pusher connection:', Echo.connector.pusher.connection);

        atualizarIconSend();
        sendImg = document.getElementById('sendCommentImg');
        sendImg.addEventListener('click', checarIC);
    
        const chatChannel = window.Echo.private('chat_proj.' + projetoId);

        chatChannel.listen('NovoComentario', (event) => {
            console.log('Event received (NovoComentario):', event);

            recarregarComentarios(projetoId);

            fetch(`/salvarComentario/${projetoId}`, {
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
            .then(data => console.log('Comment saved:', data))
            .catch(error => console.error('Error saving comment:', error));
        });

        chatChannel.listen('NovoErro', (event) => {
            console.log('Event received (NovoErro):', event);

            recarregarComentariosErro(projetoId);

            fetch(`/salvarErro/${projetoId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    ds_erro: event.ds_erro,
                    id: event.id,
                    id_usuario: event.id_usuario,
                    nr_frame: event.nr_frame,
                })
            })
            .then(response => response.json())
            .then(data => console.log('Error saved:', data))
            .catch(error => console.error('Error saving error:', error));
        });

        console.log('Connected to channel chat_proj.' + projetoId);
    });

    // Bind error event in case of connection failure
    Echo.connector.pusher.connection.bind('error', function(err) {
        console.log("Pusher connection error:", err);
    });
});

function recarregarComentarios(proj_id) {
    fetch(`/comentarios/${proj_id}`)
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

function recarregarComentariosErro(proj_id) {
    fetch(`/comentariosErro/${proj_id}`)
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

function recarregarComentariosErroCorrecao(proj_id) {
    fetch(`/comentariosErro/${proj_id}`)
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

function checarIC() {
    document.getElementById('frameSelectMargin').style.display = "none";

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

        fetch(`/corrigirErro/${idErro}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
        })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (ok && data.success) {

                recarregarComentariosErroCorrecao(document.getElementById("proj_id").textContent.trim());

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

function gotoFrame(num) {
    setFrameAtual(num);
    UpdateFrame();
}

function EnviarComentario() {

    let ds_comentario = document.getElementById('commentInput').value

    if(ds_comentario == null || ds_comentario == '') {
        return;
    }

    document.getElementById('commentInput').value = null;
    document.getElementById('commentInput').focus();

    document.getElementById('sendCommentImg').src = "Image/SendCommentDesactivated.svg";

    fetch(`/enviarComentario/${document.getElementById("proj_id").textContent.trim()}`, {
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

    if(document.getElementById('frameSelectInp').value == null || document.getElementById('frameSelectInp').value == '') {
        document.getElementById('frameSelectMargin').style.display = "block";
        document.getElementById('frameSelectInp').value = Visu_Scroll.value;
        document.getElementById('frameSelectInp').focus();
        return;
    }

    if (parseInt(document.getElementById('frameSelectInp').value) > parseInt(Visu_Scroll.max)) {
        document.getElementById('frameSelectMargin').style.display = "block";
        document.getElementById('frameSelectInp').value = Visu_Scroll.value;
        document.getElementById('frameSelectInp').focus();
        return;
    }

    if (parseInt(document.getElementById('frameSelectInp').value) < 1) {
        document.getElementById('frameSelectMargin').style.display = "block";
        document.getElementById('frameSelectInp').value = 1;
        document.getElementById('frameSelectInp').focus();
        return;
    }

    document.getElementById('commentInput').value = null;
    document.getElementById('commentInput').focus();

    document.getElementById('sendCommentImg').src = "Image/SendCommentDesactivated.svg";

    fetch(`/enviarErro/${document.getElementById("proj_id").textContent.trim()}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            ds_erro: ds_erro,
            ic_conclusao: 0,
            nr_frame: document.getElementById('frameSelectInp').value,
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

function openFrameSelector() {

    if(document.getElementById('frameSelectMargin').style.display == "none") {

        document.getElementById('frameSelectMargin').style.display = "block";
        document.getElementById('frameSelectInp').value = Visu_Scroll.value;
        document.getElementById('frameSelectInp').focus();

    }
    else {

        document.getElementById('frameSelectMargin').style.display = "none";

    }

}

document.addEventListener('click', function(event) {
    const frameSelect = document.getElementById('frameSelect');
    const frameBtn = document.getElementById('frameErrorIcon');
    const frameSelectMargin = document.getElementById('frameSelectMargin');

    if (frameSelectMargin.style.display === 'block') {
        if (!frameSelect.contains(event.target)) {

            if(!frameBtn.contains(event.target)) {
                frameSelectMargin.style.display = 'none';
            }
            
        }
    }
});

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

document.getElementById('frameSelectInp').addEventListener('keydown', function(event) {
    
    if (event.key === 'Enter') {
        
        if(document.getElementById('frameSelectInp').value == null || document.getElementById('frameSelectInp').value == '') {
            document.getElementById('frameSelectMargin').style.display = "block";
            document.getElementById('frameSelectInp').value = Visu_Scroll.value;
            document.getElementById('frameSelectInp').focus();
            return;
        }

        let ds_erro = document.getElementById('commentInput').value

        if(ds_erro == null || ds_erro == '') {
            document.getElementById('frameSelectMargin').style.display = "none";
            document.getElementById('commentInput').focus();
            return;
        }

        checarIC();

    }
});

document.getElementById('commentInput').addEventListener('keydown', function(event) {
    
    if (event.key === 'Enter') {
        
        checarIC();

    }
});

function showComments(ic) {

    if(ic == 1) {
        document.getElementById('ic1').style.backgroundColor = "#E6E6E6";
        document.getElementById('ic1').style.color = "#14141B";
        document.getElementById('CommentsCont').style.display = "block";
        document.getElementById('CommentsCont').scrollTop = document.getElementById('CommentsCont').scrollHeight;

        document.getElementById('ic0').style.backgroundColor = "transparent";
        document.getElementById('ic0').style.color = "#8A8A94";
        document.getElementById('ErrorsCont').style.display = "none";
        
        document.getElementById('frameErrorIcon').style.display = "none";
        document.getElementById('commentInput').style.width = "87%";
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

        document.getElementById('frameErrorIcon').style.display = "block";
        document.getElementById('commentInput').style.width = "76%";
        document.getElementById('commentInput').placeholder = "Describe the error found...";
    }

}

//-----------------------------------------------------------Dashboard-----------------------------------------------------------

// Open dashboard
var dashboard = document.getElementById("DashMargin");

function openDashboard() {
    dashboard.style.display = "flex";
}

// Close dashboard
function closeDashboard() {
    dashboard.style.display = "none";
}

var filtroSelecionado = 'todas';

function filtrarCenas(filtro) {

    filtroSelecionado = filtro;

    const cenas = document.querySelectorAll('.dashCenaColor');
    cenas.forEach(cena => {
        cena.style.display = 'block'; // Show all by default

        if (filtro === 'concluidas' && !cena.classList.contains('concluida')) {
            cena.style.display = 'none';
        }
        if (filtro === 'naoConcluidas' && !cena.classList.contains('naoConcluida')) {
            cena.style.display = 'none';
        }
    });

    // Update active tabs
    document.querySelectorAll('.aba').forEach(aba => aba.classList.remove('ativa'));
    document.getElementById('aba-' + filtro).classList.add('ativa');
}

// Mark the 'todas' tab as active by default
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('aba-todas').classList.add('ativa');
});

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

async function atualizarSituacaoCena(idCena, finalizada) {
  try {
    fetch(`/atualizarSituacaoCena/${idCena}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ ic_conclusao: finalizada ? 1 : 0 })
    })
    .then(resp1 => {
        if (!resp1.ok) {
            throw new Error(`Error ${resp1.status} while updating scene status.`);
        }

        if(finalizada == true) {
            document.getElementById('cena_' + idCena).classList.remove('naoConcluida');
            document.getElementById('cena_' + idCena).classList.add('concluida');

            filtrarCenas(filtroSelecionado);
        }
        else {
            document.getElementById('cena_' + idCena).classList.add('naoConcluida');
            document.getElementById('cena_' + idCena).classList.remove('concluida');

            filtrarCenas(filtroSelecionado);
        }
        
        fetch(`/graficoData/${document.getElementById("proj_id").textContent.trim()}`)
            .then(response => response.json())
            .then(data => {
                const percentual = data.percentual;
                const circunferencia = 100;
                const valorDash = (percentual / 100) * circunferencia;
                const strokeDasharray = `${valorDash}, ${circunferencia}`;

                // Update SVG
                document.getElementById('progressPath').setAttribute('stroke-dasharray', strokeDasharray);

                // Update text
                const percentualFormatado = percentual.toFixed(1);
                document.getElementById('progressText').textContent = `${percentualFormatado}%`;

                // Update color (same logic already in Blade)
                let corProgresso = '#FF0000';
                if (percentual >= 100) {
                    corProgresso = '#3ED582';
                } else if (percentual >= 85) {
                    corProgresso = '#3E52D5';
                } else if (percentual >= 45) {
                    corProgresso = '#D5B73E';
                }

                document.getElementById('progressPath').setAttribute('stroke', corProgresso);
                document.getElementById('progressText').setAttribute('fill', corProgresso);
            }
        );
    })
    .catch(error => {
        console.error(error.message);
    });

  } catch (err) {
    console.error('atualizarSituacaoCena error:', err);
  }
}