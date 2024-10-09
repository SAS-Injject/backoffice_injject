let links = document.querySelectorAll("[data-delete]");

links.forEach((link) => {
  link.style.cursor = "pointer";
  link.addEventListener('click', (event) => {
    event.preventDefault();

    if(confirm("Supprimer l'image ?")) {
      fetch(link.getAttribute("href"), {
        method: "DELETE", 
        headers: {
          "Content-Type": "Application/json"
        },
        body: JSON.stringify({'_token': link.dataset.token})
      }).then(response => response.json())
      .then(result => {
        if(result.success) {
          link.parentElement.remove();
        } else {
          alert(result.error);
        }
      })
    }
  })
})