<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dashboardStyle.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Add User</title>
</head>

<body>
    <?php
    include '../layout/navbar.php';
    session_start();
    $errors = $_SESSION["form_errors"] ?? [];
    unset($_SESSION["form_errors"]);
    ?>
    
    <div class="container">
        <h1>Enter User Details</h1>

        <form method="post" action="../../backend/addUserHandler.php">
            <div class="form_group">
                <label for="firstName">First name:</label>
                <input type="text" id="firstName" name="firstName"> 
                <span class="error"> <?= $errors['firstName'] ?? ''; ?> </span>
            </div>

            <div class="form_group">
                <label for="lastName">Last name:</label>
                <input type="text" id="lastName" name="lastName">
                <span class="error"> <?= $errors['lastName'] ?? ''; ?> </span>
            </div>

            <div class="form_group">
                <label for="email">Email :</label>
                <input type="text" id="email" name="email">
                <span class="error"> <?= $errors['email'] ?? ''; ?> </span>
            </div>

            <div class="form_group">
                <label for="phone">Phone No. :</label>
                <input type="text" id="phone" name="phone">
                <span class="error"> <?= $errors['phone'] ?? ''; ?> </span>
            </div>

            <div class="form_group">
                <label for="address">Address :</label>
                <textarea name="address" id="address"></textarea>
                <span class="error"> <?= $errors['address'] ?? ''; ?> </span>
            </div>

            <div class="form_group">
                <label for="country">Country :</label>
                <select name="country" id="country">
                    <option value="">Select Country</option>
                </select>
                <span class="error"> <?= $errors['country'] ?? ''; ?> </span>
            </div>

            <div class="form_group">
                <label for="state">State :</label>
                <select name="state" id="state">
                    <option value="">Select State</option>
                </select>
                <span class="error"> <?= $errors['state'] ?? ''; ?> </span>
            </div>

            <div class="form_group">
                <label for="pincode">Pincode :</label>
                <input type="text" name="pincode" id="pincode">
                <span class="error"> <?= $errors['pincode'] ?? ''; ?> </span>
            </div>

            <div class="form_group">
                <label for="password">Password :</label>
                <input type="password" id="password" name="password">
                <span class="error"> <?= $errors['password'] ?? ''; ?> </span>
            </div>

            <div class="form_group">
                <label for="confirmPass">Confirm Password :</label>
                <input type="password" id="confirmPass" name="confirmPass">
                <span class="error"> <?= $errors['confirmPass'] ?? '' ?> </span>
            </div>

            <div class="form_group">
                <button type="submit"> Add User</button>
            </div>
        </form>
        <div class="form_group">
            <a href="../dashboard.php"><button type="button" style="float: right;">Cancel</button></a>
        </div>
    </div>
</body>
</html>

<script src="../js/counrtyStateDropdown.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        countryStateDropdowns('country', 'state', '', '')
    });
</script>
