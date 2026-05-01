<?php
$page = 'register';
$pageTitle = 'Create account — Pet Adoption Platform';
require_once __DIR__ . '/include/auth_store.php';
require_once __DIR__ . '/include/user_audit_log.php';

$reg_error = '';

/**
 * Normalize phone number to a unified digits-only format for storage.
 */
function normalize_phone(string $raw): string
{
    return preg_replace('/\D+/', '', $raw) ?? '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim((string) $_POST['username']) : '';
    $email = isset($_POST['email']) ? trim((string) $_POST['email']) : '';
    $phoneRaw = isset($_POST['phone']) ? trim((string) $_POST['phone']) : '';
    $phone = normalize_phone($phoneRaw);
    $password = isset($_POST['password']) ? (string) $_POST['password'] : '';
    $password2 = isset($_POST['password_confirm']) ? (string) $_POST['password_confirm'] : '';

    if ($username === '' || $email === '' || $phoneRaw === '' || $password === '') {
        $reg_error = 'Please enter username, email, phone, and password.';
    } elseif (strlen($username) < 3 || strlen($username) > 32) {
        $reg_error = 'Username must be between 3 and 32 characters.';
    } elseif (!preg_match('/^[a-zA-Z0-9_\x{4e00}-\x{9fa5}]+$/u', $username)) {
        $reg_error = 'Username may only contain letters, numbers, underscores, or CJK characters.';
    } elseif (strlen($password) < 6) {
        $reg_error = 'Password must be at least 6 characters.';
    } elseif ($password !== $password2) {
        $reg_error = 'Passwords do not match.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $reg_error = 'Please enter a valid email address.';
    } elseif (!preg_match('/^\d{8,15}$/', $phone)) {
        $reg_error = 'Please enter a valid phone number (8-15 digits).';
    } elseif (strcasecmp($username, 'admin') === 0) {
        $reg_error = 'That username is reserved. Please choose another.';
    } elseif (auth_find_user_by_username($username) !== null) {
        $reg_error = 'That username is already taken. Sign in or pick a different one.';
    } else {
        if (!user_audit_db_ready()) {
            $reg_error = 'Database is not connected. Please try again later.';
            user_audit_add(0, $username, 'register', 'failed', 'db_not_connected');
        } elseif (auth_register($username, $password, $email, $phone)) {
            $createdUser = auth_find_user_by_username($username);
            $createdId = $createdUser !== null ? (int) $createdUser['user_id'] : 0;
            user_audit_add($createdId, $username, 'register', 'success', 'account_created');
            header('Location: login.php?registered=1');
            exit;
        } else {
            $reg_error = 'Registration failed. Please try again later.';
            user_audit_add(0, $username, 'register', 'failed', 'insert_failed');
        }
    }
}

include 'header.php';
?>
<link rel="stylesheet" href="assets/css/auth-pet.css">

<div class="auth-pet-page">
<div class="auth-pet-bg">
    <div class="auth-pet-paws" aria-hidden="true"></div>
    <div class="auth-pet-shell">
        <aside class="auth-pet-hero">
            <div class="auth-pet-badge">💛 New friend</div>
            <h1>Start your adoption story</h1>
            <p>Create an adopter profile and commit to informed, prepared, lifelong care—your first step toward a rescued companion.</p>
            <div class="auth-pet-tags">
                <span class="auth-pet-tag">Thoughtful matching</span>
                <span class="auth-pet-tag">Secure password</span>
                <span class="auth-pet-tag">Local-friendly</span>
            </div>
        </aside>
        <div class="auth-pet-panel">
            <div class="auth-pet-panel-head">
                <h2>Create an account</h2>
                <p class="sub">Set up your adopter profile</p>
            </div>
            <?php if ($reg_error !== ''): ?>
                <div class="auth-pet-msg err" role="alert"><?php echo htmlspecialchars($reg_error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="auth-pet-field">
                    <label for="reg_username">Username</label>
                    <input type="text" id="reg_username" name="username" placeholder="3–32 characters" autocomplete="username" required maxlength="32">
                </div>
                <div class="auth-pet-field">
                    <label for="reg_email">Email</label>
                    <input type="email" id="reg_email" name="email" placeholder="For updates and recovery" autocomplete="email" required>
                </div>
                <div class="auth-pet-field">
                    <label for="reg_phone">Phone</label>
                    <input type="tel" id="reg_phone" name="phone" placeholder="8-15 digits (symbols allowed)" autocomplete="tel" required>
                </div>
                <div class="auth-pet-field">
                    <label for="reg_password">Password</label>
                    <input type="password" id="reg_password" name="password" placeholder="At least 6 characters" autocomplete="new-password" required minlength="6">
                </div>
                <div class="auth-pet-field">
                    <label for="reg_password_confirm">Confirm password</label>
                    <input type="password" id="reg_password_confirm" name="password_confirm" placeholder="Re-enter your password" autocomplete="new-password" required>
                </div>
                <button type="submit" class="auth-pet-btn">Complete registration</button>
            </form>
            <div class="auth-pet-links">
                Already have an account? <a href="login.php">Back to sign in</a>
            </div>
        </div>
    </div>
</div>
</div>

<?php include 'footer.php'; ?>
