<?php // 2023 - Diederik Veenstra <diederik@refuzion.nl>
/** Information:
 * $_POST['api_token] =  API access token. This key needs the master_key attribute to be valid.
 * $_POST['username'] = username. this will be used to select the user that is going to get deleted.
 */
require_once("../../config.php");

// Sanitize the API key
if (!isset($_POST['api_token'])) {
    // Invalid API key
    http_response_code(401);
    echo json_encode(array("message" => "API key is not set."));
    exit;
}

// Sanitize and check if the API key is valid
$api_token = filter_var($_POST['api_token'], FILTER_SANITIZE_STRING);
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
    // No username received.
    http_response_code(400);
    echo json_encode(array("message" => "No username recieved."));
    exit;
}

// Sanitize the username and password and check if it exists.
$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    http_response_code(400);
    echo json_encode(array("message" => "Invalid username."));
    exit;
}

// Prepare DELETE statement
$query = "DELETE FROM users WHERE username = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("s", $username);

// Execute the statement
$stmt->execute();
$result = $stmt->get_result();
if ($db->affected_rows === 0) {
    http_response_code(401);
    echo json_encode(array("message" => "User does not exist."));
    exit;
}
$output = array("message" => "User was deleted.");

// Return the result as JSON
http_response_code(200);
echo json_encode($output);
?>
