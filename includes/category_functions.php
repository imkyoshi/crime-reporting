<?php
require_once '../config/db.php';

// Retrieve a user by ID from the database
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

// Retrieve all category from the database
function getAllcategory()
{
    global $mysqli;

    $sql = "SELECT * FROM crime_category";
    $result = $mysqli->query($sql);

    $category = [];
    while ($row = $result->fetch_assoc()) {
        $category[] = $row;
    }

    return $category;
}

// Add a new Category to the database
function addCategory($crimeName, $description, $crimeType)
{
    global $mysqli;

    // Check if the category already exists
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM crime_category WHERE crimeName = ?");
    $stmt->bind_param("s", $crimeName);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        // Category with the same name already exists
        return "Category with this name already exists.";
    }

    $stmt = $mysqli->prepare("INSERT INTO crime_category (crimeName, description, crimeType) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $crimeName, $description, $crimeType);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        return "Category added successfully.";
    } else {
        return "Failed to add category.";
    }
}



// Update an existing Category in the database
function updateCategory($categoryID, $crimeName, $description, $crimeType)
{
    global $mysqli;

    $sql = "UPDATE crime_category SET crimeName=?, description=?, crimeType=?
            WHERE categoryID=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sssi", $crimeName, $description, $crimeType, $categoryID);
    $result = $stmt->execute();

    return $result;
}

// Delete a Category from the database and reset the ID
function deleteCategory($categoryID)
{
    global $mysqli;

    $sql = "DELETE FROM crime_category WHERE categoryID=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $categoryID);
    $result = $stmt->execute();

    if ($result) {
        $sql = "ALTER TABLE crime_category AUTO_INCREMENT = 1";
        $mysqli->query($sql);
    }

    return $result;
}

// Retrieve category with a specified limit and offset
function getcategoryWithLimitAndOffset($limit, $offset)
{
    global $mysqli;

    $sql = "SELECT * FROM crime_category LIMIT ? OFFSET ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $category = [];
    while ($row = $result->fetch_assoc()) {
        $category[] = $row;
    }

    return $category;
}


// Retrieve category with a search filter and a specified limit and offset
function getcategoryWithSearchLimitAndOffset($search, $limit, $offset)
{
    global $mysqli;

    $search = "%" . $search . "%";
    $sql = "SELECT * FROM crime_category WHERE crimeName LIKE ? OR crimeType LIKE ? LIMIT ? OFFSET ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssii", $search, $search, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $category = [];
    while ($row = $result->fetch_assoc()) {
        $category[] = $row;
    }

    return $category;
}

// Retrieve the total count of category in the database
function getTotalCategoryCount()
{
    global $mysqli;

    $sql = "SELECT COUNT(*) as total FROM crime_category";
    $result = $mysqli->query($sql);

    $row = $result->fetch_assoc();
    $totalcategory = $row['total'];

    return $totalcategory;
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



// Retrieve status entries for pagination with a specified search filter or a default value if not set
function getStatusEntries($startIndex, $limit)
{
    $endIndex = $startIndex + $limit - 1;
    if ($endIndex > getTotalCategoryCount()) {
        $endIndex = getTotalCategoryCount();
    }

    $search = isset($_GET['search']) ? $_GET['search'] : '';

    return "Showing $startIndex to $endIndex of " . getTotalCategoryCount() . " entries (Search: $search)";
}

?>
