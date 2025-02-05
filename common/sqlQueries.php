<?php
// Queries for addUser.php
function addUserQuery($firstName, $lastName, $email, $phoneNo, $address, $country, $state, $pincode, $hashedPassword) {
    return "INSERT INTO users (first_name, last_name, email, phone_no, address, country_id, state_id, pincode, password) 
            VALUES ('$firstName', '$lastName', '$email', '$phoneNo', '$address', '$country', '$state', '$pincode', '$hashedPassword')";
}

// Queries for editUser.php
function updateUserQuery($id, $firstName, $lastName, $email, $phoneNo, $address, $country, $state, $filePath) {
    return "UPDATE users SET first_name = '$firstName', last_name = '$lastName', email = '$email',
            phone_no = '$phoneNo', address = '$address', country_id = '$country', state_id = '$state', file_path = '$filePath'
            WHERE id = '$id'";
}

// Queries for registration.php
function registerUserQuery($firstName, $lastName, $email, $phoneNo, $address, $country, $state, $pincode, $hashedPassword) {
    return "INSERT INTO users (first_name, last_name, email, phone_no, address, country_id, state_id, pincode, password) 
            VALUES ('$firstName', '$lastName', '$email', '$phoneNo', '$address', '$country' , '$state', '$pincode', '$hashedPassword')";
}

// Queries for login.php
function loginUserQuery($email) {
    return "SELECT * FROM users WHERE email = '$email'";
}

// Queries for listing users (listingUser.php)
function getSearchQuery($search, $connection) {
    return !empty($search) ? " AND CONCAT(first_name, ' ', last_name, email) LIKE '%" . $connection->real_escape_string($search) . "%'" : '';
}

function getFilterQuery($filter, $column, $connection) {
    return !empty($filter) ? " AND $column LIKE '" . $connection->real_escape_string($filter) . "'" : '';
}

function getSortQuery($sortColumn, $sortOrder) {
    $allowedColumns = ['id', 'first_name', 'last_name', 'email'];
    $sortColumn = in_array($sortColumn, $allowedColumns) ? $sortColumn : 'id';
    return " ORDER BY $sortColumn $sortOrder";
}

function getPaginationQuery($page, $recordsPerPage) {
    $startFrom = ($page - 1) * $recordsPerPage;
    return " LIMIT $startFrom, $recordsPerPage";
}

function listUsersQuery($search, $countryFilter, $stateFilter, $sortColumn, $sortOrder, $page, $recordsPerPage, $connection) {
    $whereClause = "1=1" . 
        getSearchQuery($search, $connection) . 
        getFilterQuery($countryFilter, 'c.name', $connection) . 
        getFilterQuery($stateFilter, 's.name', $connection);

    return "SELECT u.*, c.name AS country, s.name AS state
            FROM users u
            LEFT JOIN countries c ON u.country_id = c.id
            LEFT JOIN states s ON u.state_id = s.id
            WHERE $whereClause" . 
            getSortQuery($sortColumn, $sortOrder) . 
            getPaginationQuery($page, $recordsPerPage);
}

function countUsersQuery($search, $countryFilter, $stateFilter, $connection) {
    $whereClause = "1=1" . 
        getSearchQuery($search, $connection) . 
        getFilterQuery($countryFilter, 'c.name', $connection) . 
        getFilterQuery($stateFilter, 's.name', $connection);

    return "SELECT COUNT(*) AS total 
            FROM users u
            LEFT JOIN countries c ON u.country_id = c.id
            LEFT JOIN states s ON u.state_id = s.id
            WHERE $whereClause";
}
?>
