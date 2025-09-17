<?php
require 'config.php';
require_login();
 
$search = isset($_GET['search']) ? $_GET['search'] : '';
 
$query = "SELECT id, title, excerpt, publish_date, post_type, media_url 
          FROM posts WHERE author_id = ?";
 
$params = [$_SESSION['user_id']];
 
if ($search) {
    $query .= " AND (title LIKE ? OR content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
 
$query .= " ORDER BY publish_date DESC";
 
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                </ul>
                <a href="create.php" class="btn btn-primary ms-auto">Add Item</a>
                <a href="logout.php" class="btn btn-secondary ms-2">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h1>My Dashboard</h1>
        <form class="mb-4" method="GET">
            <div class="input-group">
                <input class="form-control" type="search" name="search" placeholder="Search your items" value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </div>
        </form>
        <h2>My Items</h2>
        <?php foreach ($posts as $post): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><a href="post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a> (<?php echo $post['post_type']; ?>)</h5>
                    <?php if ($post['post_type'] == 'photo' && $post['media_url']): ?>
                        <img src="<?php echo $post['media_url']; ?>" alt="Photo" class="img-fluid mb-2" style="max-height: 100px;">
                    <?php elseif ($post['post_type'] == 'video' && $post['media_url']): ?>
                        <video src="<?php echo $post['media_url']; ?>" controls class="img-fluid mb-2" style="max-height: 100px;"></video>
                    <?php endif; ?>
                    <p class="card-text"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                    <p class="card-text"><small class="text-muted">Published on <?php echo $post['publish_date']; ?></small></p>
                    <a href="edit.php?id=<?php echo $post['id']; ?>" class="btn btn-warning">Edit</a>
                    <a href="delete.php?id=<?php echo $post['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
