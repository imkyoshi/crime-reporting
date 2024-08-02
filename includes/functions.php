<?php
// Include database connections
require_once 'db.php';

function getRecentCrimeInfo($limit = 10) {
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT *, DATE(dateCreated) AS date_created FROM crime_information ORDER BY dateCreated DESC LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $recentCrimeInfo = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $recentCrimeInfo;
}



// Function to fetch data for the donut chart
function getMonthlyCrimeCounts() {
    global $mysqli;

    $sql = "SELECT DATE_FORMAT(`dateTimeOfReport`, '%Y-%m') AS `month_year`, COUNT(*) AS `count`
            FROM `crime_information`
            GROUP BY `month_year`";

    $result = $mysqli->query($sql);

    $data = array();
    $months = array();
    $years = array(); // Add this line

    while ($row = $result->fetch_assoc()) {
        $monthYear = $row['month_year'];
        $count = $row['count'];

        // Extract year from month_year
        $year = date('Y', strtotime($monthYear));

        $months[] = $monthYear;
        $data[] = $count;
        
        // Add year to the years array
        if (!in_array($year, $years)) {
            $years[] = $year;
        }
    }

    return array('data' => $data, 'months' => $months, 'years' => $years);
}

// Function to fetch data for the donut chart
function getDonutChartData() {
    global $mysqli;

    $sql = "SELECT CrimeType, COUNT(*) as count FROM crime_information GROUP BY CrimeType";
    $result = $mysqli->query($sql);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data['labels'][] = $row['CrimeType'];
        $data['data'][] = $row['count'];
    }

    return $data; // Return the array directly without json_encode
}

// Function to authenticate user
function authenticateUser($email, $password)
{
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    } else {
        return null;
    }
}

// Register a new user with the specified username, password, and email
function registerUser($fullName, $phoneNumber, $address, $dateOfBirth, $email, $password)
{
    global $mysqli;

    // Check if the email or fullname already exists
    $query = "SELECT * FROM users WHERE email = ? OR fullName = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ss", $fullName, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return false; 
    }

    // Insert the new user into the database with user role set as "user"
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (fullName, phoneNumber, address, dateOfBirth, email, password, roles) VALUES (?, ?, ?, ?, ?, ?, 'user')";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ssssss", $fullName, $phoneNumber, $address, $dateOfBirth, $email, $hashedPassword);
    $stmt->execute();

    return true; // Registration successful
}

// Function to retrieve a user by username or email
function getUserByFullNameOrEmail($fullName, $email)
{
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE fullName = ? OR email = ? LIMIT 1");
    $stmt->bind_param("ss", $fullName, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    return $user ? $user : false;
}

// Function to retrieve a user by ID
function getUserById($userId)
{
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    return $user;
}

// Function to retrieve all users from the database
function getAllUsers()
{
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT * FROM users");
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $users;
}

// Function to add a new user
function addUser($fullName, $phoneNumber, $address, $dateOfBirth, $email, $password,  $roles)
{
    global $mysqli;

    // Check if the email or fullname already exists
    $query = "SELECT * FROM users WHERE email = ? OR fullName = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ss", $fullName, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return false; // Username or email already exists
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("INSERT INTO users (fullName, phoneNumber, address, dateOfBirth, email, password, roles) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $fullName, $phoneNumber, $address, $dateOfBirth, $email, $hashedPassword, $roles);
    $success = $stmt->execute();
    $stmt->close();

    if ($success) {
        return "User added successfully.";
    } else {
        return "Failed to add user.";
    }
}

// Function to update an existing user
function updateUser($userId, $fullName, $phoneNumber, $address, $dateOfBirth, $email, $password, $roles)
{
    global $mysqli;

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("UPDATE users SET fullName = ?, phoneNumber = ?, address = ?, dateOfBirth = ?,  email = ?, password = ?,roles = ? WHERE id = ?");
    $stmt->bind_param("sssssssi", $fullName, $phoneNumber, $address, $dateOfBirth, $email, $hashedPassword, $roles, $userId);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

// Function to delete a user
function deleteUser($userId)
{
    global $mysqli;

    $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $success = $stmt->execute();
    $stmt->close();

    if ($success) {
        // Reset the ID numbering in the table
        $mysqli->query("ALTER TABLE users AUTO_INCREMENT = 1");
    }

    return $success;
}

// Function to retrieve users with the selected limit
function getUsersWithLimit($limit)
{
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT * FROM users LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $users;
}

// Function to retrieve users with search filter
function getUsersWithSearch($search)
{
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE fullName LIKE CONCAT('%', ?, '%') OR email LIKE CONCAT('%', ?, '%')");
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $users;
}

// Function to retrieve the total user count
function getTotalUserCount()
{
    global $mysqli;

    $query = "SELECT COUNT(*) as total FROM users";
    $result = $mysqli->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    return 0;
}

function getTotalCrimeCategoryCount()
{
    global $mysqli;

    $query = "SELECT COUNT(*) as total FROM crime_category";
    $result = $mysqli->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    return 0;
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

function getTotalResidentCount()
{
    global $mysqli;

    $query = "SELECT COUNT(*) as total FROM resident_information";
    $result = $mysqli->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    return 0;
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

// Function to generate the pagination links
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



// Function to get the status entries for pagination
function getStatusEntries($startIndex, $showRecords)
{
    global $totalEntries;

    $endIndex = min($startIndex + $showRecords - 1, $totalEntries);
    $status = "Showing {$startIndex} to {$endIndex} of {$totalEntries} entries";
    return $status;
}
?>
