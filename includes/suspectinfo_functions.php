<?php
require_once '../config/db.php';
require_once '../api/phpqrcode/qrlib.php';

function getUserById($userId)
{
    global $mysqli;

    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $user = $result->fetch_assoc();

    return $user;
}

function getAllSuspectInfo()
{
    global $mysqli;

    $sql = "SELECT * FROM suspect_information";
    $result = $mysqli->query($sql);

    $residents = [];
    while ($row = $result->fetch_assoc()) {
        $residents[] = $row;
    }

    return $residents;
}
function handleFileUpload($inputName, $uploadDir)
{
    // Check if files were uploaded successfully
    if (!empty($_FILES[$inputName]['name'])) {
        $fileName = $_FILES[$inputName]['name'];

        // Ensure the target directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filePath = $uploadDir . $fileName;

        // Move the uploaded file to the specified directory
        move_uploaded_file($_FILES[$inputName]['tmp_name'], $filePath);

        // Return only the file name without the path
        return $fileName;
    }

    return null;
}




function addSuspectInfo($fullName, $dateOfBirth, $gender, $address, $phoneNumber, $email, $nationality)
{
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM suspect_information WHERE FullName = ? AND Email = ?");
    $stmt->bind_param("ss", $fullName, $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        return "Suspect Information with this email already exists.";
    }

    // Generate QR code data
    $qrCodeData = "Full Name: " . $fullName . "\n";
    $qrCodeData .= "Date Of Birth: " . $dateOfBirth . "\n";
    $qrCodeData .= "Gender: " . $gender . "\n";
    $qrCodeData .= "Address: " . $address . "\n";
    $qrCodeData .= "Phone Number: " . $phoneNumber . "\n";
    $qrCodeData .= "Email: " .  $email . "\n";
    $qrCodeData .= "Nationality: " . $nationality . "\n";

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
    $qrCodeFullPath = $qrCodePath . $qrCodeFileName;
    QRcode::png($qrCodeData, $qrCodeFullPath);

    $stmt = $mysqli->prepare("INSERT INTO suspect_information (FullName, DateOfBirth, Gender, Address, PhoneNumber, Email, Nationality, qrcode) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $fullName, $dateOfBirth, $gender, $address, $phoneNumber, $email, $nationality, $qrCodeFileName);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        return "Suspect Information added successfully.";
    } else {
        return "Failed to Suspect Information resident.";
    }
}

function updateSuspectInfo($SuspectID, $fullName, $dateOBirth, $gender, $address, $phoneNumber, $email, $nationality)
{
    global $mysqli;

    // Generate QR code data
    $qrCodeData = "Full Name: " . $fullName . "\n";
    $qrCodeData .= "Date Of Birth: " . $dateOBirth . "\n";
    $qrCodeData .= "Gender: " . $gender . "\n";
    $qrCodeData .= "Address: " . $address . "\n";
    $qrCodeData .= "Phone Number: " . $phoneNumber . "\n";
    $qrCodeData .= "Email: " .  $email . "\n";
    $qrCodeData .= "Nationality " . $nationality . "\n";
    

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
    $qrCodeFullPath = $qrCodePath . $qrCodeFileName;
    QRcode::png($qrCodeData, $qrCodeFullPath);

    // Update data in the database
    $sql = "UPDATE suspect_information SET FullName=?, DateOfBirth=?, Gender=?, Address=?, PhoneNumber=?, Email=?, Nationality=?, qrcode=?
            WHERE SuspectID=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssssssssi", $fullName, $dateOBirth, $gender, $address, $phoneNumber, $email, $nationality, $qrCodeFileName, $SuspectID);
    $result = $stmt->execute();

    return $result;
}

function updateQRCodeFilename($SuspectID, $qrCodeFileName) {
    global $mysqli;

    $stmt = $mysqli->prepare("UPDATE suspect_information SET qrcode = ? WHERE SuspectID = ?");
    $stmt->bind_param("si", $qrCodeFileName, $SuspectID);
    $stmt->execute();
    $stmt->close();
}



function deleteSuspectInfo($SuspectID)
{
    global $mysqli;

    $sql = "DELETE FROM suspect_information WHERE SuspectID=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $SuspectID);
    $result = $stmt->execute();

    if ($result) {
        $sql = "ALTER TABLE suspect_information AUTO_INCREMENT = 1";
        $mysqli->query($sql);
    }

    return $result;
}

function getSuspectInfoWithLimitAndOffset($limit, $offset)
{
    global $mysqli;

    $sql = "SELECT * FROM suspect_information LIMIT ? OFFSET ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();

    $result = $stmt->get_result();

    $residents = [];
    while ($row = $result->fetch_assoc()) {
        $residents[] = $row;
    }

    $stmt->close();

    return $residents;
}

function getSuspectInfoWithSearchLimitAndOffset($search, $limit, $offset)
{
    global $mysqli;

    $search = "%" . $search . "%";
    $sql = "SELECT * FROM suspect_information WHERE FullName LIKE ? OR Email LIKE ? LIMIT ? OFFSET ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssii", $search, $search, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $resident = [];
    while ($row = $result->fetch_assoc()) {
        $resident[] = $row;
    }

    return $resident;
}

function getTotalSuspectInfoCount()
{
    global $mysqli;

    $query = "SELECT COUNT(*) as total FROM suspect_information";
    $result = $mysqli->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    return 0;
}

function generatePaginationLinks($currentPage, $totalPages)
{
    $pagination = '';

    // Previous page link
    $previousPage = $currentPage - 1;
    if ($previousPage > 0) {
        $pagination .= '<li class="page-item"><a class="page-link" href="?page=' . $previousPage . '">Previous</a></li>';
    } else {
        $pagination .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
    }

    // Individual page links
    for ($i = 1; $i <= $totalPages; $i++) {
        $activeClass = ($i == $currentPage) ? 'active' : '';
        $pagination .= '<li class="page-item ' . $activeClass . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
    }

    // Next page link
    $nextPage = $currentPage + 1;
    if ($nextPage <= $totalPages) {
        $pagination .= '<li class="page-item"><a class="page-link" href="?page=' . $nextPage . '">Next</a></li>';
    } else {
        $pagination .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
    }

    return $pagination;
}

function getStatusEntries($startIndex, $limit)
{
    $endIndex = $startIndex + $limit - 1;
    if ($endIndex > getTotalCategoryCount()) {
        $endIndex = getTotalCategoryCount();
    }

    $search = isset($_GET['search']) ? $_GET['search'] : '';

    return "Showing $startIndex to $endIndex of " . getTotalCategoryCount() . " entries (Search: $search)";
}