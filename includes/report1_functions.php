<?php 
require_once '../config/db.php';

function retrieveRecords() {
    global $mysqli;

    $sql = "SELECT u.email, c.CrimeType
            FROM users AS u
            INNER JOIN crime_category AS c ON (u.id = c.categoryID)";     

    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row["email"];
        $crimetype = $row["CrimeType"];
    } else {
        // Handle the case where no records are found
        $email = "";
        $crimetype = "";
    }

    return array("email" => $email, "crimeType" => $crimetype);
}

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

function insertCrimeInformation($email, $formFileValidID, $dateTimeOfReport, $dateTimeOfIncident, $placeOfIncident, $suspectName, $statement, $formFileEvidence, $crimetype) {
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM crime_information WHERE email = ? AND CrimeType = ?");
    $stmt->bind_param("ss", $email, $crimetype);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        return "Crime Information with this email already exists.";
    }

    $stmt = $mysqli->prepare("INSERT INTO crime_information (email, formFileValidID, dateTimeOfReport, dateTimeOfIncident, placeOfIncident, suspectName, statement, formFileEvidence, CrimeType) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $email, $formFileValidID, $dateTimeOfReport, $dateTimeOfIncident, $placeOfIncident, $suspectName, $statement, $formFileEvidence, $crimetype);
    $result = $stmt->execute();
    $stmt->close();
    

    if ($result) {
        return "Crime Information added successfully";
    } else {
        return "Failed to add Crime Information";
    }
}

?>