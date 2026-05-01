<?php
define('ADMIN_GUARD_LOGIN_PATH', '../login.php');
require_once __DIR__ . '/../include/admin_guard.php';
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../include/user_audit_log.php';

$pageTitle = 'User Audit Logs';
$adminNav = 'user_audit_logs';
require_once __DIR__ . '/../include/admin_header.php';

$rows = [];
if (user_audit_table_ready()) {
    $stmt = mysqli_prepare(
        $conn,
        'SELECT log_id, user_id, username, action_type, action_status, detail_note, ip_addr, create_time
         FROM user_audit_log
         ORDER BY log_id DESC
         LIMIT 300'
    );
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $logId, $userId, $username, $actionType, $status, $note, $ip, $ctime);
        while (mysqli_stmt_fetch($stmt)) {
            $rows[] = [
                'log_id' => (int) $logId,
                'user_id' => (int) $userId,
                'username' => (string) $username,
                'action_type' => (string) $actionType,
                'action_status' => (string) $status,
                'detail_note' => $note !== null ? (string) $note : '',
                'ip_addr' => (string) $ip,
                'create_time' => $ctime !== null ? (string) $ctime : '',
            ];
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<h1 class="admin-page-title">User Audit Logs</h1>
<p class="admin-lead">Registration and user modification records.</p>

<div class="admin-panel">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Action</th>
                    <th>Status</th>
                    <th>Note</th>
                    <th>IP</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($rows === []) : ?>
                    <tr><td colspan="8">No user audit logs yet.</td></tr>
                <?php else : ?>
                    <?php foreach ($rows as $r) : ?>
                        <?php $st = strtolower($r['action_status']); ?>
                        <tr>
                            <td><?php echo (int) $r['log_id']; ?></td>
                            <td><?php echo (int) $r['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($r['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($r['action_type'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><span class="admin-badge <?php echo $st === 'success' ? 'approved' : 'rejected'; ?>"><?php echo htmlspecialchars($r['action_status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td><?php echo htmlspecialchars($r['detail_note'], ENT_QUOTES, 'UTF-8'); ?></td>
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
