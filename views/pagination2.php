<?php

function pagination($connection) {
    // Set the number of records per page
    $records_per_page = 5;

    // Get the current page from the URL (default is 1)
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $records_per_page;

    // Get search query if it exists
    $searchResult = isset($_GET['search']) ? $_GET['search'] : '';

    // Fetch records for the current page
    $sql = "SELECT * FROM `users`";
    if (!empty($searchResult)) {
        $sql .= " WHERE CONCAT(first_name, last_name, email) LIKE '%$searchResult%'";
    }
    $sql .= " LIMIT $offset, $records_per_page";

    $result = $connection->query($sql);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    // Add a sequential row number for the current page
    $row_number = $offset + 1;
    foreach ($rows as &$row) {
        $row['row_number'] = $row_number++;
    }

    // Get total record count for pagination
    $count_sql = "SELECT COUNT(*) AS total FROM `users`";
    if (!empty($searchResult)) {
        $count_sql .= " WHERE CONCAT(first_name, last_name, email) LIKE '%$searchResult%'";
    }
    $count_result = $connection->query($count_sql);
    $count_row = $count_result->fetch_assoc();
    $total_records = $count_row['total'];

    // Calculate total pages
    $total_pages = ceil($total_records / $records_per_page);

    return [
        'result' => $rows,
        'total_pages' => $total_pages,
        'search' => $searchResult,
        'current_page' => $page
    ];
}
?>
