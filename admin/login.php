<?php

session_start();

require_once("../functions.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    userLogin();
}

require_once("header.php");
?>

<form action="login.php" method="POST">
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" name="username" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <input class="btn btn-primary" type="submit" name="submit" value="Submit">
</form>

<?php
require_once("footer.php");
?>