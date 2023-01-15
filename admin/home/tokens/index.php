<?php // 2023 - Diederik Veenstra <diederik@refuzion.nl>
// check if the cookie is set
if(isset($_COOKIE['username'])) {
    $username = $_COOKIE['username'];
    require_once('../../../config.php');
} else {
    // redirect user to the login page
    http_response_code(200);
    header("Location: ../../index.php?session_expired");
    exit();
}

if(isset($_GET['end_session'])) {
    if (isset($_COOKIE['username'])) {
        // Delete the cookie
        setcookie("username", "", time() - 3600, "/");
        header("Location: ../../index.php?loggedout");
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
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="main">
        <div class="header-container">
            <div class="column logo-wrapper">
                <img src="assets/img/logo.png">
            </div>
            <div class="column nav-bar-wrapper">
                <ul class="nav-bar">
                    <li class="nav-item active"><a href="index.php">API Tokens</a></li>
                    <li class="nav-item"><a href="../index.php">Home</a></li>
                    <li class="nav-item"><a href="?end_session">Logout</a></li>
                </ul>
            </div>
        </div>
        <div class="content">
            <div id="tokens-table">
                <table>
                    <button class="generate-btn" onclick="generateAPIKey();">Generate Token</button>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Token</th>
                            <th>Master key</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $stmt = $db->prepare("SELECT id, token, master_key FROM authentication_tokens");
                            $stmt->execute();
                            $result = $stmt->get_result();
                            if ($result->num_rows === 0) {
                                echo '<tr>';
                                echo '<td>
                                        No results in table `authentication_tokens`.
                                    </td>';
                                echo '<td>N/A</td>';
                                echo '<td>N/A</td>';
                                echo '</tr>';
                            }
                            while ($row = $result->fetch_assoc()) {
                                if ($row['master_key'] === 1) {
                                    $master_key = "Yes";
                                } else {
                                    $master_key = "No";
                                }
                                echo '<tr>';
                                echo '<td>' . $row['id'] . '</td>';
                                echo '<td data-id="'.$row['id'].'">' . $row['token'] . '</td>';
                                echo '<td data-id="'.$row['id'].'">' . $master_key . '</td>';
                                echo '<td>
                                        <button class="edit-btn" onclick="showEditModal('. $row['id'].');">Edit</button>
                                        <button class="delete-btn" onclick="confirmDelete('.$row['id'].')">Delete</button>
                                    </td>';
                                echo '</tr>';
                            }
                        ?>
                    </tbody>
                </table>
                <!-- Edit Modal -->
                <div id="editModal" style="display: none;">
                    <form>
                        <input type="hidden" id="apiKeyId" value="">
                        <label for="apiKey">API Key:</label>
                        <input type="text" id="apiKey" value="">
                        <label for="masterKey">Master Key:</label>
                        <select name="masterKey" id="masterKey" value="2">
                            <option>Yes</option>
                            <option>No</option>
                        </select>
                        <button type="button" onclick="updateAPIKey();">Update</button>
                        <button type="button" onclick="hideEditModal();">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="tokens.js"></script>
</body>
</html>
