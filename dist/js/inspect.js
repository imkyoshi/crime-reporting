document.addEventListener("keydown", function (e) {
    if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'C') {
        alert("This function is disabled.");
        e.preventDefault();
    }
});

document.addEventListener("keydown", function (e) {
    if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'I') {
        alert("This function is disabled.");
        e.preventDefault();
    }
});

document.addEventListener("contextmenu", function (e) {
    alert("Right-click is disabled.");
    e.preventDefault();
});
