<?php
session_start();
$host = 'localhost';
$db = 'dbcjescqjceymh';
$user = 'ujbzsmnzu8vkc';
$pass = 'oka0lihz9y9c';
 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
 
function is_logged_in() {
    return isset($_SESSION['user_id']);
}
 
function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
?>
