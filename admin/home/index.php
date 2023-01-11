<?php // 2023 - Diederik Veenstra <diederik@refuzion.nl>
// check if the cookie is set
if(isset($_COOKIE['username'])) {
    $username = $_COOKIE['username'];
} else {
    // redirect user to the login page
    http_response_code(200);
    header("Location: ../index.php?session_expired");
    exit();
}

if(isset($_GET['end_session'])) {
    if (isset($_COOKIE['username'])) {
        // Delete the cookie
        setcookie("username", "", time() - 3600, "/");
        header("Location: ../index.php?loggedout");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API admin interface</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="main">
        <div class="header-container">
            <div class="column logo-wrapper">
                <img src="assets/img/logo.png">
            </div>
            <div class="column nav-bar-wrapper">
                <ul class="nav-bar">
                    <li class="nav-item"><a href="tokens/index.php">API Tokens</a></li>
                    <li class="nav-item active"><a href="index.php">Home</a></li>
                    <li class="nav-item"><a href="?end_session">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
