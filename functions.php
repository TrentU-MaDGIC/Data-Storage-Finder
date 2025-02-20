<?php

/*
This file contains all the functions that are used for the application.
The code is written to be simple enough for a beginner to understand.
If you find ways to optimize the code, please submit a pull request.
If you find a bug, please submit an issue.
*/

require_once("config.php");

class Database {
    public $connection;

    public function __construct() {
        $this->openDbConnection();
    }

    public function openDbConnection() {
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->connection->connect_errno) {
            die("Database connection failed: " . $this->connection->connect_error);
        }
    }

    public function query($sql) {
        $result = $this->connection->query($sql);
        $this->confirmQuery($result);
        return $result;
    }

    public function prepare($sql) {
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            die("Database prepare failed: " . $this->connection->error);
        }
        return $stmt;
    }

    private function confirmQuery($result) {
        if (!$result) {
            die("Database query failed: " . $this->connection->error);
        }
    }

    public function real_escape_string($string) {
        return $this->connection->real_escape_string($string);
    }

    public function insertId() {
        return $this->connection->insert_id;
    }
}

$database = new Database();
$database->openDbConnection();


function getAllItemsForUpdate() {

	global $database;

	$query = "SELECT * FROM items ORDER BY item_name ASC";
	$result = $database->query($query);

	echo '<p>&nbsp;</p>';
	echo '<form action="item_update.php" method="POST">';
	echo '<select name="selectId">';

	while ($row = $result->fetch_assoc()) {
		$id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
		$item_name = htmlspecialchars($row['item_name'], ENT_QUOTES, 'UTF-8');
	 	echo "<option value='$id'>$item_name</option>";
	}

	echo '</select><br><input type="submit" value="Select"></form>';

}

function getAllItemsForDelete() {

	global $database;

	$query = "SELECT * FROM items";
	$result = $database->query($query);

	echo '<select name="item_name" id="item_name">';

	while ($row = $result->fetch_assoc()) {
		$id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
		$item_name = htmlspecialchars($row['item_name'], ENT_QUOTES, 'UTF-8');
	 	echo "<option value='$id'>$item_name</option>";
	}

	echo '</select>';

}

function createItem() {
    global $database;

    // Escape user inputs for security
    $item_name = $database->real_escape_string($_POST['item_name']);
    $item_tagline = $database->real_escape_string($_POST['item_tagline']);

    // Insert item into the items table
    $query = "INSERT INTO items (item_name, tagline) VALUES ('$item_name', '$item_tagline')";
    $result = $database->query($query);
    $item_id = $database->insertId();

    // Fetch all field types
    $query_all_field_types = "SELECT id FROM field_types";
    $result_all_field_types = $database->query($query_all_field_types);

    // Insert item fields for each field type
    while ($row = $result_all_field_types->fetch_assoc()) {
        $field_type_id = $row['id'];
        $query_insert_item_fields = "INSERT INTO item_fields (item_id, field_type, field_text) VALUES ($item_id, $field_type_id, '')";
        $result_insert_item_fields = $database->query($query_insert_item_fields);
    }

    // Check if the last insert was successful
    if ($result_insert_item_fields) {
        $statusMessage = "Item created successfully!";
    } else {
        $statusMessage = "Error: Item not created!";
    }

    return $statusMessage;
}

function deleteItem() {
    global $database;

    // Escape user input for security
    $id = $database->real_escape_string($_POST['item_name']);

    if (filter_has_var(INPUT_POST, 'deleteme')) {
        // Delete item from items table
        $query = "DELETE FROM items WHERE id='$id'";
        $result = $database->query($query);

        // Delete related item fields
        $query_delete_item_fields = "DELETE FROM item_fields WHERE item_id=$id";
        $result_delete_item_fields = $database->query($query_delete_item_fields);

        // Delete related capabilities fields
        $query_delete_capabilities_fields = "DELETE FROM capabilities_fields WHERE item_id=$id";
        $result_delete_capabilities_fields = $database->query($query_delete_capabilities_fields);
    }

    // Check if the last delete operation was successful
    if (isset($result_delete_capabilities_fields)) {
        $statusMessage = "Item deleted successfully!";
    } else {
        $statusMessage = "Error: Item not deleted!";
    }

    return $statusMessage;
}

function getAllFieldTypesForUpdate() {
    global $database;

    $query = "SELECT * FROM field_types ORDER BY display_order ASC";
    $result = $database->query($query);

    echo '<p>&nbsp;</p>';
    echo '<form action="field_types_update.php" method="POST">';
    echo '<select name="selectId">';

    while ($row = $result->fetch_assoc()) {
        $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
        $type = htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8');
        echo "<option value='$id'>$type</option>";
    }

    echo '</select><br><input type="submit" value="Select"></form>';
}

