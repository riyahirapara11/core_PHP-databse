<?php

function pagination($connection) {
    // Set the number of records per page
    $records_per_page = 5;

    // Get the current page from the URL (default is 1)
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($page - 1) * $records_per_page;

    // Get sort column and sort order
    $sort_column = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'id';
    $sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';

    // Validate sort column and order
    $allowed_columns = ['id', 'first_name', 'last_name', 'email'];
    $sort_column = in_array($sort_column, $allowed_columns) ? $sort_column : 'id';
    $sort_order = $sort_order === 'DESC' ? 'DESC' : 'ASC';

    // Get filters
    $country_filter = isset($_GET['country_filter']) ? $_GET['country_filter'] : '';
    $state_filter = isset($_GET['state_filter']) ? $_GET['state_filter'] : '';

    // Initialize the SQL query
    $sql = "SELECT * FROM `users` WHERE 1=1";

    // Apply country filter
    if (!empty($country_filter)) {
        $sql .= " AND country = '$country_filter'";
    }

    // Apply state filter
    if (!empty($state_filter)) {
        $sql .= " AND state = '$state_filter'";
    }

    // Apply sorting and pagination
    $sql .= " ORDER BY $sort_column $sort_order LIMIT $offset, $records_per_page";

    // Execute the query
    $result = $connection->query($sql);

    // Get the total number of records (without LIMIT)
    $count_sql = "SELECT COUNT(*) AS total FROM `users` WHERE 1=1";
    if (!empty($country_filter)) {
        $count_sql .= " AND country = '$country_filter'";
    }
    if (!empty($state_filter)) {
        $count_sql .= " AND state = '$state_filter'";
    }

    $count_result = $connection->query($count_sql);
    $count_row = $count_result->fetch_assoc();
    $total_records = $count_row['total'];

    // Calculate the total number of pages
    $total_pages = ceil($total_records / $records_per_page);

    // Return the result and pagination data
    return [
        'result' => $result,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'sort_column' => $sort_column,
        'sort_order' => $sort_order,
        'country_filter' => $country_filter,
        'state_filter' => $state_filter,
    ];
}
?>
