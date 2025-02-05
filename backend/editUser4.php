<?php
// editUserLogic.php

include '../config/dataBaseConnect.php';
include '../views/formValidation.php';

$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form data
    $errors = validateForm($_POST);

    if (empty($errors)) {
        // Get form data
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

        // Hash password
        $options = ["cost" => 10];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT, $options);

        // Set upload directory
        $uploadDir = realpath(__DIR__ . '/../../storage/profile_images/') . '/';
        $defaultPhoto = '/storage/default.jpg';
        $filePath = $_POST['existingFilePath'] ?? $defaultPhoto;

        // Handle file upload
        if ($_FILES['profilePhoto']['error'] == 0) {
            $fileName = uniqid() . basename($_FILES['profilePhoto']['name']);
            $fileDestination = $uploadDir . $fileName;

            // File validation
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileSizeLimit = 5000000; // 5MB
            $fileType = $_FILES['profilePhoto']['type'];
            $fileSize = $_FILES['profilePhoto']['size'];

            if (!in_array($fileType, $allowedTypes)) {
                $errors['profilePhoto'] = "Invalid file type. Only JPEG, PNG, and GIF are allowed.";
            }

            if ($fileSize > $fileSizeLimit) {
                $errors['profilePhoto'] = "File size exceeds 5MB limit.";
            }

            if (empty($errors)) {
                // Delete old profile picture if exists
                unlink('../../' . $_POST['existingFilePath']);
                if (move_uploaded_file($_FILES['profilePhoto']['tmp_name'], $fileDestination)) {
                    $filePath = '/storage/profile_images/' . $fileName;
                }
            }
        }

        // Update user in the database
        if (empty($errors)) {
            $sql = "UPDATE `users` SET 
                `first_name` = '$firstName',  
                `last_name` = '$lastName',  
                `email` = '$email',  
                `phone_no` = '$phoneNo', 
                `address` = '$address',  
                `country_id` = '$country',  
                `state_id` = '$state',  
                `file_path` = '$filePath'  
                WHERE `id` = '$id'";

            if ($connection->query($sql)) {
                session_start();
                $_SESSION["edit_message"] = "Record Updated Successfully!";
                header("Location: ../dashboard.php");
                exit;
            } else {
                $errors['database'] = "Error updating data: " . $connection->error;
            }
        }
    }
}
?>
