-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 19, 2026 at 04:40 PM
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
(1, 1, 'AMD'),
(2, 1, 'Nvidida'),
(3, 1, 'Samsung'),
(4, 2, 'Pran'),
(5, 2, 'Bashundhara'),
(7, 2, 'Fresh'),
(8, 1, 'Black Rock'),
(9, 2, 'Break Room'),
(10, 5, 'asd4'),
(11, 1, 'Lal batti');

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
(1, 1, 'Food'),
(2, 1, 'Drink'),
(3, 1, 'Alien'),
(4, 2, 'Food'),
(5, 2, 'Daily Life Things'),
(7, 1, 'monster'),
(8, 2, 'Pizza'),
(9, 5, '1516as');

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
(2, 1, 'newcoup11', '', 10.00, 0.00, 0.00, '2025-12-28'),
(3, 2, 'sum', 'fixed', 50.00, 300.00, 0.00, '2026-01-06'),
(4, 2, 'past', 'percent', 10.00, 10.00, 0.00, '2025-12-17'),
(5, 1, 'sum25', 'percent', 10.00, 100.00, 15.00, '2026-01-28');

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

--
-- Dumping data for table `held_orders`
--

INSERT INTO `held_orders` (`id`, `shop_id`, `cashier_id`, `items_json`, `customer_name`, `created_at`) VALUES
(3, 1, 5, '[{\"id\":5,\"name\":\"Burger\",\"price\":\"150.00\",\"qty\":1,\"max\":46},{\"id\":1,\"name\":\"Pran Mango Juice\",\"price\":\"150.00\",\"qty\":3,\"max\":200}]', 'Fahim will come', '2025-12-27 18:11:09');

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

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `is_read`, `created_at`) VALUES
(1, 3, 'Low Stock Alert: Fahim Vai', 'The stock for Fahim Vai is low (0 left). Please restock.', 1, '2025-12-27 18:09:23'),
(2, 6, 'Low Stock Alert: Bashundhara Napkin', 'The stock for Bashundhara Napkin is low (4 left). Please restock.', 1, '2025-12-27 21:18:03'),
(3, 3, 'Low Stock Alert: Shikto', 'The stock for Shikto is low (0 left). Please restock.', 1, '2025-12-29 17:38:36');

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
(1, 1, 5, 750.00, 0.00, 0.00, 750.00, 'Card', '2025-12-27 16:57:50', 'completed'),
(2, 1, 5, 300.00, 0.00, 0.00, 300.00, 'Cash', '2025-12-27 17:07:27', 'completed'),
(3, 1, 5, 780.00, 0.00, 60.00, 720.00, 'Card', '2025-12-27 18:09:23', 'completed'),
(4, 2, 8, 1380.00, 0.00, 50.00, 1330.00, 'Cash', '2025-12-27 21:18:03', 'completed'),
(5, 1, 4, 870.00, 0.00, 0.00, 870.00, 'Cash', '2025-12-29 17:38:36', 'completed'),
(6, 1, 4, 720.00, 36.00, 0.00, 756.00, 'Cash', '2025-12-30 05:42:28', 'completed'),
(7, 1, 4, 150.00, 7.50, 0.00, 157.50, 'Cash', '2026-01-03 09:22:45', 'completed'),
(8, 1, 4, 165.00, 8.25, 0.00, 173.25, 'Cash', '2026-01-03 09:23:12', 'completed'),
(9, 1, 4, 150.00, 7.50, 0.00, 157.50, 'Cash', '2026-01-03 09:23:44', 'completed'),
(10, 2, 8, 400.00, 20.00, 0.00, 420.00, 'Cash', '2026-01-03 09:48:32', 'completed'),
(11, 5, 11, 300.00, 22.50, 0.00, 322.50, 'Cash', '2026-01-03 10:29:55', 'completed'),
(12, 5, 11, 300.00, 15.00, 0.00, 315.00, 'Cash', '2026-01-03 10:33:14', 'completed'),
(13, 1, 4, 460.00, 31.00, 0.00, 491.00, 'Cash', '2026-01-12 15:01:14', 'completed'),
(14, 1, 4, 160.00, 16.00, 0.00, 176.00, 'Cash', '2026-01-15 17:11:28', 'completed'),
(15, 1, 4, 420.00, 21.00, 0.00, 441.00, 'Cash', '2026-01-18 15:38:03', 'completed'),
(16, 1, 4, 420.00, 21.00, 0.00, 441.00, 'Cash', '2026-01-18 15:39:29', 'completed'),
(17, 1, 4, 90.00, 7.50, 0.00, 97.50, 'Cash', '2026-01-19 14:37:34', 'returned');

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
(1, 1, 5, 2, 150.00, 300.00),
(2, 2, 5, 1, 150.00, 150.00),
(3, 3, 4, 2, 15.00, 30.00),
(4, 3, 5, 1, 150.00, 150.00),
(5, 3, 6, 0, 420.00, 0.00),
(6, 4, 7, 2, 120.00, 240.00),
(7, 4, 8, 1, 150.00, 150.00),
(8, 4, 9, 1, 200.00, 200.00),
(9, 5, 10, 0, 420.00, 0.00),
(10, 5, 5, 1, 150.00, 150.00),
(11, 6, 10, 1, 420.00, 420.00),
(12, 6, 5, 2, 150.00, 300.00),
(13, 7, 5, 1, 150.00, 150.00),
(14, 8, 5, 1, 150.00, 150.00),
(15, 8, 4, 1, 15.00, 15.00),
(16, 9, 5, 1, 150.00, 150.00),
(17, 10, 9, 2, 200.00, 400.00),
(18, 11, 12, 2, 150.00, 300.00),
(19, 12, 12, 2, 150.00, 300.00),
(20, 13, 14, 1, 160.00, 160.00),
(21, 13, 5, 2, 150.00, 300.00),
(22, 14, 14, 1, 160.00, 160.00),
(23, 15, 10, 1, 420.00, 420.00),
(24, 16, 10, 1, 420.00, 420.00),
(25, 17, 2, 3, 30.00, 90.00);

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
(1, 1, 1, 3, 'Pran Mango Juice', 150.00, 150.00, 200, 10, NULL, 1, '2025-12-27 15:58:31'),
(2, 1, 2, 3, 'Mum Drinking Water', 30.00, 30.00, 100, 10, NULL, 1, '2025-12-27 16:05:13'),
(4, 1, 1, 3, 'New Product', 15.00, 15.00, 97, 5, '695006265bef2.BMP', 1, '2025-12-27 16:15:34'),
(5, 1, 1, 1, 'Burger', 125.00, 150.00, 38, 5, '69500801efc9a.jpg', 1, '2025-12-27 16:20:05'),
(6, 1, 3, 2, 'Fahim Vai', 404.00, 420.00, 1, 0, '6950209f1a4d9.jpg', 1, '2025-12-27 18:08:31'),
(7, 2, 5, 5, 'Bashundhara Napkin', 105.00, 120.00, 4, 5, '69504bfb35fc4.jpg', 1, '2025-12-27 21:13:31'),
(8, 2, 4, 4, 'dummy', 120.00, 150.00, 9, 5, '69504c0da9b44.jpg', 1, '2025-12-27 21:13:49'),
(9, 2, 5, 4, 'sadf', 150.00, 200.00, 30, 10, '69504c1ddba28.jpg', 1, '2025-12-27 21:14:05'),
(10, 1, 3, 1, 'Jadu', 404.00, 420.00, 0, 5, '6952bbf38d456.jpg', 1, '2025-12-29 17:35:47'),
(11, 2, 8, 9, 'BBQ Pizza', 125.00, 150.00, 5, 2, '6958e33f15c79.jpg', 1, '2026-01-03 09:37:03'),
(12, 5, 9, 10, 'Bja dihsb ', 120.00, 150.00, 11, 10, '6958ef20c5863.jpg', 1, '2026-01-03 10:27:44'),
(13, 1, 2, 2, 'Mojo', 50.00, 70.00, 10, 5, '695ca92c80658.jpg', 1, '2026-01-06 06:18:20'),
(14, 1, 1, 11, 'Chowmin Medium', 125.00, 160.00, 18, 10, '69650be4d42b4.jpg', 1, '2026-01-12 14:57:40'),
(15, 1, 1, 1, 'name1asd', 10.00, 15.00, 20, 25, NULL, 1, '2026-01-17 17:36:16');

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

