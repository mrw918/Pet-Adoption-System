<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page = 'login';

$pageTitle = 'Sign in — Pet Adoption Platform';

require_once __DIR__ . '/include/auth_store.php';



$login_error = '';

$login_ok = '';

if (isset($_GET['registered']) && $_GET['registered'] === '1') {

    $login_ok = 'Registration successful. Please sign in with your new account.';

}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = isset($_POST['username']) ? trim((string) $_POST['username']) : '';

    $password = isset($_POST['password']) ? (string) $_POST['password'] : '';

    $is_admin = isset($_POST['is_admin']);



    if ($username === '' || $password === '') {

        $login_error = 'Please enter both username and password.';

    } else {

        $result = auth_login_resolve($username, $password, $is_admin);

        if ($result === 'admin') {
            $adminRow = auth_find_user_by_username($username);
            if ($adminRow !== null) {
                $_SESSION['user_id'] = $adminRow['user_id'];
                $_SESSION['username'] = $adminRow['username'];
                $_SESSION['is_admin'] = 1;
                $_SESSION['is_super_admin'] = (int) ($adminRow['is_super_admin'] ?? 0);
            }
            header('Location: admin/index.php');
            exit;
        }

        if ($result === 'user') {
            $userRow = auth_find_user_by_username($username);
            if ($userRow !== null) {
                $_SESSION['user_id'] = $userRow['user_id'];
                $_SESSION['username'] = $userRow['username'];
                $_SESSION['is_admin'] = 0;
                $_SESSION['is_super_admin'] = 0;
            }
            header('Location: browse.php');
            exit;
        }

        if ($is_admin) {

            $login_error = 'Invalid administrator username or password.';

        } else {

            $userRow = auth_find_user_by_username($username);

            if ($userRow !== null && $userRow['is_admin'] === 1 && auth_password_matches($password, $userRow['user_pwd'])) {

                $login_error = 'This account is an administrator. Please check “Sign in as administrator” and try again.';

            } else {

                $login_error = 'Invalid username or password. You can create an account below.';

            }

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

            <h1>Welcome back, future pet parent</h1>

            <p>Sign in to meet adoptable companions and keep “adopt, don’t shop” at the heart of your journey—thoughtful care starts here.</p>

            <div class="auth-pet-tags">

                <span class="auth-pet-tag">Responsible care</span>

                <span class="auth-pet-tag">Adoption-first</span>

                <span class="auth-pet-tag">Warm community</span>

            </div>

        </aside>

        <div class="auth-pet-panel">

            <div class="auth-pet-panel-head">

                <h2>Sign in</h2>

                <p class="sub">Continue your adoption journey</p>

            </div>

            <?php if ($login_error !== ''): ?>

                <div class="auth-pet-msg err" role="alert"><?php echo htmlspecialchars($login_error, ENT_QUOTES, 'UTF-8'); ?></div>

            <?php endif; ?>

            <?php if ($login_ok !== ''): ?>

                <div class="auth-pet-msg ok" role="status"><?php echo htmlspecialchars($login_ok, ENT_QUOTES, 'UTF-8'); ?></div>

            <?php endif; ?>

            <form method="post">

                <div class="auth-pet-field">

                    <label for="username">Username</label>

                    <input type="text" id="username" name="username" placeholder="Your username" autocomplete="username" required>

                </div>

                <div class="auth-pet-field">

                    <label for="password">Password</label>

                    <input type="password" id="password" name="password" placeholder="Your password" autocomplete="current-password" required>

                </div>

                <div class="auth-pet-check">

                    <input type="checkbox" id="is_admin" name="is_admin">

                    <label for="is_admin">Sign in as administrator</label>

                </div>

                <button type="submit" class="auth-pet-btn">Enter the platform</button>

            </form>

            <div class="auth-pet-links">
                <a href="reset_password.php">Forgot password?</a><br>
                No account yet? <a href="register.php">Create one</a>

            </div>

        </div>

    </div>

</div>

</div>



<?php include 'footer.php'; ?>

