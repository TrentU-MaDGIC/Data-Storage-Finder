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
    $statusMessage = deleteCapability();
}

require_once("header.php");

if (!empty($statusMessage)) {
    echo "<span style='color: green;'>$statusMessage</span><br>";
}
?>

<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
    <div class="form-group">
        <p>&nbsp;</p>
        <?php getAllCapabilitiesForDelete(); ?>
        <input type="checkbox" id="deleteme" name="deleteme" value="1">&nbsp;
        <label for="deleteme">Yes, I am sure!</label>
    </div>
    <input class="btn btn-primary" type="submit" name="submit" value="Delete">
</form>

<?php
require_once("footer.php");
?>