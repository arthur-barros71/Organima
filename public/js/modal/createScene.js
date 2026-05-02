//-----------------------------------------------------------Scene creation-----------------------------------------------------------

// Open scene creation modal
const createCenaModal = document.getElementById("ModalCreateCena");
const createCenaModalFade = document.getElementById("ModalCreateCenaFade");

// Global variables
let tempCenaDiv   = null;
let onInputHandler= null;
let onColorHandler= null;

function OpenCreateCenaModal() {
  // Opening animations
  createCenaModalFade.style.backgroundColor = "rgba(0,0,0,0.7)";
  createCenaModalFade.style.pointerEvents = "all";
  createCenaModal.style.transform = "translateY(0)";
  createCenaModal.style.opacity = 1;
  createCenaModal.style.pointerEvents = "all";

  document.getElementById('frameInicial').value = AtualFrame;
  document.getElementById('frameFinal').value = AtualFrame + 10;

  document.getElementById('nm_cena').value = null;
  document.getElementById('ds_cena').value = null;

  // Sliders
  const sliderOne = document.getElementById("frameInicial");
  const sliderTwo = document.getElementById("frameFinal");
  sliderOne.max = quantidade;
  sliderTwo.max = quantidade;
  slideOne();
  slideTwo();

  // Initial color
  const colorInput = document.getElementById("ds_cor");
  const initialColor= colorInput.value;

  // Remove old scene
  if (tempCenaDiv) tempCenaDiv.remove();

  // Create new scene
  const timeline = document.getElementById("timelineCena");
  tempCenaDiv = document.createElement("div");
  tempCenaDiv.classList.add("cenaTime");
  tempCenaDiv.style.backgroundColor = initialColor;
  tempCenaDiv.style.opacity = '0.7'; 
  timeline.appendChild(tempCenaDiv);

  // Slider listeners
  onInputHandler = updateTempCena.bind(null);
  sliderOne.addEventListener("input", onInputHandler);
  sliderTwo.addEventListener("input", onInputHandler);

  // Color input listener
  onColorHandler = () => {
    if (tempCenaDiv) {
      tempCenaDiv.style.backgroundColor = colorInput.value;
    }
  };
  colorInput.addEventListener("input", onColorHandler);

  // Initial position/size
  updateTempCena();
}

function updateTempCena() {
  if (!tempCenaDiv) return;
  const inicio = +document.getElementById("frameInicial").value;
  const fim = +document.getElementById("frameFinal").value;
  const quant = fim - inicio;

  tempCenaDiv.setAttribute("inicio", inicio);
  tempCenaDiv.setAttribute("quant",  quant);

  const frames = document.querySelectorAll(".frame");
  if (!frames.length) return;
  const f = frames[0];
  const cs= getComputedStyle(f);
  const w = parseFloat(cs.width);
  const ml= parseFloat(cs.marginLeft);
  const mr= parseFloat(cs.marginRight);
  const totalW = w + ml + mr;

  tempCenaDiv.style.left  = `${totalW * (inicio - 1)}px`;
  tempCenaDiv.style.width = `${(totalW * (quant + 1)) - mr}px`;
}

function CloseModalCreateCena() {
  // Closing animations
  createCenaModalFade.style.backgroundColor = "rgba(0,0,0,0)";
  createCenaModalFade.style.pointerEvents = "none";
  createCenaModal.style.transform = "translateY(20vh)";
  createCenaModal.style.opacity = 0;
  createCenaModal.style.pointerEvents = "none";

  // Remove div and listeners
  if (tempCenaDiv) {
    tempCenaDiv.remove();
    tempCenaDiv = null;
  }
  const sliderOne = document.getElementById("frameInicial");
  const sliderTwo = document.getElementById("frameFinal");
  sliderOne.removeEventListener("input", onInputHandler);
  sliderTwo.removeEventListener("input", onInputHandler);
  document.getElementById("ds_cor").removeEventListener("input", onColorHandler);

  onInputHandler = null;
  onColorHandler = null;
}

function criarCena() {
    const form = document.getElementById("createCenaForm");
    const url = form.action;
    const formData = new FormData(form);

    const nm_cena = document.getElementById("nm_cena");
    const frameInicial = document.getElementById("frameInicial");
    const frameFinal = document.getElementById("frameFinal");
    const ds_cena = document.getElementById("ds_cena");

    if (nm_cena.value?.trim() && ds_cena.value?.trim() && frameInicial.value?.trim() && frameFinal.value?.trim()) {

        if (nm_cena.value.length < 3 || nm_cena.value.length > 45) {
            showNotification("info", "Scene name must be between 3 and 45 characters.");
            return;
        }

        if (ds_cena.value.length < 3 || ds_cena.value.length > 250) {
            showNotification("info", "Scene description must be between 3 and 250 characters.");
            return;
        }

        if (parseInt(frameInicial.value) >= parseInt(frameFinal.value)) {
            showNotification("info", "The initial frame cannot be equal to or greater than the final frame.");
            return;
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: formData
        })
        .then(async response => {
            if (!response.ok) {
                const data = await response.json();

                if (data.errors) {
                    showNotification("error", "Error creating scene.");
                } else if (data.message) {
                    showNotification("error", "There is already a scene in the selected frame range.");
                }
            } else {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification("error", "Unexpected error while creating the scene.");
        });

    } else {
        showNotification("info", "Please fill in all fields.");
    }
}