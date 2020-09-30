function loginAutoFocus() {
    const el_username = document.getElementById("username");
    el_username.focus();
}
function editAutoFocus() {
    const el_album_title = document.getElementById("album_title");
    el_album_title.focus();
}
window.addEventListener("DOMContentLoaded", loginAutoFocus, false);
window.addEventListener("DOMContentLoaded", editAutoFocus, false);
