<?php
// Start the session
session_start();

// Check if the user is not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['roles'] !== 'officer') {
  header("Location: ../auth/login.php");
  exit;
}

// Include database connection and functions files
require_once '../config/db.php';
require_once '../includes/crimeinfo_functions.php';
require_once '../api/phpqrcode/qrlib.php';

$crimeinfo = getAllCrimeInfo();
$currentUserID = $_SESSION['user_id'];
$currentUserInfo = getUserById($currentUserID);
$records = retrieveRecords();

// Handle form submission for adding a Crime Information
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addCrimeInfo'])) {
    // Retrieve form data
    $fullName = $_POST['fullName'];
    $phoneNumber = $_POST['phoneNumber'];
    $formFileValidID = handleFileUpload('formFileValidID', __DIR__ 
        . DIRECTORY_SEPARATOR . 'dist' 
        . DIRECTORY_SEPARATOR . 'uploads' 
        . DIRECTORY_SEPARATOR . 'valid_id' 
        . DIRECTORY_SEPARATOR);
    $dateTimeOfReport = $_POST['dateTimeOfReport'];
    $dateTimeOfIncident = $_POST['dateTimeOfIncident'];
    $placeOfIncident = $_POST['placeOfIncident'];
    $suspectName = $_POST['suspectName'];
    $crimetype = $_POST['CrimeType'];
    $statement = $_POST['statement'];
    $formFileEvidence = handleFileUpload('formFileEvidence', __DIR__ 
        . DIRECTORY_SEPARATOR . 'dist' 
        . DIRECTORY_SEPARATOR . 'uploads' 
        . DIRECTORY_SEPARATOR . 'evidence' 
        . DIRECTORY_SEPARATOR);
    $status = $_POST['status'];

    // Insert data into the database
    $result = addCrimeInfo($fullName, $phoneNumber, $formFileValidID, $dateTimeOfReport, $dateTimeOfIncident, $placeOfIncident, $suspectName, $statement, $formFileEvidence, $crimetype, $status);

    if ($result === "Crime Information added successfully.") {
        header("Location: ../admin/crime-info.php");
        exit;
    } elseif ($result === "Crime Information with this name already exists.") {
        $addErrorMessage = "Crime Information already exists.";
    } else {
        $addErrorMessage = "Failed to add Crime Information.";
    }
}

// Handle form submission for updating a Resident
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateCrimeInfo'])) {
    // Retrieve form data
    $crime_id = $_POST['crime_id'];
    $fullName = $_POST['fullName'];
    $phoneNumber = $_POST['phoneNumber'];
    $dateTimeOfReport = $_POST['dateTimeOfReport'];
    $dateTimeOfIncident = $_POST['dateTimeOfIncident'];
    $placeOfIncident = $_POST['placeOfIncident']; 
    $suspectName = $_POST['suspectName'];
    $crimetype = $_POST['crimetype'];
    $statement = $_POST['statement'];
    $status = $_POST['status'];

    // Handle file uploads
    $formFileValidID = handleFileUpload('formFileValidID', '../dist/uploads/valid_id/');
    $formFileEvidence = handleFileUpload('formFileEvidence', '../dist/uploads/evidence/');

    // Generate QR code data
    $qrCodeData = "Full Name: {$fullName}\n";
    $qrCodeData .= "Mobile No: {$phoneNumber}\n";
    $qrCodeData .= "Reported At: {$dateTimeOfReport}\n";
    $qrCodeData .= "Incident At: {$dateTimeOfIncident}\n";
    $qrCodeData .= "Place: {$placeOfIncident}\n";
    $qrCodeData .= "Suspect: {$suspectName}\n";
    $qrCodeData .= "Crime Type: {$crimetype}\n";
    $qrCodeData .= "Statement: {$statement}\n";
    $qrCodeData .= "Status: {$status}\n";

    

    // Generate QR code image and save it
    $qrCodePath = __DIR__ . DIRECTORY_SEPARATOR . ".."
        . DIRECTORY_SEPARATOR . "dist"
        . DIRECTORY_SEPARATOR . "qrcodes"
        . DIRECTORY_SEPARATOR;
    // Create the qrcodes directory if it doesn't exist
    if (!is_dir($qrCodePath)) {
        mkdir($qrCodePath, 0777, true);
    }

    $qrCodeFileName = uniqid() . "_" . time() . ".png";
    $qrCodeFullPath = "{$qrCodePath} {$qrCodeFileName}";
    QRcode::png($qrCodeData, $qrCodeFullPath);

    // Update crime information
    $result = updateCrimeInfo($crime_id, $fullName, $phoneNumber, $formFileValidID, $dateTimeOfReport, $dateTimeOfIncident, $placeOfIncident, $suspectName, $statement, $formFileEvidence, $crimetype, $status);

    if ($result) {
        // Update QR code filename in the database
        updateQRCodeFilename($crime_id, $qrCodeFileName);
        $updateSuccessMessage = 'Crime information updated successfully.';
    } else {
        $errorMessage = 'Failed to update crime information.';
    }
}



