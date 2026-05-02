//----------------------------------------------------------Modal export----------------------------------------------------------

function openModalExport() {
    document.getElementById('modalExportFade').style.backgroundColor = "rgb(0, 0, 0, 0.2)";
    document.getElementById('modalExport').style.transform = "translateY(0)";
    document.getElementById('modalExport').style.pointerEvents = "all";
    document.getElementById('modalExport').style.opacity = 1;

    document.getElementById('exportFPSVideo').textContent = document.getElementById('FPS_Select').value;
    document.getElementById('exportFPSGif').textContent = document.getElementById('FPS_Select').value;
    document.getElementById('exportQtdFramesVideo').textContent = quantidade;
    document.getElementById('exportQtdFramesFrames').textContent = quantidade;
    document.getElementById('exportQtdFramesGif').textContent = quantidade;
    document.getElementById('exportVolumeVideo').textContent = document.getElementById('Vol_Select').value * 100;
    document.getElementById('exportProporcaoVideo').textContent = document.getElementById('proporcaoValor').textContent;
    document.getElementById('exportProporcaoFrames').textContent = document.getElementById('proporcaoValor').textContent;
    document.getElementById('exportProporcaoGif').textContent = document.getElementById('proporcaoValor').textContent;
    document.getElementById('exportDuracaoVideo').innerHTML = document.getElementById('MaxTime').innerHTML;
    document.getElementById('exportDuracaoGif').innerHTML = document.getElementById('MaxTime').innerHTML;

    let prop = document.getElementById('proporcaoValor').textContent;

    if(prop == "16:9") {
        const frameWrapper = document.getElementById('modalExportViewFrame');
        const height = parseFloat(window.getComputedStyle(frameWrapper).height);
        const width = (16 / 9) * height;
        frameWrapper.style.width = width + "px";
    }
    else if(prop == "16:10") {
        const frameWrapper = document.getElementById('modalExportViewFrame');
        const height = parseFloat(window.getComputedStyle(frameWrapper).height);
        const width = (16 / 10) * height;
        frameWrapper.style.width = width + "px";
    }
    else if(prop == "9:16") {
        const frameWrapper = document.getElementById('modalExportViewFrame');
        const height = parseFloat(window.getComputedStyle(frameWrapper).height);
        const width = (9 / 16) * height;
        frameWrapper.style.width = width + "px";
    }

    document.getElementById('modalExportRange').max = document.getElementById('Visu_Scroll').max;
    document.getElementById('modalExportRange').value = 1;
    updateExportFrame();

    exportType('mp4', 'exportMP4');

    var ModalPorjOpt = document.getElementById("modal_opts");
    ModalPorjOpt.style.height = "0";
    ModalPorjOpt.style.padding = "0 2% 0 1%";
}

function closeModalExport() {
    document.getElementById('modalExportFade').style.backgroundColor = "rgb(0, 0, 0, 0)";
    document.getElementById('modalExport').style.transform = "translateY(20vh)";
    document.getElementById('modalExport').style.pointerEvents = "none";
    document.getElementById('modalExport').style.opacity = 0;
}

function updateExportFrame() {
    document.getElementById('modalExportFrameImg').src = document.getElementById("frame_" + (document.getElementById('modalExportRange').value)).querySelector("img").src;
    document.getElementById('modalExportFrameNum').textContent = "Frame " + document.getElementById('modalExportRange').value;
}

function exportType(type, btn) {

    document.getElementById('modalExportButton').onclick = function() {
        exportar(type);
    };

    document.querySelectorAll('.modalExportFormat').forEach(function(button) {
        button.style.backgroundColor = "transparent";
        button.style.color = "#14141B";
        button.style.borderRight = "2px solid #14141B";
        button.style.borderBottom = "2px solid #14141B";
    });

    document.getElementById(btn).style.backgroundColor = "#3E52D5";
    document.getElementById(btn).style.color = "#FDFDFD";
    document.getElementById(btn).style.border = "none";

    if(type == "mp4" || type == "webm" || type == "mkv" || type == "avi") {
        document.getElementById('modalExportInfoVideo').style.display = "flex";
        document.getElementById('modalExportInfoFrames').style.display = "none";
        document.getElementById('modalExportInfoGif').style.display = "none";

        document.getElementById('modalExportInfoAudio').style.opacity = 1;
        document.getElementById('modalExportInfoAudio').style.pointerEvents = "all";

        document.getElementById('export').textContent = "video";
    }
    else if(type == "frames") {
        document.getElementById('modalExportInfoVideo').style.display = "none";
        document.getElementById('modalExportInfoFrames').style.display = "flex";
        document.getElementById('modalExportInfoGif').style.display = "none";

        document.getElementById('modalExportInfoAudio').style.opacity = 0;
        document.getElementById('modalExportInfoAudio').style.pointerEvents = "none";

        document.getElementById('export').textContent = "frames";
    }
    else if(type == "gif") {
        document.getElementById('modalExportInfoVideo').style.display = "none";
        document.getElementById('modalExportInfoFrames').style.display = "none";
        document.getElementById('modalExportInfoGif').style.display = "flex";

        document.getElementById('modalExportInfoAudio').style.opacity = 0;
        document.getElementById('modalExportInfoAudio').style.pointerEvents = "none";

        document.getElementById('export').textContent = "gif";
    }

}

