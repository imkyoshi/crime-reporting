    <?php 
    require_once '../config/db.php';
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
    
    

// report3_functions.php
function insertCrimeInformation($email, $formFileValidID, $dateTimeOfReport, $dateTimeOfIncident, $placeOfIncident, $suspectName, $statement, $formFileEvidence, $crimetype, $qrcode)
{
    global $mysqli;

    // Check if crime information with the same suspect name and statement already exists
    $stmtCheck = $mysqli->prepare("SELECT COUNT(*) FROM crime_information WHERE suspectName = ? AND statement = ?");
    $stmtCheck->bind_param("ss", $suspectName, $statement);
    $stmtCheck->execute();
    $stmtCheck->bind_result($count);
    $stmtCheck->fetch();
    $stmtCheck->close();

    if ($count > 0) {
        return "Crime Information with this suspect name and statement already exists.";
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

    // Insert data into the database
    $stmtInsert = $mysqli->prepare("INSERT INTO crime_information (email, formFileValidID, dateTimeOfReport, dateTimeOfIncident, placeOfIncident, suspectName, statement, formFileEvidence, CrimeType, qrcode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtInsert->bind_param("ssssssssss", $email, $formFileValidID, $dateTimeOfReport, $dateTimeOfIncident, $placeOfIncident, $suspectName, $statement, $formFileEvidence, $crimetype, $qrCodeFileName);

    $result = $stmtInsert->execute();
    $stmtInsert->close();

    if ($result) {
        return "Crime Information submitted successfully";
    } else {
        return "Failed to submit Crime Information";
    }
}

    
    ?>