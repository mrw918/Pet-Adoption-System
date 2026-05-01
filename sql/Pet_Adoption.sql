-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2026-05-01 09:08:56
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
-- 表的结构 `admin_application_review_log`
--

CREATE TABLE `admin_application_review_log` (
  `log_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `new_status` varchar(20) NOT NULL,
  `create_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(2, 3, 2, 'pending', '2026-04-12 20:14:01'),
(3, 2, 60, 'pending', '2026-04-19 20:39:29'),
(4, 2, 57, 'pending', '2026-04-20 16:03:00'),
(5, 2, 59, 'pending', '2026-04-20 16:03:27'),
(6, 4, 54, 'pending', '2026-04-27 11:34:52');

-- --------------------------------------------------------

--
-- 表的结构 `password_reset_log`
--

CREATE TABLE `password_reset_log` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(64) NOT NULL,
  `email_input` varchar(255) NOT NULL,
  `reset_status` varchar(20) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `ip_addr` varchar(45) NOT NULL,
  `create_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'Huahua', 'Border Collie', '4 years', 'Male', 'Dewormed and vaccinated', 'pending', 'Smart, curious, loyal, playful, brave', 'https://picsum.photos/id/1015/800/600', 1, '2026-04-19 19:03:14'),
(2, 'Bobo', 'Labrador Retriever', '2 years', 'Male', 'Mild allergy, neutered', 'pending', 'Brave, gentle, active, loyal', 'https://picsum.photos/id/1005/800/600', 4, '2026-04-19 19:03:14'),
(3, 'Diandian', 'Akita', '8 years', 'Female', 'Healthy, vaccinated', 'pending', 'Curious, smart, active, gentle', 'https://picsum.photos/id/133/800/600', 1, '2026-04-19 19:03:14'),
(4, 'Sora', 'Corgi', '6 years', 'Male', 'Neutered and healthy', 'pending', 'Smart, curious, active, loyal', 'https://picsum.photos/id/201/800/600', 4, '2026-04-19 19:03:14'),
(5, 'Miaomiao', 'Poodle', '12 years', 'Female', 'Mild dental calculus, dewormed', 'pending', 'Curious, smart', 'https://picsum.photos/id/1009/800/600', 1, '2026-04-19 19:03:14'),
(6, 'Jack', 'Golden Shaded British Shorthair', '6 years', 'Male', 'Neutered and healthy', 'pending', 'Playful, loyal, shy and cautious', 'https://picsum.photos/id/160/800/600', 4, '2026-04-19 19:03:14'),
(7, 'Ah Huang', 'German Shepherd', '7 years', 'Female', 'Neutered and healthy', 'pending', 'Active, gentle, calm and easygoing', 'https://picsum.photos/id/1016/800/600', 1, '2026-04-19 19:03:14'),
(8, 'Beibei', 'Border Collie', '12 years', 'Male', 'Dewormed and vaccinated', 'pending', 'Playful, loyal, independent and quiet', 'https://picsum.photos/id/1015/800/600', 1, '2026-04-19 19:03:14'),
(9, 'Maggie', 'Norwegian Forest Cat', '4 years', 'Female', 'Healthy, vaccinated', 'pending', 'Gentle, active', 'https://picsum.photos/id/1005/800/600', 4, '2026-04-19 19:03:14'),
(10, 'Elin', 'Beagle', '4 years', 'Female', 'Mild allergy, neutered', 'pending', 'Independent, calm, curious', 'https://picsum.photos/id/133/800/600', 4, '2026-04-19 19:03:14'),
(11, 'Yuanyuan', 'Beagle', '1 year', 'Male', 'Dewormed and vaccinated', 'pending', 'Calm, curious, smart', 'https://picsum.photos/id/160/800/600', 1, '2026-04-19 19:03:14'),
(12, 'Heihei', 'Scottish Fold', '1 year', 'Female', 'Neutered and healthy', 'pending', 'Smart, active', 'https://picsum.photos/id/1005/800/600', 1, '2026-04-19 19:03:14'),
(13, 'Doudou', 'Poodle', '4 years', 'Male', 'Neutered and healthy', 'pending', 'Independent, smart, curious', 'https://picsum.photos/id/1016/800/600', 1, '2026-04-19 19:03:14'),
(14, 'Tuantuan', 'Golden Retriever', '5 years', 'Female', 'Mild allergy, neutered', 'pending', 'Playful, loyal, gentle', 'https://picsum.photos/id/160/800/600', 1, '2026-04-19 19:03:14'),
(15, 'Da Huang', 'Scottish Fold', '8 years', 'Male', 'Mild allergy, neutered', 'pending', 'Smart, active', 'https://picsum.photos/id/133/800/600', 1, '2026-04-19 19:03:14'),
(16, 'momo', 'Norwegian Forest Cat', '4 years', 'Male', 'Healthy, vaccinated', 'pending', 'Gentle, playful', 'https://picsum.photos/id/201/800/600', 1, '2026-04-19 19:03:14'),
(17, 'Xingxing', 'Maine Coon', '2 years', 'Female', 'Healthy and lively', 'pending', 'Independent, calm, gentle', 'https://picsum.photos/id/133/800/600', 1, '2026-04-19 19:03:14'),
(18, 'Ali', 'Akita', '7 years', 'Male', 'Mild allergy, neutered', 'pending', 'Brave, gentle, playful', 'https://picsum.photos/id/1015/800/600', 4, '2026-04-19 19:03:14'),
(19, 'Tangtang', 'Shiba Inu', '4 years', 'Male', 'Mild dental calculus, dewormed', 'pending', 'Active, gentle', 'https://picsum.photos/id/1009/800/600', 1, '2026-04-19 19:03:14'),
(20, 'Nini', 'American Shorthair', '9 years', 'Male', 'Healthy, vaccinated', 'pending', 'Smart, calm', 'https://picsum.photos/id/160/800/600', 1, '2026-04-19 19:03:14'),
(21, 'Wangwang', 'Labrador Retriever', '11 years', 'Female', 'Healthy, vaccinated', 'adopted', 'Independent, loyal', 'https://picsum.photos/id/1016/800/600', 1, '2026-04-19 19:03:14'),
(22, 'Baobao', 'Golden Retriever', '10 years', 'Female', 'Neutered and healthy', 'adopted', 'Independent, active', 'https://picsum.photos/id/133/800/600', 1, '2026-04-19 19:03:14'),
(23, 'Maomao', 'Corgi', '6 years', 'Male', 'Mild dental calculus, dewormed', 'adopted', 'Calm, active', 'https://picsum.photos/id/201/800/600', 1, '2026-04-19 19:03:14'),
(24, 'Tuantuan', 'British Shorthair', '6 years', 'Male', 'Healthy, vaccinated', 'adopted', 'Independent, smart', 'https://picsum.photos/id/1005/800/600', 1, '2026-04-19 19:03:14'),
(25, 'Keke', 'Husky', '2 years', 'Male', 'Dewormed and vaccinated', 'adopted', 'Brave, independent', 'https://picsum.photos/id/1015/800/600', 1, '2026-04-19 19:03:14'),
(26, 'Lele', 'Scottish Fold', '5 years', 'Male', 'Healthy, vaccinated', 'adopted', 'Shy, independent', 'https://picsum.photos/id/160/800/600', 1, '2026-04-19 19:03:14'),
(27, 'vicky', 'Shiba Inu', '12 years', 'Female', 'Healthy, vaccinated', 'adopted', 'Loyal, gentle', 'https://picsum.photos/id/133/800/600', 1, '2026-04-19 19:03:14'),
(28, 'Beibei', 'Ragdoll', '11 years', 'Male', 'Healthy, vaccinated', 'adopted', 'Loyal, gentle', 'https://picsum.photos/id/1009/800/600', 1, '2026-04-19 19:03:14'),
(29, 'Doudou', 'Siamese Cat', '10 years', 'Female', 'Neutered and healthy', 'adopted', 'Independent, brave', 'https://picsum.photos/id/1016/800/600', 1, '2026-04-19 19:03:14'),
(30, 'Heihei', 'Beagle', '5 years', 'Female', 'Neutered and healthy', 'adopted', 'Brave, loyal', 'https://picsum.photos/id/201/800/600', 1, '2026-04-19 19:03:14'),
(31, 'Mimi', 'Akita', '3 years', 'Female', 'Neutered and healthy', 'pending', 'Playful, curious, smart', 'https://picsum.photos/id/1005/800/600', 1, '2026-04-19 19:03:14'),
(32, 'Ken', 'Siamese Cat', '10 years', 'Male', 'Mild allergy, neutered', 'pending', 'Calm, curious', 'https://picsum.photos/id/133/800/600', 4, '2026-04-19 19:03:14'),
(33, 'Yuni', 'Akita', '4 years', 'Female', 'Mild joint problem, neutered', 'pending', 'Playful, gentle', 'https://picsum.photos/id/160/800/600', 4, '2026-04-19 19:03:14'),
(34, 'Wein', 'Maine Coon', '5 years', 'Male', 'Healthy, vaccinated', 'pending', 'Loyal, active', 'https://picsum.photos/id/1015/800/600', 4, '2026-04-19 19:03:14'),
(35, 'Dich', 'Golden Retriever', '12 years', 'Female', 'Healthy, vaccinated', 'pending', 'Playful, independent', 'https://picsum.photos/id/1009/800/600', 1, '2026-04-19 19:03:14'),
(36, 'Fark', 'Ragdoll', '12 years', 'Male', 'Mild allergy, neutered', 'pending', 'Curious, brave', 'https://picsum.photos/id/201/800/600', 4, '2026-04-19 19:03:14'),
(37, 'Xiao Bai', 'British Shorthair', '7 years', 'Male', 'Mild allergy, neutered', 'pending', 'Curious, gentle', 'https://picsum.photos/id/133/800/600', 1, '2026-04-19 19:03:14'),
(38, 'Tuantuan', 'Persian Cat', '5 years', 'Female', 'Healthy and lively', 'pending', 'Loyal, smart', 'https://picsum.photos/id/160/800/600', 1, '2026-04-19 19:03:14'),
(39, 'Mary', 'American Shorthair', '1 year', 'Female', 'Mild joint problem, neutered', 'pending', 'Curious, calm', 'https://picsum.photos/id/1016/800/600', 4, '2026-04-19 19:03:14'),
(40, 'Uni', 'Labrador Retriever', '7 years', 'Male', 'Neutered and healthy', 'pending', 'Curious, independent', 'https://picsum.photos/id/1005/800/600', 1, '2026-04-19 19:03:14'),
(41, 'Nano', 'Ragdoll', '8 years', 'Female', 'Mild allergy, neutered', 'adopted', 'Gentle, curious', 'https://picsum.photos/id/133/800/600', 1, '2026-04-19 19:03:14'),
(42, 'Baobao', 'Scottish Fold', '9 years', 'Female', 'Neutered and healthy', 'adopted', 'Loyal, smart', 'https://picsum.photos/id/201/800/600', 1, '2026-04-19 19:03:14'),
(43, 'John', 'Shiba Inu', '4 years', 'Female', 'Mild joint problem, neutered', 'adopted', 'Loyal, calm', 'https://picsum.photos/id/160/800/600', 1, '2026-04-19 19:03:14'),
(44, 'Tom', 'Husky', '2 years', 'Male', 'Dewormed and vaccinated', 'adopted', 'Brave, independent', 'https://picsum.photos/id/1015/800/600', 1, '2026-04-19 19:03:14'),
(45, 'Lily', 'Norwegian Forest Cat', '3 years', 'Female', 'Mild allergy, neutered', 'adopted', 'Loyal, smart', 'https://picsum.photos/id/1009/800/600', 1, '2026-04-19 19:03:14'),
(46, 'Oh', 'Maine Coon', '11 years', 'Male', 'Dewormed and vaccinated', 'adopted', 'Curious, shy', 'https://picsum.photos/id/133/800/600', 1, '2026-04-19 19:03:14'),
(47, 'Key', 'Norwegian Forest Cat', '1 year', 'Female', 'Neutered and healthy', 'adopted', 'Shy, smart', 'https://picsum.photos/id/160/800/600', 1, '2026-04-19 19:03:14'),
(48, 'Quiet', 'American Shorthair', '11 years', 'Female', 'Healthy, vaccinated', 'adopted', 'Shy, loyal', 'https://picsum.photos/id/1016/800/600', 1, '2026-04-19 19:03:14'),
(49, 'Danke', 'Norwegian Forest Cat', '1 year', 'Female', 'Neutered and healthy', 'adopted', 'Smart, calm', 'https://picsum.photos/id/1005/800/600', 1, '2026-04-19 19:03:14'),
(50, 'Poral', 'Husky', '8 years', 'Female', 'Mild dental calculus, dewormed', 'adopted', 'Shy, curious', 'https://picsum.photos/id/201/800/600', 1, '2026-04-19 19:03:14'),
(51, 'Ich', 'Chinese Li Hua', '11 years', 'Male', 'Healthy and lively', 'adopted', 'Loyal, playful', 'https://picsum.photos/id/133/800/600', 1, '2026-04-19 19:03:14'),
(52, 'Nein', 'British Shorthair', '12 years', 'Female', 'Neutered and healthy', 'adopted', 'Brave, playful', 'https://picsum.photos/id/160/800/600', 1, '2026-04-19 19:03:14'),
(53, 'Coco', 'Scottish Fold', '9 years', 'Male', 'Mild joint problem, neutered', 'adopted', 'Smart, loyal', 'https://picsum.photos/id/1015/800/600', 1, '2026-04-19 19:03:14'),
(54, 'Deutsch', 'Labrador Retriever', '1 year', 'Male', 'Neutered and healthy', 'pending', 'Smart, gentle', 'https://picsum.photos/id/1009/800/600', 1, '2026-04-19 19:03:14'),
(55, 'Hahaha', 'Shiba Inu', '10 years', 'Male', 'Neutered and healthy', 'pending', 'Playful, brave', 'https://picsum.photos/id/133/800/600', 1, '2026-04-19 19:03:14'),
(56, 'Leben', 'Akita', '3 years', 'Male', 'Dewormed and vaccinated', 'pending', 'Curious, calm', 'https://picsum.photos/id/201/800/600', 1, '2026-04-19 19:03:14'),
(57, 'Ja', 'Shiba Inu', '1 year', 'Male', 'Healthy, vaccinated', 'pending', 'Shy, active', 'https://picsum.photos/id/160/800/600', 1, '2026-04-19 19:03:14'),
(58, 'WTF', 'Maine Coon', '7 years', 'Male', 'Healthy, vaccinated', 'pending', 'Smart, curious', 'https://picsum.photos/id/1005/800/600', 1, '2026-04-19 19:03:14'),
(59, 'Rose', 'Ragdoll', '6 years', 'Male', 'Neutered and healthy', 'pending', 'Active, gentle', 'https://picsum.photos/id/133/800/600', 1, '2026-04-19 19:03:14'),
(60, 'Amy', 'Beagle', '8 years', 'Male', 'Mild dental calculus, dewormed', 'pending', 'Active, loyal', 'https://picsum.photos/id/1016/800/600', 1, '2026-04-19 19:03:14');

-- --------------------------------------------------------

--
-- 表的结构 `pet_add_log`
--

CREATE TABLE `pet_add_log` (
  `log_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `pet_id` int(11) DEFAULT NULL,
  `pet_name` varchar(80) NOT NULL,
  `action_status` varchar(20) NOT NULL,
  `detail_note` varchar(255) DEFAULT NULL,
  `ip_addr` varchar(45) NOT NULL,
  `create_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(3, 'user2', '123456', 'user2@test.com', '13800003333', 'user', '2026-04-12 20:14:01'),
(4, 'mrw', '$2y$10$VwYdq4uFlrrxL2Jt4YjiKuMdK9yMJvhwWw8liDYe1J5n6WuGDWjyC', 'u55289765@gmail.com', '18057287954', 'super_admin', '2026-04-27 11:33:04');

-- --------------------------------------------------------

--
-- 表的结构 `user_audit_log`
--

CREATE TABLE `user_audit_log` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(64) NOT NULL,
  `action_type` varchar(40) NOT NULL,
  `action_status` varchar(20) NOT NULL,
  `detail_note` varchar(255) DEFAULT NULL,
  `ip_addr` varchar(45) NOT NULL,
  `create_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `user_audit_log`
--

INSERT INTO `user_audit_log` (`log_id`, `user_id`, `username`, `action_type`, `action_status`, `detail_note`, `ip_addr`, `create_time`) VALUES
(1, 4, 'mrw', 'register', 'success', 'account_created', '::1', '2026-04-27 11:33:04');

--
-- 转储表的索引
--

--
-- 表的索引 `admin_application_review_log`
--
ALTER TABLE `admin_application_review_log`
  ADD PRIMARY KEY (`log_id`);

--
-- 表的索引 `adoption_application`
--
ALTER TABLE `adoption_application`
  ADD PRIMARY KEY (`app_id`);

--
-- 表的索引 `password_reset_log`
--
ALTER TABLE `password_reset_log`
  ADD PRIMARY KEY (`log_id`);

--
-- 表的索引 `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`pet_id`);

--
-- 表的索引 `pet_add_log`
--
ALTER TABLE `pet_add_log`
  ADD PRIMARY KEY (`log_id`);

--
-- 表的索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- 表的索引 `user_audit_log`
--
ALTER TABLE `user_audit_log`
  ADD PRIMARY KEY (`log_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `admin_application_review_log`
--
ALTER TABLE `admin_application_review_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `adoption_application`
--
ALTER TABLE `adoption_application`
  MODIFY `app_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 使用表AUTO_INCREMENT `password_reset_log`
--
ALTER TABLE `password_reset_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `pets`
--
ALTER TABLE `pets`
  MODIFY `pet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- 使用表AUTO_INCREMENT `pet_add_log`
--
ALTER TABLE `pet_add_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `user_audit_log`
--
ALTER TABLE `user_audit_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
