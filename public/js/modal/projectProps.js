//----------------------------------------------------------Project proprierties----------------------------------------------------------

document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
});

document.addEventListener('DOMContentLoaded', function() {

    var ModalPorjOpt = document.getElementById("modal_opts");

    function OpenModalPorjOpt() {
        ModalPorjOpt.style.height = "30vh";
        ModalPorjOpt.style.padding = "1% 2% 2.2% 1%";
    }

    // Closes the modal if the user clicks outside it
    function closeModalPorjOpt(event) {
        if (!ModalPorjOpt.contains(event.target) && !event.target.closest('.ModalPorjOptBtn')) {
            // Removes the class that activates the modal
            ModalPorjOpt.style.height = "0";
            ModalPorjOpt.style.padding = "0 2% 0 1%";
        }
    }

    // Adds the click event to open the modal
    var profileButton = document.querySelector('.ModalPorjOptBtn');
    if (profileButton) {
        profileButton.onclick = OpenModalPorjOpt;
    }

    // Adds the click event to the document to close the modal
    document.addEventListener('click', closeModalPorjOpt);
});

function closeModalPorjOpt2() {
    var ModalPorjOpt = document.getElementById("modal_opts");
    // Removes the class that activates the modal
    ModalPorjOpt.style.height = "0";
    ModalPorjOpt.style.padding = "0 2% 0 1%";
}

//----------------------------------------------------------Close project----------------------------------------------------------

function CloseProj() {
    let formData = new FormData();
    formData.append("updated_at", new Date().toISOString()); // Sends the current date in the ISO 8601 format
    formData.append("id_projeto", document.getElementById("proj_id").textContent.trim());

    fetch("/atualizarDataModificação", {
        method: "POST",
        body: formData,
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        // Closes the project
        let CloseProjForm = document.getElementById("CloseProjForm");
        CloseProjForm.submit();
    })
    .catch(error => {
        console.error("Erro ao salvar a última modificação:", error);
    });
}

//----------------------------------------------------------Proprierties----------------------------------------------------------

// Opens the proprierties modal
var ModalPropsFade = document.getElementById("ModalPropsFade");
var ModalProps = document.getElementById("ModalProps");

function OpenModalProps() {
    ModalProps.style.display = "flex";
    ModalPropsFade.style.backgroundColor = "rgb(0, 0, 0, 0.5)";
    ModalPropsFade.style.pointerEvents = "all";
    ModalProps.style.transform = "translateY(0)";
    ModalProps.style.opacity = 1;
    ModalProps.style.pointerEvents = "all";

    var ModalPorjOpt = document.getElementById("modal_opts");
    ModalPorjOpt.style.height = "0";
    ModalPorjOpt.style.padding = "0 2% 0 1%";
}

function CloseModalProps() {
    ModalPropsFade.style.backgroundColor = "rgb(0, 0, 0, 0)";
    ModalPropsFade.style.pointerEvents = "none";
    ModalProps.style.transform = "translateY(20vh)";
    ModalProps.style.opacity = 0;
    ModalProps.style.pointerEvents = "none";
}

//----------------------------------------------------------Import frame and video----------------------------------------------------------

// Import images
function importImages() {
    var import_images_input = document.getElementById("import_frames");

    if (import_images_input) {
        import_images_input.click();
        document.getElementById("import_frames").value = "";
    } else {
        console.error("Element import_frames not found!");
    }
}

document.getElementById("import_frames").addEventListener("change", function(event) {
    let files = event.target.files; // Gets all selected files
    if (!files.length) return;

    let projectId = document.getElementById("proj_id").textContent.trim();

    Array.from(files).forEach((file) => {
        let formData = new FormData();
        formData.append("image", file);
        formData.append("id_projeto", projectId);

        fetch("/importarFrames", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let frameContainer = document.getElementById("Frames");
                let totalFrames = document.querySelectorAll(".frame").length;
        
                data.paths.forEach((path, index) => {
                    let frameId = "frame_" + (totalFrames + index);
        
                    // Create frame div
                    let frameDiv = document.createElement("div");
                    frameDiv.classList.add("frame");
                    frameDiv.id = frameId;
                    frameDiv.onclick = function() { SelectFrame(frameId); };
        
                    // Create image inside the div
                    let img = document.createElement("img");
                    img.src = path;
        
                    frameDiv.appendChild(img);
        
                    let addFrameButton = frameContainer.querySelector("img[src='Image/AddFrame.svg']");
                    frameContainer.insertBefore(frameDiv, addFrameButton);

                    document.getElementById("add_cena").disabled = false;
                });

                Visu_Scroll.max = frames_view.children.length - 1;
                quantidade = frames_view.children.length - 1;
                UpdateFPS();
            } else {
                console.error("Upload error:", data.error);
            }
        })        
        .catch(error => console.error("Upload error:", error));
    });

    // Clear input to avoid issues when selecting the same files again
    document.getElementById("import_frames").value = "";
});


