<?php
$pageTitle = 'Dashboard';
$adminNav = 'dashboard';
require_once __DIR__ . '/../include/admin_header.php';
require_once __DIR__ . '/../dbconnect.php';

function admin_scalar(mysqli $conn, string $sql): int
{
    $r = mysqli_query($conn, $sql);
    if (!$r) {
        return 0;
    }
    $row = mysqli_fetch_row($r);
    mysqli_free_result($r);
    return (int) ($row[0] ?? 0);
}

$countUsers = admin_scalar($conn, 'SELECT COUNT(*) FROM users');
$countPets = admin_scalar($conn, 'SELECT COUNT(*) FROM pets');
$countListed = admin_scalar(
    $conn,
    "SELECT COUNT(*) FROM pets WHERE pet_status NOT LIKE '%已%' AND LOWER(pet_status) NOT IN ('adopted')"
);
$countAdopted = admin_scalar(
    $conn,
    "SELECT COUNT(*) FROM pets WHERE pet_status LIKE '%已%' OR LOWER(pet_status) = 'adopted'"
);
$countPendingApps = admin_scalar(
    $conn,
    "SELECT COUNT(*) FROM adoption_application WHERE LOWER(app_status) = 'pending'"
);

if (!empty($_SESSION['admin_flash_ok'])) {
    echo '<div class="admin-flash ok">' . htmlspecialchars((string) $_SESSION['admin_flash_ok'], ENT_QUOTES, 'UTF-8') . '</div>';
    unset($_SESSION['admin_flash_ok']);
}
if (!empty($_SESSION['admin_flash_err'])) {
    echo '<div class="admin-flash err">' . htmlspecialchars((string) $_SESSION['admin_flash_err'], ENT_QUOTES, 'UTF-8') . '</div>';
    unset($_SESSION['admin_flash_err']);
}
?>

<h1 class="admin-page-title">Dashboard</h1>
<p class="admin-lead">Signed in as <?php echo htmlspecialchars((string) ($_SESSION['username'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>.</p>

<div class="admin-cards">
    <div class="admin-stat">
        <div class="admin-stat-value"><?php echo $countUsers; ?></div>
        <div class="admin-stat-label">Registered users</div>
    </div>
    <div class="admin-stat">
        <div class="admin-stat-value"><?php echo $countPets; ?></div>
        <div class="admin-stat-label">Total pets</div>
    </div>
    <div class="admin-stat">
        <div class="admin-stat-value"><?php echo $countListed; ?></div>
        <div class="admin-stat-label">Listed / not adopted</div>
    </div>
    <div class="admin-stat">
        <div class="admin-stat-value"><?php echo $countAdopted; ?></div>
        <div class="admin-stat-label">Adopted records</div>
    </div>
    <div class="admin-stat">
        <div class="admin-stat-value"><?php echo $countPendingApps; ?></div>
        <div class="admin-stat-label">Pending applications</div>
    </div>
</div>

<div class="admin-panel">
    <h2>Quick actions</h2>
    <div class="admin-actions">
        <a class="admin-btn admin-btn-primary" href="pet_edit.php">Add pet</a>
        <a class="admin-btn admin-btn-ghost" href="pets.php">Manage pets</a>
        <a class="admin-btn admin-btn-ghost" href="applications.php">Review applications</a>
        <a class="admin-btn admin-btn-ghost" href="password_resets.php">Password reset logs</a>
        <a class="admin-btn admin-btn-ghost" href="user_audit_logs.php">User logs</a>
        <a class="admin-btn admin-btn-ghost" href="pet_add_logs.php">Pet add logs</a>
        <?php if (!empty($_SESSION['is_super_admin'])) : ?>
            <a class="admin-btn admin-btn-ghost" href="super_admin_admins.php">Super admin panel</a>
        <?php endif; ?>
    </div>
</div>

    </div>
</div>
<?php require_once __DIR__ . '/../footer.php'; ?>
