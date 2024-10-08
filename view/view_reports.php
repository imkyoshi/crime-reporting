<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../crime-reporting/auth/login.php");
    exit;
}

// Include database connection and functions files
require_once '../config/db.php';
require_once '../includes/crimeinfo_functions.php';

$crimeinfo = getAllCrimeInfo();
$currentUserID = $_SESSION['user_id'];
$currentUserInfo = getUserById($currentUserID);

// Get the user ID from the session
$user_id = $_SESSION['user_id'];
// Retrieve user information from the database
$user = getUserById($user_id);

// Handle logout request
if (isset($_GET['logout'])) {
    // Unset all session variables
    session_unset();

    // Destroy the session
    session_destroy();

    // Redirect to the login page
    header("Location: ../crime-reporting/auth/login.php");
    exit;
}

$limit = isset($_GET['showRecords']) ? intval($_GET['showRecords']) : 5;

$totalCrimeinfo = getTotalCrimeInfoCount();
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
$totalPages = 3;
$startIndex = ($currentPage - 1) * $limit;
$endIndex = min($startIndex + $limit, $totalResidents);
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>San Luis Municipal Police Station</title>
    <!-- All CSS -->
    <link href="../dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../dist/img/favicon.ico">
    <!-- Mapbox CDN -->
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../dist/css/style.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container">
            <div class="logo">
                <img src="../dist/img/pnp.png" alt="" />
            </div>
            <a class="navbar-brand" id="brand" href="#">San Luis <span class="text-warning"> Police
                    Station</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php#about">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php#services">Services</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Government Link
                        </a>
                        <ul class="dropdown-menu dropdown-menu-light" aria-labelledby="navbarDarkDropdownMenuLink">
                            <li><a class="dropdown-item" href="https://pnpclearance.ph/">NBI Clearance</a></li>
                            <li><a class="dropdown-item" href="https://feo.pnp.gov.ph/">Firearms Licensing</a></li>
                            <li><a class="dropdown-item" href="https://prod10.ebpls.com/sanluisbatangas/index.php">
                                    Business Permits</a>
                            </li>
                        </ul>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../view_reports.php">My Reports List</a>
                        </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?php echo htmlspecialchars($user['fullName']); ?>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <!-- <a class="dropdown-item" href="../auth/user_profile.php">My Profile</a> -->
                                    <!-- <div class="dropdown-divider"></div> -->
                                    <a class="dropdown-item" href="../index.php?logout=true">Log Out</a>
                                </div>
                            </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../auth/login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- about section starts -->
    <section id="viewreport" class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-12 ps-lg-5 mt-md-5">
                    <div class="card card-outline card-primary">
                        <div class="card-header bg-white">
                            <h4 class="text-center pt-2">My reports</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <!-- SHOW RECORDS (unchanged) -->
                                <div class="col-sm-9" style="margin-top: 15px;">
                                    <form id="showRecordsForm" method="GET" action="">
                                        <div class="form-group">
                                            <label class="d-flex align-items-center">
                                                <span class="ml-2"><i class="las la-filter"></i>Show </span>
                                                <select class="form-control form-control-sm" id="showRecords"
                                                    name="showRecords" style="width: 60px; text-align:center;" onchange="this.form.submit()">
                                                    <option value="5" <?php if ($limit == 5)
                                                        echo 'selected'; ?>>5</option>
                                                    <option value="10" <?php if ($limit == 10)
                                                        echo 'selected'; ?>>10
                                                    </option>
                                                    <option value="20" <?php if ($limit == 20)
                                                        echo 'selected'; ?>>20
                                                    </option>
                                                    <option value="50" <?php if ($limit == 50)
                                                        echo 'selected'; ?>>50
                                                    </option>
                                                </select>
                                                <span class="ml-2"><i class="las la-filter ps-2"></i> records per
                                                    page</span>
                                            </label>
                                        </div>
                                    </form>
                                </div>

                                <!-- SEARCH (moved to the left) -->
                                <div class="col-sm-3" style="margin-top: 15px;">
                                    <div class="form-inline">
                                        <form id="searchForm" method="GET" action="">
                                            <div class="form-group mx-sm-3 mb-2 ps-5">
                                                <label for="searchInput" class="d-flex align-items-center mr-2"><i class="las la-search"></i>
                                                <span class="ml-2"><i class="las la-filter"></i>Search:</span>&nbsp;
                                                <input type="text" class="form-control" id="searchInput" name="search"
                                                    style="max-width: 200px;"
                                                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                                
                                                </label>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table" id="residentTable">
                                    <thead>
                                        <tr>
                                            <th>QR Codes</th>
                                            <th>Full Name</th>
                                            <th>Date and Time of Report</th>
                                            <th>Place of Incident</th>
                                            <th>Date and Time of Incident</th>
                                            <th>Type of Crime</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($crimeinfos as $crimeinfo): ?>
                                            <tr>
                                                <td>
                                                    <img src="<?php echo '../dist/qrcodes/' . htmlspecialchars($crimeinfo['qrcode']); ?>" alt="QR Code" width="60" height="60">
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($crimeinfo['fullName']); ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($crimeinfo['dateTimeOfReport']); ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($crimeinfo['placeOfIncident']); ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($crimeinfo['dateTimeOfIncident']); ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($crimeinfo['CrimeType']); ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($crimeinfo['status']); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <p style="font-size: 14px;">
                                        Showing
                                        <?php echo $startIndex + 1; ?> to
                                        <?php echo $endIndex; ?> of
                                        <?php echo $totalCrimeinfo; ?> entries
                                    </p>
                                </div>
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
                </div>
            </div>
        </div>
    </section>

    <footer class="p-5 bg-dark text-white text-center position-sticky">
        <div class="container">
            <p class="lead">Copyright &copy; 2023 San Luis Municipality Police Station</p>
            <a href="#" class="position-absolute bottom-0 end-0 p-5">
                <i class="bi bi-arrow-up-circle h1"></i>
            </a>
            <a href="#"><i class="bi bi-facebook text-light mx-1"></i></a>
            <a href="#"><i class="bi bi-instagram text-light mx-1"></i></a>
        </div>
    </footer>

    <script src="https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.js"></script>

    <script>
        mapboxgl.accessToken =
            "pk.eyJ1IjoiYnRyYXZlcnN5IiwiYSI6ImNrbmh0dXF1NzBtbnMyb3MzcTBpaG10eXcifQ.h5ZyYCglnMdOLAGGiL1Auw";
        var map = new mapboxgl.Map({
            container: "map",
            style: "mapbox://styles/mapbox/streets-v11",
            center: [120.91635, 13.85510],
            zoom: 18,
        });
    </script>
    <script src="../plugins/jquery/jquery.min.js"></script>
    <script src="../dist/js/bootstrap.bundle.min.js"></script>
    <script src="../dist/js/adminlte.min.js"></script>
    <script src="../dist/js/style.js"></script>

</body>

</html>
