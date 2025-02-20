<?php

session_start();

require_once '../functions.php';

// Redirect to login page if user is not authenticated
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$statusMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $statusMessage = createUser();
}

require_once 'header.php';

if ($statusMessage) {
    echo "<span style='color: green;'>$statusMessage</span><br>";
}
?>

<form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" class="form-control" required>
        <br>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>
    <input class="btn btn-primary" type="submit" name="submit" value="Add">
</form>

<?php
require_once 'footer.php';
?>