<?php
// Start the session
session_start();

// Check if the user is not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['roles'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Include database connection and functions files
require_once '../config/db.php';
require_once '../includes/functions.php';

// Retrieve all users from the database
$users = getAllUsers();
$totalUsers = getTotalUserCount();
$totalCrimeCategories = getTotalCrimeCategoryCount();
$totalResidents = getTotalResidentCount();
$totalCrimeinfo = getTotalCrimeInfoCount();

$currentUserID = $_SESSION['user_id'];
$currentUserInfo = getUserById($currentUserID);

// Call the function to get the data for the bar chart
$crimeData = getMonthlyCrimeCounts();
$barChartData = array(
    'data' => $crimeData['data'],
    'months' => $crimeData['months'],
    'years' => $crimeData['years']
);

// Call the function to get the data for the donut chart
$donutChartData = getDonutChartData();
?>

<!DOCTYPE html>
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
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color:#0B2436;">
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
                    <div class="info text-warning">
                        <?= $currentUserInfo['username']; ?>!
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                        <li class="nav-item">
                            <a href="dashboard.php" class="nav-link">
                                <i class="las la-home" id="icon"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="user_management.php" class="nav-link">
                                <i class="las la-user-friends" id="icon"></i>
                                <p>
                                    User Management
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="crime-category.php" class="nav-link">
                                <i class="las la-layer-group" id="icon"></i>
                                <p>
                                    Crime Category
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
                            <a href="resident-info.php" class="nav-link">
                                <i class="las la-archive" id="icon"></i>
                                <p>
                                    Resident Information
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
                            <h1 class="m-0">Account Profile</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Account Profile</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Default box -->
                            <div class="card card-outline card-primary">
                                <div class="card-header"> 
                                <div class="row">
                                        <div class="col-md-2">
                                            <h4 class="card-title pt-2">My Account</h4>
                                        </div>
                                        <div class="col-md-10 text-right">
                                            <button type="submit" class="btn btn-success">Update Account</button>
                                        </div>
                                    </div>  
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                    </form>

                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <!-- Default box -->
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <h4 class="card-title pt-2">Personal Information</h4>
                                        </div>
                                        <div class="col-md-10 text-right">
                                            <button type="submit" class="btn btn-success">Update Personal Information</button>
                                        </div>
                                    </div>    
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="fullName">Full Name</label>
                                        <input type="text" class="form-control" id="fullName" name="fullName" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="phoneNumber">Phone Number</label>
                                        <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Addresss</label>
                                        <input type="text" class="form-control" id="address" name="address" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="dateOfBirth">Date of Birth</label>
                                        <input type="date" class="form-control" id="dateOfBirth"
                                            name="dateOfBirth" required>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <aside class="control-sidebar control-sidebar-dark">

            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>


        <!-- Main Footer -->
        <footer class="main-footer text-center">
            <strong>Copyright &copy; 2023. BROSOTO DEV </strong> All rights reserved.
        </footer>
    </div>

    <!-- REQUIRED SCRIPTS -->

    <!-- jQuery -->
    <script>
        window.onload = function () {
            var barChartData = <?php echo json_encode($barChartData); ?>;
            var donutChartData = <?php echo json_encode($donutChartData); ?>;

            // Ensure data is available before rendering
            if (barChartData && donutChartData) {
                renderBarChart(barChartData);
                renderDonutChart(donutChartData);
            }
        };
    </script>
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <script src="../dist/js/style.js"></script>
    <!-- ChartJS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- <script src="../dist/js/barchart.js"></script> -->
    <script src="../dist/js/areachart.js?v=123"></script>
    <script src="../dist/js/charts.js?v=123"></script>
    <!-- <script src="../dist/js/inspect.js"></script> -->
</body>

</html>
