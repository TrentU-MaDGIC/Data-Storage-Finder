<?php

require_once("functions.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['newuser']) && !empty($_POST['newpass'])) {
        $newuser = $database->real_escape_string($_POST['newuser']);
        $newpass = $database->real_escape_string($_POST['newpass']);
        $newpass = crypt($newpass, SALT); // Ensure SALT is defined in config.php

        $query = "INSERT INTO users (username, password, isadmin) VALUES (?, ?, 1)";
        $stmt = $database->prepare($query);
        $stmt->bind_param('ss', $newuser, $newpass);

        if ($stmt->execute()) {
            echo "<p>User created successfully! You can now login to the Admin Menu with this user to create other users (if necessary). <span style='color: red; font-weight: bold;'>Now go delete this file (install.php)!</span></p>";
        } else {
            echo "<p>Error: User was not created.</p>";
        }

        $stmt->close();
    } else {
        echo "<p>Error: Please provide both username and password.</p>";
    }
}

echo '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DSF Initial Installation</title>
</head>
<body>

<p>&nbsp;</p>
<div align="center">
<form action="install.php" method="POST">

    <label for="newuser">Username: </label><input type="text" name="newuser" id="newuser"><br>
    <label for="newpass">Password: </label><input type="password" name="newpass" id="newpass"><br><br>
    <input type="submit" name="submit" value="Create User">

</form>
</div>
    
</body>
</html>
';


?>