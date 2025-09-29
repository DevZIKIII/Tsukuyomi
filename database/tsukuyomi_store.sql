-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 29/09/2025 às 07:26
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `tsukuyomi_store`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size` varchar(10) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `collection_votes`
--

CREATE TABLE `collection_votes` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `votes` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `collection_votes`
--

INSERT INTO `collection_votes` (`id`, `name`, `image_url`, `votes`, `is_active`) VALUES
(1, 'Akatsuki', 'naruto_akatsuki.jpg', 2, 1),
(2, 'One Piece', 'onepiece_mugiwara.jpg', 0, 1),
(4, 'Death note', 'death_note_tee.jpg', 0, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_value` decimal(10,2) DEFAULT 0.00,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0,
  `valid_from` date NOT NULL,
  `valid_until` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `description`, `discount_type`, `discount_value`, `min_order_value`, `max_discount`, `usage_limit`, `used_count`, `valid_from`, `valid_until`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'BEMVINDO10', 'Desconto de boas-vindas - 10% OFF', 'percentage', 10.00, 50.00, NULL, NULL, 0, '2025-07-01', '2025-07-31', 1, '2025-07-01 19:49:40', '2025-07-01 19:49:40'),
(2, 'FRETE20', 'R$ 20 de desconto no frete', 'fixed', 20.00, 100.00, NULL, NULL, 0, '2025-07-01', '2025-08-30', 1, '2025-07-01 19:49:40', '2025-07-01 19:49:40'),
(3, 'ANIME15', '15% OFF em produtos de anime', 'percentage', 15.00, 0.00, NULL, NULL, 0, '2025-07-01', '2025-09-29', 1, '2025-07-01 19:49:40', '2025-07-01 19:49:40'),
(4, 'DESCONTAO', 'DESCONTAO', 'percentage', 10.00, 0.00, NULL, NULL, 2, '2025-09-28', '2025-10-05', 1, '2025-09-29 00:32:08', '2025-09-29 05:16:50');

-- --------------------------------------------------------

--
-- Estrutura para tabela `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `payment_method`, `shipping_address`, `created_at`, `updated_at`) VALUES
(13, 7, 79.99, 'processing', 'pix', 'Rua Reverendo Coriolano, 19,  - Jardim Aviação, Presidente Prudente - SP, CEP: 19020500', '2025-09-28 06:04:05', '2025-09-28 06:04:05'),
(14, 8, 79.99, 'processing', 'pix', 'Rua Reverendo Coriolano, 19,  - Jardim Aviação, Presidente Prudente - SP, CEP: 19020500', '2025-09-28 06:05:32', '2025-09-28 06:05:32'),
(15, 8, 159.98, 'processing', 'boleto', 'Rua Reverendo Coriolano, 19,  - Jardim Aviação, Presidente Prudente - SP, CEP: 19020500', '2025-09-28 06:06:28', '2025-09-28 06:06:28'),
(16, 7, 71.99, 'processing', 'pix', 'Rua Reverendo Coriolano, 19,  - Jardim Aviação, Presidente Prudente - SP, CEP: 19020500', '2025-09-29 00:33:32', '2025-09-29 00:33:32'),
(17, 7, 983.88, 'delivered', 'pix', 'Rua João Corazza, 402,  - Residencial Monte Carlo, Presidente Prudente - SP, CEP: 19064-564', '2025-09-29 05:16:50', '2025-09-29 05:17:01');

-- --------------------------------------------------------

--
-- Estrutura para tabela `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `size`, `quantity`, `price`, `created_at`) VALUES
(1, 13, 16, 'GG', 1, 79.99, '2025-09-28 06:04:05'),
(2, 14, 16, 'GG', 1, 79.99, '2025-09-28 06:05:32'),
(3, 15, 16, 'G', 2, 79.99, '2025-09-28 06:06:28'),
(4, 16, 16, 'G', 1, 79.99, '2025-09-29 00:33:32'),
(5, 17, 16, 'G', 13, 79.99, '2025-09-29 05:16:50');

-- --------------------------------------------------------

--
-- Estrutura para tabela `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `category`, `image_url`, `created_at`, `updated_at`) VALUES
(16, 'Camiseta Akatsuki', 'Sim da akatsuki', 79.99, 'Camisetas', '68d8ce73ddffb-naruto_akatsuki.jpg', '2025-09-28 05:58:11', '2025-09-28 05:58:11');

-- --------------------------------------------------------

--
-- Estrutura para tabela `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size` varchar(10) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `size`, `stock_quantity`) VALUES
(1, 16, 'PP', 50),
(2, 16, 'P', 50),
(3, 16, 'M', 50),
(4, 16, 'G', 34),
(5, 16, 'GG', 48),
(6, 16, 'XG', 50);

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `user_type` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `city`, `state`, `zip_code`, `user_type`, `created_at`) VALUES
(1, 'Admin', 'admin@tsukuyomi.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, NULL, NULL, 'admin', '2025-06-24 17:16:49'),
(2, 'João Silva', 'joao@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '11987654321', 'Rua A, 123', 'São Paulo', 'SP', '01234-567', 'customer', '2025-06-24 17:16:50'),
(3, 'Maria Santos', 'maria@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '11912345678', 'Av. B, 456', 'São Paulo', 'SP', '02345-678', 'customer', '2025-06-24 17:16:50'),
(4, 'Pedro Oliveira', 'pedro@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '11923456789', 'Rua C, 789', 'São Paulo', 'SP', '03456-789', 'customer', '2025-06-24 17:16:50'),
(5, 'Daniel José Dantas Jacometo', 'marciojjacometo@gmail.com', '$2y$10$3JvpLmupHtBQilXTassyY.tPnTrvPDK1Hxa.XwJC2JNXDVAIIRXxu', '18997222271', 'Rua João Corazza, 402', 'Presidente Prudente', 'SP', '19020-500', 'admin', '2025-06-25 01:24:48'),
(7, 'Daniel José Dantas Jacometo', 'ziki@gmail.com', '$2y$10$j/KfWqeA4v.7gzqBno2Z3efMMjvUA7lXSJPtFWxFsuMaT3.tPSydO', '18997222271', 'Rua Reverendo Coriolano, 19', 'Presidente Prudente', 'SP', '19020-500', 'admin', '2025-09-27 04:41:02'),
(8, 'Daniel José Dantas Jacometo', 'marciojjacometo@hotmail.com', '$2y$10$BQCBQCz.J0bMFUvLA3Jb5.b3qw7owLm/92T5nWLFzZZi4mt9kQfvW', '18997222271', 'Rua João Corazza, 402', 'Presidente Prudente', 'SP', '19020500', 'customer', '2025-09-28 06:04:51');

-- --------------------------------------------------------

--
-- Estrutura para tabela `user_votes`
--

CREATE TABLE `user_votes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote_option_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `user_votes`
--

INSERT INTO `user_votes` (`id`, `user_id`, `vote_option_id`, `created_at`) VALUES
(1, 8, 1, '2025-09-29 04:15:33'),
(2, 7, 1, '2025-09-29 04:28:09');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Índices de tabela `collection_votes`
--
ALTER TABLE `collection_votes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Índices de tabela `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Índices de tabela `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `user_votes`
--
ALTER TABLE `user_votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `vote_option_id` (`vote_option_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `collection_votes`
--
ALTER TABLE `collection_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `user_votes`
--
ALTER TABLE `user_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `user_votes`
--
ALTER TABLE `user_votes`
  ADD CONSTRAINT `user_votes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_votes_ibfk_2` FOREIGN KEY (`vote_option_id`) REFERENCES `collection_votes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