--
-- Dumping data for table `returns`
--

INSERT INTO `returns` (`id`, `shop_id`, `order_id`, `product_id`, `quantity`, `refund_amount`, `reason`, `created_at`) VALUES
(1, 1, 1, 5, 1, 150.00, 'Customer Return', '2025-12-27 17:22:00'),
(2, 1, 3, 6, 1, 420.00, 'Customer Return', '2025-12-27 18:09:55'),
(3, 2, 4, 9, 1, 200.00, 'Customer Return', '2025-12-27 21:20:54'),
(4, 1, 5, 10, 1, 420.00, 'Customer Return', '2025-12-29 17:39:30'),
(5, 1, 5, 5, 1, 150.00, 'Customer Return', '2025-12-29 17:39:30'),
(6, 5, 11, 12, 1, 150.00, 'Customer Return', '2026-01-03 10:34:13'),
(7, 1, 13, 14, 1, 160.00, 'Customer Return', '2026-01-12 15:02:01'),
(8, 1, 14, 14, 1, 160.00, 'Customer Return', '2026-01-15 17:11:56'),
(9, 1, 17, 2, 2, 60.00, 'Customer Return', '2026-01-19 14:38:46');

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
(1, 'Bogura Store1', 'active', 2, '2025-12-27 15:44:55', '2026-01-19 14:30:33', 9),
(2, 'Pirganj Supershop', 'active', 1, '2025-12-27 21:11:23', '2025-12-27 21:11:23', 0),
(3, 'Dhaka Mart', 'active', 1, '2026-01-03 10:23:22', '2026-01-03 10:23:22', 0),
(4, 'Dhaka Mart', 'active', 1, '2026-01-03 10:26:24', '2026-01-03 10:26:24', 0),
(5, 'Bogura Store1', 'active', 1, '2026-01-03 10:27:11', '2026-01-03 10:27:11', 0);

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

