// Event for delete album confirmation
const delete_album = document.getElementById("delete_album");
delete_album.onclick = function() {
    return confirmDeleteAlbum();
}
function confirmDeleteAlbum() {
    if (confirm("Are you sure you want to delete this album?")) {
        return true;
    }
    return false;
}

// Event for delete comment confirmation
const delete_comment = document.getElementById("delete_comment");
delete_comment.onclick = function() {
    return confirmDeleteComment();
}
function confirmDeleteComment() {
    if (confirm("Are you sure you want to delete this comment?")) {
        return true;
    }
    return false;
}