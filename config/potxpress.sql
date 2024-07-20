-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 21, 2024 at 11:05 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `potxpress`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `product_id`, `quantity`) VALUES
(3, 1, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_log`
--

CREATE TABLE `inventory_log` (
  `inventorylog_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notif_id` int(11) NOT NULL,
  `type` enum('Log','Order','Product') NOT NULL,
  `text` text NOT NULL,
  `date` datetime NOT NULL,
  `status` enum('read','unread') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '(Order Placed -> Preparing to Ship -> In transit -> Delivered)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `total_amount`, `status`) VALUES
(1, 1, '2024-02-22 06:02:06', 1301.62, 2),
(2, 1, '2024-02-22 06:02:21', 100.25, 1);

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `rated` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`order_detail_id`, `order_id`, `product_id`, `quantity`, `price`, `rated`) VALUES
(1, 1, 1, 2, 500.65, 0),
(2, 1, 3, 1, 300.32, 0),
(3, 2, 4, 1, 100.25, 0);

-- --------------------------------------------------------

--
-- Table structure for table `order_status`
--

CREATE TABLE `order_status` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_placed` datetime NOT NULL,
  `in_transit` datetime DEFAULT NULL,
  `delivered` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_status`
--

INSERT INTO `order_status` (`id`, `order_id`, `order_placed`, `in_transit`, `delivered`) VALUES
(1, 1, '2024-02-22 06:02:06', '2024-02-22 06:02:41', NULL),
(2, 2, '2024-02-22 06:02:21', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `imagefilepath` varchar(255) NOT NULL DEFAULT './assets/images/default.png',
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `sold` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `imagefilepath`, `price`, `stock_quantity`, `sold`) VALUES
(1, 'Traditional Palayok', 'Traditional Filipino Palayok Pot', './assets/images/7a9807937bad87cc9f4fcbd31f152996.jpg', 500.65, 18, 0),
(2, 'Chinese Pot', 'Chinese pots used for serving food.', './assets/images/0256c269ee87fc409176ac07f9ead5ed.jpg', 200.23, 10, 0),
(3, 'Korean Pot', 'Korean pot used for serving food.', './assets/images/54acc5308cd2c2dfa0f21e0d635037ff.jpg', 300.32, 14, 0),
(4, 'Handmade Clay Flower Pot', 'Handmade Clay Flower Pot with designs.', './assets/images/4464feca4db60e384f2938cdcb705a1d.jpg', 100.25, 29, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_add_log`
--

CREATE TABLE `product_add_log` (
  `log_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `added_by_user_id` int(11) NOT NULL,
  `added_at` datetime NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_add_log`
--

INSERT INTO `product_add_log` (`log_id`, `product_id`, `product_name`, `added_by_user_id`, `added_at`, `ip_address`) VALUES
(1, 1, 'Traditional Palayok', 2, '2024-02-22 05:57:06', '::1'),
(2, 2, 'Chinese Pot', 2, '2024-02-22 05:57:52', '::1'),
(3, 3, 'Korean Pot', 2, '2024-02-22 05:58:27', '::1'),
(4, 4, 'Handmade Clay Flower Pot', 2, '2024-02-22 05:59:35', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `product_delete_log`
--

CREATE TABLE `product_delete_log` (
  `log_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `deleted_by_user_id` int(11) NOT NULL,
  `deleted_at` datetime NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_edit_log`
--

CREATE TABLE `product_edit_log` (
  `log_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `edited_by_user_id` int(11) NOT NULL,
  `edit_description` text DEFAULT NULL,
  `edited_at` datetime NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_edit_log`
--

INSERT INTO `product_edit_log` (`log_id`, `product_id`, `edited_by_user_id`, `edit_description`, `edited_at`, `ip_address`) VALUES
(1, 3, 2, 'UPDATE products SET name = Korean Pot, description = Korean pot used for serving food., imagefilepath = ./assets/images/54acc5308cd2c2dfa0f21e0d635037ff.jpg, price = 300.32, stock_quantity = 15 WHERE product_id = 3', '2024-02-22 05:58:48', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `address` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `mobile_number` varchar(11) DEFAULT NULL,
  `fname` varchar(50) DEFAULT NULL,
  `lname` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `role`, `address`, `age`, `mobile_number`, `fname`, `lname`) VALUES
(1, 'jun', '$2y$10$sY4wrfTHUbzSo/rlUe3BDOZWe5ziwvmfgwadYLQ1ZQEKpH787Cfk6', 'zufuzuz@mailinator.com', 'customer', 'Est nostrud sit vol', 50, '09728461728', 'Jun', 'Zoilo'),
(2, 'jap', '$2y$10$SY29EVERFg5iTfJcEX88IOxo/sjsybbnT2VV9qdRqkMS5Gzkmorzi', 'gihe@mailinator.com', 'admin', 'Fugiat vel dolore au', 44, '09876543212', 'Japkeen Haven', 'Sicat-Cruz');

-- --------------------------------------------------------

--
-- Table structure for table `user_feedbacks`
--

CREATE TABLE `user_feedbacks` (
  `feedback_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `text` text DEFAULT NULL,
  `datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `inventory_log`
--
ALTER TABLE `inventory_log`
  ADD PRIMARY KEY (`inventorylog_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notif_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_detail_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order_status`
--
ALTER TABLE `order_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_add_log`
--
ALTER TABLE `product_add_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `fk_product_add_log_user` (`added_by_user_id`);

--
-- Indexes for table `product_delete_log`
--
ALTER TABLE `product_delete_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `fk_product_delete_log_user` (`deleted_by_user_id`);

--
-- Indexes for table `product_edit_log`
--
ALTER TABLE `product_edit_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `fk_product_edit_log_user` (`edited_by_user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_feedbacks`
--
ALTER TABLE `user_feedbacks`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `inventory_log`
--
ALTER TABLE `inventory_log`
  MODIFY `inventorylog_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notif_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_status`
--
ALTER TABLE `order_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `product_add_log`
--
ALTER TABLE `product_add_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `product_delete_log`
--
ALTER TABLE `product_delete_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_edit_log`
--
ALTER TABLE `product_edit_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `inventory_log`
--
ALTER TABLE `inventory_log`
  ADD CONSTRAINT `fk_inventory_log_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `order_status`
--
ALTER TABLE `order_status`
  ADD CONSTRAINT `order_status_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `product_add_log`
--
ALTER TABLE `product_add_log`
  ADD CONSTRAINT `fk_product_add_log_user` FOREIGN KEY (`added_by_user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `product_delete_log`
--
ALTER TABLE `product_delete_log`
  ADD CONSTRAINT `fk_product_delete_log_user` FOREIGN KEY (`deleted_by_user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `product_edit_log`
--
ALTER TABLE `product_edit_log`
  ADD CONSTRAINT `fk_product_edit_log_user` FOREIGN KEY (`edited_by_user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `user_feedbacks`
--
ALTER TABLE `user_feedbacks`
  ADD CONSTRAINT `fk_user_feedbacks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
