function executeCommand(command, value) {
    const activeTextArea = getActiveTextArea();
    if (activeTextArea) {
        document.execCommand(command, false, value);
        console.log(value)
    }
}

function getActiveTextArea() {
    const selection = window.getSelection();
    if (selection.rangeCount === 0) return null;
    const node = selection.anchorNode;
    const element = node.nodeType === 1 ? node : node.parentElement;
    return element ? element.closest('.textArea') : null;
}

["bold", "italic", "underline", "strikethrough", "superscript", "subscript"].forEach(id => {
    document.getElementById(id).addEventListener("click", function () {
        const activeTextArea = getActiveTextArea();
        if (activeTextArea) {
            executeCommand(id);
        }
    });
});

["alignLeft", "alignCenter", "alignRight", "alignJustify"].forEach((id, i) => {
    const commands = ["justifyLeft", "justifyCenter", "justifyRight", "justifyFull"];
    document.getElementById(id).addEventListener("click", function () {
        const activeTextArea = getActiveTextArea();
        if (activeTextArea) {
            executeCommand(commands[i]);
        }
    });
});

["indent", "outdent", "orderedList", "unorderedList"].forEach(id => {
    const cmd = {
        orderedList: "insertOrderedList",
        unorderedList: "insertUnorderedList"
    }[id] || id;
    document.getElementById(id).addEventListener("click", function () {
        const activeTextArea = getActiveTextArea();
        if (activeTextArea) {
            executeCommand(cmd);
        }
    });
});

// Busque todas as Google Fonts via API
const apiUrl = '/api/google-fonts'; // Calls the API from the server
let googleFonts = []; // array de strings: ["Roboto", "Open Sans", …]

// Fontes do sistema
const systemFonts = [
  "Arial", "Times New Roman", "Courier New", "Georgia",
  "Verdana", "Tahoma", "Comic Sans MS", "Impact"
];

async function initFontSelect() {
  // Busca Google Fonts
  const res   = await fetch(apiUrl);
  const data  = await res.json();
  googleFonts = data.items.map(f => f.family);

  const fontSelect = document.getElementById('font');
  const frag       = document.createDocumentFragment();

  // Adiciona fontes do sistema
  systemFonts.forEach(f => {
    const o = document.createElement('option');
    o.value       = f;
    o.textContent = f;
    frag.appendChild(o);
  });

  // Adiciona todas as Google Fonts
  googleFonts.forEach(family => {
    const o = document.createElement('option');
    o.value       = family;
    o.textContent = family;
    frag.appendChild(o);
  });

  fontSelect.appendChild(frag);

  // inicializa o Select2 **apenas uma vez**
  $('#font').select2({
    placeholder: 'Escolha uma fonte',
    allowClear: true,
    width: '100%',
    matcher(params, data) {
      if (!params.term) return data;
      return data.text.toLowerCase().includes(params.term.toLowerCase())
        ? data
        : null;
    },
    templateResult(item) {
      if (!item.id) return item.text;
      loadGoogleFont(item.id);
      return $('<span>')
        .text(item.text)
        .css('font-family', item.id);
    },
    templateSelection(item) {
      if (!item.id) return item.text;
      loadGoogleFont(item.id);
      return $('<span>')
        .text(item.text)
        .css('font-family', item.id);
    }
  });

  // Começa a pré-carregar os CSS em batches para não travar a UI
  preloadGoogleFontsInBatches();
}

// Pre-carrega os CSS das Google Fonts em batches
function preloadGoogleFontsInBatches() {
  const batchSize = 30;   // quantas fontes carrega por lote
  const delay     = 100;  // ms de espera entre lotes
  let idx = 0;

  function loadBatch() {
    const slice = googleFonts.slice(idx, idx + batchSize);
    slice.forEach(family => {
      const linkId = `gf-${family.replace(/\s+/g,'-')}`;
      if (document.getElementById(linkId)) return;
      const link = document.createElement('link');
      link.id   = linkId;
      link.rel  = 'stylesheet';
      link.href = `https://fonts.googleapis.com/css2?family=${family.replace(/\s+/g,'+')}\
&display=swap`;
      document.head.appendChild(link);
    });
    idx += batchSize;
    if (idx < googleFonts.length) {
      setTimeout(loadBatch, delay);
    }
  }

  loadBatch();
}

