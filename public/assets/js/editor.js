const articleForm = document.querySelector('[data-form=editor]');
const contentData = document.querySelector('[data-content-editor=editor_data]');
const Data = document.getElementById('editor');

let savedData = {};
if(Data.innerText.length > 0 && typeof(Data) != undefined) {

  savedData = JSON.parse(Data.innerText);
  Object.entries(savedData.blocks).forEach(block => {
    if(block[1].data.text != undefined) {
      block[1].data.text = block[1].data.text.replaceAll('&lt;br&gt;', '<br>');
    }
  });
}

Data.innerHTML = "";

const editor_js = new EditorJS({
  holder: 'editor',
  inlineToolBar: ['header', 'text', 'bold', 'italic'],
  tools: {
    header: {
      class: Header,
      config: {
        placeholder: 'Entrez un titre',
        levels: [2,3,4],
        defaultLevel: 3
      }
    },
    // linkTool: LinkTool,
    raw: RawTool,
    image: SimpleImage,
    // checklist: {
    //   class: Checklist,
    //   inlineToolbar: true,
    // },
    list: {
      class: List,
      inlineToolbar: true,
      config: {
        defaultStyle: 'unordered'
      }
    }
  },
  data : savedData
});

articleForm.onsubmit = async (event) => {
  let saver = event.submitter.cloneNode(true);
  saver.type = 'text';
  event.preventDefault();

  // let token = await fetch(window.location.origin+'/api/getcsrftoken').then(response => response.json());

  // sauvegarde des données de l'éditeur dans la variable data
  await editor_js.save().then((response) => { 
  return response;
  }).then(formatted_data => {
    let form_data = new FormData()
    form_data.append('data', JSON.stringify(formatted_data))
    // form_data.append('_token',  token.token);
    
    fetch(window.location.origin+'/marketing/saveImageFromEditor', {
      method: 'POST',
      body: form_data,
    }).then(response => {
      return response.json();
    }).then(result => {
      let html_data = EditorJsParser(result.data);
      contentData.value = JSON.stringify({
        ...result.data, "html": html_data
      });
      articleForm.appendChild(saver);
      console.log(articleForm);
      articleForm.submit();
    });
  }).catch((error) => {
    console.log('Saving failed: ', error)
  });

}

function EditorJsParser(data) {
  let blocks = data.blocks;
  let container = document.createElement('div');
  blocks.forEach((block) => {
    
    let element = null;
    switch(block.type) {
      case 'paragraph':
        element = document.createElement('p');
        element.innerHTML = block.data.text
        break;

      case 'list':
        if(block.data.style == 'unordered') {
          element = document.createElement('ul');
        } else {
          element = document.createElement('ol');
        }
        let listElement = null;
        block.data.items.forEach((item) => {
          listElement = document.createElement('li');
          listElement.innerHTML = item;
          element.appendChild(listElement);
        })
        break;

      case 'header':
        let level = block.data.level;
        element = document.createElement('h'+level);
        element.innerHTML = block.data.text
        element.id = block.id;
        break;

      case 'image': 
        element = document.createElement('figure');
        img = document.createElement('img');
        caption = document.createElement('figcaption');

        img.src = block.data.url;
        img.height = "400";
        caption.innerText = block.data.caption.replace("<br>", "");

        element.appendChild(img);
        element.appendChild(caption);
        break;

      case 'raw':
        element = document.createElement('code');

        element.innerHTML = block.data.html;
        break;
    }
    container.appendChild(element);
  });
  return container.innerHTML;
}