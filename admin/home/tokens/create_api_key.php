<?php // 2023 - Diederik Veenstra <diederik@refuzion.nl>
require_once('../../../config.php');

// Get the token from the POST request
$api_key = $_POST['token'];

// Insert the new API key into the database
$stmt = $db->prepare("INSERT INTO authentication_tokens (token) VALUES (?)");
$stmt->bind_param("s", $api_key);
$stmt->execute();

// Check if the insertion was successful
if ($stmt->affected_rows > 0) {
    http_response_code(200);
    echo "API key has been created successfully";
} else {
    http_response_code(500);
    echo "An error has occurred, please try again";
}

// Close the statement and the database connection
$stmt->close();
$db->close();
