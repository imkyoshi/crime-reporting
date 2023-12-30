<?php
// Start the session
session_start();

// Check if the user is not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['roles'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Include database connection and functions files
require_once '../config/db.php';
require_once '../includes/category_functions.php';

// Retrieve all category from the database
$category = getAllcategory();
$currentUserID = $_SESSION['user_id'];
$currentUserInfo = getUserById($currentUserID);

// Handle form submission for adding a Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addCategory'])) {
    $CrimeType = htmlspecialchars($_POST['CrimeType']);
    $description = htmlspecialchars($_POST['description']);

    // Add the Category to the database
    $result = addCategory($CrimeType, $description);

    if ($result === "Category added successfully.") {
        header("Location: crime-category.php");
        exit;
    } elseif ($result === "Category with this name already exists.") {
        $addErrorMessage = "Category already exists.";
    } else {
        $addErrorMessage = "Failed to add category.";
    }
}

// Handle form submission for updating a Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateCategory'])) {
    $categoryID = htmlspecialchars($_POST['categoryID']);
    $CrimeType = htmlspecialchars($_POST['CrimeType']);
    $description = htmlspecialchars($_POST['description']);

    // Update the Category in the database
    $result = updateCategory($categoryID, $CrimeType, $description);

    if ($result) {
        $updateSuccessMessage = 'Category updated successfully.';
    } else {
        $errorMessage = 'Failed to update Category.';
    }
}

// Handle form submission for deleting a Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteCategory'])) {
    $categoryID = htmlspecialchars($_POST['categoryID']);

    // Delete the Category from the database
    $result = deleteCategory($categoryID);
    if ($result) {
        $deleteSuccessMessage = 'Category deleted successfully.';
    } else {
        $errorMessage = 'Failed to delete Category.';
    }
}

/// Pagination settings
$limit = isset($_GET['showRecords']) ? intval($_GET['showRecords']) : 5; // Number of records to show per page

$totalcategory = getTotalCategoryCount();
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
$totalPages = 3;
$startIndex = ($currentPage - 1) * $limit;
$endIndex = min($startIndex + $limit, $totalcategory);
$category = getcategoryWithLimitAndOffset($limit, $startIndex);
$paginationLinks = generatePaginationLinks($currentPage, $totalPages);

// Handle form submission for searching category
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search = htmlspecialchars($_GET['search']);

    // If search query is not empty, retrieve category with search filter
    if (!empty($search)) {
        $category = getcategoryWithSearchLimitAndOffset($search, $limit, $startIndex);
    }
}
?>

<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>San Luis Municipal Police Station</title>
  <link rel="icon" type="image/x-icon" href="../dist/img/favicon.ico">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=fallback">
  <!-- Line Awesome Icons -->
  <link rel="stylesheet" href="../plugins/line-awesome-free/css/line-awesome.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../dist/css/dashboard.css">
  <link href="../dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="hold-transition sidebar-mini">
  <div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="las la-bars"></i></a>
        </li>
      </ul>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4 " style="background-color:#0B2436;">
      <!-- Brand Logo -->
      <a href="index3.html" class="brand-link">
        <img src="../dist/img/PNP.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
          style="opacity: .8">
        <span class="brand-text font-weight-light">San Luis Police Station</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="info text-light">
            Welcome
          </div>
          <div class="info  text-warning">
            <?php echo $currentUserInfo['username']; ?>!
          </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
            <li class="nav-item">
              <a href="dashboard.php" class="nav-link">
                <i class="las la-home" id="icon"></i>
                <p>
                  Dashboard
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="user_management.php" class="nav-link">
                <i class="las la-user-friends" id="icon"></i>
                <p>
                  User Mangement
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="crime-category.php" class="nav-link">
                <i class="las la-layer-group" id="icon"></i></i>
                <p>
                  Crime Category
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="crime-info.php" class="nav-link">
                <i class="las la-gavel" id="icon"></i>
                <p>
                  Crime Information
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="resident-info.php" class="nav-link">
                <i class="las la-archive" id="icon"></i>
                <p>
                  Reisdent Information
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../auth/logout.php" class="nav-link">
                <i class="las la-sign-out-alt" id="icon"></i>
                <p>
                  Logout
                </p>
              </a>
            </li>
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Crime Category</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Crime Category</li>
              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>

      <div class="col-sm-12">
    <!-- Error message for adding a user -->
    <div class="error-message">
        <?php if (isset($addErrorMessage)): ?>
                <div id="successMessage" class="alert alert-danger">
                    <?php echo $addErrorMessage; ?>
                </div>
        <?php endif; ?>
    </div>
