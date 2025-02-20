<?php

session_start();

require_once("../functions.php");

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$statusMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['selectId'])) {
        $selectId = $database->real_escape_string($_POST['selectId']);
    }

    if (isset($_POST['saveId'])) {
        $saveId = $database->real_escape_string($_POST['saveId']);
    }
}

require_once("header.php");

if (isset($selectId)) {
    getSingleFieldTypeForUpdate($selectId);
} elseif (isset($saveId)) {
    $statusMessage = updateSingleFieldType($saveId);
    echo "<span style='color: green;'>$statusMessage</span><br>";
} else {
    getAllFieldTypesForUpdate();
}

require_once("footer.php");
?>