
<?php
session_start();
function isLoggedIn() { return isset($_SESSION['user_id']); }
function isAdmin() { return isset($_SESSION['is_admin']) && $_SESSION['is_admin']; }
function redirect($url) { header("Location: $url"); exit(); }
?>