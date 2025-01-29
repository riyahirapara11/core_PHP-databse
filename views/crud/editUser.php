<?php
include '../../config/dataBaseConnect.php'; // Correct path for database connection
include '../formValidation.php'; // Ensure this path is correct

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $errors = validateForm($_POST);

    // File Upload Validation
    if ($_FILES['profilePhoto']['error'] == 0) {
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
    }

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
        $filePath = $defaultPhoto;

        if ($_FILES['profilePhoto']['error'] == 0) {
            $fileName = uniqid() . "_" . basename($_FILES['profilePhoto']['name']);
            $fileDestination = $uploadDir . $fileName;

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
                `country` = '$country',
                `state` = '$state',
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

$id = $_GET['id'] ?? null;
if ($id) {
    $query = "SELECT * FROM `users` WHERE id = $id";
    $result = $connection->query($query);
    $user = $result->fetch_assoc();
} else {
    echo "Invalid User ID";
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'getCountries') {
    $query = "SELECT id , name FROM countries";
    $result = $connection->query($query);
    $countries = [];
    while ($row = $result->fetch_assoc()) {
        $countries[] = $row;
    }
    echo json_encode($countries);
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'getStates' && isset($_GET['country_id'])) {
    $countryId = $_GET['country_id'];
    $query = "SELECT id, name FROM states WHERE country_id = $countryId";
    $result = $connection->query($query);

    $states = [];
    while ($row = $result->fetch_assoc()) {
        $states[] = $row;
    }

    echo json_encode($states);
    exit;
}
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
    <!-- <?php 
    // include '../../layout/navbar.php';
     ?> -->

    <?php
    if ($user) {
    ?>
        <div class="container">
            <h1>Edit User Details</h1>
            <form method="post" action="editUser.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
                <div class="form_group">
                    <label for="firstName">Profile Photo :</label>
                    <input type="file" id="profilePhoto" name="profilePhoto">
                </div>
                <div class="form_group">
                    <label for="firstName">First name:</label>
                    <input type="text" id="firstName" name="firstName"
                        value="<?php echo $user['first_name']; ?>">
                    <span class="error"><?php echo $errors['firstName'] ?? ''; ?></span>
                </div>
                <div class="form_group">
                    <label for="lastName">Last name:</label>
                    <input type="text" id="lastName" name="lastName"
                        value="<?php echo $user['last_name']; ?>">
                    <span class="error"><?php echo $errors['lastName'] ?? ''; ?></span>
                </div>
                <div class="form_group">
                    <label for="email">Email :</label>
                    <input type="text" id="email" name="email"
                        value="<?php echo $user['email']; ?>">
                    <span class="error"><?php echo $errors['email'] ?? ''; ?></span>
                </div>
                <div class="form_group">
                    <label for="phone">Phone No. :</label>
                    <input type="text" id="phone" name="phone"
                        value="<?php echo $user['phone_no']; ?>">
                    <span class="error"><?php echo $errors['phone'] ?? ''; ?></span>
                </div>
                <div class="form_group">
                    <label for="address">Address :</label>
                    <textarea name="address" id="address"><?php echo $user['address']; ?></textarea>
                    <span class="error"><?php echo $errors['address'] ?? ''; ?></span>
                </div>
                <!-- <div class="form_group">
                    <label for="country">Country :</label>
                    <select name="country" id="country">
                        <option value=""><?php
                        //  echo $user['country']; 
                         ?></option>
                    </select>
                    <span class="error"><?php
                    //  echo $errors['country'] ?? ''; 
                     ?></span>
                </div>
                <div class="form_group">
                    <label for="state">State :</label>
                    <select name="state" id="state">
                        <option value=""><?php 
                        // echo $user['state']; 
                        ?></option>
                    </select>
                    <span class="error"><?php
                    //  echo $errors['state'] ?? '';
                      ?></span>
                </div> -->
                <div class="form_group">
                    <label for="pincode">Pincode :</label>
                    <input type="text" name="pincode" id="pincode"
                        value="<?php echo $user['pincode']; ?>">
                    <span class="error"><?php echo $errors['pincode'] ?? ''; ?></span>
                </div>
                <!-- <div class="form_group">
                    <label for="password">Password :</label>
                    <input type="password" id="password" name="password">
                    <span class="error"><?php 
                    // echo $errors['password'] ?? '';
                     ?></span>
                </div> -->
                <input type="text" name="id" style="visibility: hidden;" value="<?php echo $id ?>">
                <div class="form_group">
                    <button type="submit">Edit User</button>
                </div>
            </form>
        </div>
    <?php } ?>
</body>

</html>

<!-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        const countrySelect = document.getElementById('country');
        const stateSelect = document.getElementById('state');
       
        fetch('http://localhost/php/views/crud/editUser.php?action=getCountries')
            .then(response => response.json())
            .then(countries => {
                countries.forEach(country => {
                    const option = document.createElement('option');
                    option.value = country.id;
                    option.textContent = country.name;

                    if (country.id === selectedCountry) {
                        option.selected = true;
                    }
                    countrySelect.appendChild(option);
                });
                if (selectedCountry) {
                    fetchStates(selectedCountry, selectedState);
                }
            })
            .catch(error => console.error('Error fetching countries:', error));

        countrySelect.addEventListener('change', function () {
            const countryId = this.value;
            stateSelect.innerHTML = '<option value="">Select State</option>';

            if (countryId) {
                fetchStates(countryId);
            }
        });

        function fetchStates(countryId, preselectedState = '') {
            fetch(`http://localhost/php/views/crud/editUser.php?action=getStates&country_id=${countryId}`)
                .then(response => response.json())
                .then(states => {
                    states.forEach(state => {
                        const option = document.createElement('option');
                        option.value = state.name;
                        option.textContent = state.name;
                        if (state.name === preselectedState) {
                            option.selected = true;
                        }
                        stateSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching states:', error));
        }
    });
</script> -->
