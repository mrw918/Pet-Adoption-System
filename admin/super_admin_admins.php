<?php
define('ADMIN_GUARD_LOGIN_PATH', '../login.php');
require_once __DIR__ . '/../include/super_admin_guard.php';
require_once __DIR__ . '/../include/admin_csrf.php';
require_once __DIR__ . '/../dbconnect.php';

$pageTitle = 'Super admin';
$adminNav = 'super_admin_admins';
require_once __DIR__ . '/../include/admin_header.php';

mysqli_query(
    $conn,
    "CREATE TABLE IF NOT EXISTS admin_application_review_log (
        log_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        app_id INT NOT NULL,
        reviewer_id INT NOT NULL,
        new_status VARCHAR(20) NOT NULL,
        create_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role_action'])) {
    if (!admin_csrf_verify()) {
        $_SESSION['admin_flash_err'] = 'Invalid security token.';
        header('Location: super_admin_admins.php');
        exit;
    }

    $targetId = isset($_POST['target_user_id']) ? (int) $_POST['target_user_id'] : 0;
    $action = isset($_POST['role_action']) ? trim((string) $_POST['role_action']) : '';
    $selfId = (int) ($_SESSION['user_id'] ?? 0);

    if ($targetId <= 0 || $targetId === $selfId) {
        $_SESSION['admin_flash_err'] = 'Invalid target user.';
        header('Location: super_admin_admins.php');
        exit;
    }

    $newRole = '';
    if ($action === 'promote_admin') {
        $newRole = 'admin';
    } elseif ($action === 'make_super_admin') {
        $newRole = 'super_admin';
    } elseif ($action === 'demote_user') {
        $newRole = 'user';
    }

    if ($newRole === '') {
        $_SESSION['admin_flash_err'] = 'Invalid action.';
        header('Location: super_admin_admins.php');
        exit;
    }

    $stmt = mysqli_prepare($conn, 'UPDATE users SET user_role = ? WHERE user_id = ? LIMIT 1');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'si', $newRole, $targetId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['admin_flash_ok'] = 'Role updated successfully.';
    } else {
        $_SESSION['admin_flash_err'] = 'Failed to update role.';
    }

    header('Location: super_admin_admins.php');
    exit;
}

if (!empty($_SESSION['admin_flash_ok'])) {
    echo '<div class="admin-flash ok">' . htmlspecialchars((string) $_SESSION['admin_flash_ok'], ENT_QUOTES, 'UTF-8') . '</div>';
    unset($_SESSION['admin_flash_ok']);
}
if (!empty($_SESSION['admin_flash_err'])) {
    echo '<div class="admin-flash err">' . htmlspecialchars((string) $_SESSION['admin_flash_err'], ENT_QUOTES, 'UTF-8') . '</div>';
    unset($_SESSION['admin_flash_err']);
}

$admins = [];
$adminSql = "SELECT user_id, username, user_role, create_time
             FROM users
             ORDER BY FIELD(user_role, 'super_admin', 'admin', 'user'), user_id ASC";
$adminStmt = mysqli_prepare($conn, $adminSql);
if ($adminStmt) {
    mysqli_stmt_execute($adminStmt);
    mysqli_stmt_bind_result($adminStmt, $uid, $uname, $urole, $ctime);
    while (mysqli_stmt_fetch($adminStmt)) {
        $admins[] = [
            'user_id' => (int) $uid,
            'username' => (string) $uname,
            'user_role' => (string) $urole,
            'create_time' => $ctime !== null ? (string) $ctime : '',
        ];
    }
    mysqli_stmt_close($adminStmt);
}

