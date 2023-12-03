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

function getAllResidents()
{
    global $mysqli;

    $sql = "SELECT * FROM resident_information";
    $result = $mysqli->query($sql);

    $residents = [];
    while ($row = $result->fetch_assoc()) {
        $residents[] = $row;
    }

    return $residents;
}

function addResident($firstName, $lastName, $dateOfBirth, $address, $phoneNumber, $email)
{
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM resident_information WHERE firstName = ? AND lastName = ?");
    $stmt->bind_param("ss", $firstName, $lastName);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        return "Resident with this name already exists.";
    }

    $stmt = $mysqli->prepare("INSERT INTO resident_information (firstName, lastName, dateOfBirth, address, phoneNumber, email) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $firstName, $lastName, $dateOfBirth, $address, $phoneNumber, $email);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        return "Resident added successfully.";
    } else {
        return "Failed to add resident.";
    }
}

function updateResident($resident_id, $firstName, $lastName, $dateOfBirth, $address, $phoneNumber, $email)
{
    global $mysqli;

    $sql = "UPDATE resident_information SET firstName=?, lastName=?, dateOfBirth=?, address=?, phoneNumber=?, email=?
            WHERE resident_id=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssssssi", $firstName, $lastName, $dateOfBirth, $address, $phoneNumber, $email, $resident_id);
    $result = $stmt->execute();

    return $result;
}

function deleteResident($resident_id)
{
    global $mysqli;

    $sql = "DELETE FROM resident_information WHERE resident_id=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $resident_id);
    $result = $stmt->execute();

    if ($result) {
        $sql = "ALTER TABLE resident_information AUTO_INCREMENT = 1";
        $mysqli->query($sql);
    }

    return $result;
}

function getResidentsWithLimitAndOffset($limit, $offset)
{
    global $mysqli;

    $sql = "SELECT * FROM resident_information LIMIT ? OFFSET ?";
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

function getresidentsWithSearchLimitAndOffset($search, $limit, $offset)
{
    global $mysqli;

    $search = "%" . $search . "%";
    $sql = "SELECT * FROM resident_information WHERE firstName LIKE ? OR lastName LIKE ? LIMIT ? OFFSET ?";
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