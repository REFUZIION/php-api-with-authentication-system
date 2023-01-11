<?php // 2023 - Diederik Veenstra <diederik@refuzion.nl>
require_once('../../../config.php');

// Get the API key ID and token from the PUT request
$api_key_id = $_GET['id'];
$api_key = $_GET['token'];

// Validate the input 
if(!is_numeric($api_key_id)){
    http_response_code(400);
    echo json_encode(array("message" => "Invalid API key id."));
    exit;
}

// Update the API key in the database
$stmt = $db->prepare("UPDATE authentication_tokens SET token = ? WHERE id = ?");
$stmt->bind_param("si", $api_key, $api_key_id);
if($stmt->execute()){
    http_response_code(200);
    echo json_encode(array("message" => "API key updated successfully."));
} else {
    http_response_code(500);
    echo json_encode(array("message" => "Error updating API key: " . $stmt->error));
}
