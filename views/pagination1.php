<?php

function pagination($connection) {
    // Set the number of records per page
    $records_per_page = 5;

    // Get the current page from the URL (default is 1)
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($page - 1) * $records_per_page;

    // Get search query if it exists
    $searchResult = isset($_GET['search']) ? $_GET['search'] : '';

    // Get sorting parameters
    $sort_column = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'id';
    $sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';

    // Validate sorting parameters
    $allowed_columns = ['id', 'first_name', 'last_name', 'email'];
    $allowed_orders = ['ASC', 'DESC'];

    if (!in_array($sort_column, $allowed_columns)) {
        $sort_column = 'id';
    }
    if (!in_array($sort_order, $allowed_orders)) {
        $sort_order = 'ASC';
    }

    // Initialize the SQL query
    $sql = "SELECT * FROM `users` ORDER BY $sort_column $sort_order LIMIT $offset, $records_per_page";

    // Modify query if search is performed
    if (!empty($searchResult)) {
        $sql = "SELECT * FROM `users` WHERE CONCAT(first_name, last_name, email) LIKE '%$searchResult%' ORDER BY $sort_column $sort_order LIMIT $offset, $records_per_page";
    }

    // Execute the query
    $result = $connection->query($sql);

    // Get the total number of records (without LIMIT)
    $count_sql = "SELECT COUNT(*) AS total FROM `users`";
    if (!empty($searchResult)) {
        $count_sql = "SELECT COUNT(*) AS total FROM `users` WHERE CONCAT(first_name, last_name, email) LIKE '%$searchResult%'";
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
        'search' => $searchResult,
        'current_page' => $page,
        'sort_column' => $sort_column,
        'sort_order' => $sort_order
    ];
}
?>