$approvedRows = [];
$approvedSql = "SELECT l.log_id, l.app_id, l.reviewer_id, l.create_time,
                       r.username AS reviewer_name,
                       a.user_id AS applicant_id, u.username AS applicant_name,
                       p.pet_name
                FROM admin_application_review_log l
                LEFT JOIN users r ON r.user_id = l.reviewer_id
                LEFT JOIN adoption_application a ON a.app_id = l.app_id
                LEFT JOIN users u ON u.user_id = a.user_id
                LEFT JOIN pets p ON p.pet_id = a.pet_id
                WHERE LOWER(l.new_status) = 'approved'
                ORDER BY l.log_id DESC
                LIMIT 300";
$approvedStmt = mysqli_prepare($conn, $approvedSql);
if ($approvedStmt) {
    mysqli_stmt_execute($approvedStmt);
    mysqli_stmt_bind_result($approvedStmt, $logId, $appId, $reviewerId, $ctime, $reviewerName, $applicantId, $applicantName, $petName);
    while (mysqli_stmt_fetch($approvedStmt)) {
        $approvedRows[] = [
            'log_id' => (int) $logId,
            'app_id' => (int) $appId,
            'reviewer_id' => (int) $reviewerId,
            'reviewer_name' => $reviewerName !== null ? (string) $reviewerName : '',
            'applicant_id' => (int) $applicantId,
            'applicant_name' => $applicantName !== null ? (string) $applicantName : '',
            'pet_name' => $petName !== null ? (string) $petName : '',
            'create_time' => $ctime !== null ? (string) $ctime : '',
        ];
    }
    mysqli_stmt_close($approvedStmt);
}
?>

<h1 class="admin-page-title">Super Admin Panel</h1>
<p class="admin-lead">Manage administrator roles and view approved application records by reviewer.</p>

<div class="admin-panel">
    <h2>Administrator management</h2>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th>Change role</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($admins === []) : ?>
                    <tr><td colspan="5">No users found.</td></tr>
                <?php else : ?>
                    <?php foreach ($admins as $a) : ?>
                        <tr>
                            <td><?php echo (int) $a['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($a['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($a['user_role'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($a['create_time'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <?php if ((int) $a['user_id'] === (int) ($_SESSION['user_id'] ?? 0)) : ?>
                                    <span class="admin-badge pending">current account</span>
                                <?php else : ?>
                                    <form method="post" class="inline-form" style="display:flex;flex-wrap:wrap;gap:0.35rem;align-items:center;">
                                        <?php echo admin_csrf_field(); ?>
                                        <input type="hidden" name="target_user_id" value="<?php echo (int) $a['user_id']; ?>">
                                        <select name="role_action" aria-label="Role action for user <?php echo (int) $a['user_id']; ?>">
                                            <option value="promote_admin">set admin</option>
                                            <option value="make_super_admin">set super_admin</option>
                                            <option value="demote_user">set user</option>
                                        </select>
                                        <button type="submit" class="admin-btn admin-btn-primary" style="padding:0.4rem 0.75rem;">Apply</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="admin-panel">
    <h2>Approved applications by admin</h2>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>Application ID</th>
                    <th>Reviewer</th>
                    <th>Applicant</th>
                    <th>Pet</th>
                    <th>Approved time</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($approvedRows === []) : ?>
                    <tr><td colspan="6">No approved records yet.</td></tr>
                <?php else : ?>
                    <?php foreach ($approvedRows as $r) : ?>
                        <tr>
                            <td><?php echo (int) $r['log_id']; ?></td>
                            <td><?php echo (int) $r['app_id']; ?></td>
                            <td>
                                <?php
                                $name = $r['reviewer_name'] !== '' ? $r['reviewer_name'] : ('#' . $r['reviewer_id']);
                                echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
                                ?>
                            </td>
                            <td>
                                <?php
                                $applicant = $r['applicant_name'] !== '' ? $r['applicant_name'] : ('#' . $r['applicant_id']);
                                echo htmlspecialchars($applicant, ENT_QUOTES, 'UTF-8');
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($r['pet_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($r['create_time'], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

    </div>
</div>
<?php require_once __DIR__ . '/../footer.php'; ?>
