-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 05-02-2026 a las 21:42:51
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `jmr_match`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `admins`
--

INSERT INTO `admins` (`id`, `email`, `password`) VALUES
(1, 'babylon@salta.com', '$2y$10$F5iEtt8jCMBgQnvuEYJjVuUx.ArPWU6euHqn1tPogrXreg0ks8U3y'),
(2, 'fer@zepelin.com', '$2y$10$S2WlYAavjmPMSjROHNIbI./0sk4O/M2Y/FLfRqmJ5CyRk0n9xVO/G'),
(3, 'fer@zepellin', '$2y$10$vevLtSX7DoRSaYuwPpAe.OsN64G0gtU9DCL9nn01zYumELTxetmDm');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `boliches`
--

CREATE TABLE `boliches` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `codigo_acceso` varchar(20) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `interacciones`
--

CREATE TABLE `interacciones` (
  `id` int(11) NOT NULL,
  `usuario_emisor_id` int(11) DEFAULT NULL,
  `usuario_receptor_id` int(11) DEFAULT NULL,
  `estado` enum('like','dislike') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `usuario_da_id` int(11) DEFAULT NULL,
  `usuario_recibe_id` int(11) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `likes`
--

INSERT INTO `likes` (`id`, `usuario_da_id`, `usuario_recibe_id`, `fecha`) VALUES
(1, 3, 1, '2026-01-19 21:47:48'),
(2, 3, 2, '2026-01-19 21:47:50'),
(3, 1, 3, '2026-01-19 21:47:55'),
(4, 4, 3, '2026-01-19 21:49:38'),
(5, 3, 4, '2026-01-19 21:50:16'),
(6, 6, 5, '2026-01-19 22:04:38'),
(7, 5, 6, '2026-01-19 22:04:39'),
(8, 7, 5, '2026-01-19 22:33:29'),
(9, 7, 6, '2026-01-19 22:33:31'),
(10, 7, 8, '2026-01-19 22:33:33'),
(11, 8, 5, '2026-01-19 22:33:37'),
(12, 8, 6, '2026-01-19 22:33:40'),
(13, 8, 7, '2026-01-19 22:33:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `locales`
--

CREATE TABLE `locales` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `codigo_acceso` varchar(20) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `locales`
--

INSERT INTO `locales` (`id`, `nombre`, `codigo_acceso`, `admin_id`) VALUES
(1, 'Boliche Sky', 'VALENTIN2026', NULL),
(2, 'BabylonSalta', 'E478B4C0', 1),
(3, 'Zepellin', '2EA689AD', 2),
(4, 'Zepellin Salta', '5A744CD0', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `local_id` int(11) DEFAULT NULL,
  `boliche_id` int(11) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `sexo` enum('hombre','mujer','otro') DEFAULT NULL,
  `interes` enum('hombre','mujer','todos') DEFAULT NULL,
  `instagram` varchar(50) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `foto1` varchar(255) DEFAULT NULL,
  `foto2` varchar(255) DEFAULT NULL,
  `foto3` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `local_id`, `boliche_id`, `nombre`, `sexo`, `interes`, `instagram`, `whatsapp`, `foto1`, `foto2`, `foto3`) VALUES
(1, 1, NULL, 'Tomas Canavidez', 'hombre', 'mujer', 'jmr.tomi', '543873076160', '1768859186_696ea632c709f.png', NULL, NULL),
(2, 1, NULL, 'Sofia', 'hombre', 'mujer', 'Sofiacallave', '38737373737', '1768859221_696ea65526939.jpg', NULL, NULL),
(3, 1, NULL, 'Sofia2', 'mujer', 'hombre', 'Sofiacallave', '666666', '1768859262_696ea67ee72d3.jpg', NULL, NULL),
(4, 1, NULL, 'Tomas Canavidez', 'hombre', 'mujer', 'jmr.tomi', '543873076160', '1768859371_696ea6ebd7963.png', NULL, NULL),
(5, 2, NULL, 'Adrian', 'hombre', 'mujer', 'adrian12', '387999999999', '1768860182_696eaa16a99a1.jpg', NULL, NULL),
(6, 2, NULL, 'Patricia', 'mujer', 'todos', 'Patri66', '3879999999', '1768860269_696eaa6d8d28a.jpg', NULL, NULL),
(7, 2, NULL, 'tomas', 'hombre', 'todos', 'jmr.tomi', '387128371231', '1768861977_696eb11982c5d.png', NULL, NULL),
(8, 2, NULL, 'jigg', 'mujer', 'todos', 'jigg2', '38712938712981', '1768862006_696eb136405d0.png', NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `boliches`
--
ALTER TABLE `boliches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_acceso` (`codigo_acceso`);

--
-- Indices de la tabla `interacciones`
--
ALTER TABLE `interacciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_emisor_id` (`usuario_emisor_id`),
  ADD KEY `usuario_receptor_id` (`usuario_receptor_id`);

--
-- Indices de la tabla `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_da_id` (`usuario_da_id`),
  ADD KEY `usuario_recibe_id` (`usuario_recibe_id`);

--
-- Indices de la tabla `locales`
--
ALTER TABLE `locales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_acceso` (`codigo_acceso`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `boliche_id` (`boliche_id`),
  ADD KEY `local_id` (`local_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `boliches`
--
ALTER TABLE `boliches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `interacciones`
--
ALTER TABLE `interacciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `locales`
--
ALTER TABLE `locales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `interacciones`
--
ALTER TABLE `interacciones`
  ADD CONSTRAINT `interacciones_ibfk_1` FOREIGN KEY (`usuario_emisor_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `interacciones_ibfk_2` FOREIGN KEY (`usuario_receptor_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`usuario_da_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`usuario_recibe_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`boliche_id`) REFERENCES `boliches` (`id`),
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`local_id`) REFERENCES `locales` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
