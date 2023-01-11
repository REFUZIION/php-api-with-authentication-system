<?php // 2023 - Diederik Veenstra <diederik@refuzion.nl>
require_once('../config.php');
$message_box = false;
$message_status = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Login success.
            setcookie("username", $user['username'], time() + (86400 * 30), "/");
            header("Location: home/index.php");
        } else {
            // Login failed.
            $message_box = true;
            $message_status = "error";
            $adminmessage_box_text = "Incorrect username or password.<br>Please try again.";
        }
    } else {
        // Login failed.
        $message_box = true;
        $message_status = "error";
        $adminmessage_box_text = "Incorrect username or password.<br>Please try again.";
    }
}
if(isset($_GET['session_expired'])) {
    // Session expired.
    $message_box = true;
    $message_status = "error";
    $adminmessage_box_text = "Session exired, please login.";
}

if(isset($_GET['loggedout'])) {
    // Logged out.
    $message_box = true;
    $message_status = "success";
    $message_box_text = "Logged out successfully.";
}
$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API admin interface</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="main">
        <form method="post">
            <h1>RFZ REST API V1</h1>
        <div class="banner">
            <?php if($message_box): ?>
                <div class="message_box <?=$message_status?>">
                    <p><?=$adminmessage_box_text?></p>
                </div>
            <?php endif; ?>
        </div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br><br>
            <input type="submit" value="Log in">
        </form>
    </div>
</body>
</html>
