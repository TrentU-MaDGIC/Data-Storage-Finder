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
    $statusMessage = createCapabilities();
}

require_once("header.php");

if (!empty($statusMessage)) {
    echo "<span style='color: green;'>$statusMessage</span><br>";
}
?>

<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
    <div class="form-group">
        <label for="capability">Capability</label>
        <input type="text" name="capability" class="form-control" required>
        <p>&nbsp;</p>
        <?php getAllSectionsForCreate(); ?>
    </div>
    <input class="btn btn-primary" type="submit" name="submit" value="Add">
</form>

<?php
require_once("footer.php");
?>