<?php
include '../config/dataBaseConnect.php';

session_start();
if (!isset($_SESSION['email']) && !isset($_SESSION['password'])) {
    echo "<script>alert('Please login first');</script>";
    header("Location: login.php");
    exit;
}

// Get filter values from GET parameters
$search = $_GET['search'] ?? '';
$filterCountry = $_GET['country'] ?? '';
$filterState = $_GET['state'] ?? '';

// Base query
$sql = "SELECT * FROM `users` WHERE 1=1";

// Add search filter
if (!empty($search)) {
    $sql .= " AND CONCAT(first_name, last_name, email) LIKE ?";
}

// Add country filter
if (!empty($filterCountry)) {
    $sql .= " AND country = ?";
}

// Add state filter
if (!empty($filterState)) {
    $sql .= " AND state = ?";
}

$sql .= " ORDER BY `id` DESC";

// Prepare and bind parameters
$stmt = $connection->prepare($sql);
$params = [];
if (!empty($search)) $params[] = '%' . $search . '%';
if (!empty($filterCountry)) $params[] = $filterCountry;
if (!empty($filterState)) $params[] = $filterState;

// Dynamically bind parameters
if ($params) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

// Fetch unique countries and states for filters
$countries = $connection->query("SELECT DISTINCT country FROM `users`")->fetch_all(MYSQLI_ASSOC);
$states = $connection->query("SELECT DISTINCT state FROM `users`")->fetch_all(MYSQLI_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard with Filters</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
</head>
<body>
    <h1>Dashboard</h1>

    <nav class="navbar navbar-light bg-light">
        <form class="form-inline" method="get">
            <input class="form-control mr-sm-2" type="search" placeholder="Search" name="search" value="<?= htmlspecialchars($search) ?>">
            <select class="form-control mr-sm-2" name="country">
                <option value="">All Countries</option>
                <?php foreach ($countries as $country): ?>
                    <option value="<?= htmlspecialchars($country['country']) ?>" <?= $country['country'] === $filterCountry ? 'selected' : '' ?>>
                        <?= htmlspecialchars($country['country']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select class="form-control mr-sm-2" name="state">
                <option value="">All States</option>
                <?php foreach ($states as $state): ?>
                    <option value="<?= htmlspecialchars($state['state']) ?>" <?= $state['state'] === $filterState ? 'selected' : '' ?>>
                        <?= htmlspecialchars($state['state']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-outline-success" type="submit">Filter</button>
        </form>
    </nav>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone NO.</th>
                    <th>Address</th>
                    <th>Country</th>
                    <th>State</th>
                    <th>Pincode</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['first_name']) ?></td>
                            <td><?= htmlspecialchars($row['last_name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone_no']) ?></td>
                            <td><?= htmlspecialchars($row['address']) ?></td>
                            <td><?= htmlspecialchars($row['country']) ?></td>
                            <td><?= htmlspecialchars($row['state']) ?></td>
                            <td><?= htmlspecialchars($row['pincode']) ?></td>
                            <td>
                                <a href="./editUser.php?id=<?= $row['id'] ?>"><button class="btn btn-warning">Edit</button></a>
                                <a href="./deleteUser.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">
                                    <button class="btn btn-danger">Delete</button>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10">No Records Found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
</html>
