<?php
session_start();

// Prevent browser caching of secured pages
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// If the session variable isn't set, they aren't logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php?error=access_denied");
    exit();
}
?>