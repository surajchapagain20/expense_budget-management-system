-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2026 at 06:07 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `expense_budget_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(30) NOT NULL,
  `category` varchar(250) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `balance` float NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category`, `description`, `status`, `balance`, `date_created`, `date_updated`) VALUES
(1, 'Main Budget', '&lt;p&gt;&lt;span style=&quot;color: rgb(0, 0, 0); font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif; font-size: 14px; text-align: justify;&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed semper imperdiet tortor et rhoncus. Etiam suscipit egestas faucibus. Aenean condimentum ullamcorper turpis, vestibulum maximus eros sollicitudin ut. Morbi interdum ante quis sollicitudin consectetur. Nulla urna urna, gravida et urna eu, pretium consectetur nunc. Quisque id sem porta, blandit lectus vel, feugiat ante. Pellentesque at suscipit tellus, eget posuere augue. Etiam tristique sit amet erat ut porttitor. Duis ut tortor sagittis, mattis mauris non, luctus mauris. Phasellus nec quam a augue eleifend varius nec vel tellus. Integer cursus in nibh in semper.&lt;/span&gt;&lt;br&gt;&lt;/p&gt;', 1, 465555, '2021-07-30 09:21:36', '2026-05-08 07:40:00'),
(2, 'Maintenance', '&lt;p&gt;&lt;span style=&quot;color: rgb(0, 0, 0); font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif; font-size: 14px; text-align: justify;&quot;&gt;Nullam sed ipsum ut ligula ullamcorper ornare nec et tortor. Suspendisse dui erat, pulvinar ut sapien et, varius convallis tellus. Nulla facilisi. In ante felis, lacinia a ornare nec, interdum nec enim. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Donec venenatis orci in laoreet consectetur. Sed lobortis at sapien et fermentum. Pellentesque eros turpis, tincidunt id enim eu, lobortis laoreet neque. Quisque ut justo risus.&lt;/span&gt;&lt;br&gt;&lt;/p&gt;', 1, 1503200, '2021-07-30 09:21:52', '2026-05-08 08:54:19'),
(3, 'Electricity', '&lt;p&gt;&lt;span style=&quot;color: rgb(0, 0, 0); font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif; font-size: 14px; text-align: justify;&quot;&gt;Nullam sed ipsum ut ligula ullamcorper ornare nec et tortor. Suspendisse dui erat, pulvinar ut sapien et, varius convallis tellus. Nulla facilisi. In ante felis, lacinia a ornare nec, interdum nec enim. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Donec venenatis orci in laoreet consectetur. Sed lobortis at sapien et fermentum. Pellentesque eros turpis, tincidunt id enim eu, lobortis laoreet neque. Quisque ut justo risus.&lt;/span&gt;&lt;br&gt;&lt;/p&gt;', 1, 5000, '2021-07-30 09:22:22', '2021-07-30 14:47:13'),
(4, 'Water', '&lt;p&gt;&lt;span style=&quot;color: rgb(0, 0, 0); font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif; font-size: 14px; text-align: justify;&quot;&gt;Praesent dignissim ante id sem semper scelerisque. Maecenas ac lacus egestas, cursus odio quis, tristique diam. Donec maximus congue metus at tincidunt. Suspendisse potenti. Nunc vel quam in metus aliquam placerat sed vitae lectus. Vivamus est nisl, consequat tincidunt blandit feugiat, sagittis sit amet risus. Curabitur congue est in risus mattis, malesuada tincidunt eros sodales. Donec convallis efficitur tincidunt. Etiam tellus nulla, sollicitudin tristique lacus ac, tincidunt placerat sapien.&lt;/span&gt;&lt;br&gt;&lt;/p&gt;', 1, 3000, '2021-07-30 09:23:22', '2021-07-30 14:47:28'),
(5, 'Others', 'This is test', 1, 6000, '2021-07-30 09:23:53', '2026-05-08 07:21:04'),
(7, 'Firewall', '&lt;p&gt;Firewall&lt;/p&gt;', 1, 332500, '2026-05-08 05:37:56', '2026-05-08 07:37:41'),
(8, 'Microsoft Office Standard 30lic', '&lt;p&gt;Microsoft Office Standard 30lic&lt;/p&gt;', 1, 500000, '2026-05-08 05:38:16', '2026-05-08 05:39:41'),
(9, 'Microsoft Office Basic 300lic', '&lt;p&gt;Microsoft Office Basic 300lic&lt;/p&gt;', 1, 900000, '2026-05-08 05:38:30', '2026-05-08 05:39:26');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(30) NOT NULL,
  `name` varchar(250) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `status`, `date_created`, `date_updated`) VALUES
