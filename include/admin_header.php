<?php
if (!defined('ADMIN_GUARD_LOGIN_PATH')) {
    define('ADMIN_GUARD_LOGIN_PATH', '../login.php');
}
require_once __DIR__ . '/admin_guard.php';
require_once __DIR__ . '/admin_csrf.php';
if (!isset($pageTitle)) {
    $pageTitle = 'Admin';
}
if (!isset($adminNav)) {
    $adminNav = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?> — Admin</title>
    <link rel="stylesheet" href="../assets/css/pet-theme.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="site-pet">
<div class="pet-bg-layer" aria-hidden="true"></div>
<div class="pet-bg-paws" aria-hidden="true"></div>
<?php
$navBase = '../';
require __DIR__ . '/site_top_nav.php';
?>

<div class="admin-shell">
    <aside class="admin-sidebar" aria-label="Admin navigation">
        <p class="admin-sidebar-title">Administration</p>
        <nav class="admin-nav">
            <a href="index.php" class="<?php echo $adminNav === 'dashboard' ? 'is-active' : ''; ?>">Dashboard</a>
            <a href="pets.php" class="<?php echo $adminNav === 'pets' ? 'is-active' : ''; ?>">Pets</a>
            <a href="applications.php" class="<?php echo $adminNav === 'applications' ? 'is-active' : ''; ?>">Applications</a>
            <a href="password_resets.php" class="<?php echo $adminNav === 'password_resets' ? 'is-active' : ''; ?>">Password Resets</a>
            <a href="user_audit_logs.php" class="<?php echo $adminNav === 'user_audit_logs' ? 'is-active' : ''; ?>">User Logs</a>
            <a href="pet_add_logs.php" class="<?php echo $adminNav === 'pet_add_logs' ? 'is-active' : ''; ?>">Pet Add Logs</a>
            <?php if (!empty($_SESSION['is_super_admin'])) : ?>
                <a href="super_admin_admins.php" class="<?php echo $adminNav === 'super_admin_admins' ? 'is-active' : ''; ?>">Super Admin</a>
            <?php endif; ?>
        </nav>
    </aside>
    <div class="admin-main">
