<?php
// Start the session
session_start();

// Check if the user is not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['roles'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Include database connection and functions files
require_once '../includes/db.php';
require_once '../includes/category_functions.php';

// Retrieve all category from the database
$category = getAllcategory();
$currentUserID = $_SESSION['user_id'];
$currentUserInfo = getUserById($currentUserID);

// Handle form submission for adding a Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addCategory'])) {
    $CrimeType = $_POST['CrimeType'];
    $description = $_POST['description'];

    // Add the Category to the database
    $result = addCategory($CrimeType, $description);

    if ($result) {
        $_SESSION['success_message'] = 'Category added successfully.';
    } else {
        $_SESSION['error_message'] = 'Failed to add Category.';
    }

    // Redirect to the category page
    header("Location: crime-category.php");
    exit;
}

// Handle form submission for updating a Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateCategory'])) {
    $categoryID = $_POST['categoryID'];
    $CrimeType = $_POST['CrimeType'];
    $description = $_POST['description'];

    // Update the Category in the database
    $result = updateCategory($categoryID, $CrimeType, $description);

    if ($result) {
        $_SESSION['success_message'] = 'Category updated successfully.';
    } else {
        $_SESSION['error_message'] = 'Failed to update Category.';
    }

    // Redirect to the category page
    header("Location: crime-category.php");
    exit;
}

// Handle form submission for deleting a Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteCategory'])) {
    $categoryID = $_POST['categoryID'];

    // Delete the Category from the database
    $result = deleteCategory($categoryID);

    if ($result) {
        $_SESSION['success_message'] = 'Category deleted successfully.';
    } else {
        $_SESSION['error_message'] = 'Failed to delete Category.';
    }

    // Redirect to the category page
    header("Location: crime-category.php");
    exit;
}

/// Pagination settings
$limit = isset($_GET['showRecords']) ? intval($_GET['showRecords']) : 5; // Number of records to show per page

// Calculate pagination values
$totalcategory = getTotalCategoryCount();
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page number
$totalPages = ($limit != 0) ? ceil($totalcategory / $limit) : 0;
$startIndex = ($currentPage - 1) * $limit;
$endIndex = min($startIndex + $limit, $totalcategory);
$category = getcategoryWithLimitAndOffset($limit, $startIndex);

// Handle form submission for searching category
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search = $_GET['search'];

    // If search query is not empty, retrieve category with search filter
    if (!empty($search)) {
        $category = getcategoryWithSearchLimitAndOffset($search, $limit, $startIndex);
    }
}
?>


<!DOCTYPE html>
<html>

