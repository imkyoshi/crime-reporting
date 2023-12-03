<?php
// Start the session
session_start();

// Check if the user is not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['roles'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Include database connection and functions files
// Include database connection and functions files
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Retrieve all users from the database
$users = getAllUsers();

$currentUserID = $_SESSION['user_id'];
$currentUserInfo = getUserById($currentUserID);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Crime Reporting System</title>
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Line Awesomee CSS -->
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>

    </style>
</head>

<body>
    <div class="wrapper">
        <nav id="sidebar">
            <div class="sidebar-header" style="text-align:center;">
                <img src="../assets/img/PNP.png" class="img-fluid"  id="logooos" style="width: 50px; display:block; margin-right:auto; margin-left:auto;">
                <div class="mb-2">Welcome,
                    <?php echo $currentUserInfo['username']; ?>!
                </div>
                <button onclick="location.href='../auth/logout.php'" class="logout-button"><i class="las la-sign-out-alt"></i> Logout</button>
            </div>
            <ul>
                <li>
                    
                    <a href="../admin/dashboard.php"><i class="las la-home"></i>  Dashboard</a>
                    
                </li>
                <li>
                    <a href="../admin/user_management.php"><i class="las la-user-friends"></i>  User Management</a>
                </li>
                <li>
                    <a href="../admin/crime-category.php"><i class="las la-layer-group"></i>  Crime Categories</a>
                </li>
                <li>
                    <a href="../admin/crime-info.php"><i class="las la-gavel"></i>  Crime Information</a>
                </li>
                <li>
                    <a href="../admin/resident-info.php"><i class="las la-archive"></i>  Resident Information</a>
                </li>
            </ul>
        </nav>

        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <button type="button" id="sidebarCollapse" class="btn btn-info">
                    <i class="las la-bars"></i>
                </button>
                <div class="container-fluid">
                    <div class="navbar-brand">Crime Reporting System</div>
                </div>
            </nav>

            <div class="content-wrapper">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard Content</li>
                    </ol>
                </nav>
                <h3 style="text-align: left;">Dashboard Content</h3>
                <!-- Dashboard content -->
                <p style="text-align: justify;">Horizon Fitness Gym is a gym that provides people a satisfying fitness result that enables them to show the world what they’re capable of even if they are body shamed. This gym not only provides equipment to use but also to advocate a healthy connections between the coaches and the people we inter -connect with. We show people how to communicate with one another and help them the best way we can, we furnish people with love and care that they deserve when they are with our gym. We also offer a healthy lifestyle coach with the best of the best coaches from different countries in the world. So what are we waiting for come on now and sign up for more fitness related talks to follow.</p>
            </div>
            <!-- Fixed Footer -->
            <div class="card-footer d-flex justify-content-center">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <p style="font-size: 14px;">
                            © 2023 BROSOTO STUDIO
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- jQuery CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Bootstrap JS CDN -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Sidebar toggle
        $(document).ready(function () {
            $("#sidebarCollapse").on('click', function () {
                $("#sidebar").toggleClass('active');
            });
        });
    </script>
</body>

</html>