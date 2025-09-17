<?php
require 'config.php';
require_login();
 
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND author_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$post = $stmt->fetch();
 
if (!$post) {
    die("Post not found or you don't have permission.");
}
 
$categories = ['Technology', 'Lifestyle', 'Business', 'Travel'];
$post_types = ['article', 'photo', 'video'];
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = isset($_POST['content']) ? $_POST['content'] : '';
    $excerpt = substr(strip_tags($content), 0, 200) . '...';
    $category = $_POST['category'];
    $post_type = $_POST['post_type'];
    $media_url = $post['media_url'];
 
    if ($post_type == 'photo' || $post_type == 'video') {
        if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $file_ext = pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid() . '.' . $file_ext;
            $media_url = $upload_dir . $file_name;
            move_uploaded_file($_FILES['media']['tmp_name'], $media_url);
        }
    }
 
    $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, excerpt = ?, category = ?, post_type = ?, media_url = ? WHERE id = ?");
    $stmt->execute([$title, $content, $excerpt, $category, $post_type, $media_url, $id]);
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.tinymce.com/stable/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector: '#content',
            plugins: 'advlist autolink lists link image charmap preview anchor',
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | removeformat'
        });
    </script>
    <script>
        function toggleMediaInput() {
            var type = document.getElementById('post_type').value;
            var mediaInput = document.getElementById('media_input');
            var contentInput = document.getElementById('content_input');
            if (type === 'article') {
                mediaInput.style.display = 'none';
                contentInput.style.display = 'block';
            } else {
                mediaInput.style.display = 'block';
                contentInput.style.display = 'block'; // Optional caption
            }
        }
    </script>
</head>
<body data-bs-theme="dark" onload="toggleMediaInput()">
    <div class="container mt-5">
        <h2>Edit Item</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Title</label>
                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($post['title']); ?>" required>
            </div>
            <div class="mb-3">
                <label>Category</label>
                <select name="category" class="form-select" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat; ?>" <?php if ($post['category'] == $cat) echo 'selected'; ?>><?php echo $cat; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Type</label>
                <select name="post_type" id="post_type" class="form-select" required onchange="toggleMediaInput()">
                    <?php foreach ($post_types as $type): ?>
                        <option value="<?php echo $type; ?>" <?php if ($post['post_type'] == $type) echo 'selected'; ?>><?php echo ucfirst($type); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3" id="media_input" style="display: none;">
                <label>Upload New Media (optional)</label>
                <input type="file" name="media" class="form-control" accept="image/*,video/*">
                <?php if ($post['media_url']): ?>
                    <p>Current: <a href="<?php echo $post['media_url']; ?>" target="_blank">View</a></p>
                <?php endif; ?>
            </div>
            <div class="mb-3" id="content_input">
                <label>Content/Caption</label>
                <textarea name="content" id="content" class="form-control" rows="10"><?php echo htmlspecialchars($post['content']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</body>
</html>
