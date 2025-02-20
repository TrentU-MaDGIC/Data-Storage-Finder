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

    if (isset($_POST['section'])) {
        $sectionId = $database->real_escape_string($_POST['section']);
    }
}

require_once("header.php");

if (!empty($statusMessage)) {
    echo "<span style='color: green;'>$statusMessage</span><br>";
}

if (isset($selectId)) {
    getSingleCapabilityForUpdate($selectId);
    ?>

    <form action="capabilities_update.php" method="POST">
        <div class="form-group">
            <p>&nbsp;</p>
            <?php getAllSectionsForSelection($selectId); ?>
            <br><label for="section">Place this capability under the above header.</label>
        </div>
        <br>
        <input type="hidden" name="saveId" value="<?php echo htmlspecialchars($selectId); ?>">
        <input class="btn btn-primary" type="submit" name="submit" value="Update">
    </form>

    <?php
} elseif (isset($sectionId) && isset($saveId)) {
    assignToSection($sectionId, $saveId);

    $statusMessage = "Capability header updated successfully! Just a moment...";
    $statusMessage .= "<meta http-equiv='refresh' content='3; url=capabilities_update.php' />";
    echo $statusMessage;
} elseif (isset($saveId)) {
    $statusMessage = updateSingleCapability($saveId);
    echo $statusMessage;
} else {
    getAllCapabilitiesForUpdate();
}

require_once("footer.php");
?>