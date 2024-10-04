const selectors = document.querySelectorAll("select[data-tselect]");
console.log(selectors);
selectors.forEach((selector) => {
  if(!!selector.id) {
    new TomSelect(`#${selector.id}`, {
      maxItems: !!selector.dataset.tsMaxitems ? parseInt(selector.dataset.tsMaxitems) : 100,
      allowEmptyOption: !!selector.dataset.tsAllowempty ? true : false, 
    })
  } else {
    console.error('No id defined for tom select component.')
  }
})