// Abre somente o CSS para a família selecionada (lazy-load on demand)
function loadGoogleFont(family) {
  const linkId = `gf-${family.replace(/\s+/g,'-')}`;
  if (document.getElementById(linkId)) return;
  const link = document.createElement('link');
  link.id   = linkId;
  link.rel  = 'stylesheet';
  link.href = `https://fonts.googleapis.com/css2?family=${family.replace(/\s+/g,'+')}&display=swap`;
  document.head.appendChild(link);
}

// Aplica a fonte ao texto selecionado
function applyFont(font) {
  const sel = window.getSelection();
  if (!sel.rangeCount) return;
  const range = sel.getRangeAt(0);
  const span  = document.createElement('span');
  span.style.fontFamily = font;

  if (range.collapsed) {
    span.appendChild(document.createTextNode('\u200B'));
    range.insertNode(span);
    range.setStart(span.firstChild, 0);
    range.collapse(true);
  } else {
    const contents = range.extractContents();
    span.appendChild(contents);
    range.deleteContents();
    range.insertNode(span);
  }

  sel.removeAllRanges();
  const nr = document.createRange();
  nr.selectNodeContents(span);
  sel.addRange(nr);
}

// Ao mudar o select, aplica a fonte
$('#font').on('change', function() {
  applyFont(this.value);
});

// Sincroniza o select com a fonte sob o cursor
const ta = document.getElementById('textArea');
['mouseup','keyup'].forEach(ev =>
  ta.addEventListener(ev, () => {
    const sel = window.getSelection();
    if (!sel.rangeCount) return;
    const n    = sel.focusNode;
    const el   = n.nodeType === 3 ? n.parentElement : n;
    const cf   = window.getComputedStyle(el).fontFamily.toLowerCase();

    for (let o of document.getElementById('font').options) {
      if (cf.includes(o.value.toLowerCase())) {
        $('#font').val(o.value).trigger('change.select2');
        return;
      }
    }
    $('#font').val(null).trigger('change.select2');
  })
);

// Inicia tudo ao carregar o documento
$(document).ready(initFontSelect);

function applyFontSize(px) {
    const active = getActiveTextArea();
    if (!active) return;

    const sel = window.getSelection();
    if (!sel.rangeCount) return;
    const range = sel.getRangeAt(0);

    // criar o span com estilo
    const span = document.createElement('span');
    span.style.fontSize = px + 'px';

    if (range.collapsed) {
        // inserção no cursor
        span.appendChild(document.createTextNode('\u200B'));
        range.insertNode(span);
        // posicionar o cursor dentro do span
        range.setStart(span.firstChild, 0);
        range.collapse(true);
        sel.removeAllRanges();
        sel.addRange(range);
    } else {
        // envolver conteúdo selecionado
        const contents = range.extractContents();
        span.appendChild(contents);
        range.insertNode(span);
        // reajustar seleção para dentro do novo span
        sel.removeAllRanges();
        const newRange = document.createRange();
        newRange.selectNodeContents(span);
        sel.addRange(newRange);
    }
}

document.addEventListener("selectionchange", () => {
  const sel = window.getSelection();
  if (!sel.rangeCount) return;
  const node = sel.anchorNode;
  const el = node.nodeType === 1 ? node : node.parentElement;
  const textArea = el.closest(".textArea");
  if (!textArea) return;

  // encontra o elemento de estilo real (pode ser span, strong, etc.)
  const target = el.nodeType === 3 ? el.parentElement : el;
  const fontSize = window.getComputedStyle(target).fontSize; // ex: "16px"
  const size = parseInt(fontSize, 10);

  const select = document.getElementById("fontSize");
  if ([...select.options].some(opt => parseInt(opt.value,10) === size)) {
    select.value = size;
  }
});

