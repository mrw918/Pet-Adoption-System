<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>用户登录 - 宠物领养平台</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: "SF Pro Display", "PingFang SC", "Microsoft YaHei", sans-serif;
            background-color: #fff;
            color: #1d1d1f;
            -webkit-font-smoothing: antialiased;
            padding-top: 60px;
            background-color: #f5f5f7;
        }
        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 60px;
            background: rgba(255,255,255,0.95);
            backdrop-filter: saturate(180%) blur(20px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 5vw;
            z-index: 9999;
        }
        .logo {
            font-size: 20px;
            font-weight: 500;
        }
        .nav-links a {
            color: #1d1d1f;
            text-decoration: none;
            margin: 0 18px;
            font-size: 14px;
            transition: 0.2s;
        }
        .nav-links a.active {
            color: #0071e3;
            font-weight: 500;
        }
        .nav-links a:hover {
            color: #0071e3;
        }

        .login-wrapper {
            min-height: calc(100vh - 140px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .login-card {
            background: white;
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .login-card h2 {
            font-size: 32px;
            font-weight: 400;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-item {
            margin-bottom: 20px;
            margin-top: 10px;
        }
        .form-item label {
            display: block;
            font-size: 14px;
            color: #1d1d1f;
            margin-bottom: 8px;
        }
        .form-item input {
            width: 100%;
            height: 48px;
            padding: 0 16px;
            border: 1px solid #d2d2d7;
            border-radius: 12px;
            font-size: 16px;
            outline: none;
            transition: 0.2s;
        }
        .form-item input:focus {
            border-color: #0071e3;
            box-shadow: 0 0 0 4px rgba(0,113,227,0.1);
        }
        .btn-submit {
            width: 100%;
            height: 48px;
            background: #0071e3;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn-submit:hover {
            background: #0077ed;
        }
        .link-group {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: #86868b;
        }
        .link-group a {
            color: #0071e3;
            text-decoration: none;
        }
        footer {
            padding: 40px 5vw;
            text-align: center;
            font-size: 12px;
            color: #86868b;
            background: #f5f5f7;
        }
    </style>
</head>
<body>

<nav>
    <div class="logo">宠物领养</div>
    <div class="nav-links">
        <a href="index.php">首页</a >
        <a href="guide.php">送养指南</a >
        <a href="about.php">关于我们</a >
        <a href="login.php" class="active">登录</a >
    </div>
</nav>

<?php
$redirect_url = isset($_GET['redirect']) ? $_GET['redirect'] : 'https://待领养页面地址.com';

// 把下面的域名替换成你朋友页面的域名（如 https://friend-site.com）
$allowed_domain = 'https://你朋友的待领养页面地址.com';
if (strpos($redirect_url, $allowed_domain) !== 0) {
    $redirect_url = 'index.php';
}
?>

<div class="login-wrapper">
    <div class="login-card">
        <h2>账号登录</h2>
        <form action="" method="post">
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect_url); ?>">
            <div class="form-item">
                <label>用户名 / 手机号</label>
                <input type="text" name="username" placeholder="请输入用户名" required>
            </div>
            <div class="form-item">
                <label>密码</label>
                <input type="password" name="password" placeholder="请输入密码" required>
            </div>
            <button type="submit" class="btn-submit">登录</button>
        </form>
        <div class="link-group">
            还没有账号？<a href="#">立即注册</a >
        </div>
    </div>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取表单数据
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $redirect = trim($_POST['redirect']);

    $valid_user = 'admin';
    $valid_pass = '123456';
    // --------------------------

    if ($username === $valid_user && $password === $valid_pass) {
        header("Location: $redirect");
        exit;
    } else {
        echo "<script>alert('用户名或密码错误，请重试！');</script>";
    }
}
?>

<footer>
    © 2026 宠物领养平台 保留所有权利
</footer>

</body>
</html>