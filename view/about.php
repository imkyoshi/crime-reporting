<?php
session_start();

// Include database connection and functions files
require_once '../config/db.php';
require_once '../includes/functions.php';


// Handle logout request
if (isset($_GET['logout'])) {
    // Unset all session variables
    session_unset();

    // Destroy the session
    session_destroy();

    // Redirect to the login page
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<!--divinectorweb.com-->

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
                        <a class="nav-link" href="../view/about.php">About Us</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Government Link
                        </a>
                        <ul class="dropdown-menu dropdown-menu-light" aria-labelledby="navbarDarkDropdownMenuLink">
                            <li><a class="dropdown-item" href="https://pnpclearance.ph/">NBI Clearance</a></li>
                            <li><a class="dropdown-item" href="https://feo.pnp.gov.ph/">Firearms Licensing</a></li>
                            <li><a class="dropdown-item"
                                    href="https://prod10.ebpls.com/sanluisbatangas/index.php">Business
                                    Permits & Licensing
                                    System</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Report Guideline</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- navbar ends -->
    <section class="bg-dark text-light p-5 mt-5" id="banners">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="mb-3 mb-md-0">About Us</h3>
                </div>
                <div class="col-md-6">
                    <div class="input-group news-input">
                        <ol class="breadcrumb float-sm-right text-light">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- about section starts -->
    <section id="about" class="about section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-12 ps-lg-5 mt-md-5">
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
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-12 ps-lg-5 mt-md-5">
                    <div class="about-text">
                        <h2>History</h2>
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
                    </div>
                </div>
                
                <div class="col-lg-6 col-md-12 col-12 ps-lg-5 mt-md-5">
                    <div class="about-text">
                        <h2>Vision</h2>
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
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-12 ps-lg-5 mt-md-5">
                    <div class="about-text">
                        <h2>Mission</h2>
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
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- contact ends -->
    <!-- footer starts -->
    <footer class="p-5 bg-dark text-white text-center position-relative">
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
    <script src="../dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script src="../dist/js/inspect.js"></script>
</body>

</html>