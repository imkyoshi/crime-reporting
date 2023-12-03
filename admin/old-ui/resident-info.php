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
require_once '../includes/functions.php';


// Retrieve all users from the database
$users = getAllUsers();
$currentUserID = $_SESSION['user_id'];
$currentUserInfo = getUserById($currentUserID);

// Handle form submission for adding a user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addUser'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $roles = $_POST['role'];

    $success = addUser($username, $password, $email, $roles);
    if ($success) {
        header("Location: ../admin/user_management.php");
        exit;
    } else {
        $errorMessage = "Failed to add user.";
    }
}

// Handle form submission for updating a user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateUser'])) {
    $id = $_POST['editUserId'];
    $username = $_POST['editUsername'];
    $password = $_POST['editPassword'];
    $email = $_POST['editEmail'];
    $role = $_POST['editRole'];

    $success = updateUser($id, $username, $password, $email, $role);
    if ($success) {
        $updateSuccessMessage = "User updated successfully.";
    } else {
        $errorMessage = "Failed to update user.";
    }
}


// Handle form submission for deleting a user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteUser'])) {
    $id = $_POST['deleteUserId'];

    $success = deleteUser($id);
    if ($success) {
        $deleteSuccessMessage = "User deleted successfully.";
    } else {
        $errorMessage = "Failed to delete user.";
    }
}



/// Handle form submission for showing records per page
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['showRecords'])) {
    $limit = $_GET['showRecords'];
}

// Retrieve the selected limit from the URL parameters or use a default value
$limit = isset($_GET['showRecords']) ? $_GET['showRecords'] : 10;

// Retrieve users with the desired limit
$users = getUsersWithLimit($limit);


// Handle form submission for searching users
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search = $_GET['search'];

    // If search query is not empty, retrieve users with search filter
    if (!empty($search)) {
        $users = getUsersWithSearch($search);
    }
}

// Handle form submission for sorting users
$startIndex = 1; // Start index of the current page's entries
$showRecords = isset($_GET['showRecords']) ? intval($_GET['showRecords']) : 5;
$totalEntries = getTotalUserCount();
$statusEntries = getStatusEntries($startIndex, $showRecords);

// Handle pagination
// Determine the current page
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the total number of pages
$totalPages = 3; // Set the total number of pages as 3

// Generate the pagination links
$paginationLinks = generatePaginationLinks($currentPage, $totalPages);


?>
<!DOCTYPE html>
<html>