function getAllFieldTypesForDelete() {
    global $database;

    $query = "SELECT * FROM field_types";
    $result = $database->query($query);

    echo '<select name="type" id="type">';

    while ($row = $result->fetch_assoc()) {
        $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
        $type = htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8');
        echo "<option value='$id'>$type</option>";
    }

    echo '</select>';
}

function getSingleItemForUpdate($selectId) {
    global $database;

    echo '<form action="item_update.php" method="POST">';
    echo '<div class="form-group">';

    // Fetch and display item tagline
    $query_tagline = "SELECT tagline FROM items WHERE id = $selectId";
    $result_tagline = $database->query($query_tagline);

    while ($row0 = $result_tagline->fetch_assoc()) {
        $item_tagline = htmlspecialchars($row0['tagline'], ENT_QUOTES, 'UTF-8');
        echo "<h1>Subtitle / Tagline</h1>";
        echo '<textarea id="' . $selectId . '" name="' . $selectId . '" rows="1" cols="1" class="form-control">';
        echo $item_tagline;
        echo '</textarea>';
    }

    // Fetch and display item fields
    $query = "SELECT * FROM item_fields WHERE item_id = $selectId";
    $result = $database->query($query);

    while ($row = $result->fetch_assoc()) {
        $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
        $field_type = htmlspecialchars($row['field_type'], ENT_QUOTES, 'UTF-8');
        $field_text = htmlspecialchars($row['field_text'], ENT_QUOTES, 'UTF-8');

        $query_field_type = "SELECT * FROM field_types WHERE id = $field_type";
        $result2 = $database->query($query_field_type);

        while ($row2 = $result2->fetch_assoc()) {
            $field_type2 = htmlspecialchars($row2['type'], ENT_QUOTES, 'UTF-8');
            echo "<h1>$field_type2</h1>";
            echo '<textarea id="' . $id . '" name="' . $id . '" rows="3" cols="1" class="tinymce">';
            echo $field_text;
            echo '</textarea>';
        }
    }

    // Fetch and display sections with capabilities
    $query_sections = "SELECT id, name, description FROM sections ORDER BY display_order";
    $result_sections = $database->query($query_sections);

    while ($row1 = $result_sections->fetch_assoc()) {
        $section_id = htmlspecialchars($row1['id'], ENT_QUOTES, 'UTF-8');
        $section_name = htmlspecialchars($row1['name'], ENT_QUOTES, 'UTF-8');
        $section_description = htmlspecialchars($row1['description'], ENT_QUOTES, 'UTF-8');

        echo "<br><span style='font-size: 14px; font-family: Arial;'><b>$section_name</b></span><br>";

        $query_capabilities = "SELECT * FROM capabilities WHERE nav_menu_section = $section_id";
        $result_capabilities = $database->query($query_capabilities);

        while ($row = $result_capabilities->fetch_assoc()) {
            $id_checked = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
            $capability_checked = htmlspecialchars($row['capability'], ENT_QUOTES, 'UTF-8');

            $query_checked = "SELECT id FROM capabilities_fields WHERE item_id = '$selectId' AND capability_id = '$id_checked'";
            $result_checked = $database->query($query_checked);

            $checked_row = $result_checked->fetch_row();

            if (isset($checked_row)) {
                echo "<input type='checkbox' name='capabilities[]' value='$id_checked' checked>";
                echo "&nbsp;<label for='capabilities'>$capability_checked</label><br>";
            } else {
                echo "<input type='checkbox' name='capabilities[]' value='$id_checked'>";
                echo "&nbsp;<label for='capabilities'>$capability_checked</label><br>";
            }
        }
    }

    echo '</div><br>';
    echo '<input type="hidden" id="saveId" name="saveId" value="' . htmlspecialchars($selectId, ENT_QUOTES, 'UTF-8') . '">';
    echo '<input class="btn btn-primary" type="submit" name="submit" value="Save">';
    echo '</form>';
}

function getSingleFieldTypeForUpdate($editId) {
    global $database;

    $query = "SELECT * FROM field_types WHERE id = $editId ORDER BY display_order ASC";
    $result = $database->query($query);

    echo '<form action="field_types_update.php" method="POST">';
    echo '<div class="form-group">';

    while ($row = $result->fetch_assoc()) {
        $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
        $type = htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8');

        echo "<h1>$type</h1>";
        echo '<input type="text" id="type" name="type" value="' . $type . '" class="form-control">';
    }

    echo '</div>';
    echo '<input type="hidden" id="saveId" name="saveId" value="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '">';
    echo '<input class="btn btn-primary" type="submit" name="submit" value="Save">';
    echo '</form>';
}

