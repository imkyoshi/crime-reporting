<?php
require_once '../config/db.php';
require_once '../api/phpqrcode/qrlib.php';

// Retrieving the records of users and crime-category
function retrieveRecords() {
    global $mysqli;

    $sql = "SELECT u.fullName, u.phoneNumber, c.crimeName
            FROM users AS u
            INNER JOIN crime_category AS c ON (u.id = c.categoryID)
            ORDER BY c.crimeName ASC";     

    $result = $mysqli->query($sql);
    $records = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $records[] = array(
                "fullName" => $row["fullName"],
                "phoneNumber" => $row["phoneNumber"],
                "crimeName" => $row["crimeName"]
            );
        }
    } else {
        // Handle the case where no records are found
        // You can leave $records as an empty array in this case
    }

    return $records;
}

// Getting the user by ID
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

// Get all Crime info
function getAllCrimeInfo()
{
    global $mysqli;

    $sql = "SELECT * FROM crime_information";
    $result = $mysqli->query($sql);

    $residents = [];
    while ($row = $result->fetch_assoc()) {
        $residents[] = $row;
    }

    return $residents;
}

// It handles the file upload
function handleFileUpload($inputName, $uploadDir)
{
    if (!empty($_FILES[$inputName]['name'])) {
        $fileName = $_FILES[$inputName]['name'];

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filePath = $uploadDir . $fileName;
        move_uploaded_file($_FILES[$inputName]['tmp_name'], $filePath);
        return $fileName;
    }

    return null;
}

//Adding Records
function addCrimeInfo($fullName, $phoneNumber, $formFileValidID, $dateTimeOfReport, $dateTimeOfIncident, $placeOfIncident, $suspectName, $statement, $formFileEvidence, $crimetype, $status)
{
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM crime_information WHERE fullName = ? AND suspectName = ?");
    $stmt->bind_param("ss", $fullName, $suspectName);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        return "Crime Information with this fullName already exists.";
    }

    // Generate QR code data
    $qrCodeData = "Full Name: " . $fullName . "\n";
    $qrCodeData .= "Mobile No: " . $phoneNumber . "\n";
    $qrCodeData .= "Reported At: " . $dateTimeOfReport . "\n";
    $qrCodeData .= "Incident At: " . $dateTimeOfIncident . "\n";
    $qrCodeData .= "Place: " . $placeOfIncident . "\n";
    $qrCodeData .= "Suspect: " . $suspectName . "\n";
    $qrCodeData .= "Crime Type: " . $crimetype . "\n";
    $qrCodeData .= "Statement: " . $statement . "\n";
    $qrCodeData .= "status: " . $status . "\n";

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

    $stmt = $mysqli->prepare("INSERT INTO crime_information (fullName, formFileValidID, dateTimeOfReport, dateTimeOfIncident, placeOfIncident, suspectName, statement, formFileEvidence, CrimeType, qrcode=?, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)");
    $stmt->bind_param("ssssssssssss", $fullName, $phoneNumber, $formFileValidID, $dateTimeOfReport, $dateTimeOfIncident, $placeOfIncident, $suspectName, $statement, $formFileEvidence, $crimetype, $qrCodeFileName, $status);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        return "Crime Information added successfully.";
    } else {
        return "Failed to Crime Information resident.";
    }
}

// Updating the Records
function updateCrimeInfo($crime_id, $fullName, $phoneNumber, $formFileValidID, $dateTimeOfReport, $dateTimeOfIncident, $placeOfIncident, $suspectName, $statement, $formFileEvidence, $crimetype, $status)
{
    global $mysqli;

    // Generate QR code data
    $qrCodeData = "Full Name: " . $fullName . "\n";
    $qrCodeData .= "Mobile No: " . $phoneNumber . "\n";
    $qrCodeData .= "Reported At: " . $dateTimeOfReport . "\n";
    $qrCodeData .= "Incident At: " . $dateTimeOfIncident . "\n";
    $qrCodeData .= "Place: " . $placeOfIncident . "\n";
    $qrCodeData .= "Suspect: " . $suspectName . "\n";
    $qrCodeData .= "Crime Type: " . $crimetype . "\n";
    $qrCodeData .= "Statement: " . $statement . "\n";
    $qrCodeData .= "status: " . $status . "\n";
    

    // Generate QR code image and save it
    $qrCodePath = __DIR__ . DIRECTORY_SEPARATOR . ".."
        . DIRECTORY_SEPARATOR . "dist"
        . DIRECTORY_SEPARATOR . "qrcodes"
        . DIRECTORY_SEPARATOR;
    if (!is_dir($qrCodePath)) {
        mkdir($qrCodePath, 0777, true);
    }

    $qrCodeFileName = uniqid() . "_" . time() . ".png";
    $qrCodeFullPath = $qrCodePath . $qrCodeFileName;
    QRcode::png($qrCodeData, $qrCodeFullPath);

    // Update data in the database
    $sql = "UPDATE crime_information SET fullName=?, phoneNumber=?, formFileValidID=?, dateTimeOfReport=?, dateTimeOfIncident=?, placeOfIncident=?, suspectName=?, statement=?, formFileEvidence=?, CrimeType=?, qrcode=?, status=?
            WHERE crime_id=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssssssssssssi", $fullName, $phoneNumber, $formFileValidID, $dateTimeOfReport, $dateTimeOfIncident, $placeOfIncident, $suspectName, $statement, $formFileEvidence, $crimetype, $qrCodeFileName, $status, $crime_id);
    $result = $stmt->execute();

    return $result;
}

function updateQRCodeFilename($crime_id, $qrCodeFileName) {
    global $mysqli;

    $stmt = $mysqli->prepare("UPDATE crime_information SET qrcode = ? WHERE crime_id = ?");
    $stmt->bind_param("si", $qrCodeFileName, $crime_id);
    $stmt->execute();
    $stmt->close();
}


// Deleting the Records
function deleteCrimeInfo($crime_id)
{
    global $mysqli;

    $sql = "DELETE FROM crime_information WHERE crime_id=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $crime_id);
    $result = $stmt->execute();

    if ($result) {
        $sql = "ALTER TABLE crime_information AUTO_INCREMENT = 1";
        $mysqli->query($sql);
    }

    return $result;
}

// Pagination Page Limit
function getCrimeInfoWithLimitAndOffset($limit, $offset)
{
    global $mysqli;

    $sql = "SELECT * FROM crime_information LIMIT ? OFFSET ?";
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

// Search records
function getCrimeInfoWithSearchLimitAndOffset($search, $limit, $offset)
{
    global $mysqli;

    $search = "%" . $search . "%";
    $sql = "SELECT * FROM crime_information WHERE CrimeType LIKE ? OR suspectName LIKE ? LIMIT ? OFFSET ?";
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

// Get total Crime information
function getTotalCrimeInfoCount()
{
    global $mysqli;

    $query = "SELECT COUNT(*) as total FROM crime_information";
    $result = $mysqli->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    return 0;
}


// Pagination
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

// Shows Status Entries
function getStatusEntries($startIndex, $limit)
{
    $endIndex = $startIndex + $limit - 1;
    if ($endIndex > getTotalCategoryCount()) {
        $endIndex = getTotalCategoryCount();
    }

    $search = isset($_GET['search']) ? $_GET['search'] : '';

    return "Showing $startIndex to $endIndex of " . getTotalCategoryCount() . " entries (Search: $search)";
}