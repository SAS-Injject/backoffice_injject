const handleFlashMessage = () => {
  const FLASH_MESSAGES_CONTAINER = document.querySelector('#flash-messages');
  const FLASH_MESSAGES = document.querySelectorAll('#flash-messages .flash-item');

  let count = 0;

  FLASH_MESSAGES.forEach((message) => {
    count += 1;
    message.addEventListener('pointerdown', () => {
      message.remove();
    })
    setTimeout(() => {
      message.remove()
    }, 10000*count);
  })
}

handleFlashMessage();