function exportar(type) {
    document.getElementById('modalExportLoad').style.display = "flex";
    document.getElementById('modalExport').style.display = "none";

    if(type == "mp4" || type == "webm" || type == "mkv" || type == "avi") {
        exportVideo(type);
        document.getElementById('exportLoadText').childNodes[0].textContent = "Exporting video " + type;
    }
    else if(type == "frames") {
        exportFrames(document.getElementById('exportFormatoFrames').value);
        document.getElementById('exportLoadText').childNodes[0].textContent = "Exporting frames " + document.getElementById('exportFormatoFrames').value;
    }
    else if(type == "gif") {
        exportGif();
        document.getElementById('exportLoadText').childNodes[0].textContent = "Exporting gif";
    }

    iniciarAnimacaoPontos();

}

let exportDotsInterval;

function iniciarAnimacaoPontos() {
    const ret = document.getElementById("exportLoadRet");
    let estado = 0;

    exportDotsInterval = setInterval(() => {
        estado = (estado + 1) % 3;
        ret.textContent = '.'.repeat(estado + 1);
    }, 500);
}

function pararAnimacaoPontos() {
    clearInterval(exportDotsInterval);
    document.getElementById("exportLoadRet").textContent = '...';
}

function exportVideo(format) {
    const proj_id = document.getElementById("proj_id").textContent;
    const fps = parseInt(document.getElementById("FPS_Select").value, 10);
    const proporcao = document.getElementById("proporcaoValor").textContent;
    const volume = parseFloat(document.getElementById("Vol_Select").value);
    const audio = document.getElementById('exportarAudioCheck').checked;

    fetch(`/exportarVideo/${proj_id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Content-Type': 'application/json',
            'Accept': 'application/octet-stream'
        },
        body: JSON.stringify({ fps, proporcao, format, volume, audio })
    })
    .then(async response => {
        if (!response.ok) {
            // Attempt to read JSON error
            const err = await response.json().catch(() => ({}));
            throw err;
        }
        // Get the video blob
        const blob = await response.blob();

        // Extract filename from Content-Disposition header
        const disposition = response.headers.get('Content-Disposition') || '';
        let filename = 'download.mp4';
        const match = disposition.match(/filename=["']?(.+?)["']?(;|$)/i);
        if (match && match[1]) {
            filename = match[1];
        }

        // Trigger the download with the exact filename
        const url = URL.createObjectURL(blob);
        const a   = document.createElement('a');
        a.href    = url;
        a.download= filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);

        document.getElementById('modalExportLoad').style.display = "none";
        document.getElementById('modalExport').style.display = "flex";
        pararAnimacaoPontos();
    })
    .catch(err => {
        console.error(err);
        pararAnimacaoPontos();
        showNotification("error", "Error exporting video.");
    });
}

function exportFrames(format) {
    const proj_id = document.getElementById("proj_id").textContent;
    const proporcao = document.getElementById("proporcaoValor").textContent;

    fetch(`/exportarFrames/${proj_id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Content-Type': 'application/json',
            'Accept': 'application/octet-stream'
        },
        body: JSON.stringify({ proporcao, format })
    })
    .then(async response => {
        if (!response.ok) {
            // Attempt to read JSON error
            const err = await response.json().catch(() => ({}));
            throw err;
        }
        // Get the blob
        const blob = await response.blob();

        // Extract filename from Content-Disposition header
        const disposition = response.headers.get('Content-Disposition') || '';
        let filename = 'download.mp4';
        const match = disposition.match(/filename=["']?(.+?)["']?(;|$)/i);
        if (match && match[1]) {
            filename = match[1];
        }

        // Trigger the download with the exact filename
        const url = URL.createObjectURL(blob);
        const a   = document.createElement('a');
        a.href    = url;
        a.download= filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);

        document.getElementById('modalExportLoad').style.display = "none";
        document.getElementById('modalExport').style.display = "flex";
        pararAnimacaoPontos();
    })
    .catch(err => {
        console.error(err);
        pararAnimacaoPontos();
        showNotification("error", "Error exporting frames.");
    });
}

function exportGif() {    
    const proj_id = document.getElementById("proj_id").textContent;
    const proporcao = document.getElementById("proporcaoValor").textContent;

    fetch(`/exportarGif/${proj_id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Content-Type': 'application/json',
            'Accept': 'application/octet-stream'
        },
        body: JSON.stringify({ proporcao })
    })
    .then(async response => {
        if (!response.ok) {
            // Attempt to read JSON error
            const err = await response.json().catch(() => ({}));
            throw err;
        }
        // Get the blob
        const blob = await response.blob();

        // Extract filename from Content-Disposition header
        const disposition = response.headers.get('Content-Disposition') || '';
        let filename = 'download.mp4';
        const match = disposition.match(/filename=["']?(.+?)["']?(;|$)/i);
        if (match && match[1]) {
            filename = match[1];
        }

        // Trigger the download with the exact filename
        const url = URL.createObjectURL(blob);
        const a   = document.createElement('a');
        a.href    = url;
        a.download= filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);

        document.getElementById('modalExportLoad').style.display = "none";
        document.getElementById('modalExport').style.display = "flex";
        pararAnimacaoPontos();
    })
    .catch(err => {
        console.error(err);
        pararAnimacaoPontos();
        showNotification("error", "Error exporting gif.");
    });
}