<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>关于我们 - 宠物领养平台</title>
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
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 5vw 80px;
        }
        .page-title {
            font-size: 48px;
            font-weight: 300;
            text-align: center;
            margin-bottom: 60px;
            letter-spacing: 1px;
        }
        .content-block {
            font-size: 18px;
            line-height: 1.8;
            color: #424245;
            margin-bottom: 40px;
        }
        .content-block h3 {
            font-size: 28px;
            font-weight: 400;
            margin: 40px 0 20px;
            color: #1d1d1f;
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
        <a href="about.php" class="active">关于我们</a >
        <a href="login.php">登录</a >
    </div>
</nav>

<div class="container">
    <h1 class="page-title">关于我们</h1>
    <div class="content-block">
        <h3>平台简介</h3>
        <p>我们是一个专注于宠物领养的公益平台，致力于为流浪动物寻找温暖的家，推动“领养代替购买”的理念，减少流浪动物数量，让每一个生命都能被温柔以待。</p >
        <p>自成立以来，我们已帮助上千只流浪猫狗成功领养，搭建了送养人与领养人之间的信任桥梁，为宠物和家庭创造双向奔赴的幸福。</p >
    </div>
    <div class="content-block">
        <h3>我们的使命</h3>
        <p>✅ 让每一只流浪宠物都有机会拥有温暖的家</p >
        <p>✅ 规范领养流程，保障宠物和领养人的权益</p >
        <p>✅ 普及科学养宠知识，提升全民动物保护意识</p >
        <p>✅ 推动动物福利事业发展，共建人宠和谐社会</p >
    </div>
    <div class="content-block">
        <h3>联系我们</h3>
        <p>📧 邮箱：contact@petadopt.com</p >
        <p>📞 电话：400-XXX-XXXX</p >
        <p>📍 地址：XX市XX区XX路XX号</p >
        <p>💬 微信公众号：宠物领养平台</p >
    </div>
    <div class="content-block">
        <h3>加入我们</h3>
        <p>如果你也热爱动物，愿意为流浪宠物贡献力量，欢迎加入我们的志愿者团队，参与领养活动、救助流浪动物、宣传领养理念。</p >
        <p>志愿者申请邮箱：volunteer@petadopt.com</p >
    </div>
</div>

<footer>
    © 2026 宠物领养平台 保留所有权利
</footer>
</body>
</html>