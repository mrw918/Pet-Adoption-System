<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page = 'login';
$pageTitle = 'Reset password — Pet Adoption Platform';

require_once __DIR__ . '/include/auth_store.php';

$err = '';
$ok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim((string) $_POST['username']) : '';
    $email = isset($_POST['email']) ? trim((string) $_POST['email']) : '';
    $newPassword = isset($_POST['new_password']) ? (string) $_POST['new_password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? (string) $_POST['confirm_password'] : '';

    if ($username === '' || $email === '' || $newPassword === '' || $confirmPassword === '') {
        $err = 'Please complete all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = 'Please enter a valid email address.';
    } elseif (strlen($newPassword) < 6) {
        $err = 'New password must be at least 6 characters.';
    } elseif (!hash_equals($newPassword, $confirmPassword)) {
        $err = 'The two password fields do not match.';
    } else {
        $result = auth_reset_password($username, $email, $newPassword);
        if (!empty($result['ok'])) {
            $ok = (string) $result['message'];
        } else {
            $err = (string) ($result['message'] ?? 'Reset failed.');
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
            <div class="auth-pet-badge">🐾 Pet Adoption</div>
            <h1>Reset your password</h1>
            <p>Verify your account with your username and registered email, then set a new password safely.</p>
            <div class="auth-pet-tags">
                <span class="auth-pet-tag">Account recovery</span>
                <span class="auth-pet-tag">Secure reset</span>
                <span class="auth-pet-tag">Back to adoption</span>
            </div>
        </aside>

        <div class="auth-pet-panel">
            <div class="auth-pet-panel-head">
                <h2>Reset password</h2>
                <p class="sub">Fill in your account details</p>
            </div>

            <?php if ($err !== ''): ?>
                <div class="auth-pet-msg err" role="alert"><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if ($ok !== ''): ?>
                <div class="auth-pet-msg ok" role="status"><?php echo htmlspecialchars($ok, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="auth-pet-field">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Your username" required>
                </div>
                <div class="auth-pet-field">
                    <label for="email">Registered email</label>
                    <input type="email" id="email" name="email" placeholder="name@example.com" required>
                </div>
                <div class="auth-pet-field">
                    <label for="new_password">New password</label>
                    <input type="password" id="new_password" name="new_password" placeholder="At least 6 characters" required>
                </div>
                <div class="auth-pet-field">
                    <label for="confirm_password">Confirm new password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter new password" required>
                </div>
                <button type="submit" class="auth-pet-btn">Update password</button>
            </form>

            <div class="auth-pet-links">
                Remembered your password? <a href="login.php">Back to sign in</a>
            </div>
        </div>
    </div>
</div>
</div>

<?php include 'footer.php'; ?>