document.getElementById("fontSize").addEventListener("change", function () {
    applyFontSize(this.value);
});

function isLightColor(hex) {
    hex = hex.replace("#", "");
    const r = parseInt(hex.substr(0, 2), 16);
    const g = parseInt(hex.substr(2, 2), 16);
    const b = parseInt(hex.substr(4, 2), 16);
    const brightness = (r * 299 + g * 587 + b * 114) / 1000;
    return brightness > 128;
}

function updateColorButtonState(buttonId, color) {
    const button = document.getElementById(buttonId);
    const icon = button.querySelector('i');

    button.style.backgroundColor = color;

    if (color === "transparent" || color === "rgba(0, 0, 0, 0)") {
        button.style.backgroundColor = "#FFFFFF";
        icon.style.color = "#000";
    } else {
        icon.style.color = isLightColor(color) ? "#000" : "#fff";
    }
}

function updateColorButtonsFromCursor() {
    const activeTextArea = getActiveTextArea();
    if (!activeTextArea) return;

    const selection = window.getSelection();
    const range = selection.getRangeAt(0);

    if (range && range.startContainer) {
        const node = range.startContainer;

        const textColor = window.getComputedStyle(node.parentElement).color;
        const bgColor = window.getComputedStyle(node.parentElement).backgroundColor;

        updateColorButtonState('textColor', rgbToHex(textColor));
        updateColorButtonState('bgColor', rgbToHex(bgColor) || "transparent");
    }
}

function rgbToHex(rgb) {
    const result = /^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/.exec(rgb);
    if (!result) return rgb;
    return "#" + ((1 << 24) | (parseInt(result[1]) << 16) | (parseInt(result[2]) << 8) | parseInt(result[3])).toString(16).slice(1).toUpperCase();
}

document.addEventListener('click', function(event) {
    const activeTextArea = getActiveTextArea();
    if (activeTextArea && activeTextArea.contains(event.target)) {
        updateColorButtonsFromCursor();
    }
});

document.getElementById("textColor").addEventListener("click", () => {
    document.getElementById("textColorPicker").click();
});

document.getElementById("textColorPicker").addEventListener("input", function () {
    const color = this.value;
    executeCommand("foreColor", color);
    updateColorButtonState('textColor', color);
});

document.getElementById("bgColor").addEventListener("click", () => {
    document.getElementById("bgColorPicker").click();
});

document.getElementById("bgColorPicker").addEventListener("input", function () {
    const color = this.value;
    executeCommand("hiliteColor", color);
    updateColorButtonState('bgColor', color);
});

updateColorButtonsFromCursor();

document.addEventListener("keydown", function (e) {
    const active = getActiveTextArea();
    if (!active) return;

    if (e.key === "Tab") {
        e.preventDefault();
        const tabNode = document.createTextNode("\u00a0\u00a0\u00a0\u00a0");
        const sel = window.getSelection();
        const range = sel.getRangeAt(0);
        range.insertNode(tabNode);
        range.setStartAfter(tabNode);
        range.setEndAfter(tabNode);
        sel.removeAllRanges();
        sel.addRange(range);
    }
});

