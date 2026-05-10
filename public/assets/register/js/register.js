const message = document.getElementById('message');
if (message) {
    setTimeout(() => {
        message.classList.add('show');
    }, 100);

    setTimeout(() => {
        message.classList.remove('show');
    }, 4000);
}
