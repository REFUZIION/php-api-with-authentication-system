<?php
require_once '../../../config.php';

// Get the API key's ID from the query string
$api_key_id = $_GET['id'];

// Delete the API key from the database
$stmt = $db->prepare("DELETE FROM authentication_tokens WHERE id = ?");
$stmt->bind_param("i", $api_key_id);
$stmt->execute();

// Check if the deletion was successful
if ($stmt->affected_rows > 0) {
    http_response_code(200);
    echo "API key has been deleted successfully";
} else {
    http_response_code(500);
    echo "An error has occurred, please try again";
}

// Close the statement and the database connection
$stmt->close();
$db->close();
