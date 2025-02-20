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
    $statusMessage = createSections();
}

require_once("header.php");

if (!empty($statusMessage)) {
    echo "<span style='color: green;'>$statusMessage</span><br>";
}
?>

<form action="section_create.php" method="POST">
    <div class="form-group">
        <label for="section">Section</label>
        <input type="text" name="section" class="form-control" required>
        <br>
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="3" cols="50" class="tinymce"></textarea>
        <i>Description</i> will appear when the user clicks the question mark icon next to the section question.<br><br>
    </div>
    <input class="btn btn-primary" type="submit" name="submit" value="Add">
</form>

<?php
require_once("footer.php");
?>