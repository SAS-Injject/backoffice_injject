const trigger_filters = document.querySelectorAll('button[data-filter]');

trigger_filters.forEach( (trigger) => {
  let select = document.querySelector('#'+trigger.dataset.filter);
  trigger.onclick = () => {
    let options = select.selectedOptions;
    options = Array.from(options);
    let values = options.map( option => {
      return option.value
    });
    let url = trigger.dataset.href+"?categories="+encodeURIComponent(JSON.stringify(values));
    window.location.href = url
  }
})