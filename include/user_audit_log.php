<?php
require_once __DIR__ . '/../dbconnect.php';

function user_audit_db_ready(): bool
{
    global $conn;
    if (!isset($conn) || !($conn instanceof mysqli) || mysqli_connect_errno()) {
        return false;
    }
    $ping = mysqli_query($conn, 'SELECT 1');
    if ($ping === false) {
        return false;
    }
    mysqli_free_result($ping);
    return true;
}

function user_audit_table_ready(): bool
{
    global $conn;
    if (!user_audit_db_ready()) {
        return false;
    }

    $sql = "CREATE TABLE IF NOT EXISTS user_audit_log (
                log_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                user_id INT NULL,
                username VARCHAR(64) NOT NULL,
                action_type VARCHAR(40) NOT NULL,
                action_status VARCHAR(20) NOT NULL,
                detail_note VARCHAR(255) NULL,
                ip_addr VARCHAR(45) NOT NULL,
                create_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    return mysqli_query($conn, $sql) === true;
}

function user_audit_add(
    int $userId,
    string $username,
    string $actionType,
    string $actionStatus,
    string $detailNote = ''
): void {
    global $conn;
    if (!user_audit_table_ready()) {
        return;
    }

    $uid = $userId > 0 ? $userId : null;
    $ip = isset($_SERVER['REMOTE_ADDR']) ? trim((string) $_SERVER['REMOTE_ADDR']) : '';
    if ($ip === '') {
        $ip = 'unknown';
    }

    $stmt = mysqli_prepare(
        $conn,
        'INSERT INTO user_audit_log (user_id, username, action_type, action_status, detail_note, ip_addr, create_time)
         VALUES (?, ?, ?, ?, ?, ?, NOW())'
    );
    if ($stmt === false) {
        return;
    }
    mysqli_stmt_bind_param($stmt, 'isssss', $uid, $username, $actionType, $actionStatus, $detailNote, $ip);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
