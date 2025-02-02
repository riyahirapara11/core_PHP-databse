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
        // $password = $_POST['password'];

        
        // Check if password is provided, otherwise, keep the existing one
        if (!empty($_POST['password'])) {
            // Hash the new password
            $password = $_POST['password'];
            $options = ["cost" => 10];
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT, $options);
        } else {
            // Retain the existing password (don't modify it)
            $hashedPassword = $_POST['existingPassword'];
        }

        $uploadDir = realpath(__DIR__ . '/../../storage/profile_images/') . '/';
        $defaultPhoto = '/storage/default.jpg';
        $filePath = $_POST['existingFilePath'] ?? $defaultPhoto;

        // Handle file upload if a new file is provided
        if ($_FILES['profilePhoto']['error'] == 0) {
            $fileName = basename($_FILES['profilePhoto']['name']);
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

            if (move_uploaded_file($_FILES['profilePhoto']['tmp_name'], $fileDestination)) {
                $filePath = '/storage/profile_images/' . $fileName;
            }
        }

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
            echo "Error updating data: " . $connection->error;
        }
    }
}


$id = $_GET['id'];
$query = "SELECT u.*, c.name AS country_name, s.name AS state_name 
          FROM users u
          LEFT JOIN countries c ON u.country_id = c.id
          LEFT JOIN states s ON u.state_id = s.id
          WHERE u.id = $id";
$result = $connection->query($query);
$rows = $result->fetch_assoc();


$firstName = $rows['first_name'];
$lastName = $rows['last_name'];
$email = $rows['email'];
$phoneNo = $rows['phone_no'];
$address = $rows['address'];
$country_id = $rows['country_id'];
$state_id = $rows['state_id'];
$country_name = $rows['country_name'];
$state_name = $rows['state_name'];
$pincode = $rows['pincode'];
$filePath = $rows['file_path'] ?? '/storage/default.jpg';
$id = $rows['id'];
$existingPassword = $rows['password']; // Store existing password hash for comparison

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dashboardStyle.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Edit User</title>
</head>

<body>
    <?php include '../layout/navbar.php'; ?>

    <div class="container">
        <h1>Edit User Details</h1>
        <form method="post" action="editUser.php?id=<?= $id ?>" enctype="multipart/form-data">
            <?php 
            // Pass the existing password and other details to the form
            include '../formTemplate.php'; 
            ?>
            <input type="hidden" name="existingPassword" value="<?= $existingPassword ?>">
            <input type="hidden" name="id" value="<?= $id ?? '' ?>">
        </form>
        <div class="form_group">
            <button><a href="../dashboard.php" style="color: white;">Cancel</a></button>
        </div>
    </div>
</body>

</html>


<script src="../js/counrtyStateDropdown.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeLocationDropdowns('country', 'state', '<?= $_POST['country'] ?? '' ?>', '<?= $_POST['state'] ?? '' ?>');
    });
</script>