<head>
    <title>Crime Reporting System</title>
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Line Awesomee CSS -->
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>

    </style>
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
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <button type="button" id="sidebarCollapse" class="btn btn-info">
                    <i class="las la-bars"></i>
                </button>
                <div class="container-fluid">
                    <div class="navbar-brand">Crime Reporting System</div>
                </div>
            </nav>

             <div class="content-wrapper">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../admin/dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">User Management</li>
                    </ol>
                </nav>



                <div class="col-sm-12">
                    <!-- Success message for user update -->
                    <?php if (isset($updateSuccessMessage)): ?>
                                    <div class="alert alert-success">
                                        <?php echo $updateSuccessMessage; ?>
                                    </div>
                    <?php endif; ?>
                </div>

                <div class="card shadow">
                    <div class="card-body">
                        <h3>User Management</h3>
                        <!-- ADD NEW-->
                        <div class="row mb-2">
                            <div class="col-sm-12" style="margin-top: 15px;">
                                <button type="button" class="btn btn-primary btn-sm float-left" data-toggle="modal"
                                    data-target="#addUserModal"><i class="las la-plus-circle"></i>  Add New</button>
                            </div>

                            <!-- Add User Modal -->
                            <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog"
                                aria-labelledby="addUserModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="">
                                                <div class="form-group">
                                                    <label for="username">Username</label>
                                                    <input type="text" class="form-control" id="username"
                                                        placeholder="Enter username" name="username">
                                                </div>
                                                <div class="form-group">
                                                    <label for="password">Password</label>
                                                    <input type="password" class="form-control" id="password"
                                                        placeholder="Enter password" name="password">
                                                </div>
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control" id="email"
                                                        placeholder="Enter email" name="email">
                                                </div>
                                                <div class="form-group">
                                                    <label for="role">Role</label>
                                                    <select class="form-control" id="role" name="role">
                                                        <option value="admin">Admin</option>
                                                        <option value="user">User</option>
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
                                            <select class="form-control form-control-sm" id="showRecords"
                                                name="showRecords" style="width: 120px;">
                                                <option value="5" <?php if (
                                                    isset($_GET['showRecords']) &&
                                                    $_GET['showRecords'] == '5'
                                                )
                                                    echo 'selected'; ?>>5</option>
                                                <option value="10" <?php if (
                                                    isset($_GET['showRecords']) &&
                                                    $_GET['showRecords'] == '10'
                                                )
                                                    echo 'selected'; ?>>10</option>
                                                <option value="50" <?php if (
                                                    isset($_GET['showRecords']) &&
                                                    $_GET['showRecords'] == '50'
                                                )
                                                    echo 'selected'; ?>>50</option>
                                                <option value="100" <?php if (
                                                    isset($_GET['showRecords']) &&
                                                    $_GET['showRecords'] == '100'
                                                )
                                                    echo 'selected'; ?>>100</option>
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

                            <!-- TABLE-->

                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Password</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th style="text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo $user['username']; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $user['password']; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $user['email']; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $user['roles']; ?>
                                                        </td>
                                                        <td style="text-align:center;">
                                                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                                                data-target="#editUserModal<?php echo $user['id']; ?>"><i class="las la-edit"></i> Update</button>
                                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                                                data-target="#deleteUserModal<?php echo $user['id']; ?>"><i class="las la-trash-alt"></i>  Delete</button>
                                                        </td>

                                                    </tr>

                                                    <!-- Edit User Modal -->
                                                    <div class="modal fade" id="editUserModal<?php echo $user['id']; ?>" tabindex="-1"
                                                        role="dialog" aria-labelledby="editUserModalLabel<?php echo $user['id']; ?>"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="editUserModalLabel<?php echo $user['id']; ?>">Edit User</h5>
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
                                                                            <label for="editUsername">Username</label>
                                                                            <input type="text" class="form-control" id="editUsername"
                                                                                name="editUsername"
                                                                                value="<?php echo $user['username']; ?>">
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="editPassword">Password</label>
                                                                            <input type="password" class="form-control"
                                                                                id="editPassword" name="editPassword"
                                                                                placeholder="Enter new password">
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="editEmail">Email</label>
                                                                            <input type="email" class="form-control" id="editEmail"
                                                                                name="editEmail" value="<?php echo $user['email']; ?>">
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="editRole">Role</label>
                                                                            <select class="form-control" id="editRole" name="editRole">
                                                                                <option value="admin" <?php echo
                                                                                    ($user['roles'] === 'admin') ? 'selected' : ''; ?>
                                                                                    >Admin</option>
                                                                                <option value="user" <?php echo ($user['roles'] === 'user'
                                                                                ) ? 'selected' : ''; ?>>User</option>
                                                                                <option value="officer" <?php echo ($user['roles'] === 'officer'
                                                                                ) ? 'selected' : ''; ?>>Police Officer</option>
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
                                                        <div class="modal fade" id="deleteUserModal<?php echo $user['id']; ?>" tabindex="-1"
                                                        role="dialog" aria-labelledby="deleteUserModalLabel<?php echo $user['id']; ?>"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="deleteUserModalLabel<?php echo $user['id']; ?>">Delete User
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
        <i class="las la-database"></i> Showing <?php echo $startIndex; ?> to <?php echo min($startIndex + $showRecords - 1, $totalEntries); ?>
        of <?php echo $totalEntries; ?> entries
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

<div class="card-footer  justify-content-center">
    <div class="row mb-2">
        <div class="col-sm-12 text-center">
            <p style="font-size: 14px;">
                © 2023 BROSOTO STUDIO
            </p>
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