(1, 'IT DEPARTMENT', 'IT DEPARTMENT', 1, '2026-05-08 07:05:43', NULL),
(2, 'GSD DEPARTMENT', 'GSD DEPARTMENT', 1, '2026-05-08 07:05:52', NULL),
(3, 'CLAIM DEPARTMENT', 'GSD DEPARTMENT', 1, '2026-05-08 07:28:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `running_balance`
--

CREATE TABLE `running_balance` (
  `id` int(30) NOT NULL,
  `balance_type` tinyint(1) NOT NULL COMMENT '1=budget, 2=expense',
  `category_id` int(30) NOT NULL,
  `amount` float NOT NULL,
  `remarks` text NOT NULL,
  `user_id` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `quantity` float DEFAULT 0,
  `purchase_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `po_number` varchar(250) DEFAULT NULL,
  `bill_date` date DEFAULT NULL,
  `memo_approved_date` date DEFAULT NULL,
  `department_id` int(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `running_balance`
--

INSERT INTO `running_balance` (`id`, `balance_type`, `category_id`, `amount`, `remarks`, `user_id`, `date_created`, `date_updated`, `quantity`, `purchase_date`, `expiry_date`, `po_number`, `bill_date`, `memo_approved_date`, `department_id`) VALUES
(1, 1, 1, 30000, '&lt;p&gt;Sample entry&lt;/p&gt;', '1', '2021-07-30 11:31:03', '2026-05-08 07:02:24', 0, NULL, NULL, NULL, NULL, NULL, 1),
(3, 1, 5, 1500, '&lt;p&gt;test&lt;/p&gt;', '1', '2021-07-30 11:33:29', '2026-05-08 07:02:24', 0, NULL, NULL, NULL, NULL, NULL, 1),
(4, 1, 5, 1500, '&lt;p&gt;test&lt;/p&gt;', '1', '2021-07-30 11:33:56', '2026-05-08 07:02:24', 0, NULL, NULL, NULL, NULL, NULL, 1),
(5, 1, 5, 1500, '&lt;p&gt;test&lt;/p&gt;', '1', '2021-07-30 11:34:17', '2026-05-08 07:02:24', 0, NULL, NULL, NULL, NULL, NULL, 1),
(6, 1, 5, 1500, '&lt;p&gt;test&lt;/p&gt;', '1', '2021-07-30 11:34:44', '2026-05-08 07:02:24', 0, NULL, NULL, NULL, NULL, NULL, 1),
(7, 1, 1, 2500, '&lt;p&gt;test&lt;/p&gt;', '1', '2021-07-30 11:36:32', '2026-05-08 07:02:24', 0, NULL, NULL, NULL, NULL, NULL, 1),
(12, 2, 1, 2500, '&lt;p&gt;Sample expense&lt;/p&gt;', '1', '2021-07-30 13:07:34', '2026-05-08 07:02:24', 0, NULL, NULL, NULL, NULL, NULL, 1),
(13, 1, 1, 2555, '&lt;p&gt;test&lt;/p&gt;', '1', '2021-07-30 13:17:32', '2026-05-08 07:02:24', 0, NULL, NULL, NULL, NULL, NULL, 1),
(14, 2, 1, 2000, '&lt;p&gt;Sample expense&lt;/p&gt;', '1', '2021-07-30 13:36:10', '2026-05-08 07:02:24', 0, NULL, NULL, NULL, NULL, NULL, 1),
(15, 1, 3, 5000, '&lt;p&gt;Sample&lt;/p&gt;', '1', '2021-07-30 14:47:13', '2026-05-08 07:02:24', 0, NULL, NULL, NULL, NULL, NULL, 1),
(16, 1, 4, 3000, '&lt;p&gt;Test 103&lt;/p&gt;', '1', '2021-07-30 14:47:28', '2026-05-08 07:02:24', 0, NULL, NULL, NULL, NULL, NULL, 1),
(17, 1, 2, 2000, '&lt;p&gt;Test 103&lt;/p&gt;', '1', '2021-07-30 14:47:46', '2026-05-08 07:02:24', 0, NULL, NULL, NULL, NULL, NULL, 1),
(18, 1, 2, 3500, '&lt;p&gt;Test 106&lt;/p&gt;', '1', '2021-07-30 14:48:03', '2026-05-08 07:15:33', 0, NULL, '2026-01-10', NULL, NULL, NULL, 1),
(20, 2, 2, 800, '&lt;p&gt;Expense for Maintenance 105&lt;/p&gt;', '1', '2021-07-30 14:51:31', '2026-05-08 07:02:24', 0, NULL, NULL, NULL, NULL, NULL, 1),
(25, 1, 7, 1500000, '&lt;p&gt;Firewall 200G&lt;/p&gt;', '1', '2026-05-08 05:39:11', '2026-05-08 07:14:18', 1, NULL, '2026-01-01', NULL, NULL, NULL, 1),
(26, 1, 9, 900000, '&lt;p&gt;Microsoft Office Basic 300lic&lt;/p&gt;', '1', '2026-05-08 05:39:26', '2026-05-08 07:13:59', 300, NULL, '2026-07-12', NULL, NULL, NULL, 1),
(27, 1, 8, 500000, '&lt;p&gt;Microsoft Office Standard 30lic&lt;/p&gt;', '1', '2026-05-08 05:39:41', '2026-05-08 07:14:05', 30, NULL, '2026-07-12', NULL, NULL, NULL, 1),
(28, 2, 7, 1167500, '&lt;p&gt;Firewall for HA&lt;/p&gt;', '6', '2026-05-08 06:12:07', '2026-05-08 07:37:41', 1, '2026-05-08', '2027-05-08', 'PO-NLIC_082-83_001', '2026-05-24', '2026-05-13', 1),
(29, 1, 2, 1500000, '&lt;p&gt;Maintenance cost&lt;/p&gt;', '5', '2026-05-08 07:08:15', NULL, 100, '2026-01-01', '2027-01-01', 'PO-NLIC_082-83_001', '2026-01-05', '2026-01-03', 2),
(30, 1, 1, 450000, '&lt;p&gt;main budget&lt;/p&gt;', '6', '2026-05-08 07:32:37', '2026-05-08 07:40:00', 1, '2025-01-01', '2026-01-01', 'PO-NLIC_082-83_001', '2025-02-10', '2025-01-05', 1),
(31, 2, 1, 15000, '&lt;p&gt;asdasdasd&lt;/p&gt;', '4', '2026-05-08 07:34:01', NULL, 10, '2025-01-01', '2026-01-01', 'PO-NLIC_082-83_001', '2025-01-10', '2025-01-05', 3),
(32, 2, 2, 1500, '&lt;p&gt;tesfsfsdf&lt;/p&gt;', '5', '2026-05-08 08:54:19', NULL, 5, '2025-01-01', '2025-01-01', 'PO-NLIC_082-83_001', '2025-01-01', '2025-10-10', 2);

-- --------------------------------------------------------

--
-- Table structure for table `system_info`
--

CREATE TABLE `system_info` (
  `id` int(30) NOT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_info`
--

INSERT INTO `system_info` (`id`, `meta_field`, `meta_value`) VALUES
(1, 'name', 'Budget and Expense System'),
(6, 'short_name', 'Budget Tracker'),
(11, 'logo', 'uploads/1778207220_logo.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(50) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `lastname` varchar(250) NOT NULL,
  `email` varchar(250) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `avatar` text DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `department_id` int(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `reset_token`, `reset_expiry`, `username`, `password`, `avatar`, `last_login`, `type`, `date_added`, `date_updated`, `department_id`) VALUES
(1, 'Adminstrator', 'Admin', 'surajchapagain2023@gmail.com', NULL, NULL, 'admin', '962a36218a682120bee6374c0eb715a0', 'uploads/1778205960_WhatsApp-Image-2025-06-11-a.jpg', NULL, 1, '2021-01-20 14:02:37', '2026-05-08 08:50:14', 1),
(4, 'dipak', 'khadka', 'surajchapagain2023@gmail.com', NULL, NULL, 'dipak', '45f1a4b4a2f6bdfa31838a823064389f', 'uploads/1778200080_1778200080_1624240500_avatar.png', NULL, 0, '2021-06-19 08:36:09', '2026-05-08 08:13:55', 3),
(5, 'anil', 'shrestha', 'surajchapagain2023@gmail.com', NULL, NULL, 'anil', '45f1a4b4a2f6bdfa31838a823064389f', 'uploads/1778205900_IMG-20251015-WA0003.jpg', NULL, 0, '2021-06-19 10:01:51', '2026-05-08 08:13:58', 2),
(6, 'Sharad', 'Chandra Pyakurel', 'surajchapagain2023@gmail.com', NULL, NULL, 'sharad', '45f1a4b4a2f6bdfa31838a823064389f', 'uploads/1778205900_WhatsApp-Image-2025-06-11-a.jpg', NULL, 1, '2026-05-08 07:36:15', '2026-05-08 08:14:01', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `running_balance`
--
ALTER TABLE `running_balance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `system_info`
--
ALTER TABLE `system_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `running_balance`
--
ALTER TABLE `running_balance`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `running_balance`
--
ALTER TABLE `running_balance`
  ADD CONSTRAINT `running_balance_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
