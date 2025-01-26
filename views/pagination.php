<?php

function pagination($connection) {
    $recordsPerPage = 5;

    // Get the current page or default to 1
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $startFrom = ($page - 1) * $recordsPerPage;

    // Get search, sorting, and filter parameters
    $searchResult = isset($_GET['search']) ? $_GET['search'] : '';
    $sort_column = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'id';
    $sort_order = isset($_GET['sort_order']) && $_GET['sort_order'] === 'DESC' ? 'DESC' : 'ASC';

    $allowed_columns = ['id', 'first_name', 'last_name', 'email'];
    $sort_column = in_array($sort_column, $allowed_columns) ? $sort_column : 'id';

    $country_filter = isset($_GET['country_filter']) ? $_GET['country_filter'] : '';
    $state_filter = isset($_GET['state_filter']) ? $_GET['state_filter'] : '';

    // Build WHERE clause dynamically based on filters
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

    // Main query for fetching records
    $sql = "SELECT * FROM `users` WHERE $whereClause 
            ORDER BY $sort_column $sort_order 
            LIMIT $startFrom, $recordsPerPage";

    $result = $connection->query($sql);
    if (!$result) {
        die("SQL Query Error: " . $connection->error . " - Query: " . $sql);
    }

    // Query to count total records (for pagination)
    $count_sql = "SELECT COUNT(*) AS total FROM `users` WHERE $whereClause";
    $count_result = $connection->query($count_sql);
    if (!$count_result) {
        die("Count Query Error: " . $connection->error . " - Query: " . $count_sql);
    }
    $count_row = $count_result->fetch_assoc();
    $total_records = $count_row['total'];

    $total_pages = ceil($total_records / $recordsPerPage);

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
?>
