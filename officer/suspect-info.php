<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['roles'] !== 'officer') {
  header("Location: ../auth/login.php");
  exit;
}

require_once '../config/db.php';
require_once '../includes/suspectinfo_functions.php';


$suspecinfos = getAllSuspectInfo();
$currentUserID = $_SESSION['user_id'];
$currentUserInfo = getUserById($currentUserID);

// Handle form submission for adding a Resident
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addSuspectInfo'])) {
    $fullName = $_POST['fullName'];
    $dateOfBirth = $_POST['dateOfBirth'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];
    $nationality = $_POST['nationality'];

    $result = addSuspectInfo($fullName, $dateOfBirth, $gender, $address, $phoneNumber, $email, $nationality);

    if ($result === "Suspect Information added successfully.") {
        header("Location: ../officer/suspect-info1.php");
        exit;
    } elseif ($result === "Suspect Information with this name already exists.") {
        $addErrorMessage = "Suspect Information already exists.";
    } else {
        $addErrorMessage = "Failed to add Suspect Information.";
    }
}

// Handle form submission for updating a Resident
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateSuspectInfo'])) {
    // Retrieve form data
    $SuspectID = $_POST['SuspectID'];
    $fullName = $_POST['fullName'];
    $dateOfBirth = $_POST['dateOfBirth'];
    $gender = $_POST['gender'];
    $address = $_POST['address']; 
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];
    $nationality = $_POST['nationality'];

    $result = updateSuspectInfo($SuspectID, $fullName, $dateOfBirth, $gender, $address, $phoneNumber, $email, $nationality);

    if ($result) {
        $updateSuccessMessage = 'Resident updated successfully.';
    } else {
        $errorMessage = 'Failed to update Resident.';
    }
}

// Handle form submission for deleting a Resident
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteSuspectInfo'])) {
    $suspectinfo_id = $_POST['SuspectID'];

    $result = deleteSuspectInfo($suspectinfo_id);
    if ($result) {
        $deleteSuccessMessage = 'Resident deleted successfully.';
    } else {
        $errorMessage = 'Failed to delete Resident.';
    }   
}

$limit = isset($_GET['showRecords']) ? intval($_GET['showRecords']) : 5;

