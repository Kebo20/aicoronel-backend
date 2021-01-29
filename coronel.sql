-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-01-2021 a las 00:03:38
-- Versión del servidor: 10.4.17-MariaDB
-- Versión de PHP: 7.3.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `coronel`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id_category` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_by` tinyint(4) NOT NULL,
  `updated_by` tinyint(4) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id_category`, `name`, `status`, `created_by`, `updated_by`, `updated_at`, `created_at`) VALUES
(1, 'máquinas', 1, 1, 1, '2021-01-11 04:46:17', '2021-01-11 04:46:17'),
(2, 'abonosssss', 0, 1, 1, '2021-01-11 04:47:03', '2021-01-11 04:46:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clients`
--

CREATE TABLE `clients` (
  `id_client` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `type_doc` varchar(250) NOT NULL,
  `number_doc` varchar(250) NOT NULL,
  `address` varchar(250) NOT NULL,
  `phone` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_by` tinyint(4) NOT NULL,
  `updated_by` tinyint(4) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `clients`
--

INSERT INTO `clients` (`id_client`, `name`, `type_doc`, `number_doc`, `address`, `phone`, `email`, `status`, `created_by`, `updated_by`, `updated_at`, `created_at`) VALUES
(1, 'edgar aaaa', 'dni', '16254256', 'Incanato 588', '945621489', 'marilvg2705@hotmail.com', 0, 1, 1, '2021-01-11 03:49:22', '2021-01-11 03:39:21'),
(2, 'mañuco valdiviezo', 'dni', '16254256', 'Incanato 588', '945621489', 'marilvg2705@hotmail.com', 1, 1, 1, '2021-01-11 03:40:24', '2021-01-11 03:40:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lots`
--

CREATE TABLE `lots` (
  `id_lot` int(11) NOT NULL,
  `quantity` double(18,2) NOT NULL,
  `id_product` int(11) NOT NULL,
  `id_storage` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_by` tinyint(4) NOT NULL,
  `updated_by` tinyint(4) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `lots`
--

INSERT INTO `lots` (`id_lot`, `quantity`, `id_product`, `id_storage`, `status`, `created_by`, `updated_by`, `updated_at`, `created_at`) VALUES
(3, 20.00, 3, 1, 1, 1, 1, '2021-01-14 18:17:28', '2021-01-14 09:26:18'),
(4, 20.00, 4, 1, 1, 1, 1, '2021-01-14 09:26:18', '2021-01-14 09:26:18'),
(5, 10.00, 1, 1, 1, 1, 1, '2021-01-14 09:30:02', '2021-01-14 09:30:02'),
(6, 5.00, 3, 2, 1, 1, 1, '2021-01-18 21:46:41', '2021-01-14 09:32:59'),
(7, 20.00, 4, 2, 1, 1, 1, '2021-01-18 22:48:22', '2021-01-18 21:49:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(3, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(4, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(5, '2016_06_01_000004_create_oauth_clients_table', 1),
(6, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(7, '2019_08_19_000000_create_failed_jobs_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `moves_product`
--

CREATE TABLE `moves_product` (
  `id_move_product` int(11) NOT NULL,
  `date` date NOT NULL,
  `type` varchar(250) NOT NULL,
  `stock` double(18,2) NOT NULL,
  `quantity` double(18,2) NOT NULL,
  `price` double(18,2) NOT NULL,
  `total_cost` double(18,2) NOT NULL,
  `table_reference` varchar(250) NOT NULL,
  `id_product` int(11) NOT NULL,
  `id_lot` int(11) NOT NULL,
  `id_reference` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_by` tinyint(4) NOT NULL,
  `updated_by` tinyint(4) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `moves_product`
--

INSERT INTO `moves_product` (`id_move_product`, `date`, `type`, `stock`, `quantity`, `price`, `total_cost`, `table_reference`, `id_product`, `id_lot`, `id_reference`, `status`, `created_by`, `updated_by`, `updated_at`, `created_at`) VALUES
(1, '2021-01-13', 'entrada', 20.00, 20.00, 10.00, 200.00, 'purchases', 3, 3, 35, 1, 1, 1, '2021-01-14 09:26:18', '2021-01-14 09:26:18'),
(2, '2021-01-13', 'entrada', 20.00, 20.00, 10.00, 200.00, 'purchases', 4, 4, 35, 1, 1, 1, '2021-01-14 09:26:18', '2021-01-14 09:26:18'),
(3, '2021-01-13', 'entrada', 10.00, 10.00, 10.00, 100.00, 'purchases', 1, 5, 36, 1, 1, 1, '2021-01-14 09:30:02', '2021-01-14 09:30:02'),
(4, '2021-01-13', 'entrada', 36.00, 15.50, 15.50, 240.25, 'purchases', 3, 3, 37, 1, 1, 1, '2021-01-14 09:31:24', '2021-01-14 09:31:24'),
(5, '2021-01-13', 'entrada', 21.00, 20.50, 15.50, 317.75, 'purchases', 3, 6, 38, 1, 1, 1, '2021-01-14 09:32:59', '2021-01-14 09:32:59'),
(7, '2021-01-13', 'salidas', 0.00, 20.50, 15.50, 0.00, 'purchases', 3, 6, 38, 1, 1, 1, '2021-01-14 23:08:13', '2021-01-14 23:08:13'),
(8, '2021-01-13', 'salidas', 20.00, 15.50, 15.50, 0.00, 'purchases', 3, 3, 37, 1, 1, 1, '2021-01-14 18:17:28', '2021-01-14 18:17:28'),
(9, '2021-01-17', 'entrada', 10.00, 10.00, 10.00, 100.00, 'purchases', 3, 6, 39, 1, 1, 1, '2021-01-17 19:20:25', '2021-01-17 19:20:25'),
(10, '2021-01-17', 'salida', 20.00, 10.00, 10.00, 100.00, 'sales', 3, 6, 2, 1, 1, 1, '2021-01-17 19:26:05', '2021-01-17 19:26:05'),
(11, '2021-01-18', 'entrada', 25.00, 5.00, 50.50, 252.50, 'purchases', 3, 6, 40, 1, 1, 1, '2021-01-18 21:33:37', '2021-01-18 21:33:37'),
(12, '2021-01-18', 'salida', 15.00, 10.00, 60.00, 600.00, 'sales', 3, 6, 3, 1, 1, 1, '2021-01-18 21:41:42', '2021-01-18 21:41:42'),
(13, '2021-01-18', 'salida', 10.00, 5.00, 60.00, 300.00, 'sales', 3, 6, 4, 1, 1, 1, '2021-01-18 21:44:08', '2021-01-18 21:44:08'),
(14, '2021-01-18', 'salida', 5.00, 5.00, 60.00, 300.00, 'sales', 3, 6, 5, 1, 1, 1, '2021-01-18 21:46:41', '2021-01-18 21:46:41'),
(15, '2021-01-17', 'entrada', 15.00, 15.00, 4.00, 60.00, 'purchases', 4, 7, 41, 1, 1, 1, '2021-01-18 21:49:30', '2021-01-18 21:49:30'),
(16, '2021-01-18', 'salida', 10.00, 5.00, 100.00, 500.00, 'sales', 4, 7, 6, 1, 1, 1, '2021-01-18 21:53:31', '2021-01-18 21:53:31'),
(17, '2021-01-18', 'salida', 5.00, 5.00, 100.00, 500.00, 'sales', 4, 7, 7, 1, 1, 1, '2021-01-18 21:55:19', '2021-01-18 21:55:19'),
(18, '2021-01-18', 'salida', 0.00, 5.00, 100.00, 500.00, 'sales', 4, 7, 8, 1, 1, 1, '2021-01-18 22:00:17', '2021-01-18 22:00:17'),
(19, '2021-01-17', 'entrada', 20.00, 20.00, 5.00, 100.00, 'purchases', 4, 7, 42, 1, 1, 1, '2021-01-18 22:02:24', '2021-01-18 22:02:24'),
(20, '2021-01-18', 'salida', 10.00, 10.00, 100.00, 1000.00, 'sales', 4, 7, 9, 1, 1, 1, '2021-01-18 22:03:17', '2021-01-18 22:03:17'),
(21, '2021-01-18', 'entrada', 20.00, 10.00, 100.00, 0.00, 'sales', 4, 7, 9, 1, 1, 1, '2021-01-18 22:48:22', '2021-01-18 22:48:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `oauth_access_tokens`
--

INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('01a2e62ac30e5574587419c13088829cb549a12d971b5163ec31cbe87d808b043fc3364a61143b80', 1, 1, 'Personal Access Token', '[]', 0, '2021-01-07 09:55:39', '2021-01-07 09:55:39', '2022-01-07 04:55:39'),
('35c82ea4d08dfd0805783afb8c2999a5a7f7236478600549945620aca08b23dfd635b4d31734d50e', 1, 1, 'Personal Access Token', '[]', 0, '2021-01-11 01:11:34', '2021-01-11 01:11:34', '2022-01-10 20:11:34'),
('4dd757a825e8f0595e0631083b50875dbaba140fc25cc83c3ebffdf4b96660a8f463a607b81fecf8', 1, 1, 'Personal Access Token', '[]', 0, '2021-01-11 22:03:15', '2021-01-11 22:03:15', '2022-01-11 17:03:15'),
('4fa70987c3a35da98611f42d609fdb302d2acaa0237058d04dcc2928a5112e5d932150cffd7dc3aa', 1, 1, 'Personal Access Token', '[]', 0, '2021-01-03 09:22:00', '2021-01-03 09:22:00', '2022-01-03 04:22:00'),
('585bfc36a2d7c22aae85f0f5513b8e44f6359bb4751c5d93aed02227d4472216288d18c47c27c08a', 1, 1, 'Personal Access Token', '[]', 0, '2021-01-07 10:29:18', '2021-01-07 10:29:18', '2022-01-07 05:29:18'),
('5fbaf498cdefb58c2a86863e13ff3d3245415dbbd340306852421a1dd2b38f40331e1820256e235a', 1, 1, 'Personal Access Token', '[]', 0, '2021-01-11 03:33:25', '2021-01-11 03:33:25', '2022-01-10 22:33:25'),
('852f02bd710a4bb0744ebbd7af3536c3c9698fe2b63f9eb053d52e9dff52c5a820ec5944e16fac34', 1, 1, 'Personal Access Token', '[]', 0, '2021-01-11 22:00:42', '2021-01-11 22:00:42', '2022-01-11 17:00:42'),
('bf80cbec02c235148bc5c7459ae9d230858ba147726a32e08e589a1271f2f6a234c85a7a3a2f77a6', 1, 1, 'Personal Access Token', '[]', 0, '2021-01-11 22:02:06', '2021-01-11 22:02:06', '2022-01-11 17:02:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `oauth_auth_codes`
--

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `oauth_clients`
--

INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `provider`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Laravel Personal Access Client', 'jbT1XDurvSSzeOJxcmjaIu9Mmc89GLcnb0wcmC48', NULL, 'http://localhost', 1, 0, 0, '2021-01-03 08:29:17', '2021-01-03 08:29:17'),
(2, NULL, 'Laravel Password Grant Client', 'F2MCwZyoWr4D0YDs5wJWBlFuY98XyKhmdcvBCPLK', 'users', 'http://localhost', 0, 1, 0, '2021-01-03 08:29:17', '2021-01-03 08:29:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `oauth_personal_access_clients`
--

CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `oauth_personal_access_clients`
--

INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2021-01-03 08:29:17', '2021-01-03 08:29:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id_product` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `price` double(18,2) NOT NULL,
  `brand` varchar(250) NOT NULL,
  `units` varchar(250) NOT NULL,
  `id_category` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_by` tinyint(4) NOT NULL,
  `updated_by` tinyint(4) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id_product`, `name`, `price`, `brand`, `units`, `id_category`, `status`, `created_by`, `updated_by`, `updated_at`, `created_at`) VALUES
(1, 'ensamblador', 250.90, 'toyota', 'kilos', 1, 1, 1, 1, '2021-01-11 07:23:39', '2021-01-11 07:23:39'),
(3, 'cortadora', 50.50, 'linux', 'litros', 1, 1, 1, 1, '2021-01-11 07:42:32', '2021-01-11 07:35:34'),
(4, 'bolsas', 12.20, 'linux', 'libras', 1, 1, 1, 1, '2021-01-11 07:43:56', '2021-01-11 07:43:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `providers`
--

CREATE TABLE `providers` (
  `id_provider` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `type_doc` varchar(250) NOT NULL,
  `number_doc` varchar(250) NOT NULL,
  `address` varchar(250) NOT NULL,
  `phone` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_by` tinyint(4) NOT NULL,
  `updated_by` tinyint(4) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `providers`
--

INSERT INTO `providers` (`id_provider`, `name`, `type_doc`, `number_doc`, `address`, `phone`, `email`, `status`, `created_by`, `updated_by`, `updated_at`, `created_at`) VALUES
(1, 'henry eeeee', 'dni', '71309207', 'Incanato 588', '988730981', 'henryecv210297@hotmail.com', 1, 1, 1, '2021-01-11 01:35:05', '2021-01-11 01:30:27'),
(2, 'alert();', 'dni', '71829378', 'Incanato 588', '988730981', 'henryecv210297@hotmail.com', 1, 1, 1, '2021-01-11 02:11:17', '2021-01-11 02:11:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `purchases`
--

CREATE TABLE `purchases` (
  `id_purchase` int(11) NOT NULL,
  `date` date NOT NULL,
  `subtotal` double(18,2) NOT NULL,
  `igv` double(18,2) NOT NULL,
  `total` double(18,2) NOT NULL,
  `type_doc` varchar(250) NOT NULL,
  `number_doc` varchar(250) NOT NULL,
  `observation` varchar(250) DEFAULT NULL,
  `id_provider` int(11) NOT NULL,
  `id_storage` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_by` tinyint(4) NOT NULL,
  `updated_by` tinyint(4) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `purchases`
--

INSERT INTO `purchases` (`id_purchase`, `date`, `subtotal`, `igv`, `total`, `type_doc`, `number_doc`, `observation`, `id_provider`, `id_storage`, `status`, `created_by`, `updated_by`, `updated_at`, `created_at`) VALUES
(35, '2021-01-13', 338.98, 61.02, 400.00, 'dni', '12345678', 'ninguna', 1, 1, 1, 1, 1, '2021-01-14 09:26:18', '2021-01-14 09:26:18'),
(36, '2021-01-13', 84.75, 15.25, 100.00, 'boleta', '14253678', 'ninguna', 1, 1, 1, 1, 1, '2021-01-14 09:30:02', '2021-01-14 09:30:02'),
(37, '2021-01-13', 203.60, 36.65, 240.25, 'boleta', '17829378', 'ninguna', 1, 1, 0, 1, 1, '2021-01-14 18:17:28', '2021-01-14 09:31:24'),
(38, '2021-01-13', 269.28, 48.47, 317.75, 'boleta', '96385274', 'ninguna', 1, 2, 0, 1, 1, '2021-01-14 23:08:13', '2021-01-14 09:32:59'),
(39, '2021-01-17', 84.75, 15.25, 100.00, 'boleta', '75632147', 'ninguna', 1, 2, 1, 1, 1, '2021-01-17 19:20:25', '2021-01-17 19:20:25'),
(40, '2021-01-18', 213.98, 38.52, 252.50, 'boleta', '01020304', 'ninguna', 1, 2, 1, 1, 1, '2021-01-18 21:33:36', '2021-01-18 21:33:36'),
(41, '2021-01-17', 50.85, 9.15, 60.00, 'ruc', '14523678', 'ninguna', 2, 2, 1, 1, 1, '2021-01-18 21:49:30', '2021-01-18 21:49:30'),
(42, '2021-01-17', 84.75, 15.25, 100.00, 'ruc', '14523672', 'ninguna', 2, 2, 1, 1, 1, '2021-01-18 22:02:24', '2021-01-18 22:02:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `purchases_detail`
--

CREATE TABLE `purchases_detail` (
  `id_purchase_detail` int(11) NOT NULL,
  `quantity` double(18,2) NOT NULL,
  `price` double(18,2) NOT NULL,
  `subtotal` double(18,2) NOT NULL,
  `id_purchase` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `id_lot` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_by` tinyint(4) NOT NULL,
  `updated_by` tinyint(4) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `purchases_detail`
--

INSERT INTO `purchases_detail` (`id_purchase_detail`, `quantity`, `price`, `subtotal`, `id_purchase`, `id_product`, `id_lot`, `status`, `created_by`, `updated_by`, `updated_at`, `created_at`) VALUES
(1, 20.00, 10.00, 200.00, 35, 3, 3, 1, 1, 1, '2021-01-14 09:26:18', '2021-01-14 09:26:18'),
(2, 20.00, 10.00, 200.00, 35, 4, 4, 1, 1, 1, '2021-01-14 09:26:18', '2021-01-14 09:26:18'),
(3, 10.00, 10.00, 100.00, 36, 1, 5, 1, 1, 1, '2021-01-14 09:30:02', '2021-01-14 09:30:02'),
(4, 15.50, 15.50, 240.25, 37, 3, 3, 0, 1, 1, '2021-01-14 18:17:28', '2021-01-14 09:31:24'),
(5, 20.50, 15.50, 317.75, 38, 3, 6, 0, 1, 1, '2021-01-14 23:08:13', '2021-01-14 09:32:59'),
(6, 10.00, 10.00, 100.00, 39, 3, 6, 1, 1, 1, '2021-01-17 19:20:25', '2021-01-17 19:20:25'),
(7, 5.00, 50.50, 252.50, 40, 3, 6, 1, 1, 1, '2021-01-18 21:33:37', '2021-01-18 21:33:37'),
(8, 15.00, 4.00, 60.00, 41, 4, 7, 1, 1, 1, '2021-01-18 21:49:30', '2021-01-18 21:49:30'),
(9, 20.00, 5.00, 100.00, 42, 4, 7, 1, 1, 1, '2021-01-18 22:02:24', '2021-01-18 22:02:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_role` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_by` tinyint(4) NOT NULL,
  `updated_by` tinyint(4) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_role`, `name`, `status`, `created_by`, `updated_by`, `updated_at`, `created_at`) VALUES
(1, 'administrador', 1, 1, 1, '2021-01-11 04:06:23', '2021-01-11 04:06:23'),
(2, 'vendedor', 1, 1, 1, '2021-01-11 04:06:35', '2021-01-11 04:06:35'),
(3, 'consultores', 0, 1, 1, '2021-01-11 04:08:23', '2021-01-11 04:06:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales`
--

CREATE TABLE `sales` (
  `id_sale` int(11) NOT NULL,
  `date` date NOT NULL,
  `subtotal` double(18,2) NOT NULL,
  `igv` double(18,2) NOT NULL,
  `total` double(18,2) NOT NULL,
  `discount` double(18,2) DEFAULT NULL,
  `type_doc` varchar(250) NOT NULL,
  `number_doc` varchar(250) NOT NULL,
  `observation` varchar(250) DEFAULT NULL,
  `id_storage` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_by` tinyint(4) NOT NULL,
  `updated_by` tinyint(4) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `sales`
--

INSERT INTO `sales` (`id_sale`, `date`, `subtotal`, `igv`, `total`, `discount`, `type_doc`, `number_doc`, `observation`, `id_storage`, `id_client`, `status`, `created_by`, `updated_by`, `updated_at`, `created_at`) VALUES
(2, '2021-01-17', 84.75, 15.25, 100.00, NULL, 'boleta', '71309207', 'ninguna', 2, 2, 1, 1, 1, '2021-01-17 19:26:05', '2021-01-17 19:26:05'),
(3, '2021-01-18', 508.47, 91.53, 600.00, 0.00, 'boleta', '01234567', 'ninguna', 2, 2, 1, 1, 1, '2021-01-18 21:41:42', '2021-01-18 21:41:42'),
(4, '2021-01-18', 250.00, 45.00, 295.00, 5.00, 'boleta', '78945612', 'ninguna', 2, 2, 1, 1, 1, '2021-01-18 21:44:08', '2021-01-18 21:44:08'),
(5, '2021-01-18', 254.19, 45.76, 299.95, 0.05, 'boleta', '78945615', 'ninguna', 2, 2, 1, 1, 1, '2021-01-18 21:46:40', '2021-01-18 21:46:40'),
(6, '2021-01-18', 381.36, 68.64, 450.00, 0.10, 'boleta', '78945678', 'ninguna', 2, 2, 1, 1, 1, '2021-01-18 21:53:31', '2021-01-18 21:53:31'),
(7, '2021-01-18', 381.36, 68.64, 450.00, 0.10, 'boleta', '78945679', 'ninguna', 2, 2, 1, 1, 1, '2021-01-18 21:55:19', '2021-01-18 21:55:19'),
(8, '2021-01-18', 381.36, 68.64, 450.00, 0.10, 'boleta', '78945674', 'ninguna', 2, 2, 1, 1, 1, '2021-01-18 22:00:17', '2021-01-18 22:00:17'),
(9, '2021-01-18', 423.73, 76.27, 500.00, 50.00, 'boleta', '78945670', 'ninguna', 2, 2, 0, 1, 1, '2021-01-18 22:48:22', '2021-01-18 22:03:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales_detail`
--

CREATE TABLE `sales_detail` (
  `id_sale_detail` int(11) NOT NULL,
  `quantity` double(18,2) NOT NULL,
  `price` double(18,2) NOT NULL,
  `subtotal` double(18,2) NOT NULL,
  `discount` double(18,2) DEFAULT NULL,
  `id_sale` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `id_lot` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_by` tinyint(4) NOT NULL,
  `updated_by` tinyint(4) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `sales_detail`
--

INSERT INTO `sales_detail` (`id_sale_detail`, `quantity`, `price`, `subtotal`, `discount`, `id_sale`, `id_product`, `id_lot`, `status`, `created_by`, `updated_by`, `updated_at`, `created_at`) VALUES
(1, 10.00, 10.00, 100.00, NULL, 2, 3, 6, 1, 1, 1, '2021-01-17 19:26:05', '2021-01-17 19:26:05'),
(2, 10.00, 60.00, 600.00, 0.00, 3, 3, 6, 1, 1, 1, '2021-01-18 21:41:42', '2021-01-18 21:41:42'),
(3, 5.00, 60.00, 295.00, 5.00, 4, 3, 6, 1, 1, 1, '2021-01-18 21:44:08', '2021-01-18 21:44:08'),
(4, 5.00, 60.00, 295.00, 5.00, 5, 3, 6, 1, 1, 1, '2021-01-18 21:46:41', '2021-01-18 21:46:41'),
(5, 5.00, 100.00, 490.00, 10.00, 6, 4, 7, 1, 1, 1, '2021-01-18 21:53:31', '2021-01-18 21:53:31'),
(6, 5.00, 100.00, -4500.00, 10.00, 7, 4, 7, 1, 1, 1, '2021-01-18 21:55:19', '2021-01-18 21:55:19'),
(7, 5.00, 100.00, 450.00, 10.00, 8, 4, 7, 1, 1, 1, '2021-01-18 22:00:17', '2021-01-18 22:00:17'),
(8, 10.00, 100.00, 500.00, 50.00, 9, 4, 7, 0, 1, 1, '2021-01-18 22:48:22', '2021-01-18 22:03:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `storages`
--

CREATE TABLE `storages` (
  `id_storage` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `address` varchar(250) NOT NULL,
  `responsable` varchar(250) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_by` tinyint(4) NOT NULL,
  `updated_by` tinyint(4) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `storages`
--

INSERT INTO `storages` (`id_storage`, `name`, `address`, `responsable`, `status`, `created_by`, `updated_by`, `updated_at`, `created_at`) VALUES
(1, 'Bagua', 'xxxx 1', 'juan pérez', 1, 1, 1, '2021-01-11 04:28:34', '2021-01-11 04:27:23'),
(2, 'Bagua Grande', 'xxxx 2', 'benito calixto', 1, 1, 1, '2021-01-11 04:28:54', '2021-01-11 04:27:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_role` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  `created_by` tinyint(4) DEFAULT NULL,
  `updated_by` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `id_role`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'henry', 'henryecv210297@gmail.com', NULL, '$2y$10$U6h6eDBJqETpTFu/i/Cqx.EhSuLMAHHRVMmmoBW56neQ0I81RIsy6', NULL, 1, 1, 0, 0, '2021-01-03 09:12:54', '2021-01-03 09:12:54'),
(3, 'edgar', 'evaldiviezo@gmail.com', NULL, '12345', NULL, 2, 1, 1, 1, '2021-01-11 22:05:21', '2021-01-11 22:07:50'),
(4, 'kevin', 'kevin210297@gmail.com', NULL, '$2y$10$FUh.oEf8900i7X6fWceVGOz8j6KxhyQBGbue2qEu8Cae.77Yhk6Yy', NULL, 2, 1, NULL, NULL, '2021-01-12 08:21:55', '2021-01-12 08:21:55'),
(6, 'kebo', 'kevin@gmail.com', NULL, '$2y$10$ns/hJBw.dc8BgDP4Jl10cO6VJd9gtnIJGE5SCcvk1pjzaBIpXuJQq', NULL, 2, 1, 1, 1, '2021-01-12 08:27:22', '2021-01-12 08:27:22');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_category`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id_client`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `lots`
--
ALTER TABLE `lots`
  ADD PRIMARY KEY (`id_lot`),
  ADD KEY `id_product` (`id_product`),
  ADD KEY `id_storage` (`id_storage`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `moves_product`
--
ALTER TABLE `moves_product`
  ADD PRIMARY KEY (`id_move_product`),
  ADD KEY `id_product` (`id_product`),
  ADD KEY `id_lot` (`id_lot`),
  ADD KEY `id_reference` (`id_reference`);

--
-- Indices de la tabla `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_access_tokens_user_id_index` (`user_id`);

--
-- Indices de la tabla `oauth_auth_codes`
--
ALTER TABLE `oauth_auth_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_auth_codes_user_id_index` (`user_id`);

--
-- Indices de la tabla `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_clients_user_id_index` (`user_id`);

--
-- Indices de la tabla `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id_product`),
  ADD KEY `id_category` (`id_category`) USING BTREE;

--
-- Indices de la tabla `providers`
--
ALTER TABLE `providers`
  ADD PRIMARY KEY (`id_provider`);

--
-- Indices de la tabla `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id_purchase`),
  ADD KEY `id_provider` (`id_provider`),
  ADD KEY `id_storage` (`id_storage`);

--
-- Indices de la tabla `purchases_detail`
--
ALTER TABLE `purchases_detail`
  ADD PRIMARY KEY (`id_purchase_detail`),
  ADD KEY `id_product` (`id_product`),
  ADD KEY `id_lot` (`id_lot`),
  ADD KEY `id_purchase` (`id_purchase`) USING BTREE;

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`);

--
-- Indices de la tabla `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id_sale`),
  ADD KEY `id_storage` (`id_storage`),
  ADD KEY `id_client` (`id_client`);

--
-- Indices de la tabla `sales_detail`
--
ALTER TABLE `sales_detail`
  ADD PRIMARY KEY (`id_sale_detail`),
  ADD KEY `id_sale` (`id_sale`),
  ADD KEY `id_product` (`id_product`),
  ADD KEY `id_lot` (`id_lot`);

--
-- Indices de la tabla `storages`
--
ALTER TABLE `storages`
  ADD PRIMARY KEY (`id_storage`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `id_role` (`id_role`) USING BTREE;

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id_category` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `clients`
--
ALTER TABLE `clients`
  MODIFY `id_client` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lots`
--
ALTER TABLE `lots`
  MODIFY `id_lot` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `moves_product`
--
ALTER TABLE `moves_product`
  MODIFY `id_move_product` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `oauth_clients`
--
ALTER TABLE `oauth_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id_product` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `providers`
--
ALTER TABLE `providers`
  MODIFY `id_provider` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id_purchase` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `purchases_detail`
--
ALTER TABLE `purchases_detail`
  MODIFY `id_purchase_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `sales`
--
ALTER TABLE `sales`
  MODIFY `id_sale` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `sales_detail`
--
ALTER TABLE `sales_detail`
  MODIFY `id_sale_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `storages`
--
ALTER TABLE `storages`
  MODIFY `id_storage` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
