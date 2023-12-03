<?php
// Include database connection and functions files
require_once '../config/db.php';
require_once '../api/vendor/autoload.php';

use chillerlan\QRCode\QRCode;

function generateQRCode($filename, $data) {
    // Ensure the directory exists, create it if not
    $dir = pathinfo($filename, PATHINFO_DIRNAME);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    // Generate QR code and save it to a file
    $qrCode = (new QRCode)->render($data);
    file_put_contents($filename, $qrCode);

    // Return the path to the saved QR code
    return $filename;
}

function getCrimeTypes() {
    global $mysqli;

    $sql = "SELECT DISTINCT CrimeType FROM crime_category";

    $result = $mysqli->query($sql);

    $crimeTypes = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $crimeTypes[] = $row['CrimeType'];
        }
    }

    return $crimeTypes;
}

function retrieveRecords() {
    global $mysqli;

    $sql = "SELECT CONCAT(r.firstName, ' ', r.lastName) AS fullName, c.CrimeType
            FROM resident_information AS r
            INNER JOIN crime_category AS c ON (r.resident_id = c.categoryID)";

    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $fullName = $row["fullName"];
        $crimeType = $row["CrimeType"];
    } else {
        // Handle the case where no records are found
        $fullName = "";
        $crimeType = "";
    }

    return array("fullName" => $fullName, "crimeType" => $crimeType);
}



// Function to handle file uploads
function handleFileUpload($inputName, $uploadDir)
{
    // Check if files were uploaded successfully
    if (!empty($_FILES[$inputName]['name'])) {
        $fileName = $_FILES[$inputName]['name'];
        $filePath = $uploadDir . $fileName;

        // Move the uploaded file to the specified directory
        move_uploaded_file($_FILES[$inputName]['tmp_name'], $filePath);

        return $filePath;
    }

    return null;
}

// Function to insert crime information into the database
function insertCrimeInformation($dateTimeOfReport, $dateTimeOfIncident, $placeOfIncident, $suspectName, $statement, $validIDFilePath, $evidenceFilePath, $crimeType, $residentID)
{
    global $conn;

    $qrCode = ''; // Placeholder for QR code, to be generated later
    $status = 'Pending'; // Assuming initial status is 'Pending'

    $sql = "INSERT INTO crime_information (resident_id, category_id, dateTimeOfReport, dateTimeOfIncident, placeOfIncident, suspectName, statement, qrcode, evidenceFilePath, status, validIDFilePath, crimeType, fullName)
            SELECT r.resident_id, c.categoryID, ?, ?, ?, ?, ?, ?, ?, ?, ?, CONCAT(r.firstName, ' ', r.lastName) AS fullName
            FROM resident_information r
            INNER JOIN crime_category c ON c.CrimeType = ?
            WHERE r.resident_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssi", $dateTimeOfReport, $dateTimeOfIncident, $placeOfIncident, $suspectName, $statement, $qrCode, $evidenceFilePath, $status, $validIDFilePath, $crimeType, $crimeType, $residentID);

    if ($stmt->execute()) {
        // Return the ID of the inserted crime information
        return $stmt->insert_id;
    } else {
        // Handle the error (you might want to log it)
        echo "Error: " . $stmt->error;
        return false;
    }
}




// Function to generate QR code and return the file path
// function generateQRCode($crimeID, $qrCodeDir)
// {
//     // Include the chillerlan/php-qrcode library
//     require_once '../api/vendor/autoload.php';

//     // Use the library to generate the QR code
//     $qrCode = new chillerlan\QRCode\QRCode(new chillerlan\QRCode\QROptions([
//         'version' => 5,
//         'outputType' => chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
//         'eccLevel' => chillerlan\QRCode\QRCode::ECC_L,
//         'scale' => 4,
//         'imageBase64' => false,
//     ]));

//     $data = 'http://localhost/crime-reporting/view/view_reports.php?id=' . $crimeID; // Replace with the actual URL

//     // Generate the QR code and save it to the specified directory
//     $qrCodeImage = $qrCode->render($data);
//     $qrCodePath = $qrCodeDir . 'qr_code_' . $crimeID . '.png';
//     file_put_contents($qrCodePath, $qrCodeImage);

//     return $qrCodePath;
// }

function updateCrimeQRCodePath($crimeID, $qrCodePath)
{
    global $conn;

    // Update the crime information record with the QR code path
    $sql = "UPDATE crime_information SET qrcode = ? WHERE crime_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $qrCodePath, $crimeID);
    $stmt->execute();
}
?>
