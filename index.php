<?php
require 'config.php';
 
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
 
$query = "SELECT p.id, p.title, p.excerpt, p.publish_date, p.post_type, p.media_url, u.username as author 
          FROM posts p JOIN users u ON p.author_id = u.id WHERE 1=1";
$params = [];
 
if ($search) {
    $query .= " AND (p.title LIKE ? OR p.content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
 
if ($category) {
    $query .= " AND p.category = ?";
    $params[] = $category;
}
 
$query .= " ORDER BY p.publish_date DESC LIMIT 10";
 
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Homepage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body data-bs-theme="dark">
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container">
            <a class="navbar-brand" href="index.php">My Blog Clone</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="index.php?category=Technology">Technology</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?category=Lifestyle">Lifestyle</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?category=Business">Business</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?category=Travel">Travel</a></li>
                </ul>
                <form class="d-flex ms-auto" method="GET">
                    <input class="form-control me-2" type="search" name="search" placeholder="Search" value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
                <?php if (is_logged_in()): ?>
                    <a href="dashboard.php" class="btn btn-primary ms-2">Dashboard</a>
                    <a href="logout.php" class="btn btn-secondary ms-2">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary ms-2">Login</a>
                    <a href="register.php" class="btn btn-secondary ms-2">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h1>Recent Posts</h1>
        <?php foreach ($posts as $post): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><a href="post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h5>
                    <?php if ($post['post_type'] == 'photo' && $post['media_url']): ?>
                        <img src="<?php echo $post['media_url']; ?>" alt="Photo" class="img-fluid mb-2">
                    <?php elseif ($post['post_type'] == 'video' && $post['media_url']): ?>
                        <video src="<?php echo $post['media_url']; ?>" controls class="img-fluid mb-2"></video>
                    <?php endif; ?>
                    <p class="card-text"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                    <p class="card-text"><small class="text-muted">By <?php echo htmlspecialchars($post['author']); ?> on <?php echo $post['publish_date']; ?></small></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