<head>
     <title>Gym Management System</title>
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Line Awesomee CSS -->
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="wrapper">
        <nav id="sidebar">
            <div class="sidebar-header" style="text-align:center;">
                <img src="../assets/img/PNP.png" class="img-fluid"  id="logooos" style="width: 50px; display:block; margin-right:auto; margin-left:auto;">
                <div class="mb-2">Welcome,
                    <?php echo $currentUserInfo['username']; ?>!
                </div>
                <button onclick="location.href='../auth/logout.php'" class="logout-button"><i class="las la-sign-out-alt"></i> Logout</button>
            </div>
            <ul>
                <li>
                    
                    <a href="../admin/dashboard.php"><i class="las la-home"></i>  Dashboard</a>
                    
                </li>
                <li>
                    <a href="../admin/user_management.php"><i class="las la-user-friends"></i>  User Management</a>
                </li>
                <li>
                    <a href="../admin/crime-category.php"><i class="las la-layer-group"></i>  Crime Categories</a>
                </li>
                <li>
                    <a href="../admin/crime-info.php"><i class="las la-gavel"></i>  Crime Information</a>
                </li>
                <li>
                    <a href="../admin/resident-info.php"><i class="las la-archive"></i>  Resident Information</a>
                </li>
            </ul>
        </nav>

        <div id="content">
            <nav id="topnav" class="navbar navbar-expand-lg navbar-default navbar-inverse navbar-fixed-top"
                role="navigation">
                <button type="button" id="sidebarCollapse" class="btn btn-info">
                    <i class="las la-bars"></i>
                </button>
                <div class="container-fluid">
                    <div class="navbar-brand">Gym Management System</div>
                </div>
            </nav>

            <div class="content-wrapper">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../admin/dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">category</li>
                    </ol>
                </nav>

                <div class="card shadow">
                    <div class="card-body">
                        <h3>category</h3>
                        <!-- ADD NEW Category -->
                        <div class="row mb-2">
                            <div class="col-sm-12" style="margin-top: 15px;">
                                <button type="button" class="btn btn-primary btn-sm float-left" data-toggle="modal"
                                    data-target="#addCategoryModal"><i class="las la-plus-circle"></i>  Add New Category</button>
                            </div>

                            <!-- Add Category Modal -->
                            <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog"
                                aria-labelledby="addCategoryModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="">
                                                <div class="form-group">
                                                    <label for="CrimeType">Crime Type</label>
                                                    <input type="text" class="form-control" id="CrimeType"
                                                        name="CrimeType" placeholder="Enter Crime Type" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="description">Description</label>
                                                    <textarea class="form-control" id="description" name="description" rows="3"
                                                        placeholder="Enter description" required></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary"
                                                        name="addCategory">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- SHOW RECORDS -->
                            <div class="col-sm-3" style="margin-top: 15px;">
                                <form id="showRecordsForm" method="GET" action="">
                                    <div class="form-group">
                                        <label class="d-flex align-items-center">
                                            <select class="form-control form-control-sm" id="showRecords"
                                                name="showRecords" style="width: 120px;" onchange="this.form.submit()">
                                                <option value="5" <?php if ($limit == 5)
                                                    echo 'selected'; ?>>5</option>
                                                <option value="10" <?php if ($limit == 10)
                                                    echo 'selected'; ?>>10
                                                </option>
                                                <option value="20" <?php if ($limit == 20)
                                                    echo 'selected'; ?>>20
                                                </option>
                                                <option value="50" <?php if ($limit == 50)
                                                    echo 'selected'; ?>>50
                                                </option>
                                            </select>
                                            <span class="ml-2"><i class="las la-filter"></i>  records per page</span>
                                        </label>
                                    </div>
                                </form>
                            </div>
                            <!-- SEARCH -->
                            <div class="col-sm-9" style="margin-top: 15px;">
                                <div class="form-inline justify-content-end">
                                    <form id="searchForm" method="GET" action="">
                                        <div class="form-group mx-sm-3 mb-2">
                                            <label for="searchInput" class="mr-2"><i class="las la-search"></i>  Search:</label>
                                            <input type="text" class="form-control" id="searchInput" name="search"
                                                style="max-width: 150px;"
                                                value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Category TABLE -->
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Crime Type</th>
                                        <th>Description</th>
                                        <th style="text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($category as $Category): ?>
                                            <tr>
                                                <td>
                                                    <?php echo $Category['CrimeType']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $Category['description']; ?>
                                                </td>
                                                    <td style="text-align:center;">
                                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                                            data-target="#editCategoryModal<?php echo $Category['categoryID']; ?>"><i class="las la-edit"></i> Update</button>
                                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                                                data-target="#deleteCategoryModal<?php echo $Category['categoryID']; ?>"><i class="las la-trash-alt"></i>  Delete</button>
                                                        </td>
                                                    </tr>

                                                    <!-- Edit Category Modal -->
                                                    <div class="modal fade" id="editCategoryModal<?php echo $Category['categoryID']; ?>"
                                                tabindex="-1" role="dialog"
                                                aria-labelledby="editCategoryModalLabel<?php echo $Category['categoryID']; ?>"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="editCategoryModalLabel<?php echo $Category['categoryID']; ?>">
                                                                Edit Category
                                                            </h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form method="POST" action="">
                                                                <input type="hidden" name="categoryID"
                                                                    value="<?php echo $Category['categoryID']; ?>">
                                                                <div class="form-group">
                                                                    <label for="editCrimeType">Category Type</label>
                                                                    <input type="text" class="form-control" id="editCrimeType"
                                                                        name="CrimeType"
                                                                        value="<?php echo $Category['CrimeType']; ?>" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="editDescription">Description</label>
                                                                    <textarea class="form-control" id="editDescription" name="description" rows="3"
                                                                        required><?php echo $Category['description']; ?></textarea>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-primary"
                                                                        name="updateCategory">Save</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Delete Category Modal -->
                                            <div class="modal fade" id="deleteCategoryModal<?php echo $Category['categoryID']; ?>"
                                                tabindex="-1" role="dialog"
                                                aria-labelledby="deleteCategoryModalLabel<?php echo $Category['categoryID']; ?>"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="deleteCategoryModalLabel<?php echo $Category['categoryID']; ?>">
                                                                Delete Category
                                                            </h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Are you sure you want to delete this Category?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <form method="POST" action="">
                                                                <input type="hidden" name="categoryID"
                                                                    value="<?php echo $Category['categoryID']; ?>">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Cancel</button>    
                                                                <button type="submit" class="btn btn-danger"
                                                                    name="deleteCategory">Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- SHOW ENTRIES -->
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <p style="font-size: 14px;">
                                    Showing <?php echo $startIndex + 1; ?> to <?php echo $endIndex; ?> of
                                    <?php echo $totalcategory; ?> entries
                                </p>
                            </div>
                            <!-- PAGINATION -->
                            <div class="col-sm-6">
                                <div class="d-flex justify-content-end align-items-center">
                                    <nav>
                                        <ul class="pagination">
                                            <?php echo generatePaginationLinks($currentPage, $totalPages); ?>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                        <!-- Fixed Footer -->
            <div class="card-footer d-flex justify-content-center">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <p style="font-size: 14px;">
                            © 2023 BROSOTO STUDIO
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
            <!-- jQuery CDN -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
            <!-- Bootstrap JS CDN -->
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
            <script src="../assets/js/style.js"></script>
</body>

</html>