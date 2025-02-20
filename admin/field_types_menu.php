<?php

session_start();

require_once('../functions.php');

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
    $data = json_decode($_POST['data']);
    $counter = 1;
    foreach ($data as $val) {
        saveFieldTypesForMenu($val, $counter);
        $counter++;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Navigation Menu</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="field_types_menu.js"></script>
    <script>
        $(function() {
            $("#sortable").sortable();
            $("#sortable").disableSelection();
        });
    </script>
    <style>
        #sortable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
        #sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
        #sortable li span { position: absolute; margin-left: -1.3em; }
        #save-reorder { padding: 10px; }
           </style>
</head>
<body>
<div class="container">
    <br>
    These are the field types that are displayed on the main page when a user clicks an item.<br>
    Click and drag field types into the desired order.<br>
    If you make changes, do NOT forget to click SAVE!
    <p>&nbsp;</p>
    <ul id="sortable">
        <?php
        $data = getFieldTypesForMenu();
        foreach ($data as $record) {
            $recordId = htmlspecialchars($record['id']);
            $recordType = htmlspecialchars($record['type']);
            echo "<li data-id='$recordId' class='ui-state-default'><span class='ui-icon ui-icon-arrowthick-2-n-s'></span>$recordType</li>";
        }
        ?>
    </ul>
    <br>
    <button id="save-reorder">Save</button>
    <p>&nbsp;</p>
    <a href="index.php">Admin Main Menu</a><br>
    <a href="logout.php">Logout</a>
</div>
</body>
</html>