<?php
include '../../config/dataBaseConnect.php';
include '../formValidation.php';

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

        if ($_FILES['profilePhoto']['error'] == 0) {
            $fileName = uniqid() . basename($_FILES['profilePhoto']['name']);
            $fileDestination = $uploadDir . $fileName;

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileSizeLimit = 5000000; // 5MB
            $fileType = $_FILES['profilePhoto']['type'];
            $fileSize = $_FILES['profilePhoto']['size'];

            if (!in_array($fileType, $allowedTypes)) {
                echo '<script>alert("Invalid file type. Only JPEG, PNG, and GIF are allowed.")</script>';
                exit;
            }

            if ($fileSize > $fileSizeLimit) {
                echo '<script>alert("File size exceeds 5MB limit.")</script>';
                exit;
            }

            unlink('../../' . $_POST['existingFilePath']);

            if (move_uploaded_file($_FILES['profilePhoto']['tmp_name'], $fileDestination)) {
                $filePath = '/storage/profile_images/' . $fileName;
            }
        }

        $sql = "UPDATE users SET first_name = '$firstName', last_name = '$lastName', email = '$email',
                phone_no = '$phoneNo', address = '$address', country_id = '$country', state_id = '$state', file_path = '$filePath'
                WHERE id = '$id'";

        // if (updateUser($conn, $_SESSION['user_id'], $name, $email, $password)) {
        //     header("Location: profile.php");
        //     exit();
        // }


        if ($connection->query($sql)) {
            session_start();
            $_SESSION["edit_message"] = "Record Updated Successfully!";
            header("Location: ../dashboard.php");
            exit;
        } else {
            echo "Error updating data: " . $connection->error;
        }
    }
}

$id = $_GET['id'];
$query = "SELECT * FROM users WHERE id = $id";
$result = $connection->query($query);
$rows = $result->fetch_assoc();

include '../editUserForm.php'; // Include UI here




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

