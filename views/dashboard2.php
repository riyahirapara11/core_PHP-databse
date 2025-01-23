<?php
include '../config/dataBaseConnect.php';
include './pagination2.php';  // Include the pagination logic

session_start();
if (!isset($_SESSION['email']) && !isset($_SESSION['password'])) {
    echo "<script> alert('Please login first') </script>";
    header("Location: login.php");
    exit;
}

// Get the pagination results
$pagination_data = pagination($connection);  // Call the pagination function
$rows = $pagination_data['result'];  // Get the paginated results
$total_pages = $pagination_data['total_pages'];  // Get the total pages
$searchResult = $pagination_data['search'];  // Get the search query
$page = $pagination_data['current_page'];  // Get the current page number

// Get sorting parameters from URL (default to id ASC)
$sort_column = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'id';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';

// Validate sort column and sort order
$allowed_columns = ['id', 'first_name', 'last_name', 'email'];
$allowed_orders = ['ASC', 'DESC'];
$sort_column = in_array($sort_column, $allowed_columns) ? $sort_column : 'id';
$sort_order = in_array($sort_order, $allowed_orders) ? $sort_order : 'ASC';

// Sort the rows array based on the selected column and order
if (!empty($rows)) {
    usort($rows, function ($a, $b) use ($sort_column, $sort_order) {
        if ($sort_order === 'ASC') {
            return strcmp($a[$sort_column], $b[$sort_column]);
        } else {
            return strcmp($b[$sort_column], $a[$sort_column]);
        }
    });
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./dashboardStyle.css">
</head>

<body>
    <h1>Dashboard</h1>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Navbar</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="#">About</a></li>
                    <li class="nav-item"><a class="nav-link active" href="#">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <a href="./logout.php"><button>Logout</button></a>

    <!-- Messages -->
    <div>
        <?php
        if (isset($_SESSION["edit_message"])) {
            echo '<div class="alert alert-success">Record Updated Successfully!</div>';
            unset($_SESSION["edit_message"]);
        } elseif (isset($_SESSION["delete_message"])) {
            echo '<div class="alert alert-success">Record Deleted Successfully!</div>';
            unset($_SESSION["delete_message"]);
        } elseif (isset($_SESSION["add_message"])) {
            echo '<div class="alert alert-success">User Added Successfully!</div>';
            unset($_SESSION["add_message"]);
        }
        ?>
    </div>

    <!-- Search & Add User -->
    <nav class="navbar navbar-light bg-light">
        <div class="container-fluid">
            <form class="d-flex" action="" method="get">
                <input class="form-control me-2" type="search" placeholder="Search" name="search" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
            <a href="./addUser.php"><button>+ Add User</button></a>
        </div>
    </nav>

    <!-- Table -->
    <div class="table-wrapper">
        <table class="fl-table">
            <thead>
                <tr>
                    <th>
                        <a href="?page=<?php echo $page; ?>&search=<?php echo $searchResult; ?>&sort_column=id&sort_order=<?php echo ($sort_column === 'id' && $sort_order === 'ASC') ? 'DESC' : 'ASC'; ?>">
                            # <?php echo ($sort_column === 'id') ? ($sort_order === 'ASC' ? '↑' : '↓') : ''; ?>
                        </a>
                    </th>
                    <th>
                        <a href="?page=<?php echo $page; ?>&search=<?php echo $searchResult; ?>&sort_column=first_name&sort_order=<?php echo ($sort_column === 'first_name' && $sort_order === 'ASC') ? 'DESC' : 'ASC'; ?>">
                            First Name <?php echo ($sort_column === 'first_name') ? ($sort_order === 'ASC' ? '↑' : '↓') : ''; ?>
                        </a>
                    </th>
                    <th>
                        <a href="?page=<?php echo $page; ?>&search=<?php echo $searchResult; ?>&sort_column=last_name&sort_order=<?php echo ($sort_column === 'last_name' && $sort_order === 'ASC') ? 'DESC' : 'ASC'; ?>">
                            Last Name <?php echo ($sort_column === 'last_name') ? ($sort_order === 'ASC' ? '↑' : '↓') : ''; ?>
                        </a>
                    </th>
                    <th>
                        <a href="?page=<?php echo $page; ?>&search=<?php echo $searchResult; ?>&sort_column=email&sort_order=<?php echo ($sort_column === 'email' && $sort_order === 'ASC') ? 'DESC' : 'ASC'; ?>">
                            Email <?php echo ($sort_column === 'email') ? ($sort_order === 'ASC' ? '↑' : '↓') : ''; ?>
                        </a>
                    </th>
                    <th>Phone NO.</th>
                    <th>Address</th>
                    <th>Country</th>
                    <th>State</th>
                    <th>Pincode</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($rows)) {
                    foreach ($rows as $row) {
                        ?>
                        <tr>
                            <td><?php echo $row['row_number']; ?></td>
                            <td><?php echo $row['first_name']; ?></td>
                            <td><?php echo $row['last_name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['phone_no']; ?></td>
                            <td><?php echo $row['address']; ?></td>
                            <td><?php echo $row['country']; ?></td>
                            <td><?php echo $row['state']; ?></td>
                            <td><?php echo $row['pincode']; ?></td>
                            <td>
                                <a href="./editUser.php?id=<?php echo $row['id']; ?>"><button type="button" class="btn btn-outline-warning">Edit</button></a>
                                <a href="./deleteUser.php?id=<?php echo $row['id']; ?>"><button type="button" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this record?')">Delete</button></a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="10">No Record Found</td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <nav>
        <ul class="pagination">
            <?php
            // Previous Page Link
            if ($page > 1) {
                echo '<li class="page-item"><a class="page-link" href="?page=' . ($page - 1) . '&search=' . $searchResult . '&sort_column=' . $sort_column . '&sort_order=' . $sort_order . '">Previous</a></li>';
            }

            // Page Links
            for ($i = 1; $i <= $total_pages; $i++) {
                echo '<li class="page-item"><a class="page-link" href="?page=' . $i . '&search=' . $searchResult . '&sort_column=' . $sort_column . '&sort_order=' . $sort_order . '">' . $i . '</a></li>';
            }

            // Next Page Link
            if ($page < $total_pages) {
                echo '<li class="page-item"><a class="page-link" href="?page=' . ($page + 1) . '&search=' . $searchResult . '&sort_column=' . $sort_column . '&sort_order=' . $sort_order . '">Next</a></li>';
            }
            ?>
        </ul>
    </nav>

</body>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>

</html>
