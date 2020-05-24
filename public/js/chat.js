document.getElementById("mercure-message-input").focus();
window.scrollTo(0, document.body.scrollHeight);

const userName = document.getElementById("userName").value;

const _receiver = document.getElementById('mercure-content-receiver');
const _messageInput = document.getElementById('mercure-message-input');
const _sendForm = document.getElementById('mercure-message-form');

const sendMessage = (message, userName) => {
    if (message.trim() === '') {
        return;
    }

    var time = new Date();
    var s;

    if (time.getMinutes() < 10) {
        s = "0" + time.getMinutes();
    } else {
        s = time.getMinutes();
    }

    fetch(_sendForm.action, {
        method: _sendForm.method,
        body: 'message=' + message + '&groupName=' + document.getElementById("groupName").value + '&time=' + time.getHours() + ":" + s,
        headers: new Headers(
            { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' }
        )
    }).then(() => {
        _messageInput.value = '';
    });
};

_sendForm.onsubmit = (evt) => {
    sendMessage(_messageInput.value, userName);

    evt.preventDefault();
    return false;
};


url.searchParams.append('topic', 'http://chat.workdoo/message/' + document.getElementById("groupName").value);
const eventSource = new EventSource(url, { withCredentials: true });
eventSource.onmessage = (evt) => {
    const data = JSON.parse(evt.data);
    if (!data.mensaje || !data.user) {
        return;
    }
    console.log("hola");
    if (data.user == userName) {
        _receiver.insertAdjacentHTML('beforeend', `
        <div class="row justify-content-end">
        <div class="col-6 justify-content-end d-flex">
        <div class="message bg-secondary col-12 mr-4 mt-2 bg-secondary text-light d-flex align-items-center rounded justify-content-end" style="min-height: 70px;">
        <p class="mt-3" style="word-wrap: break-word; padding:5px; max-width: 100%; " align="right">${
            data.mensaje
            } <br><small> ${
            data.time
            }</small></p></div></div></div>`);
    } else {
        _receiver.insertAdjacentHTML('beforeend', `
        <div class="row">
        <div class="col-6 justify-content-start d-flex">
        <div class="message bg-secondary col-12 ml-4 mt-2 bg-dark text-light d-flex align-items-center rounded justify-content-start" style="min-height: 70px;">
        <p class="mt-3" style="word-wrap: break-word; padding:5px; max-width: 100%; " align="left">${
            data.mensaje
            } <br><small> ${
            data.time} : ${data.user
            } </small></p></div></div></div>`);
    }

    document.getElementById("mercure-message-input").focus();
    window.scrollTo(0, document.body.scrollHeight);
};