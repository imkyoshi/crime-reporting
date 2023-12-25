<?php
require_once '../config/db.php';

function getCrimeInfoById($mysqli, $crimeId)
{
    $stmt = $mysqli->prepare("SELECT * FROM crime_information WHERE crime_id = ?");
    $stmt->bind_param("i", $crimeId);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $crimeinfo = $result->fetch_assoc();
    } else {
        // Handle the case where no record is found
        // You may want to set default values or display an error message
        $crimeinfo = array(
            'email' => '',
            'placeOfIncident' => '',
            'status' => '',
            'dateTimeOfReport' => '',
            'dateTimeOfIncident' => '',
            'CrimeType' => ''
            // Add other fields as needed
        );
    }

    $stmt->close();

    return $crimeinfo;
}

// Usage example:
$crimeId = 1; // Replace with the actual crime ID you want to retrieve
$crimeinfo = getCrimeInfoById($mysqli, $crimeId);

// ... (The rest of your code)
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Assessment Form</title>
    <style>
        h3 {
            text-align: center;
            font-family: 'Arial Narrow';
            margin: 0px;
        }

        h5,
        h2,
        h4 {
            text-align: center;
            /*marginp per word or letter*/
            margin: 0px;

        }

        .container {
            display: flex;
        }

        .main-column {
            flex: 1;
            padding: 20px;

        }

        .side-column {
            flex: 1;
            padding: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        /* Reset default table margin and padding */
        table {
            margin: 0;
            padding: 0;
            border-collapse: collapse;
        }

        /* Center the thead */
        thead {
            text-align: center;
            background-color: #f2f2f2;
            /* Optional: Add a background color to the header */
        }

        /* Style the th elements */
        th {
            padding: 10px;
            border: 1px solid #ccc;
        }

        /* Align the tbody to the right */
        tbody {
            text-align: left;
        }

        /* Style the td elements */

        /* Center the table on the page */
        .table-container {
            display: flex;
            justify-content: center;
        }

        /* Make the table auto-fit with a max-width */
        .custom-table {
            max-width: 100%;
            width: auto;
            border-collapse: collapse;
        }

        /* Style the th and td elements */
        th {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }

        td {
            padding-top: 0;
            padding-bottom: 0;
            padding-left: 5px;
            padding-right: 50px;
            border: 1px solid #ccc;
            text-align: left;
        }

        .marg {
            margin-top: 20px;
        }

        .signatory {
            page-break-before: always;
            /* Ensure the signatory section starts on a new page */
        }

        @media print {
            body {
                margin: 0 auto;
                /* Center the content */
                background-image: url('/Cashier/images/logo.png');
                /* Set the watermark image */
                background-repeat: no-repeat;
                background-position: center;
            }
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial Narrow', sans-serif;
            font-size: 14px;
        }

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
</head>

<body>
    <div class="header">
        <img src="../dist/img/sanluislogo.png" alt="Left Logo" class="logo">
        <div class="center-content">
            <h4>San Luis Municipal Police Station</h4>
            <h5>Poblacion, San Luis, 4210, PH</h5>
            <h4>Hotline No: 0926 641 6290 Telephone No: (043) 741-5589</h4>
            <h4>sanluismpsbatangas@yahoo.com</h4><br><br>
            <h4>CRIME REPORT</h4>
        </div>
        <img src="../dist/img/pnp.png" alt="QR Code" class="logo">
    </div>

    <div class="container">
        <div class="main-column">
            <label for="email">Email: <strong><?php echo $crimeinfo['email']; ?></strong></label>

            <label for="course">Place Of Incident: <strong><?php echo $crimeinfo['placeOfIncident']; ?></strong></label>
            <label for="major">Suspect Name: <strong>Earl</strong></label>
            <label>Status: <strong><?php echo $crimeinfo['status']; ?></strong></label>
        </div>
        <div class="side-column">
            <label for="reference-number">Date and Time Of Report: <strong><?php echo $crimeinfo['dateTimeOfReport']; ?></strong></label>

            <label for="year-level">Date and Time Of Incident: <strong><?php echo $crimeinfo['dateTimeOfIncident']; ?></strong></label>
            <label for="year-level">Type Of Crime: <strong> <?php echo $crimeinfo['CrimeType']; ?></strong></label>
        </div>
    </div>
    <br>
    <h4><u>STATEMENT</u></h4>
    <div class="container">
        <div class="main-column">
            <div class="marg">
                <textarea name="statement" id=""  rows="20" style="width: 100%; text-align:justify;" >
                <?php echo $crimeinfo['statement']; ?>
                </textarea>
            </div>

        </div>


    </div>
    <br><br>

    <!-- <script>
        window.print();
        window.onafterprint = function() {
            // This function will be called after the print dialog is closed
            window.history.back(); // Navigate back to the previous page
        };
    </script>
     -->

</body>

</html>