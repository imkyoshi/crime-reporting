<?php
// Include database connections
require_once 'db.php';

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
function authenticateUser($username, $password)
{
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
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
function registerUser($username, $password, $email)
{
    global $mysqli;

    // Check if the username or email already exists
    $query = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return false; // Username or email already exists
    }

    // Insert the new user into the database with user role set as "user"
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (username, password, email, roles) VALUES (?, ?, ?, 'user')";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sss", $username, $hashedPassword, $email);
    $stmt->execute();

    return true; // Registration successful
}






// Function to retrieve a user by username or email
function getUserByUsernameOrEmail($username, $email)
{
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
    $stmt->bind_param("ss", $username, $email);
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
function addUser($username, $password, $email, $roles)
{
    global $mysqli;

    // Check if the user already exists
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        // User with the same username already exists
        return "User with this username already exists.";
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("INSERT INTO users (username, password, email, roles) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $hashedPassword, $email, $roles);
    $success = $stmt->execute();
    $stmt->close();

    if ($success) {
        return "User added successfully.";
    } else {
        return "Failed to add user.";
    }
}


// Function to update an existing user
function updateUser($userId, $username, $password, $email, $roles)
{
    global $mysqli;

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("UPDATE users SET username = ?, password = ?, email = ?, roles = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $username, $hashedPassword, $email, $roles, $userId);
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

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username LIKE CONCAT('%', ?, '%') OR email LIKE CONCAT('%', ?, '%')");
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
