<?php
include __DIR__ . 'config.php';
include __DIR__ . 'check_login.php';

$user_id = $_SESSION["user_id"];

if (!isset($_GET['id'])) {
    header("Location: all_blogs.php");
    exit();
}

$post_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT title, content FROM posts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $post_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
$stmt->close();

if (!$post) {
    echo "Post not found or you don't have permission to edit it.";
    exit();
}

// Handle Edit Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_title = $conn->real_escape_string($_POST['title']);
    $new_content = $conn->real_escape_string($_POST['content']);

    if (strlen($new_content) < 400) {
        $error_message = "Content must be at least 400 characters!";
    } else {
        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssii", $new_title, $new_content, $post_id, $user_id);

        if ($stmt->execute()) {
            header("Location: ../public/all_blogs.php");
            exit();
        } else {
            $error_message = "Error updating post.";
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
    <title>Edit Blog</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
        }

        input,
        textarea {
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            resize: none;
        }

        textarea {
            min-height: 100px;
            overflow-y: hidden;
        }

        /* Expandable height, no width resizing */
        button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .back-btn {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            background-color: #3498db;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .char-counter {
            font-size: 14px;
            color: #555;
            margin-bottom: 10px;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Edit Blog Post</h2>

        <?php if (!empty($error_message)) {
            echo "<p class='error'>$error_message</p>";
        } ?>

        <form method="post" onsubmit="return validateContent()">
            <label for="title">Title:</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>

            <label for="content">Content:</label>
            <textarea name="content" id="content" rows="5" required
                oninput="updateCounter(this)"><?php echo htmlspecialchars($post['content']); ?></textarea>
            <p class="char-counter">Minimum Characters: <span id="charCount">0</span>/400</p>

            <button type="submit">Update Post</button>
        </form>
        <a href="allBlogs.php" class="back-btn">Back to Blogs</a>
    </div>
    <script>
        function updateCounter(textarea) {
            let count = textarea.value.length;
            document.getElementById("charCount").innerText = count;
            textarea.style.height = "auto";
            textarea.style.height = (textarea.scrollHeight) + "px"; // Expand height dynamically
        }

        function validateContent() {
            let content = document.getElementById("content").value;
            if (content.length < 400) {
                alert("Content must be at least 400 characters!");
                return false;
            }
            return true;
        }

        window.onload = function () {
            let contentBox = document.getElementById("content");
            updateCounter(contentBox);
            contentBox.style.height = contentBox.scrollHeight + "px"; // Adjust initial height
        };
    </script>
</body>

</html>