// Import video
function importVideo() {
    var import_vid_input = document.getElementById("import_video");

    if (import_vid_input) {
        import_vid_input.click();
        document.getElementById("import_video").value = "";
    } else {
        console.error("Element import_video not found!");
    }
}

function handleFileChange(event) {
    const file = event.target.files[0];
    if (!file) return;

    // Create a URL object for the video file (for preview only, not for upload)
    const videoElement = document.createElement("video");
    videoElement.src = URL.createObjectURL(file);

    videoElement.onloadedmetadata = async function () {
        const fps = await getVideoFPS(file); // Passes the original file, not the video element

        // Now, send the video and FPS to the backend
        uploadVideo(file, fps);
    };
}

async function getVideoFPS(file) {
    const formData = new FormData();
    formData.append("video", file);

    try {
        const response = await fetch("/resgagarFPS", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
        });

        const data = await response.json();

        if (data.error) {
            console.error("Error:", data.error);
            return 0;
        }
        return data.fps;
    } catch (error) {
        console.error("Request error:", error);
        return 0;
    }
}


function uploadVideo(file, fps) {
    const formData = new FormData();
    formData.append("import_video", file); // The video file
    formData.append("fps", fps);           // The calculated FPS

    var LoadFade = document.getElementById("LoadFade");
    var LoadModal = document.getElementById("LoadModal");

    // Show the loading screen
    LoadFade.style.backgroundColor = "rgba(0,0,0,0.2)";
    LoadFade.style.pointerEvents = "all";
    LoadModal.style.display = "flex";
    closeModalPorjOpt2();

    startReticLoop();

    fetch("/importarVideo", {
        method: "POST",
        body: formData,
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            "X-Requested-With": "XMLHttpRequest"
        }
    }).then(response => {
        const reader = response.body.getReader();
        const decoder = new TextDecoder();
        let receivedChunks = "";

        function readStream() {
            reader.read().then(({done, value}) => {
                if (done) {
                    // End of stream — hide the loading screen
                    LoadFade.style.backgroundColor = "rgba(0,0,0,0)";
                    LoadFade.style.pointerEvents = "none";
                    LoadModal.style.display = "none";
                    return;
                }

                // Converts bytes to string and accumulates
                receivedChunks += decoder.decode(value, {stream: true});
                // Splits received data by SSE delimiters (each event is separated by "\n\n")
                const events = receivedChunks.split("\n\n");

                // The last element may be an incomplete chunk — leave it for the next read
                receivedChunks = events.pop();

                events.forEach(chunk => {
                    if (chunk.startsWith("data:")) {
                        // Remove the "data:" prefix and whitespace
                        const jsonStr = chunk.replace(/^data:\s*/, '');
                        try {
                            const data = JSON.parse(jsonStr);
                            // Update the progress bar
                            const mainText = document.getElementById("LoadMainText");

                            if (data.resgatarProgresso < 5) {
                                mainText.textContent = "Recovering FPS";
                            }
                            else if (data.resgatarProgresso < 10) {
                                mainText.textContent = "Preparing video";
                            }
                            else if (data.resgatarProgresso < 80) {
                                mainText.textContent = "Extracting frames";
                            }
                            else {
                                mainText.textContent = "Extracting audio";
                            }

                            document.getElementById("progressBar").value = data.resgatarProgresso || 0;

                            // If there is an error, display it and stop reading
                            if (data.error) {
                                console.error("Error:", data.error);
                                return;
                            }
                            
                            // If processing is complete (progress = 100)
                            if (data.resgatarProgresso === 100) {

                                if (data.success) {

                                    document.getElementById("FPS_Select").value = fps;
                                    Visu_Scroll.max = frames_view.children.length - 1;
                                    quantidade = frames_view.children.length - 1;

                                    function main() {
                                        UpdateFPS();
                                        setTimeout(() => {
                                            window.location.reload();
                                        }, 1000);
                                    }

                                    main();

                                    // Hide the loading screen
                                    LoadFade.style.backgroundColor = "rgba(0,0,0,0)";
                                    LoadFade.style.pointerEvents = "none";
                                    LoadModal.style.display = "none";
                                    stopReticLoop();

                                    document.getElementById("add_cena").disabled = false;
                                }
                            }
                        } catch (e) {
                            console.error("Error processing chunk:", e);
                        }
                    }
                });

                // Continue reading the stream
                readStream();
            }).catch(error => {
                console.error("Stream reading error:", error);
            });
        }
        readStream();
    }).catch(error => {
        console.error("Request error:", error);
    });
}