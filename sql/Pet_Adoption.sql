-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2026-04-12 14:26:04
-- 服务器版本： 10.4.28-MariaDB
-- PHP 版本： 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `Pet Adoption`
--

-- --------------------------------------------------------

--
-- 表的结构 `adoption_application`
--

CREATE TABLE `adoption_application` (
  `app_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pet_id` int(11) NOT NULL,
  `app_status` varchar(20) NOT NULL DEFAULT 'pending',
  `create_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `adoption_application`
--

INSERT INTO `adoption_application` (`app_id`, `user_id`, `pet_id`, `app_status`, `create_time`) VALUES
(1, 2, 1, 'pending', '2026-04-12 20:14:01'),
(2, 3, 2, 'pending', '2026-04-12 20:14:01');

-- --------------------------------------------------------

--
-- 表的结构 `pets`
--

CREATE TABLE `pets` (
  `pet_id` int(11) NOT NULL,
  `pet_name` varchar(50) NOT NULL,
  `pet_breed` varchar(50) DEFAULT NULL,
  `pet_age` varchar(20) DEFAULT NULL,
  `pet_gender` varchar(10) DEFAULT NULL,
  `pet_health` varchar(50) DEFAULT NULL,
  `pet_status` varchar(20) NOT NULL DEFAULT 'pending',
  `pet_intro` text DEFAULT NULL,
  `pet_img` varchar(255) DEFAULT NULL,
  `admin_id` int(11) NOT NULL,
  `create_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `pets`
--

INSERT INTO `pets` (`pet_id`, `pet_name`, `pet_breed`, `pet_age`, `pet_gender`, `pet_health`, `pet_status`, `pet_intro`, `pet_img`, `admin_id`, `create_time`) VALUES
(1, '旺财', '柯基', '1岁', 'male', '健康、已疫苗、已驱虫', 'pending', '性格活泼，非常亲人', '/upload/pet1.jpg', 1, '2026-04-12 20:14:01'),
(2, '小白', '布偶', '2岁', 'female', '健康、已疫苗', 'pending', '温顺安静，适合家养', '/upload/pet2.jpg', 1, '2026-04-12 20:14:01'),
(3, '大黄', '金毛', '3岁', 'male', '健康、已疫苗', 'adopted', '忠诚温顺，已被领养', '/upload/pet3.jpg', 1, '2026-04-12 20:14:01');

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `user_pwd` varchar(100) NOT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `user_phone` varchar(20) DEFAULT NULL,
  `user_role` varchar(20) NOT NULL DEFAULT 'user',
  `create_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `users`
--

INSERT INTO `users` (`user_id`, `username`, `user_pwd`, `user_email`, `user_phone`, `user_role`, `create_time`) VALUES
(1, 'admin', '123456', 'admin@test.com', '13800001111', 'admin', '2026-04-12 20:14:01'),
(2, 'user1', '123456', 'user1@test.com', '13800002222', 'user', '2026-04-12 20:14:01'),
(3, 'user2', '123456', 'user2@test.com', '13800003333', 'user', '2026-04-12 20:14:01');

--
-- 转储表的索引
--

--
-- 表的索引 `adoption_application`
--
ALTER TABLE `adoption_application`
  ADD PRIMARY KEY (`app_id`);

--
-- 表的索引 `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`pet_id`);

--
-- 表的索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `adoption_application`
--
ALTER TABLE `adoption_application`
  MODIFY `app_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `pets`
--
ALTER TABLE `pets`
  MODIFY `pet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
