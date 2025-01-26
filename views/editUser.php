<?php
include '../config/dataBaseConnect.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    print_r($_POST);
    // validations
    $firstNameErr  = $lastNameErr = $emailErr  = $phoneErr = $addressErr  = $countryErr = $stateErr  = $pincodeErr = $passwordErr = $confirmPassErr = "";
    $isAnyError = false;

    if (empty($_POST["firstName"])) {
        $firstNameErr = "First Name is required";
        $isAnyError = true;
    } else {
        $firstName = test_input($_POST["firstName"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $firstName)) {
            $firstNameErr = "Only letters and white spaces are allowed";
            $isAnyError = true;
        }
    }

    if (empty($_POST["lastName"])) {
        $lastNameErr = "Last Name is required";
        $isAnyError = true;
    } else {
        $lastName = test_input($_POST["lastName"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $lastName)) {
            $lastNameErr = "Only letters and white spaces are allowed";
            $isAnyError = true;
        }
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
        $isAnyError = true;
    } else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
            $isAnyError = true;
        }
    }

    if (empty($_POST["phone"])) {
        $phoneErr = "Phone No. is required";
        $isAnyError = true;
    } else {
        $phone = test_input($_POST["phone"]);
        if (!preg_match("/^[0-9]{10}$/", $phone)) {
            $phoneErr = "Phone number must be 10 digits";
            $isAnyError = true;
        }
    }

    if (empty($_POST["address"])) {
        $addressErr = "Address is required";
        $isAnyError = true;
    } else {
        $address = test_input($_POST["address"]);
    }

    if (empty($_POST["country"])) {
        $countryErr = "Must select a country";
        $isAnyError = true;
    } else {
        $country = test_input($_POST["country"]);
    }

    if (empty($_POST["states"])) {
        $stateErr = "Must select a state";
        $isAnyError = true;
    } else {
        $state = test_input($_POST["states"]);
    }

    if (empty($_POST["pincode"])) {
        $pincodeErr = "Pincode is required";
        $isAnyError = true;
    } else {
        $pincode = test_input($_POST["pincode"]);
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
        $isAnyError = true;
    } else {
        $password = test_input($_POST["password"]);
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
            $passwordErr = "Password must be at least 8 characters,one letter,digit,special character";
            $isAnyError = true;
        }
    }

    if (empty($_POST["confirmPass"])) {
        $confirmPassErr = "Confirm Password is required";
        $isAnyError = true;
    } else {
        $confirmPass = test_input($_POST["confirmPass"]);
        if ($_POST['password'] !== $_POST['confirmPass']) {
            $confirmPassErr = "Password did not match.";
            $isAnyError = true;
        }
    }

    //edit data in database
    if ($isAnyError == false) {
        $id = $_POST['id'];
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];
        $phoneNo = $_POST['phone'];
        $address = $_POST['address'];
        $country = $_POST['country'];
        $state = $_POST['states'];
        $pincode = $_POST['pincode'];
        $password = $_POST['password'];

        //encrypt password
        $options = ["cost" => 10];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT, $options);


        $sql = "UPDATE `users` SET `first_name` = '$firstName' ,`last_name` ='$lastName', `email`=  '$email', `phone_no`='$phoneNo', `address`='$address' , `country`='$country', `state` ='$state' WHERE `id`= '$id'";

        if ($connection->query($sql)) {
            session_start();
            $_SESSION["edit_message"]="Record Updated Successfully !";
            header("Location: dashboard.php");
        } else {
            echo "error updating  data .";
            echo "Error: " . $sql . "<br>" . $connection->error;
        }

        // connection close
        $connection->close();
    }
}


// Fetch all countries
if ($_GET['action'] === 'getCountries') {
    $query = "SELECT * FROM countries";
    $result = $connection->query($query);

    $countries = [];
    while ($row = $result->fetch_assoc()) {
        $countries[] = $row;
    }

    echo json_encode($countries);
    exit;
}

// Fetch states for a specific country
if ($_GET['action'] === 'getStates' && isset($_GET['country_id'])) {
    $country_id = intval($_GET['country_id']);
    $query = "SELECT * FROM states WHERE country_id = $country_id";
    $result = $connection->query($query);

    $states = [];
    while ($row = $result->fetch_assoc()) {
        $states[] = $row;
    }

    echo json_encode($states);
    exit;
}

