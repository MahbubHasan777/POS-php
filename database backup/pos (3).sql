-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 19, 2026 at 06:37 PM
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
-- Database: `pos`
--

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `shop_id`, `name`) VALUES
(12, 6, 'amul'),
(13, 6, 'cadbury'),
(14, 6, 'nestle'),
(15, 6, 'aci'),
(16, 6, 'fresh'),
(17, 6, 'rahul'),
(18, 6, 'shaad'),
(19, 6, 'local'),
(20, 6, 'chopstick'),
(21, 6, 'Mr. Noodles'),
(22, 6, 'nestle'),
(23, 6, 'Pepsico'),
(24, 6, 'Coca-Cola'),
(25, 6, 'Mojo');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `shop_id`, `name`) VALUES
(10, 6, 'chocolates'),
(11, 6, 'flours'),
(12, 6, 'fresh fruits'),
(13, 6, 'noodles'),
(15, 6, 'soft drink'),
(16, 6, 'water');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_type` enum('fixed','percent') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT 0.00,
  `max_discount_amount` decimal(10,2) DEFAULT 0.00,
  `expiry_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `shop_id`, `code`, `discount_type`, `discount_value`, `min_order_amount`, `max_discount_amount`, `expiry_date`) VALUES
(6, 6, 'NEWYEAR26', 'fixed', 30.00, 300.00, 0.00, '2026-01-22');

-- --------------------------------------------------------

--
-- Table structure for table `held_orders`
--

CREATE TABLE `held_orders` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `cashier_id` int(11) NOT NULL,
  `items_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`items_json`)),
  `customer_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `cashier_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `grand_total` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'completed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `shop_id`, `cashier_id`, `total_amount`, `tax_amount`, `discount_amount`, `grand_total`, `payment_method`, `created_at`, `status`) VALUES
(18, 6, 14, 395.00, 19.75, 0.00, 414.75, 'Cash', '2026-01-19 17:33:16', 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`, `subtotal`) VALUES
(26, 18, 34, 2, 60.00, 120.00),
(27, 18, 33, 1, 135.00, 135.00),
(28, 18, 30, 1, 140.00, 140.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `buy_price` decimal(10,2) NOT NULL,
  `sell_price` decimal(10,2) NOT NULL,
  `stock_qty` int(11) DEFAULT 0,
  `alert_threshold` int(11) DEFAULT 5,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `shop_id`, `category_id`, `brand_id`, `name`, `buy_price`, `sell_price`, `stock_qty`, `alert_threshold`, `image`, `is_active`, `created_at`) VALUES
(16, 6, 10, 12, 'amul-dark-chocolate-bar-125-gm', 50.00, 60.00, 20, 5, '696e6505528d1.jpg', 1, '2026-01-19 17:08:21'),
(17, 6, 10, 13, 'cadbury-celebrations-230-gm', 300.00, 350.00, 20, 5, '696e65781e77f.jpg', 1, '2026-01-19 17:10:16'),
(18, 6, 10, 13, 'cadbury-dairy-milk-chocolate-bar-12-gm', 70.00, 90.00, 30, 5, '696e65915ffed.jpg', 1, '2026-01-19 17:10:41'),
(19, 6, 10, 13, 'cadbury-dairy-milk-silk-chocolate-family-pack-123-gm', 120.00, 150.00, 30, 5, '696e65b714819.jpg', 1, '2026-01-19 17:11:19'),
(20, 6, 10, 22, 'nestle-kitkat-4-fingers-365-gm-2-pcs', 70.00, 90.00, 30, 5, '696e65de2df04.jpg', 1, '2026-01-19 17:11:58'),
(21, 6, 10, 13, 'cadbury-fuse-chocolate-bar-21-gm', 150.00, 180.00, 25, 5, '696e65fdd64d6.jpg', 1, '2026-01-19 17:12:29'),
(22, 6, 10, 13, 'cadbury-dairy-milk-silk-mousse-chocolate-bar-485-gm', 350.00, 450.00, 15, 5, '696e6637838f9.jpg', 1, '2026-01-19 17:13:27'),
(23, 6, 11, 15, 'aci-nutrilife-brown-atta-2-kg', 30.00, 40.00, 40, 5, '696e66608868f.jpg', 1, '2026-01-19 17:14:08'),
(24, 6, 11, 15, 'aci-nutrilife-brown-flour-atta-1-kg', 20.00, 25.00, 18, 5, '696e6679c2c9f.jpg', 1, '2026-01-19 17:14:33'),
(25, 6, 11, 15, 'aci-pure-flour-atta-2-kg', 40.00, 50.00, 22, 5, '696e66963e6c4.jpg', 1, '2026-01-19 17:15:02'),
(26, 6, 11, 16, 'fresh-flour-atta-2-kg', 50.00, 70.00, 15, 5, '696e66b345c93.jpg', 1, '2026-01-19 17:15:31'),
(27, 6, 11, 16, 'fresh-white-flour-maida-2-kg', 50.00, 70.00, 15, 5, '696e66c8ec499.jpg', 1, '2026-01-19 17:15:52'),
(28, 6, 12, 19, 'amloki-amla-12-gm-250-gm', 50.00, 70.00, 30, 5, '696e66eb46889.jpg', 1, '2026-01-19 17:16:27'),
(29, 6, 12, 19, 'banana-chompa-ready-to-eat-4-pcs', 50.00, 60.00, 20, 5, '696e6708cadee.jpg', 1, '2026-01-19 17:16:56'),
(30, 6, 12, 19, 'china-orange-40-gm-800-gm', 120.00, 140.00, 29, 5, '696e6725eeb39.jpg', 1, '2026-01-19 17:17:25'),
(31, 6, 12, 19, 'orange-indian-50-gm-1-kg', 120.00, 150.00, 30, 5, '696e6744d9adb.jpg', 1, '2026-01-19 17:17:56'),
(32, 6, 13, 20, 'chopstick-deshi-masala-instant-noodles-8-pack-496-gm', 120.00, 140.00, 25, 5, '696e6764cf85b.jpg', 1, '2026-01-19 17:18:28'),
(33, 6, 13, 20, 'chopstick-instant-noodles-masala-delight-496-gm-8-pack', 125.00, 135.00, 14, 5, '696e67db3d44f.jpg', 1, '2026-01-19 17:20:27'),
(34, 6, 15, 23, '7-up-1-ltr', 45.00, 60.00, 28, 5, '696e68064b084.jpg', 1, '2026-01-19 17:21:10'),
(35, 6, 15, 23, '7-up-175-ltr', 60.00, 80.00, 120, 5, '696e682ae1a4d.jpg', 1, '2026-01-19 17:21:46'),
(36, 6, 15, 23, '7-up-500-ml', 30.00, 35.00, 10, 5, '696e684281f1d.jpg', 1, '2026-01-19 17:22:10');

