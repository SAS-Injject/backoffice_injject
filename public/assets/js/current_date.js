let setCurrentDate = () => {
  const DATE_CONTAINER = document.querySelector("#actions-navigation > #datetime > #date ");
  const TIME_CONTAINER = document.querySelector("#actions-navigation > #datetime > #time ");
  let date_now;
  let isSecondFlicker = true;

  if(!!DATE_CONTAINER && !!TIME_CONTAINER) console.log('current_date.js loaded.')

  setInterval(() => {
    if(null !== DATE_CONTAINER && undefined !== DATE_CONTAINER) {
      date_now = Date.now();
  
      DATE_CONTAINER.innerHTML = (new Intl.DateTimeFormat('fr-FR', {
        dateStyle: 'full',
        timeZone: 'Europe/Paris'
      }).format(date_now)); 
      TIME_CONTAINER.innerHTML = (new Intl.DateTimeFormat('fr-FR', {
        timeStyle: 'short',
        timeZone: 'Europe/Paris'
      }).format(date_now));

      isSecondFlicker ? 
        TIME_CONTAINER.innerHTML = TIME_CONTAINER.innerHTML.replace(':', '<span class="fc-main">:</span>'): 
        TIME_CONTAINER.innerHTML = TIME_CONTAINER.innerHTML.replace(':', '<span class="fc-white">:</span>');
      isSecondFlicker = !isSecondFlicker;
    }
  }, 1000);

}

setCurrentDate();