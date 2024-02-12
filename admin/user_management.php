<?php
// Start the session
session_start();

// Check if the user is not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['roles'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

require_once '../config/db.php';
require_once '../includes/functions.php';

// RETRIEVE USERS
$users = getAllUsers();
$currentUserID = $_SESSION['user_id'];
$currentUserInfo = getUserById($currentUserID);

// HANDLE FORM ADD USER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addUser'])) {
  $fullName = $_POST['fullname'];
  $phoneNumber = $_POST['phoneNumber'];
  $address = $_POST['address'];
  $dateOfBirth = $_POST['dateOfBirth'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $roles = $_POST['role'];

  $result = addUser($fullName, $phoneNumber, $address, $dateOfBirth,$email, $password,  $roles);

  if ($result === "User added successfully.") {
    header("Location: ../admin/user_management.php");
    exit;
  } elseif ($result === "User with this username already exists.") {
    $addErrorMessage = "Username already exists.";
  } else {
    $addErrorMessage = "Failed to add user.";
  }
}

// HANDLE FORM DELETE USER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateUser'])) {
    $id = $_POST['editUserId'];
    $fullName = $_POST['editFullName'];
    $phoneNumber = $_POST['editPhoneNumber'];
    $address = $_POST['editAddress'];
    $dateOfBirth = $_POST['editDateOfBirth'];
    $email = $_POST['editEmail'];
    $password = $_POST['editPassword'];
    $roles = $_POST['editRole'];

    $success = updateUser($id, $fullName, $phoneNumber, $address, $dateOfBirth, $email, $password, $roles);
    if ($success) {
        $updateSuccessMessage = "User updated successfully.";
    } else {
        $errorMessage = "Failed to update user.";
    }
}

// HANDEL DELETE USER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteUser'])) {
    $id = $_POST['deleteUserId'];

    $success = deleteUser($id);
    if ($success) {
        $deleteSuccessMessage = "User deleted successfully.";
    } else {
        $errorMessage = "Failed to delete user.";
    }
}

// HANDLE SHOWING RECORDS PER PAGE
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['showRecords'])) {
  $limit = $_GET['showRecords'];
}
$limit = isset($_GET['showRecords']) ? $_GET['showRecords'] : 10;
$users = getUsersWithLimit($limit);

// HANDLE SEARCH USER
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
  $search = $_GET['search'];
  if (!empty($search)) {
    $users = getUsersWithSearch($search);
  }
}

// HANDLE SORTING USER
$startIndex = 1;
$showRecords = isset($_GET['showRecords']) ? intval($_GET['showRecords']) : 5;
$totalEntries = getTotalUserCount();
$statusEntries = getStatusEntries($startIndex, $showRecords);
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
$totalPages = 3;

