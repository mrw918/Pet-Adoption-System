<?php
define('ADMIN_GUARD_LOGIN_PATH', '../login.php');
require_once __DIR__ . '/../include/admin_guard.php';
require_once __DIR__ . '/../include/admin_csrf.php';
require_once __DIR__ . '/../dbconnect.php';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['app_action'])) {
    if (!admin_csrf_verify()) {
        $_SESSION['admin_flash_err'] = 'Invalid security token.';
        header('Location: applications.php');
        exit;
    }
    $appId = isset($_POST['app_id']) ? (int) $_POST['app_id'] : 0;
    $newStatus = isset($_POST['app_status']) ? trim((string) $_POST['app_status']) : '';
    $allowed = ['pending', 'approved', 'rejected'];
    if ($appId > 0 && in_array(strtolower($newStatus), $allowed, true)) {
        $st = strtolower($newStatus);
        $reviewerId = (int) ($_SESSION['user_id'] ?? 0);
        $stmt = mysqli_prepare($conn, 'UPDATE adoption_application SET app_status = ? WHERE app_id = ? LIMIT 1');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'si', $st, $appId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            if ($reviewerId > 0) {
                $logStmt = mysqli_prepare(
                    $conn,
                    'INSERT INTO admin_application_review_log (app_id, reviewer_id, new_status, create_time)
                     VALUES (?, ?, ?, NOW())'
                );
                if ($logStmt) {
                    mysqli_stmt_bind_param($logStmt, 'iis', $appId, $reviewerId, $st);
                    mysqli_stmt_execute($logStmt);
                    mysqli_stmt_close($logStmt);
                }
            }
        }
        $_SESSION['admin_flash_ok'] = 'Application updated.';
    } else {
        $_SESSION['admin_flash_err'] = 'Invalid application or status.';
    }
    header('Location: applications.php');
    exit;
}

$pageTitle = 'Adoption applications';
$adminNav = 'applications';
require_once __DIR__ . '/../include/admin_header.php';

$rows = [];
$sql = 'SELECT a.app_id, a.app_status, a.create_time, a.user_id, a.pet_id,
               u.username, u.user_email, u.user_phone, p.pet_name
        FROM adoption_application a
        LEFT JOIN users u ON u.user_id = a.user_id
        LEFT JOIN pets p ON p.pet_id = a.pet_id
        ORDER BY a.app_id DESC';
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $appId, $appStatus, $ctime, $uid, $pid, $uname, $uemail, $uphone, $pname);
    while (mysqli_stmt_fetch($stmt)) {
        $rows[] = [
            'app_id' => (int) $appId,
            'app_status' => (string) $appStatus,
            'create_time' => $ctime !== null ? (string) $ctime : '',
            'user_id' => (int) $uid,
            'pet_id' => (int) $pid,
            'username' => (string) $uname,
            'user_email' => $uemail !== null ? (string) $uemail : '',
            'user_phone' => $uphone !== null ? (string) $uphone : '',
            'pet_name' => (string) $pname,
        ];
    }
    mysqli_stmt_close($stmt);
}

if (!empty($_SESSION['admin_flash_ok'])) {
    echo '<div class="admin-flash ok">' . htmlspecialchars((string) $_SESSION['admin_flash_ok'], ENT_QUOTES, 'UTF-8') . '</div>';
    unset($_SESSION['admin_flash_ok']);
}
if (!empty($_SESSION['admin_flash_err'])) {
    echo '<div class="admin-flash err">' . htmlspecialchars((string) $_SESSION['admin_flash_err'], ENT_QUOTES, 'UTF-8') . '</div>';
    unset($_SESSION['admin_flash_err']);
}
?>

<h1 class="admin-page-title">Applications</h1>
<p class="admin-lead">Review requests and set status to pending, approved, or rejected.</p>

<div class="admin-panel">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Pet</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Update</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($rows === []) : ?>
                    <tr><td colspan="8">No applications yet.</td></tr>
                <?php else : ?>
                    <?php foreach ($rows as $r) : ?>
                        <tr>
                            <td><?php echo (int) $r['app_id']; ?></td>
                            <td><?php echo htmlspecialchars($r['username'] !== '' ? $r['username'] : ('#' . $r['user_id']), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($r['user_email'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($r['user_phone'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($r['pet_name'] !== '' ? $r['pet_name'] : ('#' . $r['pet_id']), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <?php
                                $cls = 'pending';
                                $ls = strtolower($r['app_status']);
                                if ($ls === 'approved') {
                                    $cls = 'approved';
                                } elseif ($ls === 'rejected') {
                                    $cls = 'rejected';
                                }
                                ?>
                                <span class="admin-badge <?php echo htmlspecialchars($cls, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($r['app_status'], ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($r['create_time'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <form method="post" class="inline-form" style="display:flex;flex-wrap:wrap;gap:0.35rem;align-items:center;">
                                    <?php echo admin_csrf_field(); ?>
                                    <input type="hidden" name="app_action" value="1">
                                    <input type="hidden" name="app_id" value="<?php echo (int) $r['app_id']; ?>">
                                    <select name="app_status" aria-label="New status for application <?php echo (int) $r['app_id']; ?>">
                                        <option value="pending"<?php echo $ls === 'pending' ? ' selected' : ''; ?>>pending</option>
                                        <option value="approved"<?php echo $ls === 'approved' ? ' selected' : ''; ?>>approved</option>
                                        <option value="rejected"<?php echo $ls === 'rejected' ? ' selected' : ''; ?>>rejected</option>
                                    </select>
                                    <button type="submit" class="admin-btn admin-btn-primary" style="padding:0.4rem 0.75rem;">Save</button>
                                </form>
                            </td>
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