function createFieldTypes() {
    global $database;

    // Escape user input for security
    $field_type = $database->real_escape_string($_POST['field_type']);

    // Insert new field type into field_types table
    $query = "INSERT INTO field_types (type, display_order) VALUES ('$field_type', 1)";
    $result = $database->query($query);
    $field_type_id = $database->insertId();

    // Fetch all items
    $query_all_items = "SELECT id FROM items";
    $result_all_items = $database->query($query_all_items);

    // Insert new field type for each item
    while ($row = $result_all_items->fetch_assoc()) {
        $item_id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
        $insert_all_query = "INSERT INTO item_fields (item_id, field_type, field_text) VALUES ($item_id, $field_type_id, '')";
        $result_all_query = $database->query($insert_all_query);
    }

    // Check if the last insert operation was successful
    if ($result_all_query) {
        $statusMessage = "Field type created successfully!";
    } else {
        $statusMessage = "Error: Field type not created!";
    }

    return $statusMessage;
}

function deleteFieldType() {
    global $database;

    // Escape user input for security
    $id = $database->real_escape_string($_POST['type']);

    if (filter_has_var(INPUT_POST, 'deleteme')) {
        // Delete field type from field_types table
        $query = "DELETE FROM field_types WHERE id='$id'";
        $result = $database->query($query);

        // Delete related item fields
        $query_delete_item_fields = "DELETE FROM item_fields WHERE field_type=$id";
        $result_delete_item_fields = $database->query($query_delete_item_fields);
    }

    // Check if the last delete operation was successful
    if (isset($result_delete_item_fields)) {
        $statusMessage = "Field type deleted successfully!";
    } else {
        $statusMessage = "Error: Field type not deleted!";
    }

    return $statusMessage;
}

function updateSingleItem($saveId) {
    global $database;

    foreach ($_POST as $key => $value) {
        if (!is_array($value) && $value != "Save" && $key != "saveId") {
            $value = $database->real_escape_string($value);

            // Update item fields
            $query = "UPDATE item_fields SET field_text='$value' WHERE id='$key'";
            $result = $database->query($query);

            // Update item tagline
            $query_tagline = "UPDATE items SET tagline='$value' WHERE id='$key'";
            $result_tagline = $database->query($query_tagline);
        }
    }

    // Clear existing capabilities
    $query_clear_capabilities = "DELETE FROM capabilities_fields WHERE item_id='$saveId'";
    $result_clear_capabilities = $database->query($query_clear_capabilities);

    // Insert new capabilities
    foreach ($_POST['capabilities'] as $checkedValue) {
        $query_insert_capabilities_fields = "INSERT INTO capabilities_fields (item_id, capability_id) VALUES ('$saveId', '$checkedValue')";
        $result_insert_capabilities_fields = $database->query($query_insert_capabilities_fields);
    }

    // Check if the last insert operation was successful
    if ($result_insert_capabilities_fields) {
        $statusMessage = "Item updated successfully! Just a moment...";
        $statusMessage .= "<meta http-equiv='refresh' content='3; url=item_update.php' />";
    } else {
        $statusMessage = "Error: Item not updated!";
    }

    return $statusMessage;
}

function updateSingleFieldType($saveId) {
    global $database;

    // Escape user input for security
    $post_type = $database->real_escape_string($_POST['type']);

    // Update field type
    $query = "UPDATE field_types SET type='$post_type' WHERE id=$saveId";
    $result = $database->query($query);

    // Check if the update operation was successful
    if ($result) {
        $statusMessage = "Field type updated successfully! Just a moment...";
        $statusMessage .= "<meta http-equiv='refresh' content='3; url=field_types_update.php' />";
    } else {
        $statusMessage = "Error: Field type not updated!";
    }

    return $statusMessage;
}

function createUser() {
    global $database;

    // Escape user inputs for security
    $username = $database->real_escape_string($_POST['username']);
    $password = $database->real_escape_string($_POST['password']);

    // Hash the password using a salt
    $password = crypt($password, SALT); // get SALT from config.php

    // Insert new user into users table
    $query = "INSERT INTO users (username, password, isadmin) VALUES ('$username', '$password', 1)";
    $result = $database->query($query);

    // Check if the insert operation was successful
    if ($result) {
        return "User created successfully!";
    } else {
        return "Error: User not created!";
    }
}