</div>


      <!-- /.content-header -->
<div class="col-sm-12">
    <!-- Success message for category update -->
    <?php if (isset($updateSuccessMessage)): ?>
          <div id="successMessage" class="alert alert-success">
              <?php echo $updateSuccessMessage; ?>
          </div>
    <?php endif; ?>
</div>


      <div class="col-sm-12">
        <!-- Success message for user update -->
        <?php if (isset($deleteSuccessMessage)): ?>
                  <div id="successMessage" class="alert alert-success">
                    <?php echo $deleteSuccessMessage; ?>
                  </div>
        <?php endif; ?>
      </div>


      <!-- Main content -->
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <!-- Default box -->
              <div class="card shadow card-outline card-primary">
                <div class="card-body">
                  <!-- ADD NEW Category -->
                  <div class="row mb-2">
                    <div class="col-sm-12" style="margin-top: 15px;">
                      <button type="button" class="btn btn-primary btn-sm float-left" data-toggle="modal"
                        data-target="#addCategoryModal"><i class="las la-plus-circle"></i> Add New Category</button>
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
                                <input type="text" class="form-control" id="CrimeType" name="CrimeType"
                                  placeholder="Enter Crime Type" required>
                              </div>
                              <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                  placeholder="Enter description" required></textarea>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="addCategory">Save</button>
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
                            <select class="form-control form-control-sm" id="showRecords" name="showRecords"
                              style="width: 120px;" onchange="this.form.submit()">
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
                            <span class="ml-2"><i class="las la-filter"></i> records per page</span>
                          </label>
                        </div>
                      </form>
                    </div>
                    <!-- SEARCH -->
                    <div class="col-sm-9" style="margin-top: 15px;">
                      <div class="form-inline justify-content-end">
                        <form id="searchForm" method="GET" action="">
                          <div class="form-group mx-sm-3 mb-2">
                            <label for="searchInput" class="mr-2"><i class="las la-search"></i> Search:</label>
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
                                  data-target="#editCategoryModal<?php echo $Category['categoryID']; ?>"><i
                                    class="las la-edit"></i> Update</button>
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                  data-target="#deleteCategoryModal<?php echo $Category['categoryID']; ?>"><i
                                    class="las la-trash-alt"></i> Delete</button>
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
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                    <form method="POST" action="">
                                      <input type="hidden" name="categoryID" value="<?php echo $Category['categoryID']; ?>">
                                      <div class="form-group">
                                        <label for="editCrimeType">Category Type</label>
                                        <input type="text" class="form-control" id="editCrimeType" name="CrimeType"
                                          value="<?php echo $Category['CrimeType']; ?>" required>
                                      </div>
                                      <div class="form-group">
                                        <label for="editDescription">Description</label>
                                        <textarea class="form-control" id="editDescription" name="description" rows="3"
                                          required><?php echo $Category['description']; ?></textarea>
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary" name="updateCategory">Save</button>
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
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                    <p>Are you sure you want to delete this Category?</p>
                                  </div>
                                  <div class="modal-footer">
                                    <form method="POST" action="">
                                      <input type="hidden" name="categoryID" value="<?php echo $Category['categoryID']; ?>">
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                      <button type="submit" class="btn btn-danger" name="deleteCategory">Delete</button>
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
              <!-- /.card -->
            </div>
            <!-- ./col -->
          </div>
          <!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    <footer class="main-footer text-center">
      <!-- Default to the left -->
      <strong>Copyright &copy; 2023. BROSOTO DEV </strong> All rights reserved.
    </footer>
  </div>
  <!-- ./wrapper -->

  <!-- REQUIRED SCRIPTS -->

  <!-- jQuery -->
  <script src="../plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- AdminLTE App -->
  <script src="../dist/js/adminlte.min.js"></script>
  <script src="../dist/js/sucessmessage.js"></script>
  <script src="../dist/js/style.js"></script>
  <script src="../dist/js/inspect.js"></script>
</body>

</html>