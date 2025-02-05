Thank you for sharing the content. Here's how we can separate session management into a reusable file (`session.php`) that you can include in your other files like `editUser.php`, `addUser.php`, `login.php`, `dashboard.php`, etc.

### 1. **Create the session management file (`session.php`)**:
This file will handle the session start, check if the user is logged in, and redirect if necessary.

```php
<?php
// session.php

// Start session
session_start();

/**
 * Checks if the user is logged in.
 */
function checkLogin() {
    if (!isset($_SESSION['email']) || !isset($_SESSION['password'])) {
        echo "<script> alert('Please login first') </script>";
        header("Location: login.php");
        exit;
    }
}

/**
 * Redirects the user if already logged in (to avoid showing login page).
 */
function checkLoggedInRedirect() {
    if (isset($_SESSION['email']) && isset($_SESSION['password'])) {
        header("Location: dashboard.php");
        exit;
    }
}

/**
 * Logs the user out by destroying the session.
 */
function logout() {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
```

### 2. **How to use this session management file in other scripts**:

#### In `login.php`:
- Use the `checkLoggedInRedirect()` function to prevent logged-in users from seeing the login page.

```php
<?php
include '../config/dataBaseConnect.php';
include '../config/session.php'; // Include session management

checkLoggedInRedirect(); // Redirect if already logged in

// The rest of the login logic continues here...
?>
```

#### In `dashboard.php`, `addUser.php`, `editUser.php`, etc.:
- Use the `checkLogin()` function to ensure that the user is logged in before they can access these pages.

```php
<?php
include '../config/dataBaseConnect.php';
include '../config/session.php'; // Include session management

checkLogin(); // Check if the user is logged in, if not redirect to login page

// The rest of the page content continues here...
?>
```

### 3. **In `logout.php` (if you want to add a logout page)**:
You can use the `logout()` function to log out the user and redirect them to the login page.

```php
<?php
include '../config/session.php'; // Include session management

logout(); // Log out the user and redirect to login page
?>
```

### 4. **Adjust other scripts accordingly**:
Now that you have a `session.php` file, you can include it in other pages as needed. It will handle all session-related tasks, reducing redundancy and making your code easier to maintain.

Let me know if you need further adjustments!