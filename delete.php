<?php
require 'config.php';
require_login();
 
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT media_url FROM posts WHERE id = ? AND author_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$post = $stmt->fetch();
 
if ($post && $post['media_url']) {
    unlink($post['media_url']); // Delete media file if exists
}
 
$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND author_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
header('Location: dashboard.php');
exit;
?>
