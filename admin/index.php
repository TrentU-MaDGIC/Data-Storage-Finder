<?php

session_start();

require_once("../functions.php");

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

require_once("header.php");

echo '<br><h3>Data Storage Finder Administration Menu</h3>';

$install_file = '../install.php';

if (file_exists($install_file)) {
    echo '<span style="color: red; font-weight: bold;">SECURITY RISK: Please delete your install.php file!</span>';
    echo '<p>&nbsp;</p>';
}
?>

<a href="section_create.php">Add section to navigation menu</a><br>
<a href="section_update.php">Update section on navigation menu</a><br>
<a href="section_delete.php">Delete section on navigation menu</a><br>
<a href="menu.php">Update navigation menu</a><br>
<i>Sections</i> represent the filter headers (e.g., <i>What is the purpose of data storage?</i>)<br>
<span style="color: red;">For new installs, do this <b>first</b>.</span><br><br>

<a href="capabilities_create.php">Add capability</a><br>
<a href="capabilities_update.php">Update capability</a><br>
<a href="capabilities_delete.php">Delete capability</a><br>
<i>Capabilities</i> represent the filter criteria when searching for storage (e.g., [I want to] Archive, Share files, etc.)<br>
<span style="color: red;">For new installs, do this <b>second</b>.</span><br><br>

<a href="field_types_create.php">Add field type</a><br>
<a href="field_types_update.php">Update field type</a><br>
<a href="field_types_menu.php">Reorder field types</a><br>
<a href="field_types_delete.php">Delete field type</a><br>
<i>Field types</i> represent the attributes for each item (e.g., Cost, Uptime, etc.)<br>
<span style="color: red;">For new installs, do this <b>third</b>.</span><br><br>

<a href="item_create.php">Add item</a><br>
<a href="item_update.php">Update item</a><br>
<a href="item_delete.php">Delete item</a><br>
<i>Items</i> represent your options for storage (e.g., AWS, SharePoint, etc.)<br>
<span style="color: red;">For new installs, do this <b>fourth</b>.</span><br><br>

<a href="user_create.php">Add user</a><br>
<a href="user_update.php">Update user</a><br>
<a href="user_delete.php">Delete user</a><br>
<i>Users</i> are people who can log into the system.<br>Currently all users are <b>administrators</b>.<br>
Visitors are not required to login.

<?php
require_once("footer.php");
?>