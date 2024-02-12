<?php
session_start();

require_once '../crime-reporting/config/db.php';
require_once '../crime-reporting/includes/functions.php';

$user_id = $_SESSION['user_id'];
// Retrieve User
$user = getUserById($user_id);

// Handle logout request
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../crime-reporting/auth/login.php");
    exit;
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
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="dist/img/favicon.ico">
    <!-- Mapbox CDN -->
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="dist/css/style.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container">
            <div class="logo">
                <img src="dist/img/pnp.png" alt="" />
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
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About Us</a>
                    </li>
                    <li class="nav-item">
                            <a class="nav-link" href="#services">Services</a>
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
                                <a class="nav-link" href="view/view_reports.php">My Reports List</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?php echo $user['username']; ?>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <!-- <a class="dropdown-item" href="auth/user_profile.php">My Profile</a> -->
                                    <!-- <div class="dropdown-divider"></div> -->
                                    <a class="dropdown-item" href="index.php?logout=true">Log Out</a>
                                </div>
                            </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>



    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"
                aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"
                aria-label="Slide 2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="dist/img/bg.png" class="d-block w-100" alt="...">
                <div class="carousel-caption">
                    <h5>Welcome to our <span class="text-warning"> Crime Reporting website </span></h5>
                    <p>We are committed to delivering the highest degree of professional police services in the municipality.</p>
                    <p><a href="view/report3.php" class="btn btn-warning mt-3">Report a crime</a></p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="dist/img/bg.png" class="d-block w-100" alt="...">
                <div class="carousel-caption">
                    <h5>Report <span class="text-warning">Now! </span></h5></h5>
                    <p>We are committed to delivering the highest degree of professional police services in the municipality</p>
                    <p><a href="view/report3.php" class="btn btn-warning mt-3">Report a crime</a></p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
            data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
            data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- about section starts -->
    <section id="about" class="about section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-12 col-12">
                    <div class="about-img">
                        <img src="dist/img/police.png" alt="" class="img-fluid">
                    </div>
                </div>
                <div class="col-lg-8 col-md-12 col-12 ps-lg-5 mt-md-5">
                    <div class="about-text">
                        <h2>About Us</h2>
                        <p class="text-xl-start">
                            At the San Luis Municipality Police Station, our mission is to serve and protect our vibrant
                            community with
                            unwavering dedication and integrity. With a team of highly trained officers and staff, we
                            are committed to
                            ensuring the safety and security of all residents and visitors.
                        </p>
                        <p class="text-xl-start">
                            We work tirelessly to build strong relationships with the people we serve, fostering trust
                            and cooperation.
                            Together, we strive for a safer, more harmonious San Luis, where everyone can thrive. Your
                            safety is our top
                            priority, and we are here for you 24/7."
                        </p>
                        <a href="#" class="btn btn-warning">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </section>    <!-- about section Ends -->

    <!-- services section Starts -->
    <section id="services" class="p-5 bg-dark">
        <div class="container">
            <h2 class="text-center text-white">Our Service</h2>
            <p class="lead text-center text-white mb-5">
                Our services at san luis municipality police station
            </p>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <img src="dist/img/crimetips.png" class="mb-3" alt="" />
                            <h3 class="card-title mb-3">Crime Tips</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <img src="dist/img/vehicle.png" class="mb-3" alt="" />
                            <h3 class="card-title mb-3">Vehicle Unlocks</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <img src="dist/img/firearms.png" class="mb-3" alt="" />
                            <h3 class="card-title mb-3">Firearms Licensing</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- services section Ends -->


    <!-- Contact & Map -->
    <section class="p-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md">
                    <h2 class="text-center mb-4">Contact Info</h2>
                    <ul class="list-group list-group-flush lead">
                        <li class="list-group-item">
                            <span class="fw-bold">Main Location:</span> Poblacion, San Luis, Philippines
                        </li>
                        <li class="list-group-item">
                            <span class="fw-bold">Hotline No:</span> 0926 641 6290
                        </li>
                        <li class="list-group-item">
                            <span class="fw-bold">Telephone No:</span> (043) 741-5589
                        </li>
                        <li class="list-group-item">
                            <span class="fw-bold">Police station Email:</span> sanluismpsbatangas@yahoo.com
                        </li>
                        <li class="list-group-item">
                            <span class="fw-bold">FB Page:</span>
                            San Luis Municipal Police Station
                        </li>
                    </ul>
                </div>
                <div class="col-md">
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- contact ends -->
    <!-- footer starts -->
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
    <!-- footer ends -->
    <!-- All Js -->
    <!-- Mapbox JS -->
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
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="plugins/popper/popper.min.js"></script>
    <script src="js/script.js"></script>
    <script src="dist/js/inspect.js"></script>
</body>

</html>