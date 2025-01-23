<?php
include '../config/dataBaseConnect.php';
include './pagination1.php';  // Include the pagination logic

session_start();
if (!isset($_SESSION['email']) && !isset($_SESSION['password'])) {
    echo "<script> alert('Please login first') </script>";
    header("Location: login.php");
    exit;
}

// Get the pagination results
$pagination_data = pagination($connection);  // Call the pagination function
$result = $pagination_data['result'];  // Get the paginated results
$total_pages = $pagination_data['total_pages'];  // Get the total pages
$searchResult = $pagination_data['search'];  // Get the search query
$page = $pagination_data['current_page'];  // Get the current page number
$sort_column = $pagination_data['sort_column'];  // Get the sort column
$sort_order = $pagination_data['sort_order'];  // Get the sort order

// Determine the next sort order
$next_sort_order = $sort_order === 'ASC' ? 'DESC' : 'ASC';
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
                    <th><a href="?sort_column=id&sort_order=<?php echo $next_sort_order; ?>&search=<?php echo $searchResult; ?>">ID</a></th>
                    <th><a href="?sort_column=first_name&sort_order=<?php echo $next_sort_order; ?>&search=<?php echo $searchResult; ?>">First Name</a></th>
                    <th><a href="?sort_column=last_name&sort_order=<?php echo $next_sort_order; ?>&search=<?php echo $searchResult; ?>">Last Name</a></th>
                    <th><a href="?sort_column=email&sort_order=<?php echo $next_sort_order; ?>&search=<?php echo $searchResult; ?>">Email</a></th>
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
                if ($result->num_rows > 0) {
                    while ($rows = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?php echo $rows['id']; ?></td>
                            <td><?php echo $rows['first_name']; ?></td>
                            <td><?php echo $rows['last_name']; ?></td>
                            <td><?php echo $rows['email']; ?></td>
                            <td><?php echo $rows['phone_no']; ?></td>
                            <td><?php echo $rows['address']; ?></td>
                            <td><?php echo $rows['country']; ?></td>
                            <td><?php echo $rows['state']; ?></td>
                            <td><?php echo $rows['pincode']; ?></td>
                            <td>
                                <a href="./editUser.php?id=<?php echo $rows['id']; ?>"><button type="button" class="btn btn-outline-warning">Edit</button></a>
                                <a href="./deleteUser.php?id=<?php echo $rows['id']; ?>"><button type="button" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this record?')">Delete</button></a>
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
       
