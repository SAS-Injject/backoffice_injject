
const DROP_CONTAINERS = document.querySelectorAll(".drop-container");

DROP_CONTAINERS.forEach((DROP_CONTAINER) => {

  const FILE_INPUT = document.querySelector(`input[data-drop]#${DROP_CONTAINER.getAttribute('for')}`);
  
  DROP_CONTAINER.addEventListener("dragover", (e) => {
    e.preventDefault()
  }, false);

  DROP_CONTAINER.addEventListener("dragenter", () => {
    DROP_CONTAINER.classList.add("drag-active")
  });

  DROP_CONTAINER.addEventListener("dragleave", () => {
    DROP_CONTAINER.classList.remove("drag-active")
  });

  DROP_CONTAINER.addEventListener("drop", (e) => {
    e.preventDefault()
    DROP_CONTAINER.classList.remove("drag-active")
    FILE_INPUT.files = e.dataTransfer.files
    console.log(window.URL.createObjectURL(FILE_INPUT.files[0]));
  });
})