function deleteUser() {
    global $database;

    // Escape user input for security
    $id = $database->real_escape_string($_POST['username']);

    if (filter_has_var(INPUT_POST, 'deleteme')) {
        // Delete user from users table
        $query = "DELETE FROM users WHERE id='$id'";
        $result = $database->query($query);

        // Check if the delete operation was successful
        if ($result) {
            return "User deleted successfully!";
        } else {
            return "Error: User not deleted!";
        }
    }
}

function updateUser() {
    global $database;

    // Escape user inputs for security
    $id = $database->real_escape_string($_POST['username']);
    $password = $database->real_escape_string($_POST['password']);

    // Hash the password using a salt
    $password = crypt($password, SALT); // get SALT from config.php

    // Update user password
    $query = "UPDATE users SET password='$password' WHERE id='$id'";
    $result = $database->query($query);

    // Check if the update operation was successful
    if ($result) {
        return "User updated successfully!";
    } else {
        return "Error: User not updated!";
    }
}

function userLogin() {
    global $database;

    // Escape user inputs for security
    $username = $database->real_escape_string($_POST['username']);
    $password = $database->real_escape_string($_POST['password']);

    // Hash the password using a salt
    $password = crypt($password, SALT); // get SALT from config.php

    // Query to check user credentials
    $query = "SELECT id FROM users WHERE username='$username' AND password='$password'";
    $result = $database->query($query);

    // Check if user exists
    if ($result->num_rows > 0) {
        $_SESSION['user'] = $username;
        header("Location: index.php");
        exit();
    } else {
        echo "Login failed";
    }
}

function getAllUsersForUpdate() {
    global $database;

    $query = "SELECT * FROM users";
    $result = $database->query($query);

    echo '<select name="username" id="username">';

    while ($row = $result->fetch_assoc()) {
        $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
        $username = htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8');
        echo "<option value='$id'>$username</option>";
    }

    echo '</select>&nbsp;';
}

function createCapabilities() {
    global $database;

    // Escape user input for security
    $capability = $database->real_escape_string($_POST['capability']);

    $section = null;
    if (isset($_POST['section'])) {
        $section = $database->real_escape_string($_POST['section']);
    }

    // Insert new capability into capabilities table
    $query = "INSERT INTO capabilities (capability, nav_menu_section) VALUES ('$capability', '$section')";
    $result = $database->query($query);

    // Check if the insert operation was successful
    if ($result) {
        $statusMessage = "Capability created successfully!";
    } else {
        $statusMessage = "Error: Capability not created!";
    }

    return $statusMessage;
}

function getSingleCapabilityForUpdate($editId) {
    global $database;

    $query = "SELECT * FROM capabilities WHERE id = $editId";
    $result = $database->query($query);

    echo '<form action="capabilities_update.php" method="POST">';
    echo '<div class="form-group">';

    while ($row = $result->fetch_assoc()) {
        $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
        $capability = htmlspecialchars($row['capability'], ENT_QUOTES, 'UTF-8');

        echo "<h1>$capability</h1>";
        echo '<textarea id="capability" name="capability" rows="3" cols="1" class="form-control">';
        echo $capability;
        echo '</textarea>';
    }

    echo '</div>';
    echo '<input type="hidden" id="saveId" name="saveId" value="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '">';
    echo '<input class="btn btn-primary" type="submit" name="submit" value="Save">';
    echo '</form>';
}

function updateSingleCapability($saveId) {
    global $database;

    // Escape user input for security
    $capability = $database->real_escape_string($_POST['capability']);

    // Update capability
    $query = "UPDATE capabilities SET capability='$capability' WHERE id='$saveId'";
    $result = $database->query($query);

    // Check if the update operation was successful
    if ($result) {
        $statusMessage = "Capability updated successfully! Just a moment...";
        $statusMessage .= "<meta http-equiv='refresh' content='3; url=capabilities_update.php' />";
    } else {
        $statusMessage = "Error: Capability not updated!";
    }

    return $statusMessage;
}

function getAllCapabilitiesForUpdate() {
    global $database;

    $query = "SELECT * FROM capabilities";
    $result = $database->query($query);

    echo '<p>&nbsp;</p>';
    echo '<form action="capabilities_update.php" method="POST">';
    echo '<select name="selectId">';

    while ($row = $result->fetch_assoc()) {
        $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
        $capability = htmlspecialchars($row['capability'], ENT_QUOTES, 'UTF-8');
        echo "<option value='$id'>$capability</option>";
    }

    echo '</select><br><input type="submit" value="Select"></form>';
}

