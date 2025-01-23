<?php
include '../config/dataBaseConnect.php';
include './pagination3.php';  // Include the pagination logic

session_start();
if (!isset($_SESSION['email']) && !isset($_SESSION['password'])) {
    echo "<script> alert('Please login first') </script>";
    header("Location: login.php");
    exit;
}

// Get the pagination results
$pagination_data = pagination($connection);
$result = $pagination_data['result'];
$total_pages = $pagination_data['total_pages'];
$page = $pagination_data['current_page'];
$sort_column = $pagination_data['sort_column'];
$sort_order = $pagination_data['sort_order'];
$country_filter = $pagination_data['country_filter'];
$state_filter = $pagination_data['state_filter'];
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

    <!-- Filter Form -->
    <form action="" method="get" class="mb-4">
        <div class="row">
            <div class="col-md-3">
            <select name="country_filter" id="country_filter" class="form-control">
    <option value="">Filter by Country</option>
    <?php
    $countries = $connection->query("SELECT DISTINCT country FROM `users`");
    while ($row = $countries->fetch_assoc()) {
        $selected = $country_filter == $row['country'] ? 'selected' : '';
        echo "<option value='{$row['country']}' $selected>{$row['country']}</option>";
    }
    ?>
</select>

            </div>
            <div class="col-md-3">
                <select name="state_filter" id="state_filter" class="form-control">
                    <option value="">Filter by State</option>
                    <?php
                    if (!empty($country_filter)) {
                        $states = $connection->query("SELECT DISTINCT state FROM `users` WHERE country = '$country_filter'");
                        while ($row = $states->fetch_assoc()) {
                            $selected = $state_filter == $row['state'] ? 'selected' : '';
                            echo "<option value='{$row['state']}' $selected>{$row['state']}</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Apply Filter</button>
                <a href="dashboard.php" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="table-wrapper">
        <table class="fl-table">
            <thead>
                <tr>
                    <th><a href="?sort_column=id&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">#</a></th>
                    <th><a href="?sort_column=first_name&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">First Name</a></th>
                    <th><a href="?sort_column=last_name&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Last Name</a></th>
                    <th><a href="?sort_column=email&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Email</a></th>
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
                echo '<li class="page-item"><a class="page-link" href="?page=' . ($page - 1) . '">Previous</a></li>';
            }
            
            // Page Links
            for ($i = 1; $i <= $total_pages; $i++) {
                echo '<li class="page-item"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
            }
            
            // Next Page Link
            if ($page < $total_pages) {
                echo '<li class="page-item"><a class="page-link" href="?page=' . ($page + 1) . '">Next</a></li>';
            }
            ?>
        </ul>
    </nav>

</body>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function() {
    // When country dropdown changes
    $('#country_filter').change(function() {
        var country = $(this).val(); // Get the selected country
        
        // Send AJAX request to get states based on selected country
        $.ajax({
            url: 'getStates.php',
            method: 'GET',
            data: {country: country},  // Send country to getStates.php
            success: function(response) {
                // Update the state filter dropdown with the received states
                $('#state_filter').html(response);
            }
        });
    });
});

</script>
        

</html>
