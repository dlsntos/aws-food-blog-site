<?php
include 'config.php';
include 'check_login.php';
include 'header.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];
$posts = $conn->query("SELECT id, title, content, image_url FROM posts WHERE user_id = $user_id ORDER BY id DESC");

// Handle Delete Post
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    
    $stmt = $conn->prepare("SELECT title FROM posts WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->bind_result($title);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $delete_id, $user_id);
    $stmt->execute();
    if ($stmt->execute()) {
        // Log the action in audit_logs
        $logStmt = $conn->prepare("INSERT INTO audit_logs (username, action, timestamp) VALUES (?, ?, NOW())");
        $action = "Deleted a Post : $title";
        $logStmt->bind_param("ss", $username, $action);
        $logStmt->execute();
        $logStmt->close();
    }
    $stmt->close();
    echo '<script>window.location.href = "my_blogs.php";</script>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Blogs</title>
    <link rel="stylesheet" href="my_blogs.css">
</head>

<body>
    <div id="show-all-blogs-container">
        <h1>My Blogs</h1>
        <p>You are logged in as User: <?php echo htmlspecialchars($username); ?></p>

        <div class="post-container">
            <?php while ($post = $posts->fetch_assoc()) { ?>
                <div class="post">
                    <?php if ($post['image_url']) { ?>
                        <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="Post Image">
                    <?php } ?>
                    <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>


                    <div class="actions">
                        <a href="edit_blog.php?id=<?php echo $post['id']; ?>" class="edit-btn">Edit</a>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?php echo $post['id']; ?>">
                            <button type="submit" class="delete-btn"
                                onclick="return confirm('Are you sure you want to delete this post?')">Delete</button>
                        </form>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</body>

</html>