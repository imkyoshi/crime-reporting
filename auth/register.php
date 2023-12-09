<?php
// Include database connection and functions files
// Include database connection and functions files
require_once '../config/db.php';
require_once '../includes/functions.php';

// Initialize variables
$username = $email = '';
$successMessage = $errorMessage = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Validate form inputs
    if (empty($username) || empty($password) || empty($email)) {
        $errorMessage = "Please fill in all fields";
    } else {
        // Validate and register user
        $success = registerUser($username, $password, $email);

        if ($success) {
            $successMessage = "Account registered successfully!";
            $username = $email = ''; // Reset the form fields
        } else {
            $errorMessage = "Failed to register account";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/x-icon" href="../dist/img/favicon.ico">
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
    <!-- Mapbox CDN -->
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../dist/css/register.css" />
    <title>SanLuis Municipality Police Station</title>
</head>

<body>
    <!-- Navbar -->
   <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container">
            <div class="logo">
                <img src="../dist/img/pnp.png" alt="" />
            </div>
            <a class="navbar-brand" id="brand" href="#">San Luis Municipal<span class="text-warning"> Police
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
                </ul>
            </div>
        </div>
    </nav>

    <section id="register">
        <div class="container p-5">
            <div class="row align-items-center justify-content-between">
                <div class="col-md" style="margin-top: 100px;">
                    <h5>Welcome to San Luis Municipality Police Station </h5>
                    <h2>Crime Reporting System</h2>
                    <p class="text-xl-start text-secondary">
                        We are committed to delivering the highest degree of professional police services in the
                        municipality.
                    </p>
                    <a class="btn btn-primary" role="button" href="../auth/login.php">Login Now</a>
                </div>
                <div class="col-md">
                    <div class="card">
                        <h3 class="card-title">Register</h3>
                        <?php if ($successMessage): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $successMessage; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($errorMessage): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $errorMessage; ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <!-- Login form fields -->
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required
                                    value="<?php echo $email; ?>">
                            </div>
                        <p class="mt-3">If you have an account already, <a href="../auth/login.php">Login now</a>.</p> 
                            <button type="submit" class="btn btn-primary">Register</button>
                        </form>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4"
        crossorigin="anonymous"></script>
    <script src="../dist/js/inspect.js"></script>
</body>

</html>