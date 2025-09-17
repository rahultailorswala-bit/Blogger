<?php
require 'config.php';
require_login();
 
$categories = ['Technology', 'Lifestyle', 'Business', 'Travel'];
$post_types = ['article', 'photo', 'video'];
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = isset($_POST['content']) ? $_POST['content'] : '';
    $excerpt = substr(strip_tags($content), 0, 200) . '...';
    $category = $_POST['category'];
    $post_type = $_POST['post_type'];
    $author_id = $_SESSION['user_id'];
    $media_url = '';
 
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
 
    $stmt = $pdo->prepare("INSERT INTO posts (title, content, excerpt, category, post_type, media_url, author_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $content, $excerpt, $category, $post_type, $media_url, $author_id]);
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Item</title>
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
                contentInput.style.display = (type === 'photo' || type === 'video') ? 'block' : 'none'; // Optional caption
            }
        }
    </script>
</head>
<body data-bs-theme="dark" onload="toggleMediaInput()">
    <div class="container mt-5">
        <h2>Add New Item</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Category</label>
                <select name="category" class="form-select" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Type</label>
                <select name="post_type" id="post_type" class="form-select" required onchange="toggleMediaInput()">
                    <?php foreach ($post_types as $type): ?>
                        <option value="<?php echo $type; ?>"><?php echo ucfirst($type); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3" id="media_input" style="display: none;">
                <label>Upload Media</label>
                <input type="file" name="media" class="form-control" accept="image/*,video/*">
            </div>
            <div class="mb-3" id="content_input">
                <label>Content/Caption</label>
                <textarea name="content" id="content" class="form-control" rows="10"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Publish</button>
        </form>
    </div>
</body>
</html>
