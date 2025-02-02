<?php
include '../config/dataBaseConnect.php';

/**
 * Generates the SQL query for the search condition.
 * @param string $search The search string input by the user.
 * @return string The SQL WHERE clause for the search.
 */
function getSearchQuery($search) {
    return !empty($search) ? " AND CONCAT(first_name, ' ', last_name, email) LIKE ?" : '';
}

/**
 * Generates the SQL query for a filter condition (either country or state).
 * @param string $filter The filter value (e.g., country or state).
 * @param string $column The column to filter by (e.g., country name or state name).
 * @return string The SQL WHERE clause for the filter.
 */
function getFilterQuery($filter, $column) {
    return !empty($filter) ? " AND $column LIKE ?" : '';
}

/**
 * Generates the SQL query for sorting the results.
 * @param string $sortColumn The column to sort by.
 * @param string $sortOrder The order to sort (ASC or DESC).
 * @return string The SQL ORDER BY clause for sorting.
 */
function getSortQuery($sortColumn, $sortOrder) {
    $allowedColumns = ['id', 'first_name', 'last_name', 'email'];
    $sortColumn = in_array($sortColumn, $allowedColumns) ? $sortColumn : 'id';
    return " ORDER BY $sortColumn $sortOrder";
}

/**
 * Generates the SQL query for pagination.
 * @param int $page The current page number.
 * @param int $recordsPerPage The number of records per page.
 * @return string The SQL LIMIT clause for pagination.
 */
function getPaginationQuery($page, $recordsPerPage) {
    $startFrom = ($page - 1) * $recordsPerPage;
    return " LIMIT ?, ?";
}

/**
 * List users with search, filters, sorting, and pagination.
 * @param mysqli $connection The database connection.
 * @return array The result data with pagination and filter information.
 */
function listUser($connection) {
    $recordsPerPage = 5;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $countryFilter = isset($_GET['countryFilter']) ? $_GET['countryFilter'] : '';
    $stateFilter = isset($_GET['stateFilter']) ? $_GET['stateFilter'] : '';
    $sortColumn = isset($_GET['sortColumn']) ? $_GET['sortColumn'] : 'id';
    $sortOrder = isset($_GET['sortOrder']) && $_GET['sortOrder'] === 'DESC' ? 'DESC' : 'ASC';

    // Build WHERE clause using search and filters
    $whereClause = "1=1" . 
        getSearchQuery($search) . 
        getFilterQuery($countryFilter, 'c.name') . 
        getFilterQuery($stateFilter, 's.name');

    // Prepare the SQL query for retrieving user data with filters, sorting, and pagination
    $sql = "SELECT u.*, c.name AS country, s.name AS state
            FROM users u
            LEFT JOIN countries c ON u.country_id = c.id
            LEFT JOIN states s ON u.state_id = s.id
            WHERE $whereClause" . 
            getSortQuery($sortColumn, $sortOrder) . 
            getPaginationQuery($page, $recordsPerPage);

    $stmt = $connection->prepare($sql);

    // Bind parameters for search, filters, and pagination
    $paramTypes = '';
    $params = [];

    if (!empty($search)) {
        $paramTypes .= 's';
        $params[] = "%$search%";
    }
    if (!empty($countryFilter)) {
        $paramTypes .= 's';
        $params[] = "%$countryFilter%";
    }
    if (!empty($stateFilter)) {
        $paramTypes .= 's';
        $params[] = "%$stateFilter%";
    }
    // Add pagination parameters
    $params[] = ($page - 1) * $recordsPerPage;
    $params[] = $recordsPerPage;

    // Bind parameters dynamically
    $stmt->bind_param($paramTypes, ...$params);

    // Execute query
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        die("SQL Query Error: " . $connection->error . " - Query: " . $sql);
    }

    // Count query for pagination
    $countSql = "SELECT COUNT(*) AS total 
                 FROM users u
                 LEFT JOIN countries c ON u.country_id = c.id
                 LEFT JOIN states s ON u.state_id = s.id
                 WHERE $whereClause";

    $countStmt = $connection->prepare($countSql);
    
    // Bind parameters for search and filters in count query
    $countStmt->bind_param($paramTypes, ...$params);
    
    // Execute count query
    $countStmt->execute();
    $countResult = $countStmt->get_result();

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
        'search' => $search,
        'currentPage' => $page,
        'sortColumn' => $sortColumn,
        'sortOrder' => $sortOrder,
        'countryFilter' => $countryFilter,
        'stateFilter' => $stateFilter,
    ];
}
?>