function getAllCapabilitiesForDelete() {
    global $database;

    $query = "SELECT * FROM capabilities";
    $result = $database->query($query);

    echo '<select name="capability" id="capability">';

    while ($row = $result->fetch_assoc()) {
        $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
        $capability = htmlspecialchars($row['capability'], ENT_QUOTES, 'UTF-8');
        echo "<option value='$id'>$capability</option>";
    }

    echo '</select>';
}

function deleteCapability() {
    global $database;

    // Escape user input for security
    $id = $database->real_escape_string($_POST['capability']);

    if (filter_has_var(INPUT_POST, 'deleteme')) {
        // Delete capability from capabilities table
        $query = "DELETE FROM capabilities WHERE id='$id'";
        $result = $database->query($query);
    }

    // Check if the delete operation was successful
    if (isset($result)) {
        $statusMessage = "Capability deleted successfully!";
    } else {
        $statusMessage = "Error: Capability not deleted!";
    }

    return $statusMessage;
}

function createSections() {
    global $database;

    // Escape user inputs for security
    $section = $database->real_escape_string($_POST['section']);
    $description = $database->real_escape_string($_POST['description']);

    // Get the maximum display order
    $query_max = "SELECT MAX(display_order) AS max_display_order FROM sections";
    $result_max = $database->query($query_max);

    $row = $result_max->fetch_array();
    $max_display_order = $row['max_display_order'] + 1;

    // Insert new section into sections table
    $query = "INSERT INTO sections (name, description, display_order) VALUES ('$section', '$description', '$max_display_order')";
    $result = $database->query($query);

    // Check if the insert operation was successful
    if ($result) {
        $statusMessage = "Section created successfully!";
    } else {
        $statusMessage = "Error: Section not created!";
    }

    return $statusMessage;
}

function deleteSection() {
    global $database;

    // Escape user input for security
    $id = $database->real_escape_string($_POST['section']);

    if (filter_has_var(INPUT_POST, 'deleteme')) {
        // Delete section from sections table
        $query = "DELETE FROM sections WHERE id='$id'";
        $result = $database->query($query);

        // Reset capabilities associated with the deleted section
        $query_reset_capability = "UPDATE capabilities SET nav_menu_section=0 WHERE nav_menu_section=$id";
        $result_reset_capability = $database->query($query_reset_capability);
    }

    // Check if the reset operation was successful
    if (isset($result_reset_capability)) {
        $statusMessage = "Section deleted successfully!";
    } else {
        $statusMessage = "Error: Section not deleted!";
    }

    return $statusMessage;
}

function getAllSectionsForDelete() {
    global $database;

    $query = "SELECT * FROM sections";
    $result = $database->query($query);

    echo '<select name="section" id="section">';

    while ($row = $result->fetch_assoc()) {
        $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
        $section = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
        echo "<option value='$id'>$section</option>";
    }

    echo '</select>';
}

function getSingleSectionForUpdate($editId) {
    global $database;

    $query = "SELECT * FROM sections WHERE id = $editId";
    $result = $database->query($query);

    echo '<form action="section_update.php" method="POST">';
    echo '<div class="form-group">';

    while ($row = $result->fetch_assoc()) {
        $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
        $section = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8');

        echo "<h1>Section Title</h1>";
        echo '<textarea id="section" name="section" rows="3" cols="1" class="form-control">';
        echo $section;
        echo '</textarea>';

        echo "<h1>Description</h1>";
        echo '<textarea id="description" name="description" rows="3" cols="1" class="tinymce">';
        echo $description;
        echo '</textarea>';
    }

    echo '</div>';
    echo '<input type="hidden" id="saveId" name="saveId" value="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '">';
    echo '<input class="btn btn-primary" type="submit" name="submit" value="Save">';
    echo '</form>';
}

function updateSingleSection($saveId) {
    global $database;

    // Escape user inputs for security
    $section = $database->real_escape_string($_POST['section']);
    $description = $database->real_escape_string($_POST['description']);

    // Update section details
    $query = "UPDATE sections SET name='$section', description='$description' WHERE id='$saveId'";
    $result = $database->query($query);

    // Check if the update operation was successful
    if ($result) {
        $statusMessage = "Section updated successfully! Just a moment...";
        $statusMessage .= "<meta http-equiv='refresh' content='3; url=section_update.php' />";
    } else {
        $statusMessage = "Error: Section not updated!";
    }

    return $statusMessage;
}

