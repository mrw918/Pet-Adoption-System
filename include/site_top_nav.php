<?php
/**
 * Shared top navigation (same markup as public site).
 * Before include: set $navBase to '' from site root pages, or '../' from /admin/.
 */
if (!isset($navBase)) {
    $navBase = '';
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$petLoggedIn = isset($_SESSION['user_id']);
$self = basename($_SERVER['PHP_SELF'] ?? '');
$script = $_SERVER['SCRIPT_NAME'] ?? '';
$inAdminArea = strpos($script, '/admin/') !== false;
$homeActive = $self === 'index.php' && !$inAdminArea;
$adminHref = $inAdminArea ? 'index.php' : $navBase . 'admin/index.php';
?>
<header class="nav-bar">
    <a href="<?php echo htmlspecialchars($navBase . 'index.php', ENT_QUOTES, 'UTF-8'); ?>" class="nav-logo"><span aria-hidden="true">🐾</span> Pet Adoption</a>
    <nav class="nav-menu">
        <a href="<?php echo htmlspecialchars($navBase . 'index.php', ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo $homeActive ? 'active' : ''; ?>">Home</a>
        <?php if ($petLoggedIn) : ?>
            <a href="<?php echo htmlspecialchars($navBase . 'browse.php', ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo $self === 'browse.php' ? 'active' : ''; ?>">Browse pets</a>
            <a href="<?php echo htmlspecialchars($navBase . 'my_applications.php', ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo $self === 'my_applications.php' ? 'active' : ''; ?>">My applications</a>
            <?php if (!empty($_SESSION['is_admin'])) : ?>
                <a href="<?php echo htmlspecialchars($adminHref, ENT_QUOTES, 'UTF-8'); ?>">Admin</a>
            <?php endif; ?>
            <a href="<?php echo htmlspecialchars($navBase . 'logout.php', ENT_QUOTES, 'UTF-8'); ?>">Log out</a>
        <?php else : ?>
            <a href="<?php echo htmlspecialchars($navBase . 'login.php', ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo $self === 'login.php' ? 'active' : ''; ?>">Login</a>
        <?php endif; ?>
    </nav>
</header>
