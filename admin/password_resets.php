<?php
define('ADMIN_GUARD_LOGIN_PATH', '../login.php');
require_once __DIR__ . '/../include/admin_guard.php';
require_once __DIR__ . '/../dbconnect.php';

$pageTitle = 'Password reset logs';
$adminNav = 'password_resets';
require_once __DIR__ . '/../include/admin_header.php';

$rows = [];

$createSql = "CREATE TABLE IF NOT EXISTS password_reset_log (
                log_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                user_id INT NULL,
                username VARCHAR(64) NOT NULL,
                email_input VARCHAR(255) NOT NULL,
                reset_status VARCHAR(20) NOT NULL,
                note VARCHAR(255) NULL,
                ip_addr VARCHAR(45) NOT NULL,
                create_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($conn, $createSql);

$sql = 'SELECT log_id, user_id, username, email_input, reset_status, note, ip_addr, create_time
        FROM password_reset_log
        ORDER BY log_id DESC
        LIMIT 300';
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $logId, $userId, $username, $emailInput, $status, $note, $ip, $ctime);
    while (mysqli_stmt_fetch($stmt)) {
        $rows[] = [
            'log_id' => (int) $logId,
            'user_id' => (int) $userId,
            'username' => (string) $username,
            'email_input' => (string) $emailInput,
            'reset_status' => (string) $status,
            'note' => $note !== null ? (string) $note : '',
            'ip_addr' => (string) $ip,
            'create_time' => $ctime !== null ? (string) $ctime : '',
        ];
    }
    mysqli_stmt_close($stmt);
}
?>

<h1 class="admin-page-title">Password Reset Logs</h1>
<p class="admin-lead">View reset attempts and successful password updates from the login page.</p>

<div class="admin-panel">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email Input</th>
                    <th>Status</th>
                    <th>Note</th>
                    <th>IP</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($rows === []) : ?>
                    <tr><td colspan="8">No reset logs yet.</td></tr>
                <?php else : ?>
                    <?php foreach ($rows as $r) : ?>
                        <?php $st = strtolower($r['reset_status']); ?>
                        <tr>
                            <td><?php echo (int) $r['log_id']; ?></td>
                            <td><?php echo (int) $r['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($r['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($r['email_input'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <span class="admin-badge <?php echo $st === 'success' ? 'approved' : 'rejected'; ?>">
                                    <?php echo htmlspecialchars($r['reset_status'], ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($r['note'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($r['ip_addr'], ENT_QUOTES, 'UTF-8'); ?></td>
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