$totalSuspectInfo = getTotalSuspectInfoCount();
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
$totalPages = 3;
$startIndex = ($currentPage - 1) * $limit;
$endIndex = min($startIndex + $limit, $totalSuspectInfo);
$suspectinfos = getSuspectInfowithLimitAndOffset($limit, $startIndex);
$paginationLinks = generatePaginationLinks($currentPage, $totalPages);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search = $_GET['search'];

    if (!empty($search)) {
        $suspectinfos = getSuspectInfoWithSearchLimitAndOffset($search, $limit, $startIndex);
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
    <!-- Mapquest CDN -->
    <link type="text/css" rel="stylesheet" href="https://api.mqcdn.com/sdk/place-search-js/v1.0.0/place-search.css" />
    <link type="text/css" rel="stylesheet" href="https://api.mqcdn.com/sdk/mapquest-js/v1.3.2/mapquest.css" />
    <link href="../dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            #actionss {
                display: none;
                min-width: 100%;
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
                        <?php echo $currentUserInfo['username']; ?>!
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <!-- Add icons to the links using the .nav-icon class
                            with font-awesome or any other icon font library -->
                        <li class="nav-item">
                            <a href="../officer/dashboard.php" class="nav-link">
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
                            <a href="../officer/resident-info.php" class="nav-link">
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
                            <h1 class="m-0">Suspect Information</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Suspect Information</li>
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
                        s</div>
                    <?php endif; ?>
                </div>
            </div>


            <!-- /.content-header -->
            <div class="col-sm-12">
                <!-- Success message for category update -->
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
                                    <!-- ADD NEW Category -->
                                    <div class="row mb-2">
                                        <div class="col-sm-12" style="margin-top: 15px;">
                                            <button type="button" class="btn btn-primary btn-sm float-left"
                                                data-toggle="modal" data-target="#addSuspectInfoModal"><i
                                                    class="las la-plus-circle"></i> Add New Suspect</button>
                                                <button type="button" class="btn btn-success btn-sm float-left" onclick="printModal1()" style="margin-left:10px;"><i class="las la-print"></i> Print</button>
                                        </div>


                                        <!-- Add Resident Modal -->
                                        <div class="modal fade" id="addSuspectInfoModal" tabindex="-1" role="dialog"
                                            aria-labelledby="addSuspectInfoModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="addSuspectInfoModalLabel">Add New
                                                            Resident</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="POST" action="">
                                                            <div class="form-group">
                                                                <label for="fullName">Full Name</label>
                                                                <input type="text" class="form-control" id="fullName"
                                                                    name="fullName" placeholder="Enter your First Name"
                                                                    required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="dateOfBirth">Date of Birth</label>
                                                                <input type="date" class="form-control" id="dateOfBirth"
                                                                    name="dateOfBirth" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="gender">Gender</label>
                                                                <select class="form-control" id="gender" name="gender">
                                                                    <option value="Female">Female</option>
                                                                    <option value="Male">Male</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="address" class="form-label">Address</label>
                                                                <input type="search" id="search-input" 
                                                                    placeholder="Enter your Address" name="address" class="form-control" required/>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="phoneNumber">Phone Number</label>
                                                                <input type="tel" class="form-control" id="phoneNumber"
                                                                    name="phoneNumber" placeholder="Enter Phone Number"
                                                                    required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="email">Email</label>
                                                                <input type="email" class="form-control" id="email"
                                                                    name="email" placeholder="Enter your Email" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="nationality">Nationality</label>
                                                                <input type="text" class="form-control" id="nationality"
                                                                    name="nationality" placeholder="Enter your Nationality" required>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary"
                                                                    name="addSuspectInfo">Save</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- SHOW RECORDS -->
                                        <div class="col-sm-3" style="margin-top: 15px;">
                                            <form id="showRecordsForm" method="GET" action="">
                                                <div class="form-group">
                                                    <label class="d-flex align-items-center">
                                                        <select class="form-control form-control-sm" id="showRecords"
                                                            name="showRecords" style="width: 120px;"
                                                            onchange="this.form.submit()">
                                                            <option value="5" <?php if ($limit == 5)
                                                                echo 'selected'; ?>
                                                                >5</option>
                                                            <option value="10" <?php if ($limit == 10)
                                                                echo 'selected';
                                                            ?>>10
                                                            </option>
                                                            <option value="20" <?php if ($limit == 20)
                                                                echo 'selected';
                                                            ?>>20
                                                            </option>
                                                            <option value="50" <?php if ($limit == 50)
                                                                echo 'selected';
                                                            ?>>50
                                                            </option>
                                                        </select>
                                                        <span class="ml-2"><i class="las la-filter"></i> records per
                                                            page</span>
                                                    </label>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- SEARCH -->
                                        <div class="col-sm-9" style="margin-top: 15px;">
                                            <div class="form-inline justify-content-end">
                                                <form id="searchForm" method="GET" action="">
                                                    <div class="form-group mx-sm-3 mb-2">
                                                        <label for="searchInput" class="mr-2"><i
                                                                class="las la-search"></i> Search:</label>
                                                        <input type="text" class="form-control" id="searchInput"
                                                            name="search" style="max-width: 150px;"
                                                            value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Category TABLE -->
                                    <div id="printable-modal-body1" class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>QR Codes</th>
                                                    <th>Full Name</th>
                                                    <!-- <th>Upload Valid ID</th> -->
                                                    <th>Date of Birth</th>
                                                    <th>Gender</th>
                                                    <th>Address</th>
                                                    <th>Phone Number</th>
                                                    <th>Email</th>
                                                    <th>Nationality</th>
                                                    <th id="actionss" style="text-align: center;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($suspectinfos as $suspectinfo): ?>
                                                <tr>
                                                    <td>
                                                        <img src="<?php echo '../dist/qrcodes/' . $suspectinfo['qrcode']; ?>" alt="QR Code" width="70" height="70">
                                                    </td>
                                                    <td>
                                                        <?php echo $suspectinfo['FullName']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $suspectinfo['DateOfBirth']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $suspectinfo['Gender']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $suspectinfo['Address']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $suspectinfo['PhoneNumber']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $suspectinfo['Email']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $suspectinfo['Nationality']; ?>
                                                    </td>
                                                    <td id="actionss" style="text-align:center;">
                                                        <button type="button" class="btn btn-success btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#editResidentModal<?php echo $suspectinfo['SuspectID']; ?>"><i
                                                                class="las la-edit"></i> Update</button>
                                                        <button type="button" class="btn btn-warning btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#viewSuspectInfoModal<?php echo $suspectinfo['SuspectID']; ?>"><i
                                                                class="las la-eye"></i>
                                                            View</button>        
                                                    </td>
                                                </tr>

                                                <!-- Edit Resident Modal -->
                                                <div class="modal fade"
                                                    id="editResidentModal<?php echo $suspectinfo['SuspectID']; ?>"
                                                    tabindex="-1" role="dialog"
                                                    aria-labelledby="editResidentModalLabel<?php echo $suspectinfo['SuspectID']; ?>"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="editResidentModalLabel<?php echo $suspectinfo['SuspectID']; ?>">
                                                                    Edit Suspect Information
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form method="POST" action="">
                                                                    <input type="hidden" name="SuspectID"
                                                                        value="<?php echo $suspectinfo['SuspectID']; ?>">
                                                                    <div class="form-group">
                                                                        <label for="editfullName">Full Name</label>
                                                                        <input type="text" class="form-control"
                                                                            id="editfullName" name="fullName"
                                                                            value="<?php echo $suspectinfo['FullName']; ?>"
                                                                            required>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="editDateOfBirth">Date of Birth</label>
                                                                        <input type="date" class="form-control"
                                                                            id="editDateOfBirth" name="dateOfBirth"
                                                                            value="<?php echo $suspectinfo['DateOfBirth']; ?>"
                                                                            required>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="editGender">Gender</label>
                                                                        <select class="form-control" id="editGender" name="gender">
                                                                        <option value="Female" <?php echo ($suspectinfo['Gender'] === 'Female') ? 'selected' : '';
                                                                        ?>
                                                                            >Female</option>
                                                                        <option value="Male" <?php echo ($suspectinfo['Gender'] === 'Male') ? 'selected' : ''; ?>
                                                                            >Male
                                                                        </option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="editAddress">Address</label>
                                                                        <input type="text" class="form-control"
                                                                            id="editAddress" name="address"
                                                                            value="<?php echo $suspectinfo['Address']; ?>"
                                                                            required>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="editPhoneNumber">Phone
                                                                            Number</label>
                                                                        <input type="tel" class="form-control"
                                                                            id="editPhoneNumber" name="phoneNumber"
                                                                            value="<?php echo $suspectinfo['PhoneNumber']; ?>"
                                                                            required>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="editEmail">Email</label>
                                                                        <input type="email" class="form-control"
                                                                            id="editEmail" name="email"
                                                                            value="<?php echo $suspectinfo['Email']; ?>"
                                                                            required>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="editNationality">Nationality</label>
                                                                        <input type="text" class="form-control"
                                                                            id="editNationality" name="nationality"
                                                                            value="<?php echo $suspectinfo['Nationality']; ?>"
                                                                            required>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">Cancel</button>
                                                                        <button type="submit" class="btn btn-primary"
                                                                            name="updateSuspectInfo">Save</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- View Suspect Modal -->
                                                <div class="modal fade"
                                                    id="viewSuspectInfoModal<?php echo $suspectinfo['SuspectID']; ?>"
                                                    tabindex="-1" role="dialog"
                                                    aria-labelledby="viewSuspectInfoModalLabel<?php echo $suspectinfo['SuspectID']; ?>"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="viewSuspectInfoLabel<?php echo $suspectinfo['SuspectID']; ?>">
                                                                    View Suspect Information
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form method="POST" action="">
                                                                    <input type="hidden" name="SuspectID"
                                                                        value="<?php echo $suspectinfo['SuspectID']; ?>">
                                                                    <div class="form-group">
                                                                        <label for="viewfullName">Full Name</label>
                                                                        <input type="text" class="form-control"
                                                                            id="viewfullName" name="fullName"
                                                                            value="<?php echo $suspectinfo['FullName']; ?>"
                                                                            readonly>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="viewDateOfBirth">Date of Birth</label>
                                                                        <input type="date" class="form-control"
                                                                            id="viewDateOfBirth" name="dateOfBirth"
                                                                            value="<?php echo $suspectinfo['DateOfBirth']; ?>"
                                                                            readonly>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="viewGender">Gender</label>
                                                                        <input type="text" class="form-control"
                                                                            id="viewGender" name="Gender"
                                                                            value="<?php echo $suspectinfo['Gender']; ?>"
                                                                            readonly>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="viewAddress">Address</label>
                                                                        <input type="text" class="form-control"
                                                                            id="viewAddress" name="address"
                                                                            value="<?php echo $suspectinfo['Address']; ?>"
                                                                            readonly>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="viewPhoneNumber">Phone
                                                                            Number</label>
                                                                        <input type="tel" class="form-control"
                                                                            id="viewPhoneNumber" name="phoneNumber"
                                                                            value="<?php echo $suspectinfo['PhoneNumber']; ?>"
                                                                            readonly>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="viewEmail">Email</label>
                                                                        <input type="email" class="form-control"
                                                                            id="viewEmail" name="email"
                                                                            value="<?php echo $suspectinfo['Email']; ?>"
                                                                            readonly>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="viewNationality">Nationality</label>
                                                                        <input type="text" class="form-control"
                                                                            id="viewNationality" name="nationality"
                                                                            value="<?php echo $suspectinfo['Nationality']; ?>"
                                                                            readonly>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>QR Code:</label><br>
                                                                        <img src="<?php echo '../dist/qrcodes/' . $suspectinfo['qrcode']; ?>" alt="QR Code" style="max-width: 100%; height: 150px;" class="mx-auto my-auto d-block">
                                                                    </div>
                                                                    <!-- Remove the submit button from the view modal -->
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">Close</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                                                                
                                                <!-- Delete Resident Modal -->
                                                <div class="modal fade"
                                                    id="deleteSuspectInfoModal<?php echo $suspectinfo['SuspectID']; ?>"
                                                    tabindex="-1" role="dialog"
                                                    aria-labelledby="deleteSuspectInfoModalLabel<?php echo $suspectinfo['SuspectID']; ?>"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="deleteSuspectInfoModalLabel<?php echo $suspectinfo['SuspectID']; ?>">
                                                                    Delete Resident
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to delete this Resident?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <form method="POST" action="">
                                                                    <input type="hidden" name="SuspectID"
                                                                        value="<?php echo $suspectinfo['SuspectID']; ?>">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-danger"
                                                                        name="deleteSuspectInfo">Delete</button>
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
                                                <?php echo $totalSuspectInfo; ?> entries
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
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- Map Quest -->
    <script src="https://api.mqcdn.com/sdk/place-search-js/v1.0.0/place-search.js"></script>
    <script src="https://api.mqcdn.com/sdk/mapquest-js/v1.3.2/mapquest.js"></script>
    <!-- Costum JS -->
    <script src="../api/mapquest/mapquest.js"></script>
    <script src="../dist/js/sucessmessage.js"></script>
    <script src="../dist/js/style.js"></script>
    <script src="../dist/js/suspect-print.js"></script>
    <script src="../dist/js/inspect.js"></script>
</body>

</html>