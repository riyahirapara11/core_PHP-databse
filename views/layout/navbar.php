<?php
// Define the base directory relative to the project root
$baseDir = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__ . '/../');

?>
<ul>
    <li><a href="<?php echo $baseDir; ?>views/dashboard.php" style="font-size: larger;">Dashboard</a></li>
    <li><a href="<?php echo $baseDir; ?>views/home.php">Home</a></li>
    <li><a href="<?php echo $baseDir; ?>views/about.php">About</a></li>
    <li><a href="<?php echo $baseDir; ?>views/contact.php">Contact</a></li>
    <li style="float:right">
        <a href="<?php echo $baseDir; ?>views/logout.php" onclick="return confirm('Are You Sure You Want to Log OUT ?')">Logout</a>
    </li>
</ul>
