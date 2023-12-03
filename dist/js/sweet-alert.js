// sweet-alert.js
function showSuccessMessage(message, redirectUrl) {
    Swal.fire({
        icon: 'success',
        title: message,
        showConfirmButton: false,
        timer: 1500
    }).then(() => {
        window.location.href = redirectUrl;
    });
}

function showErrorMessage(message) {
    Swal.fire({
        icon: 'error',
        title: 'Failed to submit report',
        text: message,
    });
}
