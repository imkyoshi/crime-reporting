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
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="las la-bars"></i></a>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color:#0B2436;">
            <a href="index3.html" class="brand-link">
                <img src="../dist/img/PNP.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
                    style="opacity: .8">
                <span class="brand-text font-weight-light">San Luis Police Station</span>
            </a>

            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="info text-light">
                        Welcome
                    </div>
                    <div class="info text-warning">
                        <?= $currentUserInfo['fullName']; ?>!
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <!-- <li class="nav-item">
                            <a href="adminprofile.php" class="nav-link">
                                <i class="las la-home" id="icon"></i>
                                <p>
                                    My Profile
                                </p>
                            </a>
                        </li> -->
                        <li class="nav-item"></li>
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
            </div>
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Dashboard</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-navy">
                                <div class="inner">
                                    <h3><?= $totalUsers; ?></h3>
                                    <p>Total Users</p>
                                </div>
                                <div class="icon" id="icon">
                                    <i class="las la-user-friends"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-navy">
                                <div class="inner">
                                    <h3><?= $totalCrimeCategories; ?></h3>
                                    <p>Crime Categories</p>
                                </div>
                                <div class="icon" id="icon">
                                    <i class="las la-layer-group"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-navy">
                                <div class="inner">
                                    <h3><?= $totalCrimeinfo; ?></h3>
                                    <p>Crime Info</p>
                                </div>
                                <div class="icon" id="icon">
                                    <i class="las la-gavel"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-navy">
                                <div class="inner">
                                    <h3><?= $totalResidents; ?></h3>
                                    <p>Resident Info</p>
                                </div>
                                <div class="icon" id="icon">
                                    <i class="las la-archive"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <!-- BARCHART -->
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Crime Reports</h3>

                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="las la-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="chart">
                                        <canvas id="barChart"
                                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- DONUT CHART-->
                        <div class="col-md-4">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Crime Report Overview</h3>

                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="las la-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <canvas id="donutChart"
                                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <!-- Default box -->
                            <div class="card card-outline card-primary">
                                <div class="card-body">
                                    <h3>Welcome to the Dashboard</h3>
                                    <p class="text-xl-start text-secondary">
                                        At the San Luis Municipality Police Station, our mission is to serve and protect
                                        our vibrant community with unwavering dedication and integrity. With a team of
                                        highly trained officers and staff, we are committed to ensuring the safety and
                                        security of all residents and visitors.
                                    </p>
                                    <p class="text-xl-start text-secondary">
                                        We work tirelessly to build strong relationships with the people we serve,
                                        fostering trust and cooperation. Together, we strive for a safer, more
                                        harmonious San Luis, where everyone can thrive. Your safety is our top
                                        priority, and we are here for you 24/7.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Control Sidebar -->
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
