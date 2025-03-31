<?php
include 'config.php';
include 'check_login.php';
include 'header.php';

$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"]; // Assuming you store the username in session
$message = "";

// Handle post creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_post'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $image_url = NULL;

    // Validate content length
    if (strlen($content) < 400) {
        $message = "Content must be at least 400 characters.";
    } else {
        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $target_dir = __DIR__ . "/uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url = "/uploads/" . basename($_FILES["image"]["name"]);
            } else {
                $message = "Error uploading file.";
            }
        }

        // Insert post into database
        $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, image_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $title, $content, $image_url);

        if ($stmt->execute()) {
            // Log the action in audit_logs
            $logStmt = $conn->prepare("INSERT INTO audit_logs (username, action, timestamp) VALUES (?, ?, NOW())");
            $action = "Created a post: $title";
            $logStmt->bind_param("ss", $username, $action);
            $logStmt->execute();
            $logStmt->close();

            $message = "Post created successfully!";
            header("Location: all_blogs.php");
            exit();
        } else {
            $message = "Error creating post.";
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
    <title>Create Blog Post</title>
    <link rel="stylesheet" href="/css/signin.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        h1 {
            text-align: center;
            font-size: 24px;
            margin-top: 20px;
        }

        p {
            text-align: center;
            font-size: 14px;
        }

        form {
            max-width: 90%;
            width: 400px;
            margin: 20px auto;
            display: flex;
            flex-direction: column;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            align-items: center;
            /* Centers all form elements horizontally */
        }

        label {
            font-size: 16px;
            margin-top: 10px;
            width: 100%;
            /* Ensures labels span full width */
            text-align: left;
            /* Aligns label text to the left */
        }

        input,
        textarea {
            width: 90%;
            /* Makes input fields slightly smaller to center them visually */
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
            text-align: center;
            /* Centers the text inside the input fields */
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        button {
            width: 90%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
            font-size: 16px;
            transition: background 0.3s ease;
        }

        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .message {
            text-align: center;
            color: green;
            font-size: 14px;
            margin-top: 10px;
        }

        @media (max-width: 480px) {
            form {
                width: 95%;
                padding: 15px;
            }

            h1 {
                font-size: 20px;
            }

            input,
            textarea {
                font-size: 14px;
                padding: 8px;
            }

            button {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
    <script>
        function updateCharacterCount() {
            let content = document.getElementById('content');
            let counter = document.getElementById('char-counter');
            let submitBtn = document.getElementById('submit-btn');
            let minLength = 400;
            let currentLength = content.value.length;

            counter.textContent = currentLength + "/" + minLength;
            submitBtn.disabled = currentLength < minLength;
        }
    </script>
</head>

<body>
    <h1>Create a Blog Post</h1>
    <p>You are logged in as User: <?php echo htmlspecialchars($username); ?></p>

    <?php if (!empty($message)) {
        echo "<p class='message'>$message</p>";
    } ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" name="title" required>

        <label for="content">Content (Minimum 400 characters):</label>
        <textarea id="content" name="content" oninput="updateCharacterCount()" required></textarea>
        <div class="char-counter" id="char-counter">0/400</div>

        <label for="image">Upload Image:</label>
        <input type="file" name="image" id="image" accept="image/*" required>

        <button type="submit" name="create_post" id="submit-btn" disabled>Create Post</button>
    </form>
</body>

</html>