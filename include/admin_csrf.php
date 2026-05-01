<?php

function admin_csrf_token(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['admin_csrf'])) {
        $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['admin_csrf'];
}

function admin_csrf_field(): string
{
    $t = htmlspecialchars(admin_csrf_token(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf" value="' . $t . '">';
}

function admin_csrf_verify(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $sent = isset($_POST['csrf']) ? (string) $_POST['csrf'] : '';
    $ok = isset($_SESSION['admin_csrf']) && hash_equals($_SESSION['admin_csrf'], $sent);
    if ($ok) {
        $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
    }
    return $ok;
}
