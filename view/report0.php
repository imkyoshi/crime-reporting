<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Include database connection and functions files
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../api/QRcode/gr1.php';
require_once '../includes/report2_functions.php';

// Get the user ID from the session
$user_id = $_SESSION['user_id'];
// Retrieve user information from the database
$user = getUserById($user_id);
$data = retrieveRecords();
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $fullName = $_POST['fullName'];
    $dateTimeOfReport = $_POST['dateTimeOfReport'];
    $dateTimeOfIncident = $_POST['dateTimeOfIncident'];
    $placeOfIncident = $_POST['address'];
    $suspectName = $_POST['suspect'];
    $crimeType = $_POST['crimetype'];
    $statement = $_POST['exampleTextarea'];
    $validIDFilePath = handleFileUpload('formFileValidID', '../uploads/valid_id/');
    $evidenceFilePath = handleFileUpload('formFileEvidence', '../uploads/evidence/');

    // Insert data into the database
    $crimeID = insertCrimeInformation($dateTimeOfReport, $dateTimeOfIncident, $placeOfIncident, $suspectName, $statement, $validIDFilePath, $evidenceFilePath, $crimeType, $user_id);

    // Generate QR code
    $qrCodePath = generateQRCode($crimeID, '../uploads/qrcodes/');

    // Update the crime information record with the QR code path
    updateCrimeQRCodePath($crimeID, $qrCodePath);

    // Redirect to the reports list page
    header("Location: ../view/view_reports.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/x-icon" href="dist/img/favicon.ico">
    <!-- Bootstrap CDN -->
     <link href="../dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <!-- Mapquest CDN -->
    <link type="text/css" rel="stylesheet" href="https://api.mqcdn.com/sdk/place-search-js/v1.0.0/place-search.css" />
    <link type="text/css" rel="stylesheet" href="https://api.mqcdn.com/sdk/mapquest-js/v1.3.2/mapquest.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../dist/css/report.css" />
    
    <title>SanLuis Municipality Police Station</title>
</head>

<body>
    <!-- Navbar -->
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
                                <a class="nav-link" href="../view/view_reports.php">My Reports List</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="../index.php?logout=true">Logout</a>
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

    <!-- Learn Sections -->
    <div class="content-wrapper" id="login">
        <div class="container p-5">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-10">
                    <div class="card">
                        <form method="POST" action="../view/report.php">
                            <div class="row">
                                <div class="card-header bg-white">
                                    <h4 class="text-center">Report Crime</h4>
                                </div>
                                <h5 class="text-start mt-4"><u>Guidelines</u></h5>
                                <p class="text-justify mt-2">Reporters must present a valid ID for user identity verification to ensure the authenticity of crime reports and prevent the submission of illegal or false information</p>
                                <div class="col-md-12">
                                    
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="fullname">Full Name:</label>
                                        <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Enter your fullname" value="<?php echo $data['fullName']; ?>" readonly>
                                    </div>
                                </div>

                                    <div class="form-group">
                                        <label for="formFileValidID" class="form-label">Upload Valid ID:</label>
                                        <div class="label-wrapper">
                                            <input class="form-control" type="file" id="formFileValidID" multiple
                                                onchange="previewValidID()">
                                        </div>
                                        <div id="ValidIDPreviews" style="margin-top: 10px;"></div>
                                    </div>
                                </div>
                                <div id="mediaModal" class="modal">
                                    <span class="close" onclick="closeMediaModal()">&times;</span>
                                    <div id="modalMedia" class="modal-content"></div>
                                </div>

                                <h5 class="text-start"><u>Crime Information</u></h5>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="dateTimeOfReport">Date and Time of Report:</label>
                                        <input type="datetime-local" class="form-control" id="dateTimeOfReport"
                                            name="dateTimeOfReport" required="">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="dateTimeOfIncident">Date and Time of Incident:</label>
                                        <input type="datetime-local" class="form-control" id="dateTimeOfIncident"
                                            name="dateTimeOfIncident" required="">
                                    </div>
                                </div>

                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label for="placeOfIncident" class="form-label">Place of Incident:</label>
                                        <input type="text" class="form-control" id="address" name="address" readonly>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <!--
                                        <input type="hidden" name="lat" id="lat">
                                        <input type="hidden" name="lng" id="lng">
                                        -->
                                     <button type="button" id="pickLocationBtn" class="btn btn-primary mt-4">Pick a Location</button>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="placeOfIncident">Suspect Name:</label>
                                        <input type="text" class="form-control" id="suspect" name="suspect" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="typeOfCrime">Type of Crime:</label>
                                        <select class="form-control" id="crimetype" name="crimetype">
                                            <option value="<?php echo $data['crimeType']; ?>" selected><?php echo $data['crimeType']; ?></option>
                                            <!-- Add other options if needed -->
                                        </select>
                                    </div>
                                </div>
                                                            
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="statement">Statement:</label>
                                        <textarea class="form-control form-control-md" id="exampleTextarea" rows="6"
                                            placeholder="Enter your statement here"></textarea>
                                    </div>
                                </div>

                                <!-- <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="QR Code">QR Codes: </label>
                                    </div>
                                    <div class="form-group">
                                        <img src="<?php echo $qrCodePath; ?>" alt="QR Code Generated">
                                    </div>
                                </div> -->

                                <div class="col-md-12">
                                       <div class="form-group">
                                        <label for="QR Code">QR Codes: </label>
                                       </div>
                                       <div class="form-group">
                                        <?php echo '<img src="'.$imageData.'" class="" alt="" style="width:150px;">'; ?>
                                       </div>
                                   </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="formFileEvidence" class="form-label">Upload Evidence:</label>
                                        <div class="label-wrapper">
                                            <input class="form-control" type="file" id="formFileEvidence" multiple
                                                onchange="previewEvidence()">
                                        </div>
                                        <div id="EvidencePreviews" style="margin-top: 10px;"></div>
                                    </div>
                                </div>
                                <div id="mediaModal" class="modal">
                                    <span class="close" onclick="closeMediaModal()">&times;</span>
                                    <div id="modalMedia" class="modal-content"></div>
                                </div>

                        <button type="button" class="btn btn-primary">
                            <i class="las la-map-marker"></i>Report
                        </button>

                        </form>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

        <div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title" id="locationModalLabel">Select a Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Your Map and Input Content Goes Here -->
                    <div id="map" style="width: 100%; height: 400px;"></div>
                    <input type="search" id="search-input" class="form-control mt-3" placeholder="Enter a location" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitBtn">Submit</button>
                </div>
            </div>
        </div>
    </div>
    </div>

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
    <!-- Bootstrap -->
    <script src="../dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <!-- Map Quest -->
    <script src="https://api.mqcdn.com/sdk/place-search-js/v1.0.0/place-search.js"></script>
    <script src="https://api.mqcdn.com/sdk/mapquest-js/v1.3.2/mapquest.js"></script>
    <!-- Costumize -->
    <script src="../dist/js/imagePreview.js"></script>
    <script src="../api/mapquest/mapquest.js"></script>
    <!-- <script src="../dist/js/inspect.js"></script> -->
</body>

</html>