function getAllSectionsForUpdate() {
    global $database;

    $query = "SELECT * FROM sections";
    $result = $database->query($query);

    echo '<p>&nbsp;</p>';
    echo '<form action="section_update.php" method="POST">';
    echo '<select name="selectId">';

    while ($row = $result->fetch_assoc()) {
        $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
        $section = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
        echo "<option value='$id'>$section</option>";
    }

    echo '</select><br><input type="submit" value="Select"></form>';
}

function getAllSectionsForSelection($editId) {
    global $database;

    // Fetch current nav_menu_section for the given capability
    $query1 = "SELECT nav_menu_section FROM capabilities WHERE id='$editId'";
    $result1 = $database->query($query1);

    $row1 = $result1->fetch_array();
    $current_nav_menu_section = $row1['nav_menu_section'];

    $additional_option = '';
    if (isset($current_nav_menu_section)) {
        // Fetch the current section details
        $query2 = "SELECT id, name FROM sections WHERE id='$current_nav_menu_section'";
        $result2 = $database->query($query2);

        while ($row2 = $result2->fetch_assoc()) {
            $id2 = htmlspecialchars($row2['id'], ENT_QUOTES, 'UTF-8');
            $section2 = htmlspecialchars($row2['name'], ENT_QUOTES, 'UTF-8');
            $additional_option = "<option value='$id2'>$section2</option>";
        }
    }

    // Fetch all sections
    $query3 = "SELECT id, name FROM sections";
    $result3 = $database->query($query3);

    echo '<select name="section" id="section">';

    if (!empty($additional_option)) {
        echo $additional_option;
    }

    while ($row3 = $result3->fetch_assoc()) {
        $id3 = htmlspecialchars($row3['id'], ENT_QUOTES, 'UTF-8');
        $section3 = htmlspecialchars($row3['name'], ENT_QUOTES, 'UTF-8');
        if ($id3 != $id2) {
            echo "<option value='$id3'>$section3</option>";
        }
    }

    echo '</select>';
}

function getAllSectionsForCreate() {
    global $database;

    $current_nav_menu_section = null;
    if (isset($_POST['section'])) {
        $current_nav_menu_section = $database->real_escape_string($_POST['section']);
    }

    $additional_option = '';
    if ($current_nav_menu_section) {
        $query = "SELECT id, name FROM sections WHERE id = '$current_nav_menu_section'";
        $result = $database->query($query);

        if ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $name = $row['name'];
            $additional_option = "<option value='$id' selected='selected'>$name</option>";
        }
    }

    $query = "SELECT id, name FROM sections";
    $result = $database->query($query);

    echo '<select name="section" id="section">';
    if ($additional_option) {
        echo $additional_option;
    }

    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $name = $row['name'];
        echo "<option value='$id'>$name</option>";
    }
    echo '</select>';
}

function assignToSection($sectionId, $saveId) {
    global $database;

    $query = "UPDATE capabilities SET nav_menu_section = ? WHERE id = ?";
    $stmt = $database->prepare($query);
    $stmt->bind_param('ii', $sectionId, $saveId);
    $stmt->execute();
    $stmt->close();
}

function buildNavMenu() {
    global $database;

    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>';

    echo '
    <script type="text/javascript">
        $(parent.document).ready(function () {
            $("div.tags").on("click", "input:checkbox", function () {
                var rel = $(this).attr("rel");
                var labels = $(".cont-checkbox > label." + rel, window.parent.document);
                labels.css({"font-weight": "", "color": "", "border": ""});

                if ($(this).is(":checked")) {
                    labels.css({"font-weight": "900", "color": "black", "border": "1px solid #003300"});
                }

                if (!$("div.tags").find("input:checked").length) {
                    $(".cont-checkbox > label", window.parent.document).css({"font-weight": "", "color": "", "border": ""});
                }
            });
        });
    </script>
    ';

    $query_sections = "SELECT id, name, description FROM sections ORDER BY display_order";
    $result_sections = $database->query($query_sections);

    $popupCounter = 1;

    while ($row1 = $result_sections->fetch_assoc()) {
        $section_id = $row1['id'];
        $section_name = $row1['name'];
        $section_description = $row1['description'];

        echo "<span style='font-size: 14px; font-family: Arial;'><b>$section_name</b>&nbsp;&nbsp;<button class='open$popupCounter' style='border-radius: 25px; cursor: pointer; font-weight: bold;'>i</button></span><br>";

        echo "
        <script type='text/javascript'>
        $(document).ready(function () {
            $('.open$popupCounter').on('click', function() {
                $('.popup-overlay$popupCounter, .popup-content$popupCounter').addClass('active');
            });

            $('.close$popupCounter, .popup-overlay$popupCounter').on('click', function() {
                $('.popup-overlay$popupCounter, .popup-content$popupCounter').removeClass('active');
            });
        });
        </script>
        ";

        echo "
        <div class='popup-overlay$popupCounter'>
            <div class='popup-content$popupCounter'>
                <p>$section_description</p>
                <button class='close$popupCounter'>close</button><br/>
            </div>
        </div>
        ";

        $popupCounter++;

        echo "<div class='tags' style='font-size: 12px; font-family: Arial;'>";

        $query_capabilities = "SELECT * FROM capabilities WHERE nav_menu_section = $section_id";
        $result_capabilities = $database->query($query_capabilities);

        while ($row2 = $result_capabilities->fetch_assoc()) {
            $capability_id = $row2['id'];
            $capability_name = $row2['capability'];
            $capability_name_formatted = strtolower(str_replace([" ", "/", "-", "(", ")", ",", "."], "_", $capability_name));

            echo "<input type='checkbox' rel='$capability_name_formatted' value='$capability_id'>&nbsp;";
            echo "<label for='capability'>$capability_name</label><br>";
        }

        echo '</div><br>';
    }
}

