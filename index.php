<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>领养宠物 - 给它们一个家</title>
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
        .hero {
            width: 100%;
            height: calc(100vh - 60px);
            background: url(https://picsum.photos/id/237/1920/1080) center center / cover no-repeat;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }
        .hero h1 {
            font-size: 56px;
            font-weight: 300;
            margin-bottom: 16px;
            letter-spacing: 1px;
        }
        .hero p {
            font-size: 22px;
            font-weight: 300;
            margin-bottom: 40px;
        }
        .btn {
            padding: 12px 30px;
            background: #0071e3;
            color: white;
            border-radius: 30px;
            text-decoration: none;
            font-size: 16px;
            transition: 0.3s;
        }
        .btn:hover {
            background: #0077ed;
        }
        .pet-section {
            padding: 100px 5vw;
            text-align: center;
        }
        .pet-section h2 {
            font-size: 40px;
            font-weight: 300;
            margin-bottom: 60px;
        }
        .pet-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 40px;
        }
        .pet-card {
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        .pet-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.08);
        }
        .pet-card img {
            width: 100%;
            height: 280px;
            object-fit: cover;
        }
        .pet-info {
            padding: 20px;
            text-align: left;
        }
        .pet-name {
            font-size: 20px;
            font-weight: 500;
            margin-bottom: 6px;
        }
        .pet-desc {
            font-size: 14px;
            color: #86868b;
        }
        .idea {
            padding: 120px 10vw;
            background: #f5f5f7;
            text-align: center;
        }
        .idea p {
            font-size: 24px;
            font-weight: 300;
            line-height: 1.6;
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
        <a href="index.php" class="active">首页</a >
        <a href="guide.php">送养指南</a >
        <a href="about.php">关于我们</a >
        <a href="login.php">登录</a >
    </div>
</nav>

<section class="hero">
    <h1>给它们一个家</h1>
    <p>领养代替购买，用爱结束流浪</p >
    <a href="main" class="btn">查看待领养宠物</a >
</section>

<section class="pet-section" id="pets">
    <h2>寻得归宿的小生命</h2>
    <div class="pet-list">
     <?php
$pets = [
    [
        'id' => 21,
        'name' => '旺旺',
        'breed' => '拉布拉多',
        'age' => '11岁',
        'gender' => '雌性',
        'health' => '健康,已接种疫苗',
        'personality' => '独立安静,忠诚护主',
        'status' => '已领养',
        'imgurl' => 'https://picsum.photos/id/1016/800/600'
    ],
    [
        'id' => 22,
        'name' => '宝宝',
        'breed' => '金毛犬',
        'age' => '10岁',
        'gender' => '雌性',
        'health' => '已绝育,健康',
        'personality' => '独立安静,活泼好动',
        'status' => '已领养',
        'imgurl' => 'https://picsum.photos/id/133/800/600'
    ],
    [
        'id' => 23,
        'name' => '毛毛',
        'breed' => '柯基',
        'age' => '6岁',
        'gender' => '雄性',
        'health' => '轻微牙结石,已驱虫',
        'personality' => '慵懒随和,活泼好动',
        'status' => '已领养',
        'imgurl' => 'https://picsum.photos/id/201/800/600'
    ],
    [
        'id' => 24,
        'name' => '团团',
        'breed' => '英短',
        'age' => '6岁',
        'gender' => '雄性',
        'health' => '健康,已接种疫苗',
        'personality' => '独立安静,聪明机敏',
        'status' => '已领养',
        'imgurl' => 'https://picsum.photos/id/1005/800/600'
    ]
];


foreach ($pets as $pet) {
    echo "
    <div class='pet-card'>
        < img src='{$pet['imgurl']}' alt='{$pet['name']}'>
        <div class='pet-info'>
            <div class='pet-name'>{$pet['name']}</div>
            <div class='pet-desc'>
                {$pet['breed']} · {$pet['age']} · {$pet['gender']}<br>
                {$pet['health']} · {$pet['personality']}
            </div>
        </div>
    </div>
    ";
}
?>
    </div>
</section>

<section class="idea">
    <p>每一只生命都值得被温柔以待<br>领养不是施舍，是相互救赎</p >
</section>

<footer>
    © 2026 宠物领养平台 保留所有权利
</footer>
</body>
</html>