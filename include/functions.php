<?php

require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/user_audit_log.php';

/**
 * True if stored value looks like a bcrypt hash (new signups use this).
 */
function auth_stored_password_is_bcrypt(string $stored): bool
{
    return (bool) preg_match('/^\$2[ayb]\$.{56}$/', $stored);
}

/**
 * Match plain password against DB value (bcrypt or legacy plain text).
 */
function auth_password_matches(string $plain, string $stored): bool
{
    $stored = (string) $stored;
    if ($stored === '') {
        return false;
    }
    if (auth_stored_password_is_bcrypt($stored)) {
        return password_verify($plain, $stored);
    }

    return hash_equals($stored, $plain);
}

/**
 * @return array{
 *   user_id:int,
 *   username:string,
 *   user_pwd:string,
 *   user_email:string,
 *   user_phone:string,
 *   user_role:string,
 *   create_time:?string,
 *   is_admin:int,
 *   is_super_admin:int
 * }|null
 */
function auth_find_user_by_username(string $username): ?array
{
    global $conn;

    $sql = 'SELECT user_id, username, user_pwd, user_email, user_phone, user_role, create_time
            FROM users
            WHERE LOWER(username) = LOWER(?)
            LIMIT 1';
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
        return null;
    }

    mysqli_stmt_bind_param($stmt, 's', $username);
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return null;
    }

    mysqli_stmt_bind_result($stmt, $user_id, $uname, $user_pwd, $user_email, $user_phone, $user_role, $create_time);
    if (!mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_close($stmt);
        return null;
    }
    mysqli_stmt_close($stmt);

    $role = strtolower((string) $user_role);

    $isAdmin = in_array($role, ['admin', 'super_admin'], true) ? 1 : 0;
    $isSuperAdmin = $role === 'super_admin' ? 1 : 0;

    return [
        'user_id' => (int) $user_id,
        'username' => (string) $uname,
        'user_pwd' => (string) $user_pwd,
        'user_email' => $user_email !== null ? (string) $user_email : '',
        'user_phone' => $user_phone !== null ? (string) $user_phone : '',
        'user_role' => (string) $user_role,
        'create_time' => $create_time !== null ? (string) $create_time : null,
        'is_admin' => $isAdmin,
        'is_super_admin' => $isSuperAdmin,
    ];
}

/**
 * @deprecated use auth_find_user_by_username
 */
function auth_find_registered_user(string $username): ?array
{
    return auth_find_user_by_username($username);
}

function auth_register(string $username, string $password, string $email, string $phone): bool
{
    global $conn;

    $pwdStored = password_hash($password, PASSWORD_DEFAULT);

    $sql = 'INSERT INTO users (username, user_pwd, user_email, user_phone, user_role, create_time)
            VALUES (?, ?, ?, ?, \'user\', NOW())';
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, 'ssss', $username, $pwdStored, $email, $phone);

    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $ok;
}

function auth_password_reset_log_table_ready(): bool
{
    global $conn;

    $sql = "CREATE TABLE IF NOT EXISTS password_reset_log (
                log_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                user_id INT NULL,
                username VARCHAR(64) NOT NULL,
                email_input VARCHAR(255) NOT NULL,
                reset_status VARCHAR(20) NOT NULL,
                note VARCHAR(255) NULL,
                ip_addr VARCHAR(45) NOT NULL,
                create_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    return mysqli_query($conn, $sql) === true;
}

function auth_password_reset_log_add(
    int $userId,
    string $username,
    string $emailInput,
    string $status,
    string $note = ''
): void {
    global $conn;
    if (!auth_password_reset_log_table_ready()) {
        return;
    }

    $ip = isset($_SERVER['REMOTE_ADDR']) ? trim((string) $_SERVER['REMOTE_ADDR']) : '';
    if ($ip === '') {
        $ip = 'unknown';
    }
    $uid = $userId > 0 ? $userId : null;

    $sql = 'INSERT INTO password_reset_log (user_id, username, email_input, reset_status, note, ip_addr, create_time)
            VALUES (?, ?, ?, ?, ?, ?, NOW())';
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
        return;
    }

    mysqli_stmt_bind_param($stmt, 'isssss', $uid, $username, $emailInput, $status, $note, $ip);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

/**
 * Reset user password by username + email match.
 *
 * @return array{ok:bool,message:string}
 */
function auth_reset_password(string $username, string $email, string $newPassword): array
{
    global $conn;

    if (!user_audit_db_ready()) {
        user_audit_add(0, $username, 'password_reset', 'failed', 'db_not_connected');
        return ['ok' => false, 'message' => 'Database is not connected. Please try again later.'];
    }

    $user = auth_find_user_by_username($username);
    if ($user === null) {
        auth_password_reset_log_add(0, $username, $email, 'failed', 'username_not_found');
        user_audit_add(0, $username, 'password_reset', 'failed', 'username_not_found');
        return ['ok' => false, 'message' => 'Username or email is incorrect.'];
    }

    $dbEmail = strtolower(trim((string) $user['user_email']));
    $inputEmail = strtolower(trim($email));
    if ($dbEmail === '' || $inputEmail === '' || !hash_equals($dbEmail, $inputEmail)) {
        auth_password_reset_log_add((int) $user['user_id'], $username, $email, 'failed', 'email_mismatch');
        user_audit_add((int) $user['user_id'], $username, 'password_reset', 'failed', 'email_mismatch');
        return ['ok' => false, 'message' => 'Username or email is incorrect.'];
    }

    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn, 'UPDATE users SET user_pwd = ? WHERE user_id = ? LIMIT 1');
    if ($stmt === false) {
        auth_password_reset_log_add((int) $user['user_id'], $username, $email, 'failed', 'db_prepare_failed');
        user_audit_add((int) $user['user_id'], $username, 'password_reset', 'failed', 'db_prepare_failed');
        return ['ok' => false, 'message' => 'Failed to update password. Please try again.'];
    }

    $uid = (int) $user['user_id'];
    mysqli_stmt_bind_param($stmt, 'si', $newHash, $uid);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if (!$ok) {
        auth_password_reset_log_add($uid, $username, $email, 'failed', 'db_execute_failed');
        user_audit_add($uid, $username, 'password_reset', 'failed', 'db_execute_failed');
        return ['ok' => false, 'message' => 'Failed to update password. Please try again.'];
    }

    auth_password_reset_log_add($uid, $username, $email, 'success', 'password_updated');
    user_audit_add($uid, $username, 'password_reset', 'success', 'password_updated');
    return ['ok' => true, 'message' => 'Password updated. Please sign in with your new password.'];
}

/**
 * @return 'admin'|'user'|null
 */
function auth_login_resolve(string $username, string $password, bool $wantAdmin): ?string
{
    $row = auth_find_user_by_username($username);
    if ($row === null) {
        return null;
    }

    if (!auth_password_matches($password, $row['user_pwd'])) {
        return null;
    }

    if ($wantAdmin) {
        return $row['is_admin'] === 1 ? 'admin' : null;
    }

    if ($row['is_admin'] === 1) {
        return null;
    }

    return 'user';
}
