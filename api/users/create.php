<?php // 2023 - Diederik Veenstra <diederik@refuzion.nl>
/** Information:
 * $_POST['api_token] =  API access token. This key needs the master_key attribute to be valid.
 * $_POST['username'] = username for new user. This needs to be original.
 * $_POST['password'] = password for new user.;
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

if(!isset($_POST['username']) && !isset($_POST['password'])) {
    // Invalid username or password
    http_response_code(400);
    echo json_encode(array("message" => "No username or password recieved."));
    exit;
}

// Sanitize the username and password and check if its valid.
$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
// Check if user exists.
$stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows !== 0) {
    // username already exists
    http_response_code(401);
    echo json_encode(array("message" => "user already exists."));
    exit;
}

if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    http_response_code(400);
    echo json_encode(array("message" => "Invalid username."));
    exit;
}

// Prepare the INSERT statement
$query = "INSERT INTO `users`(`username`, `password`) VALUES (?,?)";
$stmt = $db->prepare($query);
$stmt->bind_param("ss", $username, $password);

// Execute the statement
$stmt->execute();
$result = $stmt->get_result();
if ($db->affected_rows === 0) {
    http_response_code(401);
    echo json_encode(array("message" => "User was not created."));
    exit;
}
$output = array("message" => "User was created.");

// Return the result as JSON
http_response_code(200);
echo json_encode($output);
?>
