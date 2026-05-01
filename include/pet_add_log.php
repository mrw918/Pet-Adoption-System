<?php
require_once __DIR__ . '/../dbconnect.php';

function pet_add_db_ready(): bool
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

function pet_add_log_table_ready(): bool
{
    global $conn;
    if (!pet_add_db_ready()) {
        return false;
    }

    $sql = "CREATE TABLE IF NOT EXISTS pet_add_log (
                log_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                admin_id INT NULL,
                pet_id INT NULL,
                pet_name VARCHAR(80) NOT NULL,
                action_status VARCHAR(20) NOT NULL,
                detail_note VARCHAR(255) NULL,
                ip_addr VARCHAR(45) NOT NULL,
                create_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    return mysqli_query($conn, $sql) === true;
}

function pet_add_log_add(
    int $adminId,
    int $petId,
    string $petName,
    string $status,
    string $note = ''
): void {
    global $conn;
    if (!pet_add_log_table_ready()) {
        return;
    }

    $aid = $adminId > 0 ? $adminId : null;
    $pid = $petId > 0 ? $petId : null;
    $ip = isset($_SERVER['REMOTE_ADDR']) ? trim((string) $_SERVER['REMOTE_ADDR']) : '';
    if ($ip === '') {
        $ip = 'unknown';
    }

    $stmt = mysqli_prepare(
        $conn,
        'INSERT INTO pet_add_log (admin_id, pet_id, pet_name, action_status, detail_note, ip_addr, create_time)
         VALUES (?, ?, ?, ?, ?, ?, NOW())'
    );
    if ($stmt === false) {
        return;
    }
    mysqli_stmt_bind_param($stmt, 'iissss', $aid, $pid, $petName, $status, $note, $ip);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
