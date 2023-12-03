function togglePasswordVisibility() {
        var passwordInput = document.querySelector('.password-input');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
        } else {
            passwordInput.type = 'password';
        }
    }


// Sidebar toggle
$(document).ready(function () {
    $("#sidebarCollapse").on('click', function () {
        $("#sidebar").toggleClass('active');
    });
});

// Submit the form when the select option changes
document.getElementById('showRecords').addEventListener('change', function () {
    document.getElementById('showRecordsForm').submit();
});

// Handle form submission when typing stops
let typingTimer;
const doneTypingInterval = 500; // Delay in milliseconds

// Handle form submission when typing stops
const searchInput = document.getElementById('searchInput');
searchInput.addEventListener('input', function () {
    clearTimeout(typingTimer);
    typingTimer = setTimeout(submitSearchForm, doneTypingInterval);
});

// Submit the search form
function submitSearchForm() {
    document.getElementById('searchForm').submit();
}

// Prevent form submission when pressing Enter key
searchInput.addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
        event.preventDefault();
    }
});

