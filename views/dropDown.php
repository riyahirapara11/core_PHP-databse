If you don't want to use AJAX, you can still dynamically populate the states dropdown based on the selected country by fetching both countries and states at the beginning (on page load) and submitting the form with all the necessary data. Here's how you can modify your form and logic to achieve this:

### 1. **PHP Logic to Fetch Countries and States**:

Modify the PHP code to fetch both countries and states in the same request. You can fetch all countries and their respective states on the form load.

**addUser.php**:

```php
<?php
include '../config/dataBaseConnect.php';

// Fetch countries
$query = "SELECT * FROM countries"; // Assuming you have a table for countries
$countriesResult = $connection->query($query);

// Fetch states based on the selected country if it's already posted
$statesResult = [];
if (isset($_POST['country'])) {
    $country = $_POST['country'];
    $queryStates = "SELECT * FROM states WHERE country_id = (SELECT id FROM countries WHERE name = '$country')";
    $statesResult = $connection->query($queryStates);
}

// Insert data into database
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submission (as you already did before)
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
        session_start();
        $_SESSION["add_message"] = "User Added Successfully!";
        header("Location: dashboard.php");
    } else {
        echo "Error inserting data: " . $connection->error;
    }

    // Close connection after inserting
    $connection->close();
}
?>
```

### 2. **HTML Form for Countries and States**:

Now, modify the HTML to populate the states dropdown based on the selected country. You can fetch the states when the country is selected using `POST`, and then display them on page load if a country is selected.

```php
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Add User</title>
</head>

<body>
    <h1>Enter User Details</h1>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">

        <!-- Other form fields -->

        <div class="form_group">
            <label for="country">Country :</label>
            <select name="country" id="selectCountry">
                <option value="">Select Country</option>
                <?php while ($row = $countriesResult->fetch_assoc()): ?>
                    <option value="<?= $row['name'] ?>" <?= isset($_POST['country']) && $_POST['country'] == $row['name'] ? 'selected' : '' ?>>
                        <?= $row['name'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <span class="error"><?php echo $countryErr; ?></span>
        </div>

        <div class="form_group">
            <label for="state">State :</label>
            <select name="states" id="selectStates">
                <option value="">Select State</option>
                <?php if (isset($_POST['country']) && $statesResult->num_rows > 0): ?>
                    <?php while ($state = $statesResult->fetch_assoc()): ?>
                        <option value="<?= $state['name'] ?>" <?= isset($_POST['states']) && $_POST['states'] == $state['name'] ? 'selected' : '' ?>>
                            <?= $state['name'] ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <span class="error"><?php echo $stateErr; ?></span>
        </div>

        <!-- Other form fields -->

        <div class="form_group">
            <button type="submit">Add User</button>
        </div>

    </form>
</body>

</html>
```

### Explanation:

1. **Countries Fetching**: On page load, we fetch all countries from the `countries` table and display them in the country dropdown.
   
2. **States Fetching**: When the user submits the form, it sends the selected country through the `POST` request. The states corresponding to the selected country are fetched and displayed dynamically in the state dropdown.

3. **Form Handling**: The form submits to the same page using `POST`. After submission, the states dropdown is populated with the states corresponding to the selected country.

4. **Selected Values**: After form submission, the selected country and state are retained, ensuring that the user sees their previous selections.

This solution avoids the use of AJAX and handles everything through regular form submission and server-side processing.




To make the country-state dropdown work dynamically based on your database, you should fetch the country and state data from your database and populate the dropdowns accordingly. Instead of using a static object for countries and states in the JavaScript, you will need to:

1. Fetch countries from the database and populate the country dropdown.
2. Fetch states based on the selected country using AJAX when the country changes.

Here’s how you can implement that:

### 1. PHP to fetch countries and states (Backend)

First, modify your PHP code to fetch the countries and states from the database.

**addUser.php**:

```php
<?php
include '../config/dataBaseConnect.php';

// Fetch countries
$query = "SELECT * FROM countries"; // Assuming you have a table for countries
$countriesResult = $connection->query($query);

// Fetch states based on selected country
$states = [];
if (isset($_GET['country'])) {
    $country = $_GET['country'];
    $queryStates = "SELECT * FROM states WHERE country_id = (SELECT id FROM countries WHERE name = '$country')";
    $statesResult = $connection->query($queryStates);
    while ($state = $statesResult->fetch_assoc()) {
        $states[] = $state;
    }
}

// Close connection after fetching data
$connection->close();
?>
```

### 2. Modify the HTML Form

In your form, populate the countries dropdown with the fetched data from the database:

```php
<select name="country" id="selectCountry">
    <option value="">Select Country</option>
    <?php while ($row = $countriesResult->fetch_assoc()): ?>
        <option value="<?= $row['name'] ?>" <?= isset($_POST['country']) && $_POST['country'] == $row['name'] ? 'selected' : '' ?>><?= $row['name'] ?></option>
    <?php endwhile; ?>
</select>
```

### 3. JavaScript for Dynamic States

To handle the states dynamically based on the selected country, you'll need to use AJAX. Here’s how you can do it:

```javascript
document.addEventListener('DOMContentLoaded', function() {
    const countrySelect = document.getElementById('selectCountry');
    const stateSelect = document.getElementById('selectStates');

    countrySelect.addEventListener('change', function() {
        const country = countrySelect.value;

        // If country is selected, fetch states
        if (country) {
            fetchStates(country);
        } else {
            stateSelect.innerHTML = '<option value="">Select State</option>';
            stateSelect.disabled = true;
        }
    });

    // Function to fetch states based on the selected country
    function fetchStates(country) {
        fetch(`getStates.php?country=${country}`)
            .then(response => response.json())
            .then(states => {
                stateSelect.innerHTML = '<option value="">Select State</option>';
                states.forEach(state => {
                    let option = document.createElement('option');
                    option.value = state.name;
                    option.textContent = state.name;
                    stateSelect.appendChild(option);
                });
                stateSelect.disabled = false;
            })
            .catch(error => console.error('Error fetching states:', error));
    }
});
```

### 4. Create `getStates.php` to handle AJAX requests

Create a new file `getStates.php` that will return the states as a JSON response.

```php
<?php
include '../config/dataBaseConnect.php';

if (isset($_GET['country'])) {
    $country = $_GET['country'];
    
    // Fetch states based on country
    $statesQuery = "SELECT * FROM states WHERE country_id = (SELECT id FROM countries WHERE name = '$country')";
    $statesResult = $connection->query($statesQuery);

    $states = [];
    while ($state = $statesResult->fetch_assoc()) {
        $states[] = $state;
    }

    echo json_encode($states);
} else {
    echo json_encode([]);
}

// Close the connection
$connection->close();
?>
```

### 5. Final Notes:

- Make sure your `countries` and `states` tables are correctly set up. The `states` table should have a `country_id` column that links to the `countries` table.
- The `getStates.php` script sends a JSON response containing all the states for the selected country.
- The JavaScript fetches the states when the country dropdown is changed, and it dynamically populates the state dropdown.

This approach will allow your country and state dropdowns to work dynamically with data coming from the database.