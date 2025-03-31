<?php
// Ensure session is started
include 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $message = "All fields are required!";
    } else {
        $password_hashed = md5($password);

        $stmt = $conn->prepare("SELECT id, is_admin FROM users WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password_hashed);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $is_admin);
            $stmt->fetch();

            // Authenticate user by ID
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;
            $_SESSION["is_admin"] = (bool) $is_admin;

            // ✅ Insert into audit_logs (user login)
            $logStmt = $conn->prepare("INSERT INTO audit_logs (user_id, username, action) VALUES (?, ?, 'Login')");
            $logStmt->bind_param("is", $id, $username);
            $logStmt->execute();
            $logStmt->close();

            // Redirect based on admin status
            if ($is_admin) {
                header("Location: ../admin/admin_page.php");
            } else {
                header("Location: ../public/all_blogs.php");
            }
            exit();
        } else {
            $message = "Invalid username or password!";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <div id="login-container">
        <div id="forms-container">
            <img src="" alt="">
            <?php if (!empty($message)) {
                echo "<p class='message'>$message</p>";
            } ?>
            <h1>Log in</h1>
            <form action="" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            <div class="signup-link">
                <span>Don't have an account?</span>
                <a href="signup.php">Sign up</a>
            </div>
        </div>
    </div>
</body>

</html>