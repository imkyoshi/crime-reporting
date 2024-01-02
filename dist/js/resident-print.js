function printModal1() {
    // Set the title directly using document.title
    document.title = "Resident Information Print Form";

    // Header content with styles
    var headerContent = `
        <style>
            .header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 1rem;
            }

            .logo {
                width: 100px;
                height: 100px;
            }

            .header h3 {
                margin: 0;
                font-size: 1.5rem;
            }

            /* Additional styles for the enlarged heading */
            .header .crime {
                margin: 0;
                padding-bottom: 10px;
                font-size: 2.0rem; /* Set the desired font size for the CRIME REPORT heading */
            }

            .header h4 {
                margin: 0;
                font-size: 1rem;
            }

            .header h2 {
                margin: 0;
                font-size: 2rem;
            }

            .header h5 {
                margin: 0;
                font-size: 0.875rem;
            }

            .center-content {
                text-align: center;
            }

            .center-content h3,
            .center-content h4,
            .center-content h2,
            .center-content h5 {
                margin: 0;
            }

            .center-content h3 {
                font-size: 1.5rem;
            }

            .center-content h4 {
                font-size: 1rem;
            }

            .center-content h2 {
                font-size: 2rem;
            }

            .center-content h5 {
                font-size: 0.875rem;
            }
        </style>
        <div class="header">
            <img src="../dist/img/sanluislogo.png" style="width:100px;height:100px;" alt="Left Logo" class="logo">
            <div class="center-content">
                <h4>San Luis Municipal Police Station</h4>
                <h5>Poblacion, San Luis, Philippines</h5>
                <h5><b>Hotline No: </b>0926 641 6290 <b>Telephone No: </b>(043) 741-5589</h5>
                <h5><b>sanluismpsbatangas@yahoo.com</h5><br>
                <h4 class="crime">RESIDENT LIST </h4>
            </div>
            <img src="../dist/img/pnp.png" style="width:100px;height:100px;" alt="QR Code">
        </div>
    `;
    // Modal body content
    var printableContent = headerContent + document.getElementById('printable-modal-body1').innerHTML;
    
    var originalContent = document.body.innerHTML;

    document.body.innerHTML = printableContent;

    // Trigger print and listen for both 'afterprint' and 'beforeprint' events
    window.print();

    // Listen for the 'afterprint' event to close the modal after successful printing
    window.onafterprint = function () {
        document.body.innerHTML = originalContent;
        window.onafterprint = null; // Remove the event listener to prevent unwanted behavior
        // Redirect to the specified URL after printing
        window.location.href = 'http://localhost/crime-reporting/officer/crime-info.php';
    };

    // Listen for the 'beforeprint' event to redirect if the user cancels the print
    window.onbeforeprint = function () {
        document.body.innerHTML = originalContent;
        window.onbeforeprint = null; // Remove the event listener to prevent unwanted behavior
        // Redirect to the specified URL after canceling print
        window.location.href = 'http://localhost/crime-reporting/officer/crime-info.php';
    };
}