function populateResults() {
    global $database;

    echo '
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $(".cont-checkbox label").on("click", function (event) {
                var $check = $(":checkbox", this);
                $check.prop("checked", !$check.prop("checked"));
                $(".flex-container").remove();
            });

            $(".cont-checkbox :checkbox").on("click", function (event) {
                event.stopPropagation();
                var selected = $("input:checkbox:checked").map(function() {
                    return $(this).val();
                }).get();

                $.post("panel.php", { ids: selected }, function(response) {
                    $(".panel").after(response);
                    $(".panel").slideToggle("slow");
                });
            });
        });
    </script>
    ';

    echo '
    <div class="cont-bg">    
        <div class="cont-main">
    ';

    $chkBoxCounter = 1;

    $query_items = "SELECT * FROM items ORDER BY item_name ASC";
    $result_items = $database->query($query_items);

    while ($row1 = $result_items->fetch_assoc()) {
        $item_id = $row1['id'];
        $item_name = $row1['item_name'];
        $item_tagline = $row1['tagline'];

        $query_capabilities_fields = "SELECT capability_id FROM capabilities_fields WHERE item_id = $item_id";
        $result_capabilities_fields = $database->query($query_capabilities_fields);

        $capability_rel_string = "";

        while ($row2 = $result_capabilities_fields->fetch_assoc()) {
            $capability_field_id = $row2['capability_id'];

            $query_capabilities = "SELECT capability FROM capabilities WHERE id = $capability_field_id";
            $result_capabilities = $database->query($query_capabilities);

            $capability = $result_capabilities->fetch_row();
            $capability_name = $capability[0];

            $capability_name_formatted = strtolower(str_replace([" ", "/", "-", "(", ")", ",", "."], "_", $capability_name));
            $capability_rel_string .= $capability_name_formatted . " ";
        }

        echo "
        <div class='cont-checkbox'>
            <input type='checkbox' value='$item_id' id='serviceCheckbox$chkBoxCounter'>
            <label for='serviceCheckbox$chkBoxCounter' class='$capability_rel_string'>
                <img src='images/unchecked.png' width='150'>
                <span class='cover-checkbox'>
                    <svg viewBox='0.5 1 12 12'>
                        <polyline points='1.5 6 4.5 9 10.5 1'></polyline>
                    </svg>
                </span>
                <div class='info'>$item_name</div>
                <div class='infoSubTitle'>$item_tagline</div>
            </label>
        </div>
        ";

        $chkBoxCounter++;
    }

    echo '</div>';
}

