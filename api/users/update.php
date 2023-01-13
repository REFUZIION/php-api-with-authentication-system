<?php // 2023 - Diederik Veenstra <diederik@refuzion.nl>
/** Information:
 * $_POST['api_token] =  API access token. This key needs the master_key attribute to be valid.
 * $_POST['username'] = current username used as selector;
 * $_POST['new_username'] = update data for new username;
 * $_POST['new_password'] = update data for new password;
 */
require_once("../../config.php");

// Sanitize the API key
if (!isset($_POST['api_token'])) {
    // Invalid API key
    http_response_code(401);
    echo json_encode(array("message" => "API key is not set."));
    exit;
}
$api_token = filter_var($_POST['api_token'], FILTER_SANITIZE_STRING);

// Check if the API key is valid
$stmt = $db->prepare("SELECT id FROM authentication_tokens WHERE token = ? AND master_key = 1");
$stmt->bind_param("s", $api_token);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    // Invalid API key
    http_response_code(401);
    echo json_encode(array("message" => "Invalid API key."));
    exit;
}

if(!isset($_POST['username'])) {
    // Invalid username or password
    http_response_code(400);
    echo json_encode(array("message" => "No username recieved."));
    exit;
}

// Sanitize the username.
$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
// Check if user exists.
$stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    // username already exists
    http_response_code(401);
    echo json_encode(array("message" => "user does not exist."));
    exit;
}
$userData = $result->fetch_assoc();
$userId = $userData['id'];

// check if username is valid.
if (isset($_POST['new_username'])) {
    $new_username = $_POST['new_username'];
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $new_username)) {
        http_response_code(400);
        echo json_encode(array("message" => "Invalid username."));
        exit;
    }
    $new_username = filter_var($_POST['new_username'], FILTER_SANITIZE_STRING);
}

// if both new user and new password are set:
if(isset($_POST['new_password']) && isset($_POST['new_username'])) {
    // Hash password.
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    // Prepare to update username and password.
    $query = "UPDATE `users` SET `username`=?, `password`=? WHERE id = {$userId}";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ss", $new_username, $new_password);
}
// if only new password is set.
if(isset($_POST['new_password']) && !isset($_POST['new_username'])) {
    // Hash password.
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    // Prepare to update password.
    $query = "UPDATE `users` SET `password`=? WHERE id = {$userId}";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $new_password);
}

// if only new username is set.
if(!isset($_POST['new_password']) && isset($_POST['new_username'])) {
    // Prepare to update username.
    $query = "UPDATE `users` SET `username`=? WHERE id = {$userId}";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $new_username);
}
// if neither new arguments are set.
if(!isset($_POST['new_password']) && !isset($_POST['new_username'])) {
    http_response_code(401);
    echo json_encode(array("message" => "No update data received."));
    exit;
}

// Execute the statement
$stmt->execute();
$result = $stmt->get_result();
if ($db->affected_rows === 0) {
    http_response_code(401);
    echo json_encode(array("message" => "User was not updated."));
    exit;
}
$output = array("message" => "User with username '{$username}' has been updated.");

// Return the result as JSON
http_response_code(200);
echo json_encode($output);
?>
