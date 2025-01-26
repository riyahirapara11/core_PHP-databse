<?php

include '../config/dataBaseConnect.php';
include './formValidation.php';

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
if (isset($_GET['action']) && $_GET['action'] === 'getCountries') {
    header('Content-Type: application/json');
    $query = "SELECT id, name FROM countries";
    $result = $connection->query($query);

    $countries = [];
    while ($row = $result->fetch_assoc()) {
        $countries[] = $row;
    }

    echo json_encode($countries);
    exit;
}

// Fetch states
if (isset($_GET['action']) && $_GET['action'] === 'getStates' && isset($_GET['country_id'])) {
    header('Content-Type: application/json');

    $countryId = intval($_GET['country_id']);
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
    <title>Registration</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
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
</body>

</html>

<script>

document.addEventListener('DOMContentLoaded', function () {
    const countrySelect = document.getElementById('country');
    const stateSelect = document.getElementById('state');
    const selectedCountry = '<?= $_POST['country'] ?? '' ?>';
    const selectedState = '<?= $_POST['state'] ?? '' ?>';

    // Fetch countries only once
    fetch('http://localhost/core_PHP-databse/views/registration.php?action=getCountries')
        .then(response => response.json())
        .then(countries => {
            countries.forEach(country => {
                const option = document.createElement('option');
                option.value = country.id;
                option.textContent = country.name;

                // Preselect the country if it matches the submitted value
                if (country.id === selectedCountry) {
                    option.selected = true;
                }

                countrySelect.appendChild(option);
            });

            // If a country is preselected, fetch states
            if (selectedCountry) {
                fetchStates(selectedCountry, selectedState);
            }
        })
        .catch(error => console.error('Error fetching countries:', error));

    // Event listener for country change
    countrySelect.addEventListener('change', function () {
        const countryId = this.value;
        stateSelect.innerHTML = '<option value="">Select State</option>'; // Clear previous states

        if (countryId) {
            fetchStates(countryId);
        }
    });

    // Fetch states for the selected country
    function fetchStates(countryId, preselectedState = '') {
        fetch(`http://localhost/core_PHP-databse/views/registration.php?action=getStates&country_id=${countryId}`)
            .then(response => response.json())
            .then(states => {
                states.forEach(state => {
                    const option = document.createElement('option');
                    option.value = state.id;
                    option.textContent = state.name;

                    // Preselect the state if it matches the submitted value
                    if (state.id === preselectedState) {
                        option.selected = true;
                    }

                    stateSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching states:', error));
    }
});


  
</script>

</body>

</html>