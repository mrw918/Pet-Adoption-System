<?php

function browse_csrf_token(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['browse_csrf'])) {
        $_SESSION['browse_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['browse_csrf'];
}

function browse_csrf_verify_string(string $sent): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['browse_csrf']) && hash_equals($_SESSION['browse_csrf'], $sent);
}