function panelQuery() {
    global $database;

    if (isset($_POST['ids'])) {
        echo '<div class="flex-container">';

        foreach ($_POST['ids'] as $post_id) {
            if (!isset($post_id)) {
                continue;
            }

            $query_items = "SELECT * FROM items WHERE id = ?";
            $stmt_items = $database->prepare($query_items);
            $stmt_items->bind_param('i', $post_id);
            $stmt_items->execute();
            $result_items = $stmt_items->get_result();

            while ($row1 = $result_items->fetch_assoc()) {
                $item_id = $row1['id'];
                $item_name = $row1['item_name'];

                echo '<table id="compare-tbl">';
                echo '<thead>';
                echo '<th>' . htmlspecialchars($item_name) . '</th>';
                echo '</thead>';
                echo '<tbody>';

                $query_item_fields = "
                    SELECT * FROM item_fields 
                    INNER JOIN field_types ON field_type = field_types.id 
                    WHERE item_id = ? 
                    ORDER BY field_types.display_order ASC
                ";
                $stmt_item_fields = $database->prepare($query_item_fields);
                $stmt_item_fields->bind_param('i', $item_id);
                $stmt_item_fields->execute();
                $result_item_fields = $stmt_item_fields->get_result();

                $divNum = 1;
                while ($row2 = $result_item_fields->fetch_assoc()) {
                    $field_type = $row2['field_type'];
                    $field_text = $row2['field_text'] ?: "<p>undefined</p>";

                    echo "<tr valign='top'>";

                    $query_field_type = "SELECT type FROM field_types WHERE id = ?";
                    $stmt_field_type = $database->prepare($query_field_type);
                    $stmt_field_type->bind_param('i', $field_type);
                    $stmt_field_type->execute();
                    $result_field_type = $stmt_field_type->get_result();

                    while ($row3 = $result_field_type->fetch_assoc()) {
                        $field_name = $row3['type'];

                        echo "<td><div class='div$divNum'><span style='color: green; font-weight: bold;'>$field_name:</span> $field_text</div></td>";

                        echo "<script type='text/javascript'>
                            var heights = $('div.div$divNum').map(function () {
                                return $(this).height();
                            }).get();
                            var maxHeight = Math.max.apply(null, heights);
                            $('#compare-tbl div.div$divNum').css('height', maxHeight);
                        </script>";

                        $divNum++;
                    }

                    echo "</tr>";
                }

                echo '</tbody>';
                echo '</table>';
            }

            $stmt_items->close();
        }

        echo '</div>';
    }
}

function allServices() {
    global $database;

    $query_items = "SELECT * FROM items";
    $result_items = $database->query($query_items);

    while ($row1 = $result_items->fetch_assoc()) {
        $item_id = $row1['id'];
        $item_name = htmlspecialchars($row1['item_name']);

        echo "<h3>$item_name</h3>";

        $query_item_fields = "
            SELECT * FROM item_fields 
            INNER JOIN field_types ON field_type = field_types.id 
            WHERE item_id = ? 
            ORDER BY field_types.display_order ASC
        ";
        $stmt_item_fields = $database->prepare($query_item_fields);
        $stmt_item_fields->bind_param('i', $item_id);
        $stmt_item_fields->execute();
        $result_item_fields = $stmt_item_fields->get_result();

        while ($row2 = $result_item_fields->fetch_assoc()) {
            $field_type = $row2['field_type'];
            $field_text = $row2['field_text'] ?: "<p>&nbsp;</p>";

            $query_field_type = "SELECT type FROM field_types WHERE id = ?";
            $stmt_field_type = $database->prepare($query_field_type);
            $stmt_field_type->bind_param('i', $field_type);
            $stmt_field_type->execute();
            $result_field_type = $stmt_field_type->get_result();

            while ($row3 = $result_field_type->fetch_assoc()) {
                $field_name = htmlspecialchars($row3['type']);

                echo "<div class='resultscell'><span style='color: green; font-weight: bold;'>$field_name:</span> $field_text</div>";
            }

            $stmt_field_type->close();
        }

        $stmt_item_fields->close();
    }
}

function getSectionsForMenu() {
    global $database;

    $query = "SELECT * FROM sections ORDER BY display_order ASC";
    $result = $database->query($query);

    $sections = [];
    while ($row = $result->fetch_assoc()) {
        $sections[] = $row;
    }

    return $sections;
}

function saveSectionsForMenu($id, $order) {
    global $database;

    $query = "UPDATE sections SET display_order = ? WHERE id = ?";
    $stmt = $database->prepare($query);
    $stmt->bind_param('ii', $order, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $statusMessage = "Menu updated successfully!";
    } else {
        $statusMessage = "Error: Menu not updated!";
    }

    $stmt->close();
    return $statusMessage;
}

function getFieldTypesForMenu() {
    global $database;

    $query = "SELECT * FROM field_types ORDER BY display_order ASC";
    $result = $database->query($query);

    $fieldTypes = [];
    while ($row = $result->fetch_assoc()) {
        $fieldTypes[] = $row;
    }

    return $fieldTypes;
}

function saveFieldTypesForMenu($id, $order) {
    global $database;

    $query = "UPDATE field_types SET display_order = ? WHERE id = ?";
    $stmt = $database->prepare($query);
    $stmt->bind_param('ii', $order, $id);
    $stmt->execute();
    $stmt->close();
}

?>