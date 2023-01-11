<?php // 2023 - Diederik Veenstra <diederik@refuzion.nl>
$DBHOST = "localhost";
$DBUSER = "root";
$DBPASS = "";
$DBNAME = "php_api";

$db = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
