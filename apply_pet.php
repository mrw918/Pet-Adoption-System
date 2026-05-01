<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=UTF-8');

/**
 * @param array<string, mixed> $data
 */
function apply_json_response(int $httpCode, array $data): void
{
    http_response_code($httpCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    apply_json_response(401, ['ok' => false, 'error' => 'Please log in to apply.']);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    apply_json_response(405, ['ok' => false, 'error' => 'Method not allowed.']);
}

require_once __DIR__ . '/include/browse_csrf.php';

$raw = file_get_contents('php://input');
$input = json_decode($raw !== false && $raw !== '' ? $raw : '[]', true);
if (!is_array($input)) {
    $input = [];
}

$csrf = isset($input['csrf']) ? (string) $input['csrf'] : (isset($_POST['csrf']) ? (string) $_POST['csrf'] : '');
$petId = isset($input['pet_id']) ? (int) $input['pet_id'] : (isset($_POST['pet_id']) ? (int) $_POST['pet_id'] : 0);

if ($csrf === '' || !browse_csrf_verify_string($csrf)) {
    apply_json_response(403, ['ok' => false, 'error' => 'Invalid security token. Refresh the page and try again.']);
}

if ($petId <= 0) {
    apply_json_response(400, ['ok' => false, 'error' => 'Invalid pet.']);
}

require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/include/pets_data.php';

$stmt = mysqli_prepare($conn, 'SELECT pet_status FROM pets WHERE pet_id = ? LIMIT 1');
if ($stmt === false) {
    apply_json_response(500, ['ok' => false, 'error' => 'Server error.']);
}

mysqli_stmt_bind_param($stmt, 'i', $petId);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $petStatusRaw);
$found = mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!$found) {
    apply_json_response(404, ['ok' => false, 'error' => 'Pet not found.']);
}

$petStatusRaw = (string) $petStatusRaw;
if (pets_status_key($petStatusRaw) !== 'available') {
    apply_json_response(400, ['ok' => false, 'error' => 'This pet is not available for adoption.']);
}

$userId = (int) $_SESSION['user_id'];

$dup = mysqli_prepare(
    $conn,
    "SELECT app_id FROM adoption_application WHERE user_id = ? AND pet_id = ? AND LOWER(app_status) = 'pending' LIMIT 1"
);
if ($dup === false) {
    apply_json_response(500, ['ok' => false, 'error' => 'Server error.']);
}
mysqli_stmt_bind_param($dup, 'ii', $userId, $petId);
mysqli_stmt_execute($dup);
mysqli_stmt_bind_result($dup, $dupAppId);
$hasPending = mysqli_stmt_fetch($dup);
mysqli_stmt_close($dup);

if ($hasPending) {
    apply_json_response(409, ['ok' => false, 'error' => 'You already have a pending application for this pet.']);
}

$status = 'pending';
$ins = mysqli_prepare(
    $conn,
    'INSERT INTO adoption_application (user_id, pet_id, app_status) VALUES (?, ?, ?)'
);
if ($ins === false) {
    apply_json_response(500, ['ok' => false, 'error' => 'Server error.']);
}
mysqli_stmt_bind_param($ins, 'iis', $userId, $petId, $status);
$ok = mysqli_stmt_execute($ins);
mysqli_stmt_close($ins);

if (!$ok) {
    apply_json_response(500, ['ok' => false, 'error' => 'Could not save your application.']);
}

apply_json_response(200, [
    'ok' => true,
    'message' => 'Your application has been submitted. Our team will review it.',
]);