function test_input($data)
{
    return $data;
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Edit User</title>

</head>

<body>
    <a href="./dashboard.php">Dashboard</a>
    <h1>Edit User Details</h1>

    <?php
    $id = $_GET['id'];
    $query = "SELECT * FROM `users` WHERE id = " . $_GET['id'];
    if ($result = $connection->query($query)) {
        while ($rows = $result->fetch_assoc()) {
    ?>

            <form method="post" action="editUser.php?id=<?php echo $Id; ?>">
                <div class="form_group">
                    <label for="firstName">First name:</label>
                    <input type="text" id="firstName" name="firstName"
                        value="<?php echo $rows['first_name']; ?>"> <span class="error">
                        <?php echo $firstNameErr; ?>
                    </span>
                </div>
                <div class="form_group">
                    <label for="lastName">Last name:</label>
                    <input type="text" id="lastName" name="lastName"
                        value="<?php echo $rows['last_name']; ?>"><span class="error">
                        <?php echo $lastNameErr; ?>
                    </span>
                </div>
                <div class="form_group">
                    <label for="email">Email :</label>
                    <input type="text" id="email" name="email"
                        value="<?php echo $rows['email']; ?>">
                    <span class="error">
                        <?php echo $emailErr; ?>
                    </span>
                </div>
                <div class="form_group">
                    <label for="phone">Phone No. :</label>
                    <input type="text" id="phone" name="phone"
                        value="<?php echo $rows['phone_no']; ?>"><span class="error">
                        <?php echo $phoneErr; ?>
                    </span>
                </div>
                <div class="form_group">
                    <label for="address">Address :</label>
                    <textarea name="address" id="address" value=""><?php echo $rows['address']; ?>
                    </textarea>
                    <span class="error" onchange="" onclick="">
                        <?php echo $addressErr; ?>
                    </span>
                </div>
                <div class="form_group">
                    <label for="country">Country :</label>
                    <select name="country" id="selectCountry" value="">
                        <option value=""><?php echo $rows['country']; ?></option>
                    </select>
                    <span class="error">
                        <?php echo $countryErr; ?>
                    </span>
                </div>
                <div class="form_group">
                    <label for="state">State :</label>
                    <select name="states" id="selectStates" value="">
                        <option value=""><?php echo $rows['state']; ?></option>
                    </select><span class="error">
                        <?php echo $stateErr; ?>
                    </span>
                </div>
                <div class="form_group">
                    <label for="pincode">Pincode :</label>
                    <input type="text" name="pincode" id="pincode"
                        value="<?php echo $rows['pincode']; ?>"><span class="error">
                        <?php echo $pincodeErr; ?>
                    </span>
                </div>
                <div class="form_group">
                    <label for="password">Password :</label>
                    <input type="password" id="password" name="password"
                        value="<?php echo $rows['password']; ?>"><span
                        class="error">

                    </span>
                </div>
                <div class="form_group">
                    <label for="confirmPass">Confirm Password :</label>
                    <input type="password" id="confirmPass" name="confirmPass"
                        value="<?php echo $rows['password']; ?>"><span
                        class="error">

                    </span>
                </div>
                <input type="text" name="id" style="visibility: hidden;" value="<?php echo $id ?>">
                <div class="form_group">
                    <button type="submit">Edit User</button>
                </div>
            </form>
    <?php
        }
    }
    ?>
</body>

</html>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const countries = {
            "India": ["Gujarat", "Maharashtra", "Tamilnadu", "Rajasthan"],
            "Canada": ["Alberta", "British Columbia", "Manitoba", "Quebec"],
            "USA": ["California", "Alaska", "Georgia"],
            "Japan": ["Hokkaido", "Fukushima", "Hiroshima"]
        };

        const countrySelect = document.getElementById('selectCountry');
        const stateSelect = document.getElementById('selectStates');
        const selectedCountry = "<?php echo $rows['country']; ?>"; // Get from database
        const selectedState = "<?php echo $rows['state']; ?>"; // Get from database

        // Populate country dropdown
        for (let country in countries) {
            let option = document.createElement('option');
            option.value = country;
            option.textContent = country;

            // Mark as selected if it matches the database value
            if (country === selectedCountry) {
                option.selected = true;
            }

            countrySelect.appendChild(option);
        }

        // Function to populate state dropdown based on selected country
        function populateStates(selectedCountry, selectedState = '') {
            stateSelect.innerHTML = '<option value="" disabled>Select a state</option>';
            let states = countries[selectedCountry];

            if (states) {
                for (let state of states) {
                    let option = document.createElement('option');
                    option.value = state;
                    option.textContent = state;

                    // Mark as selected if it matches the database value
                    if (state === selectedState) {
                        option.selected = true;
                    }

                    stateSelect.appendChild(option);
                }
            }
        }

        // Populate states on page load
        if (selectedCountry) {
            populateStates(selectedCountry, selectedState);
            stateSelect.disabled = false;
        } else {
            stateSelect.disabled = true;
        }

        // Handle country change event
        countrySelect.addEventListener('change', function () {
            let selectedCountry = countrySelect.value;
            populateStates(selectedCountry);
            stateSelect.disabled = false;
        });
    });
</script>

<script></script>
