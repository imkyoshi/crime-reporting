<?php
require_once '../config/db.php';

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

// // Search records
// function getCrimeInfoWithSearchLimitAndOffset($search, $limit, $offset)
// {
//     global $mysqli;

//     $search = "%" . $search . "%";
//     $sql = "SELECT * FROM crime_information WHERE CrimeType LIKE ? OR suspectName LIKE ? LIMIT ? OFFSET ?";
//     $stmt = $mysqli->prepare($sql);
//     $stmt->bind_param("ssii", $search, $search, $limit, $offset);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     $resident = [];
//     while ($row = $result->fetch_assoc()) {
//         $resident[] = $row;
//     }

//     return $resident;
// }

// // // Get total Crime information
// // function getTotalCrimeInfoCount()
// // {
// //     global $mysqli;

// //     $query = "SELECT COUNT(*) as total FROM crime_information";
// //     $result = $mysqli->query($query);

// //     if ($result && $result->num_rows > 0) {
// //         $row = $result->fetch_assoc();
// //         return $row['total'];
// //     }

// //     return 0;
// // }


// // Pagination
// function generatePaginationLinks($currentPage, $totalPages)
// {
//     $pagination = '';

//     // Previous page link
//     $previousPage = $currentPage - 1;
//     if ($previousPage > 0) {
//         $pagination .= '<li class="page-item"><a class="page-link" href="?page=' . $previousPage . '">Previous</a></li>';
//     } else {
//         $pagination .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
//     }

//     // Individual page links
//     for ($i = 1; $i <= $totalPages; $i++) {
//         $activeClass = ($i == $currentPage) ? 'active' : '';
//         $pagination .= '<li class="page-item ' . $activeClass . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
//     }

//     // Next page link
//     $nextPage = $currentPage + 1;
//     if ($nextPage <= $totalPages) {
//         $pagination .= '<li class="page-item"><a class="page-link" href="?page=' . $nextPage . '">Next</a></li>';
//     } else {
//         $pagination .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
//     }

//     return $pagination;
// }

// // Shows Status Entries
// function getStatusEntries($startIndex, $limit)
// {
//     $endIndex = $startIndex + $limit - 1;
//     if ($endIndex > getTotalCategoryCount()) {
//         $endIndex = getTotalCategoryCount();
//     }

//     $search = isset($_GET['search']) ? $_GET['search'] : '';

//     return "Showing $startIndex to $endIndex of " . getTotalCategoryCount() . " entries (Search: $search)";
// }