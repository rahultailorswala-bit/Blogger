<?php
require 'config.php';
 
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT p.*, u.username as author FROM posts p JOIN users u ON p.author_id = u.id WHERE p.id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();
 
if (!$post) {
    die("Post not found.");
}
 
// Get comments
$stmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = ? ORDER BY date DESC");
$stmt->execute([$id]);
$comments = $stmt->fetchAll();
 
// Get related posts (same category, limit 3)
$stmt = $pdo->prepare("SELECT id, title FROM posts WHERE category = ? AND id != ? ORDER BY publish_date DESC LIMIT 3");
$stmt->execute([$post['category'], $id]);
$related = $stmt->fetchAll();
 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $author_name = $_POST['author_name'];
    $content = $_POST['content'];
    $stmt = $pdo->prepare("INSERT INTO comments (post_id, author_name, content) VALUES (?, ?, ?)");
    $stmt->execute([$id, $author_name, $content]);
    header('Location: post.php?id=' . $id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body data-bs-theme="dark">
    <div class="container mt-5">
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <p><small class="text-muted">By <?php echo htmlspecialchars($post['author']); ?> on <?php echo $post['publish_date']; ?> (<?php echo ucfirst($post['post_type']); ?>)</small></p>
        <?php if ($post['post_type'] == 'photo' && $post['media_url']): ?>
            <img src="<?php echo $post['media_url']; ?>" alt="Photo" class="img-fluid mb-3">
        <?php elseif ($post['post_type'] == 'video' && $post['media_url']): ?>
            <video src="<?php echo $post['media_url']; ?>" controls class="img-fluid mb-3"></video>
        <?php endif; ?>
        <div class="content"><?php echo $post['content']; ?></div>
 
        <?php if (is_logged_in() && $post['author_id'] == $_SESSION['user_id']): ?>
            <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-warning">Edit</a>
            <a href="delete.php?id=<?php echo $id; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
        <?php endif; ?>
 
        <h3 class="mt-4">Related Posts</h3>
        <ul>
            <?php foreach ($related as $rel): ?>
                <li><a href="post.php?id=<?php echo $rel['id']; ?>"><?php echo htmlspecialchars($rel['title']); ?></a></li>
            <?php endforeach; ?>
        </ul>
 
        <h3 class="mt-4">Comments</h3>
        <?php foreach ($comments as $comment): ?>
            <div class="card mb-2">
                <div class="card-body">
                    <h6><?php echo htmlspecialchars($comment['author_name']); ?> <small class="text-muted"><?php echo $comment['date']; ?></small></h6>
                    <p><?php echo htmlspecialchars($comment['content']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
 
        <h4>Add Comment</h4>
        <form method="POST">
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="author_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Comment</label>
                <textarea name="content" class="form-control" rows="3" required></textarea>
            </div>
            <button type="submit" name="comment" class="btn btn-primary">Submit</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
