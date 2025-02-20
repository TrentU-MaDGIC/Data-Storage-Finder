<?php

session_start();

require_once("../functions.php");

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$statusMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $statusMessage = updateUser();
}

require_once("header.php");

if (!empty($statusMessage)) {
    echo "<span style='color: green;'>$statusMessage</span><br>";
}
?>

<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
    <div class="form-group">
        <?php getAllUsersForUpdate(); ?>
        <br>
        <label for="password">Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <input class="btn btn-primary" type="submit" name="submit" value="Update">
</form>

<?php
require_once("footer.php");
?>