// Handle form submission for deleting a Crime Information
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteCrimeInfo'])) {
    $crime_id = $_POST['crime_id'];

    $result = deleteCrimeInfo($crime_id);
    if ($result) {
        $deleteSuccessMessage = 'Crime Information deleted successfully.';
    } else {
        $errorMessage = 'Failed to delete Resident.';
    }
}

$limit = isset($_GET['showRecords']) ? intval($_GET['showRecords']) : 5;

$totalCrimeinfo = getTotalCrimeInfoCount();
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
$totalPages = 3;
$startIndex = ($currentPage - 1) * $limit;
$endIndex = min($startIndex + $limit, $totalCrimeinfo);
$crimeinfos = getCrimeInfoWithLimitAndOffset($limit, $startIndex);
$paginationLinks = generatePaginationLinks($currentPage, $totalPages);
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search = $_GET['search'];

    if (!empty($search)) {
        $crimeinfos = getCrimeInfoWithSearchLimitAndOffset($search, $limit, $startIndex);
    }
}



?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>San Luis Municipal Police Station</title>
    <link rel="icon" type="image/x-icon" href="../dist/img/favicon.ico">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=fallback">
    <!-- Line Awesome Icons -->
    <link rel="stylesheet" href="../plugins/line-awesome-free/css/line-awesome.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../dist/css/dashboard.css">
    <link rel="stylesheet" href="../dist/css/viewprint.css">
    

    <!-- Mapquest CDN -->
    <link type="text/css" rel="stylesheet" href="https://api.mqcdn.com/sdk/place-search-js/v1.0.0/place-search.css" />
    <link type="text/css" rel="stylesheet" href="https://api.mqcdn.com/sdk/mapquest-js/v1.3.2/mapquest.css" />
    <link href="../dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {

            .modal-footer {
                display: none;
            }
            #status, #actionss {
                display: none;
            }
                /* Styles for the three-column layout */
                .container {
                display: flex;
                min-width: 100%;
            }

            .left {
                flex: 1.1;
                margin-left: 5px;
                padding-top: 5px;
        
            }
            
            .middle{
                flex: 1.2;
                margin-left: 60px;
                padding-top: 5px;
                box-sizing: border-box;
            }

            .right {
                margin-left: 30px;
                padding-top: 5px;
        
            }

            /* Add additional styling as needed */
            .form-group {
                margin-bottom: 10px;
            }

            p {
                margin: 0;
            }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="las la-bars"></i></a>
                </li>
            </ul>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4 " style="background-color:#0B2436;">
            <!-- Brand Logo -->
            <a href="index3.html" class="brand-link">
                <img src="../dist/img/PNP.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
                    style="opacity: .8">
                <span class="brand-text font-weight-light">San Luis Police Station</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="info text-light">
                        Welcome
                    </div>
                    <div class="info  text-warning">
                        <?php echo $currentUserInfo['fullName']; ?>!
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <!-- Add icons to the links using the .nav-icon class
                with font-awesome or any other icon font library -->
                        <li class="nav-item">
                            <a href="dashboard.php" class="nav-link">
                                <i class="las la-home" id="icon"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="crime-info.php" class="nav-link">
                                <i class="las la-gavel" id="icon"></i>
                                <p>
                                    Crime Information
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../officer/suspect-info.php" class="nav-link">
                                <i class="las la-gavel" id="icon"></i>
                                <p>
                                    Suspect Information
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="resident-info.php" class="nav-link">
                                <i class="las la-archive" id="icon"></i>
                                <p>
                                    Reisdent Information
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../auth/logout.php" class="nav-link">
                                <i class="las la-sign-out-alt" id="icon"></i>
                                <p>
                                    Logout
                                </p>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Crime Information</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Crime Information</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>

            <div class="col-sm-12">
                <!-- Error message for adding a user -->
                <div class="error-message">
                    <?php if (isset($addErrorMessage)): ?>
                    <div id="successMessage" class="alert alert-danger">
                        <?php echo $addErrorMessage; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>


            <!-- /.content-header -->
            <div class="col-sm-12">
                <!-- Success message for user update -->
                <?php if (isset($updateSuccessMessage)): ?>
                <div id="successMessage" class="alert alert-success">
                    <?php echo $updateSuccessMessage; ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="col-sm-12">
                <!-- Success message for user update -->
                <?php if (isset($deleteSuccessMessage)): ?>
                <div id="successMessage" class="alert alert-success">
                    <?php echo $deleteSuccessMessage; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <!-- Default box -->
                            <div class="card shadow card-outline card-primary">
                                <div class="card-body">
                                    <!-- ADD NEW-->
                                    <div class="row mb-2">
                                        <div class="col-sm-12" style="margin-top: 15px;">
                                            <button type="button" class="btn btn-primary btn-sm float-left"
                                                data-toggle="modal" data-target="#addCrimeInfoModal"><i
                                                    class="las la-plus-circle"></i> Add New Crime Information</button>
                                            <button type="button" class="btn btn-success btn-sm float-left"
                                                style="margin-left:10px;" onclick="printModal1()"><i class="las la-print"></i> Print</button>
                                        </div>
                                        <!-- Add User Modal -->
                                        <div class="modal fade" id="addCrimeInfoModal" tabindex="-1" role="dialog"
                                            aria-labelledby="addCrimeInfoModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="addCrimeInfoModalLabel">Add New
                                                            Crime Information</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="POST" action="" enctype="multipart/form-data">
                                                            <div class="form-group">
                                                                <label for="fullName">Full Name</label>
                                                                <input type="text" class="form-control" id="fullName"
                                                                    placeholder="Enter your Full Name" name="fullName">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="phoneNumber">Mobile No</label>
                                                                <input type="text" class="form-control" id="phoneNumber"
                                                                    placeholder="Enter your Full Name" name="phoneNumber">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="formFileValidID" class="form-label">Upload
                                                                    Valid ID:</label>
                                                                <div class="label-wrapper">
                                                                    <input class="form-control" type="file"
                                                                        id="formFileValidID" name="formFileValidID"
                                                                        multiple onchange="previewValidID()">
                                                                </div>
                                                                <div id="ValidIDPreviews" style="margin-top: 10px;">
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="dateTimeOfReport">Date and Time of
                                                                    Report:</label>
                                                                <input type="datetime-local" class="form-control"
                                                                    id="dateTimeOfReport" name="dateTimeOfReport"
                                                                    required="">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="dateTimeOfIncident">Date and Time of
                                                                    Incident:</label>
                                                                <input type="datetime-local" class="form-control"
                                                                    id="dateTimeOfIncident" name="dateTimeOfIncident"
                                                                    required="">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="placeOfIncident" class="form-label">Place of
                                                                    Incident:</label>
                                                                <input type="search" id="search-input"
                                                                    name="placeOfIncident" class="form-control" />
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="suspectName">Suspect Name</label>
                                                                <input type="text" class="form-control" id="suspectName"
                                                                    name="suspectName" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="typeOfCrime">Type of Crime</label>
                                                                <!-- <select class="form-control" id="crimetype"
                                                                    name="crimetype">
                                                                    <?php
                                                                $records = retrieveRecords();

                                                                if (empty($records)) {
                                                                    echo '<option value="" disabled>No crime types found</option>';
                                                                } else {
                                                                    foreach ($records as $record) {
                                                                        echo '<option value="' . $record['crimeType'] . '">' . $record['crimeType'] . '</option>';
                                                                    }
                                                                }
                                                                ?>
                                                                </select> -->
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="statement">Statement:</label>
                                                                <textarea class="form-control form-control-md"
                                                                    id="exampleTextarea" name="statement" rows="6"
                                                                    placeholder="Enter your statement here"></textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="formFileEvidence" class="form-label">Upload
                                                                    Evidence:</label>
                                                                <div class="label-wrapper">
                                                                    <input class="form-control" type="file"
                                                                        id="formFileEvidence" name="formFileEvidence"
                                                                        multiple onchange="previewEvidence()">
                                                                </div>
                                                                <div id="EvidencePreviews" style="margin-top: 10px;">
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="status">Status</label>
                                                                <select class="form-control" id="status" name="status">
                                                                    <option value="Pending">Pending</option>
                                                                    <option value="UnderInvestigation">Under
                                                                        Investigation</option>
                                                                    <option value="Confirmed">Confirmed</option>
                                                                </select>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary"
                                                                    name="addCrimeInfo">Save</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- SHOW RECORDS -->
                                        <div class="col-sm-9" style="margin-top: 15px;">
                                            <form id="showRecordsForm" method="GET" action="">
                                                <div class="form-group">
                                                    <label class="d-flex align-items-center pt-4">
                                                        <span class="ml-2"><i class="las la-filter"></i>Show </span>
                                                        <select class="form-control form-control-sm" id="showRecords"
                                                            name="showRecords" style="width: 60px; text-align:center;"
                                                            onchange="this.form.submit()">
                                                            <option value="5" <?php if ($limit==5) echo 'selected' ; ?>
                                                                >5</option>
                                                            <option value="10" <?php if ($limit==10) echo 'selected' ;
                                                                ?>>10
                                                            </option>
                                                            <option value="20" <?php if ($limit==20) echo 'selected' ;
                                                                ?>>20
                                                            </option>
                                                            <option value="50" <?php if ($limit==50) echo 'selected' ;
                                                                ?>>50
                                                            </option>
                                                        </select>
                                                        <span class="ml-2"><i class="las la-filter ps-2"></i> records
                                                            per
                                                            page</span>
                                                    </label>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- SEARCH -->
                                        <div class="col-sm-3" style="margin-top: 15px;">
                                            <div class="form-inline">
                                                <form id="searchForm" method="GET" action="">
                                                    <div class="form-group mx-sm-3 mb-2 ps-5">
                                                        <label for="searchInput" class="mr-2"><i
                                                                class="las la-search"></i>
                                                            Search:</label>
                                                        <input type="text" class="form-control" id="searchInput"
                                                            name="search" style="max-width: 200px;"
                                                            value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <!-- TABLE-->
                                    </div>
                                    <div id="printable-modal-body1" class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>QR Codes</th>
                                                    <th>Full Name</th>
                                                    <th>Phone Number</th>
                                                    <th>Date and Time of Report</th>
                                                    <th>Date and Time of Incident</th>
                                                    <th>Place of Incident</th>
                                                    <th>Suspect Name</th>
                                                    <th>Type of Crime</th>
                                                    <th>Statement</th>
                                                    <th id="status">Status</th>
                                                    <th id="actionss" style="text-align:center;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($crimeinfos as $crimeinfo): ?>
                                                <tr>
                                                    <td>
                                                        <img src="<?php echo '../dist/qrcodes/' . $crimeinfo['qrcode']; ?>" alt="QR Code" width="70" height="70">
                                                    </td>
                                                    <td>
                                                        <?php echo $crimeinfo['fullName']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $crimeinfo['phoneNumber']; ?>
                                                    </td>
                                                    <!-- <td>
                                                            <?php echo $crimeinfo['formFileValidID']; ?>
                                                        </td> -->
                                                    <td>
                                                        <?php echo $crimeinfo['dateTimeOfReport']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $crimeinfo['dateTimeOfIncident']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $crimeinfo['placeOfIncident']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $crimeinfo['suspectName']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $crimeinfo['CrimeType']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $crimeinfo['statement']; ?>
                                                    </td>
                                                    <!-- <td>
                                                            <?php echo $crimeinfo['formFileEvidence']; ?>
                                                        </td> -->
                                                    <td id="status">
                                                        <?php $status = $crimeinfo['status'];

                                                            if ($status == 'Pending') {
                                                                $badgeClass = 'badge-warning';
                                                                $textColorClass = 'text-dark'; // Black text color for warning
                                                            } elseif ($status == 'UnderInvestigation') {
                                                                $badgeClass = 'badge-primary';
                                                                $textColorClass = 'text-light'; // White text color for primary
                                                            } elseif ($status == 'Confirmed') {
                                                                $badgeClass = 'badge-success';
                                                                $textColorClass = 'text-light'; // White text color for success
                                                            } else {
                                                                $badgeClass = 'badge-secondary';
                                                                $textColorClass = 'text-light'; // White text color for secondary
                                                            }
                                                        ?>
                                                        <span class="badge <?php echo $badgeClass . ' ' . $textColorClass; ?>">
                                                            <?php echo $status; ?>
                                                        </span>
                                                    </td>
                                                    <td id="actionss" style="text-align:center;">
                                                        <button type="button" class="btn btn-success btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#editCrimeInfoModal<?php echo $crimeinfo['crime_id']; ?>"><i
                                                                class="las la-edit"></i>
                                                            Update</button>
                                                        <button type="button" class="btn btn-warning btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#viewCrimeInfoModal<?php echo $crimeinfo['crime_id']; ?>"><i
                                                                class="las la-eye"></i>
                                                            View</button>
                                                    </td>
                                                </tr>
                                                <!-- Edit Crime Information Modal -->
                                                <div class="modal fade"
                                                    id="editCrimeInfoModal<?php echo $crimeinfo['crime_id']; ?>"
                                                    tabindex="-1" role="dialog"
                                                    aria-labelledby="editCrimeInfoModalLabel<?php echo $crimeinfo['crime_id']; ?>"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg"
                                                        role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="editCrimeInfoModalLabel<?php echo $crimeinfo['crime_id']; ?>">
                                                                    Update Crime Information</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form method="POST" action=""
                                                                    enctype="multipart/form-data">
                                                                    <input type="hidden" name="crime_id"
                                                                        value="<?php echo $crimeinfo['crime_id']; ?>">
                                                                        <div class="form-group">
                                                                        <label for="editFullName">Full Name</label>
                                                                        <input type="text" class="form-control"
                                                                            id="editFullName" name="fullName"
                                                                            value="<?php echo $crimeinfo['fullName']; ?>"
                                                                            required>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="editPhoneNumber">Phone Number</label>
                                                                        <input type="text" class="form-control"
                                                                            id="editPhoneNumber" name="phoneNumber"
                                                                            value="<?php echo $crimeinfo['phoneNumber']; ?>"
                                                                            required>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="formFileValidID" class="form-label">Upload
                                                                            Valid ID:</label>
                                                                        <div class="label-wrapper">
                                                                            <input class="form-control" type="file"
                                                                                id="formFileValidID" name="formFileValidID"
                                                                                multiple onchange="previewValidID()">
                                                                        </div>
                                                                        <div id="ValidIDPreviews" style="margin-top: 10px;">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="editDateTimeOfReport">Date and Time
                                                                            of
                                                                            Report:</label>
                                                                        <input type="datetime-local"
                                                                            class="form-control"
                                                                            id="editDateTimeOfReport"
                                                                            name="dateTimeOfReport"
                                                                            value="<?php echo $crimeinfo['dateTimeOfReport']; ?>"
                                                                            required="">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="editDateTimeOfIncident">Date and
                                                                            Time of
                                                                            Incident:</label>
                                                                        <input type="datetime-local"
                                                                            class="form-control"
                                                                            id="editDateTimeOfIncident"
                                                                            name="dateTimeOfIncident"
                                                                            value="<?php echo $crimeinfo['dateTimeOfIncident']; ?>"
                                                                            required="">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="placeOfIncident" class="form-label">Place of
                                                                            Incident:</label>
                                                                        <input type="search" id="search-input"
                                                                            name="placeOfIncident"
                                                                            value="<?php echo $crimeinfo['placeOfIncident']; ?>" 
                                                                            class="form-control" />
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="editSuspectName">Suspect
                                                                            Name</label>
                                                                        <input type="text" class="form-control"
                                                                            id="editSuspectName" name="suspectName"
                                                                            value="<?php echo $crimeinfo['suspectName']; ?>"
                                                                            required>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="typeOfCrime">Type of Crime</label>
                                                                        <select class="form-control" id="crimetype"
                                                                            name="crimetype">
                                                                            <?php
                                                                            $records = retrieveRecords();

                                                                            if (empty($records)) {
                                                                                echo '<option value="" disabled>No crime types found</option>';
                                                                            } else {
                                                                                foreach ($records as $record) {
                                                                                    echo '<option value="' . $record['crimeName'] . '">' . $record['crimeName'] . '</option>';
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                    <!-- Display QR Code in Edit Crime Information Modal -->
                                                                    <div class="form-group">
                                                                        <label for="formFileEvidence"
                                                                            class="form-label">Upload
                                                                            Evidence:</label>
                                                                        <div class="label-wrapper">
                                                                            <input class="form-control" type="file"
                                                                                id="formFileEvidence"
                                                                                name="formFileEvidence" multiple
                                                                                onchange="previewEvidence()">
                                                                        </div>
                                                                        <div id="EvidencePreviews" style="margin-top: 10px;">
                                                                            <?php if (!empty($crimeinfo['formFileEvidence'])): ?>
                                                                                <img src="<?php echo '../dist/uploads/evidence/' . $crimeinfo['formFileEvidence']; ?>" alt="evidence" style="max-width: 100%; height: 150px;">
                                                                            <?php endif; ?>
                                                                        </div>
                                                                        <div id="mediaModal" class="modal">
                                                                            <span class="close" onclick="closeMediaModal()">&times;</span>
                                                                            <div id="modalMedia" class="modal-content"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="statement">Statement:</label>
                                                                        <textarea class="form-control form-control-md"
                                                                            id="exampleTextarea" name="statement"
                                                                            rows="6"><?php echo $crimeinfo['statement']; ?></textarea>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="qrCode">QR Code:</label><br>
                                                                        <img src="<?php echo '../dist/qrcodes/' . $crimeinfo['qrcode']; ?>" alt="QR Code" style="max-width: 100%; height: 150px;">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="status">Status</label>
                                                                        <select class="form-control" id="status"
                                                                            name="status">
                                                                            <option value="Pending" <?php echo
                                                                                ($crimeinfo['status']==='Pending' )
                                                                                ? 'selected' : '' ; ?>
                                                                                >Pending</option>
                                                                            <option value="UnderInvestigation" <?php
                                                                                echo
                                                                                ($crimeinfo['status']==='UnderInvestigation'
                                                                                ) ? 'selected' : '' ; ?>
                                                                                >Under Investigation</option>
                                                                            <option value="Confirmed" <?php echo
                                                                                ($crimeinfo['status']==='Confirmed' )
                                                                                ? 'selected' : '' ; ?>
                                                                                >Confirmed</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">Close</button>
                                                                        <button type="submit" class="btn btn-primary"
                                                                            name="updateCrimeInfo">Save</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- View Crime Information Modal -->
                                                <div class="modal fade" 
                                                id="viewCrimeInfoModal<?php echo $crimeinfo['crime_id']; ?>" 
                                                tabindex="-1" role="dialog" 
                                                aria-labelledby="viewCrimeInfoModalLabel<?php echo $crimeinfo['crime_id']; ?>" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="viewCrimeInfoModalLabel<?php echo $crimeinfo['crime_id']; ?>">
                                                                        View Crime Information
                                                                    </h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div id="printable-modal-body<?php echo $crimeinfo['crime_id']; ?>" class="modal-body">
                                                                    <div class="container">
                                                                        <div class="left">
                                                                            <!-- Move information to left -->
                                                                            <div class="form-group">
                                                                                <label>Full Name:</label>
                                                                                <p style="display: inline;"><?php echo $crimeinfo['fullName']; ?></p>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label>Mobile No:</label>
                                                                                <p style="display: inline;"><?php echo $crimeinfo['phoneNumber']; ?></p>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label>Place of Incident:</label>
                                                                                <p style="display: inline;"><?php echo $crimeinfo['placeOfIncident']; ?></p>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label>Suspect Name:</label>
                                                                                <p style="display: inline;"><?php echo $crimeinfo['suspectName']; ?></p>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label>Status:</label>
                                                                                <p style="display: inline;"><?php echo $crimeinfo['status']; ?></p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="middle">
                                                                            <!-- Move information to middle -->
                                                                            <div class="form-group">
                                                                                <label>Date and Time of Report:</label>
                                                                                <p style="display: inline;"><?php echo $crimeinfo['dateTimeOfReport']; ?></p>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label>Date and Time of Incident:</label>
                                                                                <p style="display: inline;"><?php echo $crimeinfo['dateTimeOfIncident']; ?></p>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label>Type of Crime:</label>
                                                                                <p style="display: inline;"><?php echo $crimeinfo['CrimeType']; ?></p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="right">
                                                                            <!-- Move QR code to right -->
                                                                            <div class="form-group">
                                                                                <img src="<?php echo '../dist/qrcodes/' . $crimeinfo['qrcode']; ?>" alt="QR Code" width="150" height="150">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group px-4">
                                                                        <label>Valid ID:</label><br>
                                                                        <img src="<?php echo '../dist/uploads/valid_id/' . $crimeinfo['formFileValidID']; ?>" alt="QR Code" style="max-width: 100%; height: 120px;">
                                                                    </div>

                                                                    <div class="form-group px-4">
                                                                        <label>evidence:</label><br>
                                                                        <img src="<?php echo '../dist/uploads/evidence/' . $crimeinfo['formFileEvidence']; ?>" alt="QR Code" style="max-width: 100%; height: 120px;">
                                                                    </div>
                                                                    <div class="form-group px-4">
                                                                        <label>Statement:</label>
                                                                        <textarea class="form-control" rows="6" readonly><?php echo $crimeinfo['statement']; ?></textarea>
                                                                    </div>
                                                                <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                        <button type="button" class="btn btn-primary" onclick="printModal('<?php echo $crimeinfo['crime_id']; ?>')">Print</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <!-- Delete User Modal -->
                                                <div class="modal fade"
                                                    id="deleteCrimeInfo<?php echo $crimeinfo['crime_id']; ?>"
                                                    tabindex="-1" role="dialog"
                                                    aria-labelledby="deleteCrimeInfoLabel<?php echo $crimeinfo['crime_id']; ?>"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="deleteCrimeInfoLabel<?php echo $crimeinfo['crime_id']; ?>">
                                                                    Delete User
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to delete this user?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Cancel</button>
                                                                <form method="POST" action="">
                                                                    <input type="hidden" name="crime_id"
                                                                        value="<?php echo $crimeinfo['crime_id']; ?>">
                                                                    <button type="submit" class="btn btn-danger"
                                                                        name="deleteCrimeInfo">Delete</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- SHOW ENTRIES -->
                                    <div class="row mb-2">
                                        <div class="col-sm-6">
                                            <p style="font-size: 14px;">
                                                Showing
                                                <?php echo $startIndex + 1; ?> to
                                                <?php echo $endIndex; ?> of
                                                <?php echo $totalCrimeinfo; ?> entries
                                            </p>
                                        </div>
                                        <!-- PAGINATION -->
                                        <div class="col-sm-6">
                                            <div class="d-flex justify-content-end align-items-center">
                                                <nav>
                                                    <ul class="pagination">
                                                        <?php echo generatePaginationLinks($currentPage, $totalPages); ?>
                                                    </ul>
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- ./col -->
                    </div>
                    <!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        <footer class="main-footer text-center">
            <!-- Default to the left -->
            <strong>Copyright &copy; 2023. BROSOTO DEV </strong> All rights reserved.
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->

    <!-- jQuery -->
    <script>
//     function printModal() {
//         var printableContent = document.getElementById('printable-modal-body').innerHTML;
//         var originalContent = document.body.innerHTML;

//         document.body.innerHTML = printableContent;

//         window.print();

//         document.body.innerHTML = originalContent;
//     }
// </script>

    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <script src="../dist/js/sucessmessage.js"></script>
    <script src="../dist/js/style.js"></script>
    <!-- <script src="../dist/js/inspect.js"></script> -->
    <script src="../api/mapquest/mapquest.js"></script>
    <script src="../dist/js/imagePreview.js"></script>
    <script src="../dist/js/viewprint.js"></script>
    <script src="../dist/js/viewprint1.js"></script>
    <!-- Map Quest -->
    <script src="https://api.mqcdn.com/sdk/place-search-js/v1.0.0/place-search.js"></script>
    <script src="https://api.mqcdn.com/sdk/mapquest-js/v1.3.2/mapquest.js"></script>
</body>

</html>