--
-- Dumping data for table `subscription_payments`
--

INSERT INTO `subscription_payments` (`id`, `shop_id`, `plan_id`, `amount`, `payment_status`, `payment_method`, `payment_date`) VALUES
(1, 1, 1, 0.00, 'paid', 'system_init', '2026-01-03 09:05:15'),
(2, 1, 2, 29.99, 'paid', 'SSLCommerz Sandbox', '2026-01-03 09:26:23'),
(3, 1, 1, 0.00, 'paid', 'SSLCommerz Sandbox', '2026-01-03 09:28:23'),
(4, 1, 2, 29.99, 'paid', 'SSLCommerz Sandbox', '2026-01-03 09:31:12'),
(5, 1, 2, 29.99, 'paid', 'SSLCommerz Sandbox', '2026-01-12 14:53:51'),
(6, 1, 2, 29.99, 'paid', 'SSLCommerz Sandbox', '2026-01-18 15:24:35'),
(7, 1, 2, 29.99, 'paid', 'SSLCommerz Sandbox', '2026-01-18 15:26:11'),
(8, 1, 2, 29.99, 'paid', 'SSLCommerz Sandbox', '2026-01-18 15:33:17'),
(9, 1, 2, 29.99, 'paid', 'SSLCommerz Sandbox', '2026-01-18 15:37:20'),
(10, 1, 2, 29.99, 'paid', 'SSLCommerz Sandbox', '2026-01-19 14:30:33');

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
(2, 'Pro Tier', 29.99, 10, '2025-12-27 15:09:54'),
(5, 'Go Plan', 5.00, 1000, '2025-12-30 05:33:51');

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
(3, 1, 'shop_admin', 'Mahbub Hasan', 'bnlimon0@gmail.com', '$2y$10$9yRuyHPdtaMdEFDRkttl1uQwPSieTG3iVhxGm4FSzTHyAnKQnYXOu', 'Bogura Store1', 'default.png', '2025-12-27 15:44:55'),
(4, 1, 'cashier', 'cash1', 'cash1@gmail.com', '$2y$10$6dJlRuaXOST3DI0pTz3VBe9dOviTQ5s8IQRV4a5GUQBp1eqAy2W3q', 'cash1', 'default.png', '2025-12-27 15:56:33'),
(5, 1, 'cashier', 'cash3', 'cash3@gmail.com', '$2y$10$Auuozf3bw74KemfejZR9JOPh4k8A6bFPHwFY93k7b1mWONKlE3w4a', 'cash3', 'default.png', '2025-12-27 16:01:27'),
(6, 2, 'shop_admin', 'Mujtahid Tabassum', 'mujtahid@gmail.com', '$2y$10$ohIPf6VM4Or/aPeUDb2hZOdoQ1xF7MLIWJG.Zi4jn2LJaTuHF2Lae', 'Pirganj Supershop', 'default.png', '2025-12-27 21:11:23'),
(8, 2, 'cashier', 'cash03', 'cash03@gmail.com', '$2y$10$k331bvcGwqpT08n2DsG95egLYCRoqb4UpZKJHpOWVZg/hXnzsTy4a', 'cash03', 'default.png', '2025-12-27 21:16:49'),
(9, 4, 'shop_admin', 'Hello World', 'newmail@gmail.com', '$2y$10$4gVoznXUoVqtitNpU8kPCuGN8Fgl4B59ftMEA5bL2UdAXkZJFsCQa', 'Dhaka Mart', 'default.png', '2026-01-03 10:26:24'),
(10, 5, 'shop_admin', 'Mahbub', 'bnlimon1@gmail.com', '$2y$10$uMkr7bQPczkSuauzpKrL7OfpkmCoZOsU5P1CO5uY8eV2//dM5crDq', 'Bogura Store1', 'default.png', '2026-01-03 10:27:11'),
(11, 5, 'cashier', 'cash10', 'cash10@gmail.com', '$2y$10$QUjvFThEIsqx/vX6SSseQuIT6FieWlvIku.EtxyUwkYYfqwN.8sF.', 'cash10', 'default.png', '2026-01-03 10:29:04');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `returns`
--
ALTER TABLE `returns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `shops`
--
ALTER TABLE `shops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
