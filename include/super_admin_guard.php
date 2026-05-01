<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin']) || empty($_SESSION['is_super_admin'])) {
    header('Location: ' . (defined('ADMIN_GUARD_LOGIN_PATH') ? ADMIN_GUARD_LOGIN_PATH : '../login.php'));
    exit;
}
