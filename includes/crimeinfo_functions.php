<?php
require_once '../config/db.php';
require_once '../api/phpqrcode/qrlib.php';

function retrieveRecords() {
    global $mysqli;

    $sql = "SELECT u.email, c.CrimeType
            FROM users AS u
            INNER JOIN crime_category AS c ON (u.id = c.categoryID)";     

    $result = $mysqli->query($sql);

    $records = array(); // Initialize an array to store all records

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $records[] = array(
                "email" => $row["email"],
                "crimeType" => $row["CrimeType"]
            );
        }
    } else {
        // Handle the case where no records are found
        // You can leave $records as an empty array in this case

        
    }

    return $records;
}

// function retrieveRecords() {
//     global $mysqli;

//     $sql = "SELECT users.email, CONCAT (resident_information.firstName, ' ', resident_information.lastName) AS fullName,crime_information.dateTimeOfReport,
//             crime_information.dateTimeOfIncident, crime_information.placeOfIncident, crime_information.suspectName, crime_information.status
//             FROM crime_information
//             JOIN users ON crime_information.crime_id = users.id
//             JOIN resident_information ON crime_information.crime_id = resident_information.resident_id
//             WHERE crime_information.crime_id;";    

//     $result = $mysqli->query($sql);
//     $records = array(); // Initialize an array to store all records

//     if ($result->num_rows > 0) {
//         while ($row = $result->fetch_assoc()) {
//             $records[] = array(
//                 "email" => $row["email"],
//                 "fullName" => $row["fullName"],
//                 "dateTimeOfReport" => $row["dateTimeOfReport"],
//                 "dateTimeOfIncident" => $row["dateTimeOfIncident"],
//                 "placeOfIncident" => $row["placeOfIncident"],
//                 "suspectName" => $row["suspectName"],
//                 "status" => $row["status"]
//             );
//         }
//     } else {
//         // Handle the case where no records are found
//         // You can leave $records as an empty array in this case        
//     }

//     return $records;
// }


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



function addCrimeInfo($email, $formFileValidID, $dateTimeOfReport, $dateTimeOfIncident, $placeOfIncident, $suspectName, $statement, $formFileEvidence, $crimetype, $status)
{
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM crime_information WHERE email = ? AND suspectName = ?");
    $stmt->bind_param("ss", $email, $suspectName);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        return "Crime Information with this email already exists.";
    }

    // Generate QR code data
    $qrCodeData = "Email: " . $email . "\n";
    $qrCodeData .= "Reported At: " . $dateTimeOfReport . "\n";
    $qrCodeData .= "Incident At: " . $dateTimeOfIncident . "\n";
    $qrCodeData .= "Place: " . $placeOfIncident . "\n";
    $qrCodeData .= "Suspect: " . $suspectName . "\n";
    $qrCodeData .= "Crime Type: " . $crimetype . "\n";
    $qrCodeData .= "Statement: " . $statement . "\n";

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

    $stmt = $mysqli->prepare("INSERT INTO crime_information (email, formFileValidID, dateTimeOfReport, dateTimeOfIncident, placeOfIncident, suspectName, statement, formFileEvidence, CrimeType, qrcode=?, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)");
    $stmt->bind_param("sssssssssss", $email, $formFileValidID, $dateTimeOfReport, $dateTimeOfIncident, $placeOfIncident, $suspectName, $statement, $formFileEvidence, $crimetype, $qrCodeFileName, $status);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        return "Crime Information added successfully.";
    } else {
        return "Failed to Crime Information resident.";
    }
}

function updateCrimeInfo($crime_id, $email, $formFileValidID, $dateTimeOfReport, $dateTimeOfIncident, $placeOfIncident, $suspectName, $statement, $formFileEvidence, $crimetype, $status)
{
    global $mysqli;

    // Generate QR code data
    $qrCodeData = "Email: " . $email . "\n";
    $qrCodeData .= "Reported At: " . $dateTimeOfReport . "\n";
    $qrCodeData .= "Incident At: " . $dateTimeOfIncident . "\n";
    $qrCodeData .= "Place: " . $placeOfIncident . "\n";
    $qrCodeData .= "Suspect: " . $suspectName . "\n";
    $qrCodeData .= "Crime Type: " . $crimetype . "\n";
    $qrCodeData .= "Statement: " . $statement . "\n";

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
    $sql = "UPDATE crime_information SET email=?, formFileValidID=?, dateTimeOfReport=?, dateTimeOfIncident=?, placeOfIncident=?, suspectName=?, statement=?, formFileEvidence=?, CrimeType=?, qrcode=?, status=?
            WHERE crime_id=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssssssssssi", $email, $formFileValidID, $dateTimeOfReport, $dateTimeOfIncident, $placeOfIncident, $suspectName, $statement, $formFileEvidence, $crimetype, $qrCodeFileName, $status, $crime_id);
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