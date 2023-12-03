document.addEventListener("DOMContentLoaded", function() {
    // Get the success message element
    var successMessage = document.querySelector(".alert.alert-success");

    // Check if the success message exists
    if (successMessage) {
        // Hide the success message after 5 seconds
        setTimeout(function() {
            successMessage.style.display = "none";
        }, 3000); // 3000 milliseconds = 3 seconds
    }
});