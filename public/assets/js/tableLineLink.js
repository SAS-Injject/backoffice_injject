const lineLink = document.querySelectorAll('.line-link');
const remove_buttons = document.querySelectorAll('[data-remove]');
let removing = false;

if (!!remove_buttons) {
  remove_buttons.forEach(remove_button => {
    remove_button.onclick = event => {
      event.preventDefault();
      removing = true;
      if(window.confirm("Voulez-vous supprimer l'élément "+remove_button.dataset.remove+" ?")){
        window.location.href = remove_button.href;
      }
    }
  })
}

if(lineLink != undefined){
  lineLink.forEach((element) => {
    if(('href') in element.dataset){
      element.onclick = () => {
        console.log(removing)
        if(!removing) {
          window.location.href = element.dataset.href;
        }
      }
    }
  });
}