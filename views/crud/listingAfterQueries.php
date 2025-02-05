<?php
include '../config/dataBaseConnect.php';
include '../includes/queries.php';  // Include the queries file

// Get search, filter, sort, and page parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$countryFilter = isset($_GET['countryFilter']) ? $_GET['countryFilter'] : '';
$stateFilter = isset($_GET['stateFilter']) ? $_GET['stateFilter'] : '';
$sortColumn = isset($_GET['sortColumn']) ? $_GET['sortColumn'] : 'id';
$sortOrder = isset($_GET['sortOrder']) && $_GET['sortOrder'] === 'DESC' ? 'DESC' : 'ASC';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$recordsPerPage = 5;

// Get the query to list users
$sql = listUsersQuery($search, $countryFilter, $stateFilter, $sortColumn, $sortOrder, $page, $recordsPerPage, $connection);
$result = $connection->query($sql);

// Get total count for pagination
$countSql = countUsersQuery($search, $countryFilter, $stateFilter, $connection);
$countResult = $connection->query($countSql);
$countRow = $countResult->fetch_assoc();
$totalRecords = $countRow['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// Display the results
?>

    