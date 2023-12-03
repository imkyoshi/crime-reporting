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
            $filePath = $uploadDir . $fileName;

        // Ensure the target directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filePath = $uploadDir . $fileName;

            // Move the uploaded file to the specified directory
            move_uploaded_file($_FILES[$inputName]['tmp_name'], $filePath);

            return $filePath;
        }

        return null;
    }

    function insertCrimeInformation($email, $formFileValidID, $dateTimeOfReport, $dateTimeOfIncident, $placeOfIncident, $suspectName, $statement, $formFileEvidence, $crimetype)
    {
        global $mysqli;
    
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM crime_information WHERE suspectName = ? AND statement = ?");
        $stmt->bind_param("ss", $suspectName, $statement);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    
        if ($count > 0) {
            return "Crime Information with this suspect name already exists.";
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