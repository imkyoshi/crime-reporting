document.addEventListener("DOMContentLoaded", function () {
    // Select the QR Code button
    var generateQrCodeBtn = document.getElementById("generateQrCodeBtn");

    // Add a click event listener to the QR Code button
    generateQrCodeBtn.addEventListener("click", function () {
        // Get the data for the QR Code (you can modify this based on your requirements)
        var qrCodeData = 'Your QR Code Data'; // Replace with the actual data you want in the QR code
        var qrCodeFilename = 'qrcode.png'; // Set the desired filename

        // Send an AJAX request to the server to generate the QR Code
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "QRGenerate.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        // Handle the response from the server
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Parse the JSON response from the server
                var response = JSON.parse(xhr.responseText);

                // Check if the QR code was generated successfully
                if (response.success) {
                    // Display the generated QR code image
                    var qrCodePreview = document.getElementById("qrCodePreview");
                    qrCodePreview.innerHTML = '<img src="' + response.qrCodeImagePath + '" alt="QR Code Preview" class="img-fluid">';
                } else {
                    // Display an error message
                    alert("Error generating QR code.");
                }
            }
        };

        // Send the data to the server
        xhr.send("data=" + encodeURIComponent(qrCodeData) + "&filename=" + encodeURIComponent(qrCodeFilename));
    });
});