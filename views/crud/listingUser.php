<?php
include '../config/dataBaseConnect.php';
// include './crud/pagination.php' ;

function listUser($connection) {
    $recordsPerPage = 5;

    // Get the current page, filters, and search query
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $startFrom = ($page - 1) * $recordsPerPage;

    // Get search query and filter values
    $searchResult = isset($_GET['search']) ? $connection->real_escape_string($_GET['search']) : '';
    $countryFilter = isset($_GET['countryFilter']) ? $connection->real_escape_string($_GET['countryFilter']) : '';
    $stateFilter = isset($_GET['stateFilter']) ? $connection->real_escape_string($_GET['stateFilter']) : '';

    // echo $_GET['countryFilter'] ;

    // Sorting values
    $sortColumn = isset($_GET['sortColumn']) ? $_GET['sortColumn'] : 'id';
    $sortOrder = isset($_GET['sortOrder']) && $_GET['sortOrder'] === 'DESC' ? 'DESC' : 'ASC';

    // Allowed columns for sorting
    $allowedColumns = ['id', 'first_name', 'last_name', 'email'];
    $sortColumn = in_array($sortColumn, $allowedColumns) ? $sortColumn : 'id';

    // Build the WHERE clause
    $whereClause = "1=1"; // Default condition for flexibility

    // Apply the filters and search query
    if (!empty($searchResult)) {
        $whereClause .= " AND CONCAT(first_name, ' ', last_name, email) LIKE '%$searchResult%'";
    }
    if (!empty($countryFilter)) {
        $whereClause .= " AND c.name LIKE '$countryFilter'";
    }
    if (!empty($stateFilter)) {
        $whereClause .= " AND s.name LIKE '$stateFilter'";
    }

    $sql = "SELECT u.*, c.name AS country, s.name AS state
        FROM users u
        LEFT JOIN countries c ON u.country_id = c.id
        LEFT JOIN states s ON u.state_id = s.id
        WHERE $whereClause 
        ORDER BY $sortColumn $sortOrder 
        LIMIT $startFrom, $recordsPerPage";

    $result = $connection->query($sql);

    if (!$result) {
        die("SQL Query Error: " . $connection->error . " - Query: " . $sql);
    }

    // Count query for pagination
    $countSql = "SELECT COUNT(*) AS total FROM `users` u
                 LEFT JOIN countries c ON u.country_id = c.id
                 LEFT JOIN states s ON u.state_id = s.id
                 WHERE $whereClause";
    $countResult = $connection->query($countSql);

    if (!$countResult) {
        die("Count Query Error: " . $connection->error . " - Query: " . $countSql);
    }

    $countRow = $countResult->fetch_assoc();
    $totalRecords = $countRow['total'];
    $totalPages = ceil($totalRecords / $recordsPerPage);

    // Return result and filter values
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