// GENERATE PAGINATION
$paginationLinks = generatePaginationLinks($currentPage, $totalPages);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>San Luis Municipal Police Station</title>
    <!-- PAGE ICON -->
    <link rel="icon" type="image/x-icon" href="../dist/img/favicon.ico">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=fallback">
    <!-- Line Awesome Icons -->
    <link rel="stylesheet" href="../plugins/line-awesome-free/css/line-awesome.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../dist/css/dashboard.css">
    <!-- COSTUMIZE CSS -->
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

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4 " style="background-color:#0B2436;">
            <!-- Brand Logo -->
            <a href="../admin/dashboard.php" class="brand-link">
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
                    <div class="info text-warning">
                        <?php echo $currentUserInfo['username']; ?>!
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
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
                                <i class="las la-layer-group" id="icon"></i>
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
            </div>
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">User Management</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">User Management</li>
                            </ol>
                        </div>
                    </div>
                </div>
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


            <div class="col-sm-12">
                <!-- Success message for user update -->
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
                                    <!-- ADD NEW-->
                                    <div class="row mb-2">
                                        <div class="col-sm-12" style="margin-top: 15px;">
                                            <button type="button" class="btn btn-primary btn-sm float-left"
                                                data-toggle="modal" data-target="#addUserModal"><i
                                                    class="las la-plus-circle"></i> Add New User</button>
                                        </div>
                                        <!-- Add User Modal -->
                                        <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog"
                                            aria-labelledby="addUserModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="POST" action="">
                                                            <div class="form-group">
                                                                <label for="fullName">Full Name:</label>
                                                                <input type="text" class="form-control" id="fullName"
                                                                    name="fullName" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="phoneNumber">Phone Number:</label>
                                                                <input type="text" class="form-control" id="phoneNumber"
                                                                    name="phoneNumber" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="address">Addresss:</label>
                                                                <input type="text" class="form-control" id="address"
                                                                    name="address" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="dateOfBirth">Date of Birth:</label>
                                                                <input type="date" class="form-control" id="dateOfBirth"
                                                                    name="dateOfBirth" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="email">Email:</label>
                                                                <input type="email" class="form-control" id="email"
                                                                    placeholder="Enter email" name="email">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="password">Password:</label>
                                                                <input type="password" class="form-control"
                                                                    id="password" placeholder="Enter password"
                                                                    name="password">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="role">Role:</label>
                                                                <select class="form-control" id="role" name="role">
                                                                    <option value="admin">Admin</option>
                                                                    <option value="user">User</option>
                                                                    <option value="officer">Police Officer</option>
                                                                </select>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary"
                                                                    name="addUser">Save</button>
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
                                                        <span class="ml-2"><i class="las la-filter"></i>Show
                                                            &nbsp</span>
                                                        <select class="form-control form-control-sm" id="showRecords"
                                                            name="showRecords" style="width: 40px; text-align: center;"
                                                            onchange="this.form.submit()">
                                                            <option value="5" <?php if ($limit==5) echo 'selected' ; ?>
                                                                >5</option>
                                                            <option value="10" <?php if ($limit==10) echo 'selected' ;
                                                                ?>>10
                                                            </option>
                                                            <option value="20" <?php if ($limit==20) echo 'selected' ;
                                                                ?>>20
                                                            </option>
                                                            <option value="50" <?php if ($limit==50) echo 'selected' ;
                                                                ?>>50
                                                            </option>
                                                        </select>
                                                        <span class="ml-2">records per pages</span>
                                                    </label>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- SEARCH -->
                                        <div id="search" class="col-sm-9" style="margin-top: 15px;">
                                            <div class="form-inline justify-content-end">
                                                <form id="searchForm" method="GET" action="">
                                                    <div class="form-group mx-sm-3 mb-2">
                                                        <label for="searchInput" class="mr-2"><i
                                                                class="las la-search"></i> Search:</label>
                                                        <input type="text" class="form-control" id="searchInput"
                                                            name="search" style="max-width: 150px;"
                                                            value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <!-- TABLE-->
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Full Name</th>
                                                    <th>Phone Number</th>
                                                    <th>Address</th>
                                                    <th>Date Of Birth</th>
                                                    <th>Email</th>
                                                    <th>Password</th>
                                                    <th>Role</th>
                                                    <th style="text-align:center;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($users as $user): ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $user['fullName']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $user['phoneNumber']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $user['address']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $user['dateOfBirth']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $user['email']; ?>
                                                    </td>
                                                    <td>
                                                        <input type="password" value="<?php echo $user['password']; ?>"
                                                            readonly class="password-input"
                                                            id="password_<?php echo $user['id']; ?>">
                                                        <span class="toggle-password"
                                                            onclick="togglePasswordVisibility(event, <?php echo $user['id']; ?>)">
                                                            <i class="las la-eye"></i>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php echo $user['roles']; ?>
                                                    </td>
                                                    <td style="text-align:center;">
                                                        <button type="button" class="btn btn-success btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#editUserModal<?php echo $user['id']; ?>"><i
                                                                class="las la-edit"></i>
                                                            Update</button>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#deleteUserModal<?php echo $user['id']; ?>"><i
                                                                class="las la-trash-alt"></i>
                                                            Delete</button>
                                                    </td>
                                                </tr>
                                                <!-- Edit User Modal -->
                                                <div class="modal fade" id="editUserModal<?php echo $user['id']; ?>"
                                                    tabindex="-1" role="dialog"
                                                    aria-labelledby="editUserModalLabel<?php echo $user['id']; ?>"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="editUserModalLabel<?php echo $user['id']; ?>">
                                                                    Edit User</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form method="POST" action="">
                                                                    <input type="hidden" name="editUserId"
                                                                        value="<?php echo $user['id']; ?>">
                                                                    <div class="form-group">
                                                                        <label for="editFullName">Full Name</label>
                                                                        <input type="text" class="form-control"
                                                                            id="editFullName" name="editFullName"
                                                                            value="<?php echo $user['fullName']; ?>">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="editPhoneNumber">Phone
                                                                            Number</label>
                                                                        <input type="text" class="form-control"
                                                                            id="editPhoneNumber" name="editPhoneNumber"
                                                                            value="<?php echo $user['phoneNumber']; ?>">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="editAddress">Address</label>
                                                                        <input type="text" class="form-control"
                                                                            id="editAddress" name="editAddress"
                                                                            value="<?php echo $user['address']; ?>">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="editDateOfBirth">Date Of
                                                                            Birth</label>
                                                                        <input type="date" class="form-control"
                                                                            id="editDateOfBirth" name="editDateOfBirth"
                                                                            value="<?php echo $user['dateOfBirth']; ?>">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="editEmail">Email</label>
                                                                        <input type="email" class="form-control"
                                                                            id="editEmail" name="editEmail"
                                                                            value="<?php echo $user['email']; ?>">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="editPassword">Password</label>
                                                                        <input type="password" class="form-control"
                                                                            id="editPassword" name="editPassword"
                                                                            placeholder="Enter new password">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="editRole">Role</label>
                                                                        <select class="form-control" id="editRole"
                                                                            name="editRole">
                                                                            <option value="admin" <?php echo
                                                                                ($user['roles']==='admin' ) ? 'selected'
                                                                                : '' ; ?>
                                                                                >Admin</option>
                                                                            <option value="user" <?php echo
                                                                                ($user['roles']==='user' ) ? 'selected'
                                                                                : '' ; ?>
                                                                                >User
                                                                            </option>
                                                                            <option value="officer" <?php echo
                                                                                ($user['roles']==='officer' )
                                                                                ? 'selected' : '' ; ?>
                                                                                >Police Officer</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">Cancel</button>
                                                                        <button type="submit" class="btn btn-primary"
                                                                            name="updateUser">Save</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Delete User Modal -->
                                                <div class="modal fade" id="deleteUserModal<?php echo $user['id']; ?>"
                                                    tabindex="-1" role="dialog"
                                                    aria-labelledby="deleteUserModalLabel<?php echo $user['id']; ?>"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="deleteUserModalLabel<?php echo $user['id']; ?>">
                                                                    Delete User
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to delete this user?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Cancel</button>
                                                                <form method="POST" action="">
                                                                    <input type="hidden" name="deleteUserId"
                                                                        value="<?php echo $user['id']; ?>">
                                                                    <button type="submit" class="btn btn-danger"
                                                                        name="deleteUser">Delete</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row mb-2">
                                        <!-- showing status entries-->
                                        <!-- showing status entries -->
                                        <div class="col-sm-6">
                                            <p style="font-size:14px;">
                                                <i class="las la-database"></i> Showing
                                                <?php echo $startIndex; ?> to
                                                <?php echo min($startIndex + $showRecords - 1, $totalEntries); ?>
                                                of
                                                <?php echo $totalEntries; ?> entries
                                            </p>
                                        </div>

                                        <!-- pagination -->
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
                    </div>
                </div>
            </div>
        </div>

        <footer class="main-footer text-center">
            <strong>Copyright &copy; 2023. BROSOTO DEV </strong> All rights reserved.
        </footer>
    </div>

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