To separate the SQL queries into separate files and use them in your project, follow these steps:

### 1. **Create a `queries.php` file** (or similar name):
Create a new file that will hold all the SQL query functions. This will be the file where you'll place the functions for adding, editing, registering, and listing users.

#### `queries.php` (located in a folder like `includes/queries.php`):

```php
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
```

### 2. **Use the queries in your main files:**

Now that the queries are separated into a file, you need to include this file in your main PHP files (like `addUser.php`, `editUser.php`, `registration.php`, etc.) and call the functions for executing the queries.

For example:

#### In `addUser.php`:

```php
<?php
session_start();
include '../config/dataBaseConnect.php';
include '../includes/queries.php';  // Include the queries file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = validateForm($_POST);

    if (!empty($errors)) {
        $_SESSION["form_errors"] = $errors;
        header("Location: ../views/crud/addUser.php");
        exit();
    }
    
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phoneNo = $_POST['phone'];
    $address = $_POST['address'];
    $country = $_POST['country'];
    $state = $_POST['state'];
    $pincode = $_POST['pincode'];
    $password = $_POST['password'];

    $options = ["cost" => 10];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT, $options);

    // Use the query function
    $sql = addUserQuery($firstName, $lastName, $email, $phoneNo, $address, $country, $state, $pincode, $hashedPassword);

    if ($connection->query($sql)) {
        $_SESSION["add_message"] = "User Added Successfully!";
        header("Location: ../dashboard.php");
    } else {
        $_SESSION["form_errors"] = ["database" => "Error inserting data."];
        header("Location:../views/crud/addUser.php");
    }

    $connection->close();
}
?>
```

#### In `editUser.php`:

```php
<?php
include '../../config/dataBaseConnect.php';
include '../includes/queries.php';  // Include the queries file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = validateForm($_POST);

    if (empty($errors)) {
        $id = $_POST['id'];
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];
        $phoneNo = $_POST['phone'];
        $address = $_POST['address'];
        $country = $_POST['country'];
        $state = $_POST['state'];
        $pincode = $_POST['pincode'];
        $password = $_POST['password'];

        $options = ["cost" => 10];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT, $options);

        $uploadDir = realpath(__DIR__ . '/../../storage/profile_images/') . '/';
        $defaultPhoto = '/storage/default.jpg';
        $filePath = $_POST['existingFilePath'] ?? $defaultPhoto;

        // Handle file upload (omitted for brevity)

        // Use the query function for update
        $sql = updateUserQuery($id, $firstName, $lastName, $email, $phoneNo, $address, $country, $state, $filePath);

        if ($connection->query($sql)) {
            $_SESSION["edit_message"] = "Record Updated Successfully!";
            header("Location: ../dashboard.php");
        } else {
            echo "Error updating data: " . $connection->error;
        }
    }
}

$id = $_GET['id'];
$query = "SELECT * FROM users WHERE id = $id";
$result = $connection->query($query);
$rows = $result->fetch_assoc();

include '../editUserForm.php';  // Include UI here
?>
```

#### In `listingUser.php`:

```php
<?php
include '../config/dataBaseConnect.php';
include '../includes/queries.php';  // Include the queries file

// Get users list
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

// Output the results
// Render your user listing table here...
?>
```

### 3. **Benefits**:
- **Separation of concerns**: Keeps your query logic separate from the business logic, improving maintainability and readability.
- **Reusability**: You can reuse query functions in different files, making the code more modular.
- **Cleaner code**: Your main files (`addUser.php`, `editUser.php`, etc.) become much cleaner and easier to read.

By following this approach, you centralize SQL queries and reduce duplication across your project.