document.addEventListener("selectionchange", () => {
    const selection = window.getSelection();
    const activeTextArea = getActiveTextArea();
    if (!selection.rangeCount || !activeTextArea) return;

    const node = selection.anchorNode;
    const element = node.nodeType === 1 ? node : node.parentElement;

    // FORMATOS DE TEXTO
    const buttons = [
        { id: "bold", command: "bold" },
        { id: "italic", command: "italic" },
        { id: "underline", command: "underline" },
        { id: "strikethrough", command: "strikethrough" },
        { id: "superscript", command: "superscript" },
        { id: "subscript", command: "subscript" }
    ];

    // Verifica se o estado do comando está ativo na seleção dentro da .textArea
    buttons.forEach(btn => {
        const active = document.queryCommandState(btn.command);
        // Verifica se a seleção está dentro da .textArea
        if (activeTextArea.contains(document.activeElement)) {
            document.getElementById(btn.id).classList.toggle("active", active);
        }
    });

    // ALINHAMENTO
    if (!element) return;
    const computedAlign = window.getComputedStyle(element).textAlign;
    const alignButtons = {
        "alignLeft": "left",
        "alignCenter": "center",
        "alignRight": "right",
        "alignJustify": "justify"
    };

    for (const [id, align] of Object.entries(alignButtons)) {
        document.getElementById(id).classList.toggle("active", computedAlign === align);
    }

    // DETECTAR TÓPICOS (listas ordenadas ou não ordenadas)
    const listButtons = {
        "orderedList": "ol",
        "unorderedList": "ul"
    };

    for (const [id, tag] of Object.entries(listButtons)) {
        const isList = element.closest(tag);
        document.getElementById(id).classList.toggle("active", isList);
    }
});


document.querySelectorAll("button").forEach(btn => {
    btn.addEventListener("click", () => {
        const activeTextArea = getActiveTextArea();
        if (activeTextArea) setTimeout(() => activeTextArea.focus(), 0);
    });
});


//---------------------------------------------------------
//                  SALVAMENTO AUTOMÁTICO
//---------------------------------------------------------

let timeout = null;
let lastSavedContent = '';

function triggerAutoSave() {
    const allPages = document.querySelectorAll('.textArea');
    let content = '';

    // Pega o conteúdo da página atual
    allPages.forEach(function(page) {
        if (page.getAttribute('data-idCena') == window.idCena) {
            content = page.innerHTML;
        }
    });

    // Só salva se o conteúdo foi alterado e o conteúdo salvo não está vazio
    if (content !== lastSavedContent && lastSavedContent != '') {
        mostrarSalvando();
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            const scriptId = document.getElementById("script_id").textContent;
            saveContent(content, scriptId);
            lastSavedContent = content; // Atualiza o conteúdo salvo
        }, 1000);
    } else if (content !== lastSavedContent) {
        // Atualiza lastSavedContent apenas quando há uma alteração
        lastSavedContent = content;
    }
}

['input', 'change', 'click', 'keydown', 'mouseup', 'paste', 'cut', 'drop'].forEach(eventType => {
    document.addEventListener(eventType, event => {

            let cenasText = document.querySelectorAll('.textArea');

            const target = event.target;
            
            cenasText.forEach(function(cena) {
        
                if (cena.getAttribute('data-idCena') == window.idCena) {

                    // Obter texto da cena atual
                    const text = cena.innerText || cena.textContent;

                    // Contar palavras (ignora espaços duplos/vazios)
                    const wordCount = text.trim().split(/\s+/).filter(word => word.length > 0).length;

                    // Contar caracteres (sem contar espaços em branco extras)
                    const charCount = text.replace(/\s/g, '').length;

                    // Atualizar os spans
                    document.getElementById("qt_words_cena").textContent = wordCount;
                    document.getElementById("qt_char_cena").textContent = charCount;
                }
            });
            triggerAutoSave();
        })
    }
);

function saveContent(ds_texto, id_roteiro) {
    const formData = new URLSearchParams();
    formData.append("ds_texto", ds_texto);
    formData.append("id_roteiro", id_roteiro);

    fetch('/salvarTexto', {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.ok ? response.json() : Promise.reject(response))
    .then(data => {
        restaurarTextoSalvo(new Date().toISOString());
    })
    .catch(err => {
        console.error("Erro ao salvar:", err);
    });
}