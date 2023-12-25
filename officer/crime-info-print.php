<?php
// Start the session
session_start();
// Include database connection and functions files
require_once '../config/db.php';
require_once '../includes/crimeinfo_functions.php';

$crimeinfo = getAllCrimeInfo();
$currentUserID = $_SESSION['user_id'];
$currentUserInfo = getUserById($currentUserID);
$records = retrieveRecords();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Crime Report</title>
    <!-- Bootstrap CSS -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <style>
        /* Your existing styles remain unchanged */
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
        #statement {
            margin-top: 20px;
            padding: 50px;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="../dist/img/sanluislogo.png" style="width:100px;height:100px;"alt="Left Logo" class="logo">
        <div class="center-content">
            <h4>San Luis Municipal Police Station</h4>
            <h5>Poblacion, San Luis, Philippines</h5>
            <h5><b>Hotline No: </b>0926 641 6290 <b>Telephone No: </b>(043) 741-5589</h5>
            <h5><b>sanluismpsbatangas@yahoo.com</h5><br>
            <h4>CRIME REPORT </h4>
        </div>
        <img src="../dist/img/pnp.png" style="width:100px;height:100px;" alt="QR Code">
    </div>
    <div class="container">
        <div class="main-column">
            <label for="name">Email: <strong>BROSOTO, LOWELL JAY GODOYO</strong></label>

            <label for="course">Place of Incident:  <strong>BS in Information Technology</strong></label>
            <label for="major">Suspect Name:  <strong>N/A</strong></label>
        </div>
        <div class="side-column">
            <label for="reference-number">Date &Time of Report:  <strong>2021-12453</strong></label>

            <label for="year-level">Year Level: <strong>Fourth Year</strong></label>
            <label for="year-level">Section: <strong>B</strong></label>
            <label for="year-level">Semester: <strong>1</strong></label>
        </div>
    </div>
    <h4 class="mt-4"><u>Statement</u></h4>
    <textarea name="statement" id="statement" cols="100%" rows="10"><?php echo $crimeinfo['statement']; ?></textarea>
    <!-- <div class="container mt-4">
            <div  id="main-column">
                <label for="email">Email: <strong><?php echo $crimeinfo['email']; ?></strong></label>
                <label for="placeOfIncident">Place of Incident: <strong><?php echo $crimeinfo['placeOfIncident']; ?></strong></label>
                <label for="suspectName">Suspect Name: <strong><?php echo $crimeinfo['suspectName']; ?></strong></label>
                <label for="status">Status: <strong><?php echo $crimeinfo['status']; ?></strong></label>
            </div>
            <div id="side-column">
                <label for="dateTimeOfReport">Date & Time of Report: <strong><?php echo $crimeinfo['dateTimeOfReport']; ?></strong></label>
                <label for="dateTimeOfIncident">Date & Time of Incident: <strong><?php echo $crimeinfo['dateTimeOfIncident']; ?></strong></label>
                <label for="crimetype">Type of Crime: <strong><?php echo $crimeinfo['crimetype']; ?></strong></label>
            </div>
            <h4 class="mt-4"><u>Statement</u></h4>
            <div class="col">
                <textarea name="statement" id="statement" cols="100%" rows="10"><?php echo $crimeinfo['statement']; ?></textarea>
            </div>
    </div> -->

    <!-- Bootstrap JS and Popper.js (if needed) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.print();
        window.onafterprint = function () {
            // This function will be called after the print dialog is closed
            window.history.back(); // Navigate back to the previous page
        };
    </script>
</body>

</html>
