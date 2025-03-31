<?php
include __DIR__ . '/../includes/config.php';
include __DIR__ . '/../includes/check_login.php';
include __DIR__ . '/../includes/header.php';

$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];

// Pagination setup
$limit = 3; // Number of posts per page
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$page = max($page, 1); // Ensure page is at least 1
$offset = ($page - 1) * $limit;

// Fetch posts with limit and offset
$posts = $conn->query("
    SELECT posts.id, posts.title, posts.content, posts.image_url, 
           users.username AS author, posts.created_at 
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    ORDER BY posts.id DESC
    LIMIT $limit OFFSET $offset
");

// Get total post count
$result = $conn->query("SELECT COUNT(*) AS total FROM posts");
$total_posts = $result->fetch_assoc()['total'];
$total_pages = ceil($total_posts / $limit);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogs</title>
    <link rel="stylesheet" href="../assets/css/all_blogs.css">
</head>

<body>
    <div id="show-all-blogs-container">
        <h1>All Blogs Feed</h1>
        <p>You are logged in as User: <?php echo htmlspecialchars($username); ?></p>
        <div class="post-container">
            <?php while ($post = $posts->fetch_assoc()) { ?>
                <a href="view_post.php?id=<?php echo $post['id']; ?>" class="post-link">
                    <div class="post">
                        <?php if (!empty($post['image_url'])) { ?>
                            <img src="../uploads/<?php echo htmlspecialchars($post['image_url']); ?>" alt="Post Image">
                        <?php } ?>
                        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p class="post-meta">
                            Posted by <strong><?php echo htmlspecialchars($post['author']); ?></strong>
                            on <em><?php echo date("F j, Y, g:i a", strtotime($post['created_at'])); ?></em>
                        </p>
                        <p class="post-content"><?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 100))); ?>...
                        </p>
                        <a href="../public/view_post.php?id=<?php echo $post['id']; ?>" class="read-more">Read more →</a>
                    </div>
                </a>
            <?php } ?>
        </div>
        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1) { ?>
                <a href="?page=<?php echo $page - 1; ?>">← Previous</a>
            <?php } ?>

            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php } ?>

            <?php if ($page < $total_pages) { ?>
                <a href="?page=<?php echo $page + 1; ?>">Next →</a>
            <?php } ?>
        </div>
    </div>
</body>

</html>