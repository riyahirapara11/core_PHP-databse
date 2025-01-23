<?php
include '../config/dataBaseConnect.php';
include 'formValidation.php'; // Include the validation function

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Call the validation function
    $errors = validate_form($_POST);

    if (empty($errors)) {
        // If no errors, proceed with form processing and insertion
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];
        $phoneNo = $_POST['phone'];
        $address = $_POST['address'];
        $country = $_POST['country'];
        $state = $_POST['states'];
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

        // Close the connection
        $connection->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="./style.css">
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

            <!-- Country Field -->
            <div class="form_group">
                <label for="country">Country :</label>
                <select name="country" id="selectCountry">
                    <option value="">Select Country</option>
                    <!-- Country options dynamically added -->
                </select>
                <span class="error"><?php echo $errors['country'] ?? ''; ?></span>
            </div>

            <!-- State Field -->
            <div class="form_group">
                <label for="state">State :</label>
                <select name="states" id="selectStates">
                    <option value="">Select State</option>
                    <!-- State options dynamically added -->
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
        const country = document.getElementById("selectCountry");
        const state = document.getElementById("selectStates");
        state.disabled = true;
        country.addEventListener("change", stateHandle);

        function stateHandle() {
            if (country.value === " ") {
                state.disabled = true;
            } else {
                state.disabled = false;
            }
        }

        // dynamic
        document.addEventListener('DOMContentLoaded', function() {
            console.log("on DOm");

            const countries = {
                "India": ["Gujarat", "Maharastra", "Tamilnadu", "Rajasthan"],
                "canada": ["Alberta", "BritishColumbia", "Manitoba", "Quebec"],
                "USA": ["California", "Alaska", "Georgia"],
                "Japan": ["Hokkaido", "Fukushima", "Hiroshima"]
            };
            const countrySelect = document.getElementById('selectCountry');
            const stateSelect = document.getElementById('selectStates');
            const selectedCountry = "<?php echo isset($_POST['country']) ? $_POST['country'] : ''; ?>";
            const selectedState = "<?php echo isset($_POST['states']) ? $_POST['states'] : ''; ?>";

            // console.log("selectedCountry", selectedCountry);

            for (let country in countries) {
                // console.log("country", country);

                let option = document.createElement('option');
                option.value = country;
                option.textContent = country;
                if (selectedCountry && country == selectedCountry) {
                    option.selected = true;
                }
                // console.log("option", option);

                countrySelect.appendChild(option);
            }

            stateSelect.innerHTML = '<option value="" disabled selected>Select a state</option>';

            let states = countries[countrySelect.value];
            if (states) {
                console.log("dada");

                for (let state of states) {
                    let option = document.createElement('option');
                    option.value = state;
                    option.innerText = state;
                    if (selectedState && state == selectedState) {
                        option.selected = true;
                    }
                    console.log("option", option);

                    stateSelect.appendChild(option);
                }
            }

            countrySelect.addEventListener('change', function() {
                console.log("nonad add");

                stateSelect.innerHTML = '<option value="" disabled selected>Select a state</option>';

                let states = countries[countrySelect.value];
                for (let state of states) {
                    let option = document.createElement('option');
                    option.value = state;
                    option.innerText = state;
                    if (selectedState && state == selectedState) {
                        option.selected = true;
                    }
                    console.log("option", option);

                    stateSelect.appendChild(option);
                }
            });
        })
    </script>


</body>

</html>