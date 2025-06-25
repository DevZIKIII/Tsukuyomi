-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 25/06/2025 às 07:19
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
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(1, 2, 1, 2, '2025-06-24 17:16:50'),
(2, 2, 5, 1, '2025-06-24 17:16:50'),
(3, 3, 3, 1, '2025-06-24 17:16:50');

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
(1, 2, 279.70, 'delivered', 'credit_card', 'Rua A, 123, São Paulo - SP, 01234-567', '2025-06-24 17:16:51', '2025-06-24 17:16:51'),
(2, 3, 249.90, 'processing', 'pix', 'Rua C, 789, São Paulo - SP, 03456-789', '2025-06-24 17:16:51', '2025-06-24 17:16:51');

-- --------------------------------------------------------

--
-- Estrutura para tabela `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `created_at`) VALUES
(1, 1, 1, 2, 89.90, '2025-06-24 17:16:51'),
(2, 1, 5, 1, 99.90, '2025-06-24 17:16:51'),
(3, 2, 4, 1, 249.90, '2025-06-24 17:16:51');

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
  `size` varchar(10) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `category`, `size`, `stock_quantity`, `image_url`, `created_at`, `updated_at`) VALUES
(1, 'Camiseta Naruto - Akatsuki', 'Camiseta preta com estampa da Akatsuki em vermelho. 100% algodão, estampa em silk screen de alta qualidade.', 89.90, 'Camisetas', 'M', 50, 'naruto_akatsuki.jpg', '2025-06-24 17:16:50', '2025-06-24 17:16:50'),
(2, 'Moletom Tokyo Ghoul', 'Moletom com capuz preto, estampa do Ken Kaneki. Material: 70% algodão, 30% poliéster.', 159.90, 'Moletons', 'G', 30, 'tokyo_ghoul_hoodie.jpg', '2025-06-24 17:16:50', '2025-06-24 17:16:50'),
(3, 'Camiseta Attack on Titan - Survey Corps', 'Camiseta preta com emblema da Tropa de Exploração. Design minimalista streetwear.', 79.90, 'Camisetas', 'M', 40, 'aot_survey_corps.jpg', '2025-06-24 17:16:50', '2025-06-25 05:08:05'),
(4, 'Jaqueta Demon Slayer', 'Jaqueta bomber inspirada no uniforme do Tanjiro. Detalhes em verde e preto.', 249.90, 'Jaquetas', 'GG', 20, 'demon_slayer_jacket.jpg', '2025-06-24 17:16:50', '2025-06-24 17:16:50'),
(5, 'Camiseta One Piece - Mugiwara', 'Camiseta preta com rostos dos Chapéu de Palha. Edição limitada.', 99.90, 'Camisetas', 'P', 35, 'onepiece_mugiwara.jpg', '2025-06-24 17:16:50', '2025-06-25 05:09:47'),
(6, 'Moletom Jujutsu Kaisen', 'Moletom preto com estampa do Gojo Satoru. Super confortável para o dia a dia.', 179.90, 'Moletons', 'M', 25, 'jjk_gojo_hoodie.jpg', '2025-06-24 17:16:50', '2025-06-25 05:09:59'),
(7, 'Calça Cargo Cyberpunk', 'Calça cargo preta com detalhes em neon. Inspirada em Cyberpunk Edgerunners.', 189.90, 'Calças', '42', 15, 'cyberpunk_cargo.jpg', '2025-06-24 17:16:50', '2025-06-24 17:16:50'),
(8, 'Camiseta Death Note', 'Camiseta preta com design do protagonista do Death Note. Premium quality.', 89.90, 'Camisetas', 'G', 45, 'death_note_tee.jpg', '2025-06-24 17:16:50', '2025-06-25 05:10:39'),
(9, 'Jaqueta Evangelion', 'Jaqueta varsity roxa e verde inspirada no EVA-01. Coleção exclusiva.', 299.90, 'Jaquetas', 'M', 10, 'evangelion_jacket.jpg', '2025-06-24 17:16:50', '2025-06-24 17:16:50'),
(10, 'Shorts Dragon Ball', 'Shorts laranja inspirado no uniforme de treino do Goku. Perfeito para academia.', 119.90, 'Shorts', 'G', 30, 'dbz_shorts.jpg', '2025-06-24 17:16:50', '2025-06-24 17:16:50'),
(11, 'Camiseta Hunter x Hunter', 'Camiseta cinza com estampa de personagem. Design discreto e elegante.', 84.90, 'Camisetas', 'GG', 40, 'hxh_nen_tee.jpg', '2025-06-24 17:16:50', '2025-06-25 05:19:54'),
(12, 'Moletom My Hero Academia', 'Moletom azul e branco da U.A. High School. Licenciado oficial.', 199.90, 'Moletons', 'P', 20, 'mha_ua_hoodie.jpg', '2025-06-24 17:16:50', '2025-06-25 05:02:30'),
(13, 'Calça Jogger Chainsaw Man', 'Calça jogger preta com estampa do Pochita. Streetwear premium.', 169.90, 'Calças', '40', 25, 'chainsaw_jogger.jpg', '2025-06-24 17:16:50', '2025-06-24 17:16:50'),
(14, 'Camiseta Spy x Family', 'Camiseta rosa com a Anya. Design fofo e moderno.', 79.90, 'Camisetas', 'M', 50, 'spy_family_anya.jpg', '2025-06-24 17:16:50', '2025-06-24 17:16:50'),
(15, 'Jaqueta Corta-Vento Pokémon', 'Jaqueta leve com estampa de uniforme de treinador. Ideal para dias frescos.', 139.90, 'Jaquetas', 'G', 35, 'pokemon_windbreaker.jpg', '2025-06-24 17:16:50', '2025-06-25 05:11:46');

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
(4, 'Pedro Oliveira', 'pedro@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '11923456789', 'Rua C, 789', 'São Paulo', 'SP', '03456-789', 'customer', '2025-06-24 17:16:50');

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
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
