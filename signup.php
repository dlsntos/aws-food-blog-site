<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    //This section for validation
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $message = "All fields are required.";
        $message_type = "error";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match!";
        $message_type = "error";
    } else {
        //HASH ALL PASSWORDS TO MD5
        $password_hashed = md5($password);

        //Check if the username exists.
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Username already taken!";
            $message_type = "error";
        } else {
            //Insert the user into the db (SIGN UP PART)
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $password_hashed);
            if ($stmt->execute()) {
                //audit logs logic
                $user_id = $stmt->insert_id; // Get the newly created user ID
                $logStmt = $conn->prepare("INSERT INTO audit_logs (user_id, username, action, timestamp) VALUES (?, ?, 'Signup', NOW())");
                $logStmt->bind_param("is", $user_id, $username);
                $logStmt->execute();
                $logStmt->close();
                $message = "Signup successful!";
                $message_type = "success";
            } else {
                $message = "Error: " . $conn->error;
                $message_type = "error";
            }
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="signup.css">
</head>

<body>
    <div id="sign-up-container">
        <div id="box">
            <img src="" alt="">
            <h1>Create Account</h1>

            <!-- Display feedback message -->
            <?php if (!empty($message)): ?>
                <p class="message <?php echo $message_type; ?>"><?php echo $message; ?></p>
            <?php endif; ?>

            <form action="" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Re-enter Password" required>
                <button type="submit">Sign up</button>
            </form>
            <div class="signin-link">
                <span>Have an account?</span><a href="login.php">Sign in</a>
            </div>
        </div>
    </div>
</body>

</html>