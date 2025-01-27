<?php

include '../config/dataBaseConnect.php';
include './formValidation.php';
include './country_state.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Call the validation function
    $errors = validate_form($_POST);

    if (empty($errors)) {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];
        $phoneNo = $_POST['phone'];
        $address = $_POST['address'];
        $country = $_POST['country'];
        $state = $_POST['state'];
        $pincode = $_POST['pincode'];
        $password = $_POST['password'];

        // Encrypt password
        $options = ["cost" => 10];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT, $options);

        $sql = "INSERT INTO `users` (`first_name`, `last_name`, `email`, `phone_no`, `address`, `country`, `state`, `pincode`, `password`) 
                VALUES ('$firstName', '$lastName', '$email', '$phoneNo', '$address', '$country', '$state', '$pincode', '$hashedPassword')";

        if ($connection->query($sql)) {
            header("Location: login.php");
        } else {
            echo "Error inserting data: " . $connection->error;
        }

        $connection->close();
    }
}

// Fetch countries
$countries = getCountries($connection);

// Fetch states if country is selected
$states = [];
if (isset($_POST['country']) && !empty($_POST['country'])) {
    $countryId = $_POST['country'];
    $states = getStates($connection, $countryId);
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <script src="./js/country_state.js"></script>
</head>

<body>
    <div class="container">
        <h1> Registration form</h1>
       

        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">

            <!-- First Name Field -->
            <div class="form_group">
                <label for="firstName">First name:</label>
                <input type="text" id="firstName" name="firstName" value="<?= $_POST['firstName'] ?? '' ?>">
                <span class="error"><?php echo $errors['firstName'] ?? ''; ?></span>
            </div>

            <!-- Last Name Field -->
            <div class="form_group">
                <label for="lastName">Last name:</label>
                <input type="text" id="lastName" name="lastName" value="<?= $_POST['lastName'] ?? '' ?>">
                <span class="error"><?php echo $errors['lastName'] ?? ''; ?></span>
            </div>

            <!-- Email Field -->
            <div class="form_group">
                <label for="email">Email :</label>
                <input type="text" id="email" name="email" value="<?= $_POST['email'] ?? '' ?>">
                <span class="error"><?php echo $errors['email'] ?? ''; ?></span>
            </div>

            <!-- Phone Field -->
            <div class="form_group">
                <label for="phone">Phone No. :</label>
                <input type="text" id="phone" name="phone" value="<?= $_POST['phone'] ?? '' ?>">
                <span class="error"><?php echo $errors['phone'] ?? ''; ?></span>
            </div>

            <!-- Address Field -->
            <div class="form_group">
                <label for="address">Address :</label>
                <textarea name="address" id="address"><?= $_POST['address'] ?? '' ?></textarea>
                <span class="error"><?php echo $errors['address'] ?? ''; ?></span>
            </div>

            <div class="form_group">
                <label for="country">Country:</label>
                <select id="country" name="country">
                    <option value="">Select Country</option>
                    <?php
                    // // Fetch countries from the database
                    // $countriesQuery = "SELECT * FROM countries";
                    // $countriesResult = $connection->query($countriesQuery);

                    // while ($country = $countriesResult->fetch_assoc()) {
                    //     // Check if the country was selected
                    //     $selected = ($_POST['country'] ?? '') == $country['id'] ? 'selected' : '';
                    //     echo "<option value='{$country['id']}' $selected>{$country['name']}</option>";
                    // }
                    ?>
                </select>
                <span class="error"><?php echo $errors['country'] ?? ''; ?></span>
            </div>

            <div class="form_group">
                <label for="state">State:</label>
                <select id="state" name="state">
                    <option value="">Select State</option>
                    <?php
                    if (!empty($_POST['country'])) {
                        // Fetch states based on selected country
                        $countryId = intval($_POST['country']);
                        $statesQuery = "SELECT * FROM states WHERE country_id = $countryId";
                        $statesResult = $connection->query($statesQuery);

                        while ($state = $statesResult->fetch_assoc()) {
                            // Check if this state was selected in the form submission
                            $selected = (isset($_POST['state']) && $_POST['state'] == $state['id']) ? 'selected' : '';
                            echo "<option value='{$state['id']}' $selected>{$state['name']}</option>";
                        }
                    }
                    ?>  
                </select>
                <span class="error"><?php echo $errors['state'] ?? ''; ?></span>
            </div>


            <!-- Pincode Field -->
            <div class="form_group">
                <label for="pincode">Pincode :</label>
                <input type="text" name="pincode" id="pincode" value="<?= $_POST['pincode'] ?? '' ?>">
                <span class="error"><?php echo $errors['pincode'] ?? ''; ?></span>
            </div>

            <!-- Password Field -->
            <div class="form_group">
                <label for="password">Password :</label>
                <input type="password" id="password" name="password" value="<?= $_POST['password'] ?? '' ?>">
                <span class="error"><?php echo $errors['password'] ?? ''; ?></span>
            </div>

            <!-- Confirm Password Field -->
            <div class="form_group">
                <label for="confirmPass">Confirm Password :</label>
                <input type="password" id="confirmPass" name="confirmPass" value="<?= $_POST['confirmPass'] ?? '' ?>">
                <span class="error"><?php echo $errors['confirmPass'] ?? ''; ?></span>
            </div>

            <!-- Submit Button -->
            <div class="form_group">
                <button type="submit">Register</button>
            </div>

            <div class="form_group">
                <p>Already have an account? <a href="/login"><span>Login</span></a></p>
            </div>
        </form>
    </div>

    <div id="location-data" data-country="<?= $_POST['country'] ?? '' ?>" data-state="<?= $_POST['state'] ?? '' ?>"></div>
</body>

</html>



</body>

</html>