<?php

/**
 * Handles pagination logic for fetching and counting records from the database.
 *
 * @param mysqli $connection The database connection object.
 * @return array An array containing paginated results, total pages, and other query parameters.
 */
function pagination($connection) {
    // Define how many records to show per page
    $recordsPerPage = 5;

    // Get pagination, search, sorting, and filter parameters from the request
    $page = getCurrentPage(); // Get current page number
    $startFrom = calculateOffset($page, $recordsPerPage); // Calculate offset for SQL query

    $searchResult = getRequestParam('search', ''); // Search term
    $sort_column = getSortColumn(['id', 'first_name', 'last_name', 'email'], 'id'); // Sorting column
    $sort_order = getSortOrder('ASC'); // Sorting order (ASC/DESC)

    $country_filter = getRequestParam('country_filter', ''); // Country filter
    $state_filter = getRequestParam('state_filter', ''); // State filter

    // Build the WHERE clause dynamically
    $whereClause = buildWhereClause($searchResult, $country_filter, $state_filter);

    // Query to fetch paginated records
    $sql = buildSelectQuery($whereClause, $sort_column, $sort_order, $startFrom, $recordsPerPage);
    $result = executeQuery($connection, $sql); // Execute query for paginated data

    // Query to count total records for pagination
    $total_records = getTotalRecords($connection, $whereClause); // Get total record count
    $total_pages = calculateTotalPages($total_records, $recordsPerPage); // Calculate total pages

    // Return data in an array
    return [
        'result' => $result,
        'total_pages' => $total_pages,
        'search' => $searchResult,
        'current_page' => $page,
        'sort_column' => $sort_column,
        'sort_order' => $sort_order,
        'country_filter' => $country_filter,
        'state_filter' => $state_filter,
    ];
}

/**
 * Get the current page number from the request.
 *
 * @return int The current page number or 1 if not set.
 */
function getCurrentPage() {
    return isset($_GET['page']) ?  ($_GET['page']) : 1;
}

/**
 * Calculate the SQL offset for pagination.
 *
 * @param int $page The current page number.
 * @param int $recordsPerPage The number of records to display per page.
 * @return int The starting record index for the current page.
 */
function calculateOffset($page, $recordsPerPage) {
    return ($page - 1) * $recordsPerPage;
}

/**
 * Get a specific request parameter with a default value.
 *
 * @param string $key The key to fetch from the request.
 * @param mixed $default The default value if the key is not set.
 * @return mixed The value from the request or the default value.
 */
function getRequestParam($key, $default = '') {
    return isset($_GET[$key]) ? $_GET[$key] : $default;
}

/**
 * Validate and fetch the sorting column.
 *
 * @param array $allowed_columns An array of allowed column names.
 * @param string $default The default column to use if the parameter is invalid.
 * @return string The validated column name.
 */
function getSortColumn($allowed_columns, $default) {
    return isset($_GET['sort_column']) && in_array($_GET['sort_column'], $allowed_columns)
        ? $_GET['sort_column']
        : $default;
}

/**
 * Validate and fetch the sorting order.
 *
 * @param string $default The default sorting order (e.g., ASC).
 * @return string The validated sorting order (ASC or DESC).
 */
function getSortOrder($default = 'ASC') {
    return isset($_GET['sort_order']) && strtoupper($_GET['sort_order']) === 'DESC' ? 'DESC' : $default;
}

/**
 * Build the WHERE clause for the SQL query.
 *
 * @param string $searchResult The search term.
 * @param string $country_filter The country filter value.
 * @param string $state_filter The state filter value.
 * @return string The dynamically built WHERE clause.
 */
function buildWhereClause($searchResult, $country_filter, $state_filter) {
    $whereClause = "1=1";
    if (!empty($searchResult)) {
        $whereClause .= " AND CONCAT(first_name, last_name, email) LIKE '%$searchResult%'";
    }
    if (!empty($country_filter)) {
        $whereClause .= " AND country = '$country_filter'";
    }
    if (!empty($state_filter)) {
        $whereClause .= " AND state = '$state_filter'";
    }
    return $whereClause;
}

/**
 * Build the SELECT query for fetching records.
 *
 * @param string $whereClause The WHERE clause for filtering records.
 * @param string $sort_column The column to sort by.
 * @param string $sort_order The sorting order (ASC or DESC).
 * @param int $startFrom The offset for SQL LIMIT.
 * @param int $recordsPerPage The number of records to fetch.
 * @return string The complete SQL SELECT query.
 */
function buildSelectQuery($whereClause, $sort_column, $sort_order, $startFrom, $recordsPerPage) {
    return "SELECT * FROM `users` WHERE $whereClause 
            ORDER BY $sort_column $sort_order 
            LIMIT $startFrom, $recordsPerPage";
}

/**
 * Execute a SQL query and handle errors.
 *
 * @param mysqli $connection The database connection object.
 * @param string $sql The SQL query to execute.
 * @return mysqli_result|false The result set on success or false on failure.
 */
function executeQuery($connection, $sql) {
    $result = $connection->query($sql);
    if (!$result) {
        die("SQL Query Error: " . $connection->error . " - Query: " . $sql);
    }
    return $result;
}

/**
 * Get the total number of records matching the WHERE clause.
 *
 * @param mysqli $connection The database connection object.
 * @param string $whereClause The WHERE clause for filtering records.
 * @return int The total number of matching records.
 */
function getTotalRecords($connection, $whereClause) {
    $count_sql = "SELECT COUNT(*) AS total FROM `users` WHERE $whereClause";
    $count_result = $connection->query($count_sql);
    if (!$count_result) {
        die("Count Query Error: " . $connection->error . " - Query: " . $count_sql);
    }
    $count_row = $count_result->fetch_assoc();
    return $count_row['total'];
}

/**
 * Calculate the total number of pages for pagination.
 *
 * @param int $total_records The total number of records.
 * @param int $recordsPerPage The number of records per page.
 * @return int The total number of pages.
 */
function calculateTotalPages($total_records, $recordsPerPage) {
    return ceil($total_records / $recordsPerPage);
}
