<?php // 2023 - Diederik Veenstra <diederik@refuzion.nl>
require_once("../config.php");

if(!isset($_POST['table_name'])) {
    // Invalid API key
    http_response_code(400);
    echo json_encode(array("message" => "No search criteria received."));
    exit;
}
// Get the API key and table name from the POST request
$api_token = $_POST['api_token'];
$table_name = $_POST['table_name'];

// Check if the API key is valid
$stmt = $db->prepare("SELECT id FROM authentication_tokens WHERE token = ?");
$stmt->bind_param("s", $api_token);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    // Invalid API key
    http_response_code(401);
    echo json_encode(array("message" => "Invalid API key."));
    exit;
}



// Prepare the SELECT statement
$stmt = $db->prepare("SELECT * FROM ? WHERE 1");
$stmt->bind_param("s", $table_name);

// Execute the statement
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(array("message" => "No results found."));
    exit;
}
// Fetch the result as an associative array
$output = $result->fetch_all(MYSQLI_ASSOC);

// Return the result as JSON
http_response_code(200);
echo json_encode($output);
?>
