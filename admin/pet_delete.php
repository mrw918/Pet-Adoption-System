<?php
define('ADMIN_GUARD_LOGIN_PATH', '../login.php');
require_once __DIR__ . '/../include/admin_guard.php';
require_once __DIR__ . '/../include/admin_csrf.php';
require_once __DIR__ . '/../dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: pets.php');
    exit;
}

if (!admin_csrf_verify()) {
    $_SESSION['admin_flash_err'] = 'Invalid security token. Try again.';
    header('Location: pets.php');
    exit;
}

$petId = isset($_POST['pet_id']) ? (int) $_POST['pet_id'] : 0;
if ($petId <= 0) {
    $_SESSION['admin_flash_err'] = 'Invalid pet.';
    header('Location: pets.php');
    exit;
}

$stmt = mysqli_prepare($conn, 'DELETE FROM adoption_application WHERE pet_id = ?');
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $petId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

$stmt = mysqli_prepare($conn, 'DELETE FROM pets WHERE pet_id = ? LIMIT 1');
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $petId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

$_SESSION['admin_flash_ok'] = 'Pet deleted.';
header('Location: pets.php');
exit;
