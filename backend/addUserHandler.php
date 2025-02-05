<?php
session_start();
include '../config/dataBaseConnect.php';
include '../views/formValidation.php';
// include '../../roles/checkPermission.php';

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

    // Using prepared statements to prevent SQL injection
    $stmt = $connection->prepare("INSERT INTO users (first_name, last_name, email, phone_no, address, country_id, state_id, pincode, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $firstName, $lastName, $email, $phoneNo, $address, $country, $state, $pincode, $hashedPassword);

    // Use the query function
    $sql = addUserQuery($firstName, $lastName, $email, $phoneNo, $address, $country, $state, $pincode, $hashedPassword);

    if ($stmt->execute()) {
        $_SESSION["add_message"] = "User Added Successfully!";
        header("Location: ../dashboard.php");
    } else {
        $_SESSION["form_errors"] = ["database" => "Error inserting data."];
        header("Location:../views/crud/addUser.php");
    }
// include '../views/crud/addUser.php' ;
    $stmt->close();
    $connection->close();
}
?>



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
