<?php
$db_host = "sql312.infinityfree.com";
$db_user = "if0_41802016";
$db_pwd = "918930Bb";
$db_name = "if0_41802016_main";

$conn = mysqli_connect($db_host, $db_user, $db_pwd, $db_name);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>