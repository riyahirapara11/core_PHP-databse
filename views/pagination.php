<?php

function listUser($connection) {
    $recordsPerPage = 5;

    // Get the current page, filters, and search query
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $startFrom = ($page - 1) * $recordsPerPage;

    $searchResult = isset($_GET['search']) ? $connection->real_escape_string($_GET['search']) : '';
    $sortColumn = isset($_GET['sortColumn']) ? $_GET['sortColumn'] : 'id';
    $sortOrder = isset($_GET['sortOrder']) && $_GET['sortOrder'] === 'DESC' ? 'DESC' : 'ASC';
    $countryFilter = isset($_GET['countryFilter']) ? $connection->real_escape_string($_GET['countryFilter']) : '';
    $stateFilter = isset($_GET['stateFilter']) ? $connection->real_escape_string($_GET['stateFilter']) : '';

    // Allowed columns for sorting
    $allowedColumns = ['id', 'first_name', 'last_name', 'email'];
    $sortColumn = in_array($sortColumn, $allowedColumns) ? $sortColumn : 'id';

    // Build the WHERE clause
    $whereClause = "1=1"; // Default condition for flexibility
    if (!empty($searchResult)) {
        $whereClause .= " AND CONCAT(first_name, ' ', last_name, email) LIKE '%$searchResult%'";
    }
    if (!empty($countryFilter)) {
        $whereClause .= " AND country = '$countryFilter'";
    }
    if (!empty($stateFilter)) {
        $whereClause .= " AND state = '$stateFilter'";
    }

    // Main query with filters, search, sorting, and pagination
    $sql = "SELECT * FROM `users` WHERE $whereClause 
            ORDER BY $sortColumn $sortOrder 
            LIMIT $startFrom, $recordsPerPage";
    $result = $connection->query($sql);

    if (!$result) {
        die("SQL Query Error: " . $connection->error . " - Query: " . $sql);
    }

    // Count query for pagination
    $countSql = "SELECT COUNT(*) AS total FROM `users` WHERE $whereClause";
    $countResult = $connection->query($countSql);

    if (!$countResult) {
        die("Count Query Error: " . $connection->error . " - Query: " . $countSql);
    }

    $countRow = $countResult->fetch_assoc();
    $totalRecords = $countRow['total'];
    $totalPages = ceil($totalRecords / $recordsPerPage);

    return [
        'result' => $result,
        'totalPages' => $totalPages,
        'search' => $searchResult,
        'currentPage' => $page,
        'sortColumn' => $sortColumn,
        'sortOrder' => $sortOrder,
        'countryFilter' => $countryFilter,
        'stateFilter' => $stateFilter,
    ];
}
?>