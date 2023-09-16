/**
 * Oculta la capa del loading y ejecuta las funciones generales del body
 */
function Init() {
  const Body = document.querySelector("body");
  let time = 100;

  setTimeout(() => {
    Body.classList.add("ready");
    BodyFunctions();
  }, time);
}

Init();

/***************************************/

/**
 * Condiciona e inicializa las funciones del sitio
 */
function BodyFunctions() {
  Modal();
}

/**
 * Forma abreviada para seleccionar elementos del DOM
 *
 * @param {string} target Query css del/los elementos a seleccionar
 * @param {bool} single `True` para devolver un sólo elemento, `False` para devolver una lista de elementos
 * @param {Element} parent Elemento del cuál se seleccionará el `target`
 *
 * @return
 */
function $(target, single = true, parent = document) {
  if (single) {
    return parent.querySelector(target);
  } else {
    return parent.querySelectorAll(target);
  }
}

/**
 * Controla la apertura y cierre de los modales
 */
function Modal() {
  const openButtons = $('[data-action="Open"]', false);
  const closeButtons = $('[data-action="Close"]', false);
  const editButtons = $('[data-action="Edit"]', false);
  const className = "Modal--active";

  const getModal = (e) => $(`[data-modal-name="${e.dataset.target}"]`);
  const getData = (e) => JSON.parse($("span", true, e).textContent);

  const setData = (data, modal) => {
    let title = $(".Modal__header h2", true, modal);
    let form = $(".Form", true, modal);

    title.textContent = "Editar producto";

    $("#id", true, form).value = data.id;
    $("#title", true, form).value = data.title;
    $("#price", true, form).value = data.price;
    $(`#pet option[value="${data.pet}"]`, true, form).setAttribute(
      "selected",
      true
    );
    $(`#category option[value="${data.category}"]`, true, form).setAttribute(
      "selected",
      true
    );
    $("#submit", true, form).value = "Editar";
  };

  const clearData = (modal) => {
    let title = $(".Modal__header h2", true, modal);
    let form = $(".Form", true, modal);

    title.textContent = "Añade un nuevo producto";

    $("#id", true, form).value = null;
    $("#title", true, form).value = "";
    $("#price", true, form).value = "";

    let pet = $(`#pet option[selected="true"]`, true, form);
    let category = $(`#category option[selected="true"]`, true, form);

    if (pet) pet.removeAttribute("selected");
    if (category) category.removeAttribute("selected");

    $("#submit", true, form).value = "Guardar";
  };

  openButtons.forEach((button) => {
    let modal = getModal(button);

    button.addEventListener("click", () => {
      modal.classList.add(className);
    });
  });
  
  closeButtons.forEach((button) => {
    let modal = getModal(button);
    
    button.addEventListener("click", () => {
      modal.classList.remove(className);
      setTimeout( () => clearData(modal), 350);
    });
  });

  editButtons.forEach((button) => {
    let modal = getModal(button);
    let data = getData(button);

    button.addEventListener("click", () => {
      setData(data, modal);
      modal.classList.add(className);
    });
  });
}
