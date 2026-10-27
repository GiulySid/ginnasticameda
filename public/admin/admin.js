(() => {
  document.querySelectorAll("[data-add-row]").forEach((button) => {
    const target = document.querySelector(button.getAttribute("data-add-row"));
    const template = document.querySelector(button.getAttribute("data-template"));
    if (!target || !template) return;
    button.addEventListener("click", () => {
      target.appendChild(template.content.cloneNode(true));
    });
  });

  document.addEventListener("click", (event) => {
    const remove = event.target.closest("[data-remove-row]");
    if (!remove) return;
    const row = remove.closest("[data-row]");
    const parent = row?.parentElement;
    if (!row || !parent) return;
    if (parent.querySelectorAll("[data-row]").length <= 1) {
      row.querySelectorAll("input").forEach((input) => {
        input.value = "";
      });
      return;
    }
    row.remove();
  });
})();