-- --------------------------------------------------------

--
-- Table structure for table `returns`
--

CREATE TABLE `returns` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

CREATE TABLE `shops` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` enum('active','suspended','pending') DEFAULT 'active',
  `subscription_plan_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cycle_start_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `rollover_sales` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shops`
--

INSERT INTO `shops` (`id`, `name`, `status`, `subscription_plan_id`, `created_at`, `cycle_start_date`, `rollover_sales`) VALUES
(6, 'AIUB Supershop#1', 'active', 1, '2026-01-19 15:55:44', '2026-01-19 15:55:44', 0);

-- --------------------------------------------------------

--
-- Table structure for table `subscription_payments`
--

CREATE TABLE `subscription_payments` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_status` enum('paid','pending','failed') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscription_plans`
--

CREATE TABLE `subscription_plans` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `max_sales` int(11) DEFAULT -1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscription_plans`
--

INSERT INTO `subscription_plans` (`id`, `name`, `price`, `max_sales`, `created_at`) VALUES
(1, 'Free Tier', 0.00, 7, '2025-12-27 15:09:54'),
(2, 'Pro Tier', 30.00, 10, '2025-12-27 15:09:54'),
(5, 'Go Plan', 50.00, 15, '2025-12-30 05:33:51');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_info` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) DEFAULT NULL,
  `role` enum('super_admin','shop_admin','cashier') NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `photo` varchar(255) DEFAULT 'default.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `shop_id`, `role`, `username`, `email`, `password_hash`, `full_name`, `photo`, `created_at`) VALUES
(1, NULL, 'super_admin', 'superadmin', 'admin@pos.com', '$2y$10$YQyCY5hRb8ZQV6RHY9DQa.9nS0yP.YOMLBz.oEMQyE9mHcH7qm89.', 'Super Administrator', 'default.png', '2025-12-27 15:09:54'),
(12, 6, 'shop_admin', 'Mahbub Hasan', 'mahbub@hasan.com', '$2y$10$cW6tKdMXpkQrRYF16rbQFe.uBSdemSJM.MnhB0LH8I4OZSNfxAPSS', 'AIUB Supershop#1', 'default.png', '2026-01-19 15:55:44'),
(14, 6, 'cashier', 'cashier1', 'cashier1@gmail.com', '$2y$10$AE4t4gO4irhG6/IVqUD13evMHq1p8.VPvfYN5H8t0sNG5V/QNNLPu', 'Limon', 'default.png', '2026-01-19 17:09:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shop_id` (`shop_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shop_id` (`shop_id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shop_id` (`shop_id`);

--
-- Indexes for table `held_orders`
--
ALTER TABLE `held_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shop_id` (`shop_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shop_id` (`shop_id`),
  ADD KEY `cashier_id` (`cashier_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shop_id` (`shop_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `brand_id` (`brand_id`);

--
-- Indexes for table `returns`
--
ALTER TABLE `returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shop_id` (`shop_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `shops`
--
ALTER TABLE `shops`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscription_plan_id` (`subscription_plan_id`);

--
-- Indexes for table `subscription_payments`
--
ALTER TABLE `subscription_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shop_id` (`shop_id`),
  ADD KEY `plan_id` (`plan_id`);

--
-- Indexes for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shop_id` (`shop_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `shop_id` (`shop_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `held_orders`
--
ALTER TABLE `held_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `returns`
--
ALTER TABLE `returns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `shops`
--
ALTER TABLE `shops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subscription_payments`
--
ALTER TABLE `subscription_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `brands`
--
ALTER TABLE `brands`
  ADD CONSTRAINT `brands_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `coupons`
--
ALTER TABLE `coupons`
  ADD CONSTRAINT `coupons_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `held_orders`
--
ALTER TABLE `held_orders`
  ADD CONSTRAINT `held_orders_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `returns`
--
ALTER TABLE `returns`
  ADD CONSTRAINT `returns_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `returns_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `returns_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `shops`
--
ALTER TABLE `shops`
  ADD CONSTRAINT `shops_ibfk_1` FOREIGN KEY (`subscription_plan_id`) REFERENCES `subscription_plans` (`id`);

--
-- Constraints for table `subscription_payments`
--
ALTER TABLE `subscription_payments`
  ADD CONSTRAINT `subscription_payments_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subscription_payments_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `subscription_plans` (`id`);

--
-- Constraints for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `suppliers_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
