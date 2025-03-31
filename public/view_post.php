<?php
include __DIR__ . '/../includes/config.php';
include __DIR__ . '/../includes/check_login.php';
include __DIR__ . '/../includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Post ID");
}

$post_id = intval($_GET['id']);

// Fetch the post details securely without checking user_id
$stmt = $conn->prepare("SELECT title, content, image_url FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Post not found.");
}

$post = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            height: 100vh;
            width: 100vw;
            font-family: Arial, sans-serif;
        }

        .post-container {
            margin: 0 auto;
            margin-top: 40px;
            height: 70%;
            max-width: 800px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            background: #f9f9f9;
        }

        h1 {
            text-align: center;
        }

        img {
            max-width: 100%;
            max-height: 400px;
            display: block;
            margin: 20px auto;
            border-radius: 8px;
        }

        p {
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
            max-width: 100%;
        }
    </style>
</head>

<body>
    <div>
        <a href="all_blogs.php">
            <button style="
            position: absolute;
            top: 170px;
            left: 100px;
            padding: 10px 15px;
            font-size: 16px;
            border: none;
            background-color: #007BFF;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        ">⬅ Back</button>
        </a>
        <div class="post-container">
            <?php if (!empty($post['image_url'])) { ?>
                <div style="display: flex; justify-content: center; margin: 20px 0;">
                    <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="Post Image">
                </div>
            <?php } ?>
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
        </div>
    </div>
</body>

</html>