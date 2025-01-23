<?php
function validate_form($data) {
    $errors = [];

    // Validate First Name
    if (empty($data["firstName"])) {
        $errors['firstName'] = "First Name is required";
    } else {
        $firstName = test_input($data["firstName"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $firstName)) {
            $errors['firstName'] = "Only letters and white spaces are allowed";
        }
    }

    // Validate Last Name
    if (empty($data["lastName"])) {
        $errors['lastName'] = "Last Name is required";
    } else {
        $lastName = test_input($data["lastName"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $lastName)) {
            $errors['lastName'] = "Only letters and white spaces are allowed";
        }
    }

    // Validate Email
    if (empty($data["email"])) {
        $errors['email'] = "Email is required";
    } else {
        $email = test_input($data["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email format";
        }
    }

    // Validate Phone Number
    if (empty($data["phone"])) {
        $errors['phone'] = "Phone No. is required";
    } else {
        $phone = test_input($data["phone"]);
        if (!preg_match("/^[0-9]{10}$/", $phone)) {
            $errors['phone'] = "Phone number must be 10 digits";
        }
    }

    // Validate Address
    if (empty($data["address"])) {
        $errors['address'] = "Address is required";
    } else {
        $address = test_input($data["address"]);
    }

    // Validate Country
    if (empty($data["country"])) {
        $errors['country'] = "Must select a country";
    } else {
        $country = test_input($data["country"]);
    }

    // Validate State
    if (empty($data["states"])) {
        $errors['state'] = "Must select a state";
    } else {
        $state = test_input($data["states"]);
    }

    // Validate Pincode
    if (empty($data["pincode"])) {
        $errors['pincode'] = "Pincode is required";
    } else {
        $pincode = test_input($data["pincode"]);
    }

    // Validate Password
    if (empty($data["password"])) {
        $errors['password'] = "Password is required";
    } else {
        $password = test_input($data["password"]);
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
            $errors['password'] = "Password must be at least 8 characters, one letter, one digit, and one special character";
        }
    }

    // Validate Confirm Password
    if (empty($data["confirmPass"])) {
        $errors['confirmPass'] = "Confirm Password is required";
    } else {
        $confirmPass = test_input($data["confirmPass"]);
        if ($data['password'] !== $data['confirmPass']) {
            $errors['confirmPass'] = "Password did not match.";
        }
    }

    return $errors;
}

function test_input($data) {
    return htmlspecialchars(trim($data));
}
?>
