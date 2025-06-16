-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 16-06-2025 a las 22:39:20
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `logginp`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('admin','normal','invitado') NOT NULL DEFAULT 'normal',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `user_type`, `created_at`) VALUES
(1, 'admin', '$2y$10$nVnULktNBH0C7e6r8Smbe.gSF61wjZh/7a6mmorhlXC4ECmbV2BAS', 'normal', '2025-06-10 18:00:57'),
(4, 'usuario 2', '$2y$10$klCTwpDb/FXEbKPtjUd24OMrJ68Q1/btZ5XqZso95rqG8nZnh8deO', 'normal', '2025-06-10 23:24:20'),
(5, 'usuario 3', '$2y$10$KE48HaHOmCrw/5jNZO5D3e6V8F5bvgKpudpahFOoqk1bPgVLX1.Lq', 'normal', '2025-06-11 00:39:40'),
(6, 'Hector', '$2y$10$4LFnZRqRGVo6HEl.bINhfO9qY0QBf3u0QTKjv/nIuZSR4h6AYu.9S', 'normal', '2025-06-11 01:02:51'),
(7, 'usuario 4', '$2y$10$NyT.lJpTCfdJqEcWFAma2.3CTxZDSx8rHpCHP9qYc/.UeZ8HWslM2', 'normal', '2025-06-11 01:03:24'),
(8, 'usuario 5', '$2y$10$Yablh/zlIq/7tB4eEX9truvkp.1ZSpWaxg.GnGKJOINiLoFvP1qYG', 'normal', '2025-06-11 01:06:24'),
(10, 'ADMINISTRADOR', '$2y$10$kv1DWqv6xZFvIgKaH/cx5ufVBC8VOvDYcoTwdwx9NV7vfxroTgT6G', 'admin', '2025-06-11 01:11:51'),
(11, 'invitado', '$2y$10$Gcd.fUryDmfF1wUCY8lvWelE1FPQcOYMuZWxEzXDtgMAgjZziXJUW', 'invitado', '2025-06-15 04:32:37');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
