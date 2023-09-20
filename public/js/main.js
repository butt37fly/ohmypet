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
  Cards();
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

  if (!openButtons || !closeButtons || !editButtons) return;

  const getModal = (e) => $(`[data-modal-name="${e.dataset.target}"]`);
  const getData = (e) => JSON.parse($("span", true, e).textContent);

  const setData = (data, modal) => {
    let title = $(".Modal__header h2", true, modal);
    let form = $(".Form", true, modal);

    title.textContent = "Editar producto";

    $("#id", true, form).value = data.id;
    $("#title", true, form).value = data.title;
    $("#price", true, form).value = data.price;
    $("#amount", true, form).value = data.amount;
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
    $("#amount", true, form).value = "";

    let pet = $(`#pet option[selected="true"]`, true, form);
    let category = $(`#category option[selected="true"]`, true, form);

    if (pet) pet.removeAttribute("selected");
    if (category) category.removeAttribute("selected");

    $("#submit", true, form).value = "Guardar";
  };

  openButtons.forEach((button) => {
    let modal = getModal(button);

    if (!modal) return;

    button.addEventListener("click", () => {
      modal.classList.add(className);
    });
  });

  closeButtons.forEach((button) => {
    let modal = getModal(button);

    if (!modal) return;

    button.addEventListener("click", () => {
      modal.classList.remove(className);
      setTimeout(() => clearData(modal), 350);
    });
  });

  editButtons.forEach((button) => {
    let modal = getModal(button);
    let data = getData(button);

    if (!modal) return;

    button.addEventListener("click", () => {
      setData(data, modal);
      modal.classList.add(className);
    });
  });
}

/**
 *
 */
function Cards() {
  const cards = $(".Card", false);
  const className = "Card__wrapper--flipped";

  if (!cards) return;

  const setListeners = (els, target) => {
    els.forEach((el) => {
      el.addEventListener("click", () => {
        target.classList.toggle(className);
      });
    });
  };

  const copyLink = async (link) => {
    await navigator.clipboard.writeText(link);
  };

  const share = (el) => {
    let link = el.dataset.link;
    let defaultMsg = el.dataset.tooltip;
    let newMsg = "¡Link copiado al portapapeles!";
    let className = "checked";

    el.addEventListener("click", () => {
      try {
        copyLink(link);
        el.classList.add(className);
        el.dataset.tooltip = newMsg;
      } catch (error) {
        el.classList.remove(className);
        el.classList.add("error");
        el.dataset.tooltip =
          "Parece que algo ha salido mal, inténtalo de nuevo más tarde";
      } finally {
        setTimeout(() => {
          el.classList.remove(className);
          el.dataset.tooltip = defaultMsg;
        }, 5000);
      }
    });
  };

  cards.forEach((card) => {
    let wrapper = $(".Card__wrapper", true, card);
    let triggers = $('[data-action="flip"]', false, card);
    let shareButton = $('[data-action="share"]', true, card);

    if (!wrapper || !triggers) return;

    setListeners(triggers, wrapper);

    if (!shareButton) return;

    share(shareButton);
  });
}
