-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 09-04-2026 a las 19:44:08
-- Versión del servidor: 8.3.0
-- Versión de PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `levantamientosreactor`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades`
--

DROP TABLE IF EXISTS `actividades`;
CREATE TABLE IF NOT EXISTS `actividades` (
  `Id_Actividad` int NOT NULL AUTO_INCREMENT,
  `Tipo` enum('usuario','cotizacion','producto','levantamiento','cliente') NOT NULL,
  `Accion` varchar(50) NOT NULL,
  `Descripcion` varchar(255) NOT NULL,
  `Referencia_Id` int DEFAULT NULL,
  `Id_Usuario` int DEFAULT NULL,
  `Fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id_Actividad`),
  KEY `Id_Usuario` (`Id_Usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `actividades`
--

INSERT INTO `actividades` (`Id_Actividad`, `Tipo`, `Accion`, `Descripcion`, `Referencia_Id`, `Id_Usuario`, `Fecha`) VALUES
(1, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-02-17 17:17:17'),
(2, 'levantamiento', 'tipo_creado', 'Nuevo tipo de levantamiento creado: Camaras', 1, 1, '2026-02-17 17:17:55'),
(3, 'levantamiento', 'campo_creado', 'Nuevo campo creado: Descripción del Trabajo', 1, 1, '2026-02-17 17:18:40'),
(4, 'levantamiento', 'campo_creado', 'Nuevo campo creado: Describa el material extra', 1, 1, '2026-02-17 17:19:05'),
(5, 'producto', 'agregado', 'Producto agregado: Nodo', 1, 1, '2026-02-17 17:24:53'),
(6, 'cliente', 'creado', 'Nuevo cliente registrado: Ruben Garsa / Taller de Mecanica', 1, 1, '2026-02-17 17:31:53'),
(7, 'cliente', 'actualizado', 'Cliente actualizado: Ruben Garsa / Taller de Mecanica', 1, 1, '2026-02-17 17:32:02'),
(8, 'cliente', 'creado', 'Nuevo cliente registrado: Ruben Garsa / Taller de Mecanica', 2, 1, '2026-02-17 17:35:00'),
(9, 'cliente', 'actualizado', 'Cliente actualizado: Ruben Garsa / Taller de Mecanica', 2, 1, '2026-02-17 17:35:19'),
(10, 'cliente', 'actualizado', 'Cliente actualizado: Ruben Garsa / Taller de Mecanica', 2, 1, '2026-02-17 17:35:33'),
(11, 'producto', 'asociado', 'Artículo \"Nodo\" asociado a cliente', 1, 1, '2026-02-17 17:35:52'),
(12, 'producto', 'agregado', 'Producto agregado: camara', 2, 1, '2026-02-17 17:36:21'),
(13, 'levantamiento', 'creado', 'Nuevo levantamiento creado: LEV-00001', 1, 1, '2026-02-17 17:36:44'),
(14, 'levantamiento', 'actualizado', 'Levantamiento LEV-00001 actualizado', 1, 1, '2026-02-17 17:40:32'),
(15, 'levantamiento', 'actualizado', 'Levantamiento LEV-00001 actualizado', 1, 1, '2026-02-17 11:47:05'),
(16, 'usuario', 'logout', 'Cierre de sesión: admin', 1, 1, '2026-02-17 17:54:37'),
(17, 'usuario', 'login', 'Inicio de sesión: Hector Vivero Martinez', 8, 8, '2026-02-17 17:54:58'),
(18, 'usuario', 'logout', 'Cierre de sesión: Hector Vivero Martinez', 8, 8, '2026-02-17 17:55:37'),
(19, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-02-17 17:56:06'),
(20, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-02-17 18:07:30'),
(21, 'usuario', 'login', 'Inicio de sesión: Hector Vivero Martinez', 8, 8, '2026-02-17 18:07:53'),
(22, 'levantamiento', 'campo_creado', 'Nuevo campo creado: Describa el material extra', 1, 1, '2026-02-17 18:12:28'),
(23, 'levantamiento', 'campo_eliminado', 'Campo eliminado: Describa el material extra', 1, 1, '2026-02-17 18:12:59'),
(24, 'levantamiento', 'campo_creado', 'Nuevo campo creado: Describa el material extra', 1, 1, '2026-02-17 18:13:10'),
(25, 'levantamiento', 'campo_eliminado', 'Campo eliminado: Describa el material extra', 1, 1, '2026-02-17 18:13:32'),
(26, 'usuario', 'login', 'Inicio de sesión: Antonio Betancourt', 9, 9, '2026-02-17 18:16:54'),
(27, 'producto', 'agregado', 'Artículo creado: Camara Bala TURBOHD', 3, 9, '2026-02-17 18:22:01'),
(28, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-02-17 18:24:05'),
(29, 'usuario', 'logout', 'Cierre de sesión: admin', 1, 1, '2026-02-17 18:24:41'),
(30, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-02-17 18:24:50'),
(31, 'producto', 'actualizado', 'Modelo definido para artículo \"camara\": DS-2CE16K0T-LTS', 2, 1, '2026-02-17 12:26:47'),
(32, 'producto', 'eliminado', 'Producto eliminado: Camara Bala TURBOHD', 3, 1, '2026-02-17 18:27:08'),
(33, 'producto', 'agregado', 'Producto agregado: [Audio Bidireccional + Dual Light + ColorVu] Bala TURBOHD 3K', 4, 1, '2026-02-17 12:36:31'),
(34, 'producto', 'agregado', 'Producto agregado: Fuente de Poder de 4 Salidas tipo Jack Macho de 12 Vcc / 5 Amper', 5, 1, '2026-02-17 12:38:47'),
(35, 'levantamiento', 'actualizado', 'Levantamiento LEV-00001 actualizado', 1, 1, '2026-02-17 12:40:15'),
(36, 'levantamiento', 'actualizado', 'Levantamiento LEV-00001 actualizado', 1, 1, '2026-02-17 12:40:28'),
(37, 'producto', 'actualizado', 'Producto actualizado: [Audio Bidireccional + Dual Light + ColorVu] Bala TURBOHD 3K (5 Megapixel) / Lente 2.8 mm Angulo de vision 112° / 30 mts IR EXIR + 20 mts Luz Blanca / Micrófono y Bocina Integrado / Exterior IP67 / Metal / dWDR', 4, 1, '2026-02-17 18:59:40'),
(38, 'producto', 'agregado', 'Producto agregado: Fuente de Poder de 4 Salidas tipo Jack Macho de 12 Vcc / 5 Amper', 6, 1, '2026-02-17 19:03:00'),
(39, 'producto', 'eliminado', 'Producto eliminado: Fuente de Poder de 4 Salidas tipo Jack Macho de 12 Vcc / 5 Amper', 5, 1, '2026-02-17 19:03:17'),
(40, 'producto', 'actualizado', 'Producto actualizado: [Audio Bidireccional + Dual Light + ColorVu] Bala TURBOHD 3K (5 Megapixel) / Lente 2.8 mm Angulo de vision 112° / 30 mts IR EXIR + 20 mts Luz Blanca / Micrófono y Bocina Integrado / Exterior IP67 / Metal / dWDR', 4, 1, '2026-02-17 19:05:30'),
(41, 'producto', 'actualizado', 'Producto actualizado: Fuente de Poder de 4 Salidas tipo Jack Macho de 12 Vcc / 5 Ampers', 6, 1, '2026-02-17 19:05:51'),
(42, 'levantamiento', 'actualizado', 'Levantamiento LEV-00001 actualizado', 1, 1, '2026-02-17 13:08:55'),
(43, 'levantamiento', 'cambio_estatus', 'Levantamiento LEV-00001 cambió a Completado', 1, 1, '2026-02-17 13:08:57'),
(44, 'producto', 'agregado', 'Producto agregado: hector', 7, 1, '2026-02-17 19:10:46'),
(45, 'producto', 'eliminado', 'Producto eliminado: hector', 7, 1, '2026-02-17 19:10:51'),
(46, 'producto', 'agregado', 'Producto agregado: hector', 8, 1, '2026-02-17 19:11:18'),
(47, 'producto', 'eliminado', 'Producto eliminado: hector', 8, 1, '2026-02-17 19:11:21'),
(48, 'producto', 'agregado', 'Producto agregado: Nodo', 9, 1, '2026-02-17 13:34:04'),
(49, 'producto', 'eliminado', 'Producto eliminado: camara', 2, 1, '2026-02-17 19:34:27'),
(50, 'producto', 'eliminado', 'Producto eliminado: Fuente de Poder de 4 Salidas tipo Jack Macho de 12 Vcc / 5 Ampers', 6, 1, '2026-02-17 19:34:32'),
(51, 'producto', 'agregado', 'Producto agregado: Fuente de Poder de 4 Salidas tipo Jack Macho de 12 Vcc / 5 Amper', 10, 1, '2026-02-17 13:35:06'),
(52, 'producto', 'eliminado', 'Producto eliminado: Nodo', 9, 1, '2026-02-17 19:36:54'),
(53, 'producto', 'agregado', 'Producto agregado: Nodo', 11, 1, '2026-02-17 13:37:13'),
(54, 'producto', 'eliminado', 'Producto eliminado: Nodo', 1, 1, '2026-02-17 19:37:22'),
(55, 'producto', 'agregado', 'Producto agregado: Nodo', 12, 1, '2026-02-17 13:38:13'),
(56, 'usuario', 'logout', 'Cierre de sesión: admin', 1, 1, '2026-02-17 19:44:22'),
(57, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-02-17 19:45:29'),
(58, 'usuario', 'logout', 'Cierre de sesión: admin', 1, 1, '2026-02-17 19:46:50'),
(59, 'usuario', 'login', 'Inicio de sesión: Hector Vivero Martinez', 8, 8, '2026-02-17 19:46:57'),
(60, 'usuario', 'logout', 'Cierre de sesión: Hector Vivero Martinez', 8, 8, '2026-02-17 20:14:15'),
(61, 'usuario', 'login', 'Inicio de sesión: Antonio Betancourt', 9, 9, '2026-02-17 20:14:31'),
(62, 'cliente', 'creado', 'Nuevo cliente registrado: Ruben Garsa / Taller de Mecanica', 3, 9, '2026-02-17 20:17:11'),
(63, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-02-17 20:18:13'),
(64, 'usuario', 'logout', 'Cierre de sesión: admin', 1, 1, '2026-02-17 20:18:18'),
(65, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-02-17 20:18:26'),
(66, 'producto', 'eliminado', 'Producto eliminado: Fuente de Poder de 4 Salidas tipo Jack Macho de 12 Vcc / 5 Amper', 10, 1, '2026-02-17 20:18:33'),
(67, 'producto', 'agregado', 'Artículo creado: Fuente de Poder de 4 Salidas tipo Jack Macho de 12 Vcc / 5 Amper', 13, 9, '2026-02-17 20:32:00'),
(68, 'usuario', 'permisos_otorgados', 'Permisos especiales otorgados para: Hector Vivero Martinez', 8, 1, '2026-02-17 20:36:46'),
(69, 'usuario', 'permisos_revocados', 'Permisos especiales revocados para: Hector Vivero Martinez', 8, 1, '2026-02-17 20:36:50'),
(70, 'usuario', 'logout', 'Cierre de sesión: Antonio Betancourt', 9, 9, '2026-02-17 20:37:02'),
(71, 'usuario', 'login', 'Inicio de sesión: Hector Vivero Martinez', 8, 8, '2026-02-17 20:37:08'),
(72, 'usuario', 'permisos_otorgados', 'Permisos especiales otorgados para: Hector Vivero Martinez', 8, 1, '2026-02-17 20:37:43'),
(73, 'usuario', 'login', 'Inicio de sesión: Antonio Betancourt', 9, 9, '2026-02-17 20:38:39'),
(74, 'producto', 'agregado', 'Artículo creado: Disco Duro WD Purple Surveillance / 4TB / SATA 6Gb/s / 3.5\" / Tecnología AllFrame / 180TB año Workload / Soporte 64 Cámaras HD / 16 Bays / Componentes Anti-Corrosión / 3 Años Garantía', 14, 9, '2026-02-17 20:42:52'),
(75, 'producto', 'agregado', 'Artículo creado: DVR 16 Canales TurboHD + 16 Canales IP / 8 Megapíxel (4K) / Audio por Coaxitron / ACUSENSE Lite / 2 Bahías de Disco Duro / H.265+ / Salida en Video en 4K', 15, 9, '2026-02-17 20:45:40'),
(76, 'producto', 'agregado', 'Artículo creado: [Audio Bidireccional + Sirena & Estrobo + Dual Light + ColorVu] Bala TURBOHD 4K (8 Megapixel) / Lente 2.8 mm / 30 mts IR EXIR + 20 mts Luz Blanca / Micrófono y Bocina Integrado / Exterior IP67 / Metal / dWDR', 16, 9, '2026-02-17 20:46:48'),
(77, 'producto', 'agregado', 'Artículo creado: Kit de transceptores activos con conector para alimentación (12V/24Vcc/AC) TurboHD para aplicaciones de video por UTP Cat5e/6 en HD. Distancia de hasta 150 m en 4K', 17, 9, '2026-02-17 20:48:03'),
(78, 'producto', 'agregado', 'Artículo creado: Bobina de cable de 305 m, Cat5e, color negro, sin blindar, para aplicaciones de videovigilancia, redes de datos. Uso en intemperie', 18, 9, '2026-02-17 20:49:47'),
(79, 'producto', 'agregado', 'Artículo creado: Servicios Profesionales', 19, 9, '2026-02-17 20:53:22'),
(80, 'levantamiento', 'creado', 'Nuevo levantamiento creado: LEV-00002', 2, 9, '2026-02-17 20:59:06'),
(81, 'levantamiento', 'actualizado', 'Levantamiento LEV-00002 actualizado', 2, 9, '2026-02-17 21:03:21'),
(82, 'usuario', 'actualizado', 'Usuario actualizado: Antonio Betancourt', 9, 1, '2026-02-17 21:05:33'),
(83, 'usuario', 'logout', 'Cierre de sesión: Antonio Betancourt', 9, 9, '2026-02-17 21:06:00'),
(84, 'usuario', 'login', 'Inicio de sesión: Antonio Betancourt', 9, 9, '2026-02-17 21:06:07'),
(85, 'levantamiento', 'actualizado', 'Levantamiento LEV-00002 actualizado', 2, 1, '2026-02-17 15:07:46'),
(86, 'levantamiento', 'cambio_estatus', 'Levantamiento LEV-00002 cambió a Completado', 2, 1, '2026-02-17 15:07:55'),
(87, 'producto', 'actualizado', 'Producto actualizado: Servicios Profesionales', 19, 9, '2026-02-17 21:13:23'),
(88, 'producto', 'agregado', 'Producto agregado: Servicios Profesionales', 20, 9, '2026-02-17 21:14:41'),
(89, 'producto', 'eliminado', 'Producto eliminado: Servicios Profesionales', 20, 9, '2026-02-17 21:15:07'),
(90, 'usuario', 'registro', 'Nuevo usuario registrado: Mario Ladrillero', 10, 9, '2026-02-17 21:35:10'),
(91, 'usuario', 'logout', 'Cierre de sesión: Hector Vivero Martinez', 8, 8, '2026-02-17 22:06:20'),
(92, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-02-17 22:06:32'),
(93, 'levantamiento', 'actualizado', 'Levantamiento LEV-00002 actualizado', 2, 1, '2026-02-17 16:14:24'),
(94, 'levantamiento', 'actualizado', 'Levantamiento LEV-00002 actualizado', 2, 1, '2026-02-17 16:14:40'),
(95, 'levantamiento', 'cambio_estatus', 'Levantamiento LEV-00002 cambió a Completado', 2, 1, '2026-02-17 16:14:42'),
(96, 'levantamiento', 'cambio_estatus', 'Levantamiento LEV-00002 cambió a En Proceso', 2, 1, '2026-02-17 16:16:15'),
(97, 'levantamiento', 'cambio_estatus', 'Levantamiento LEV-00002 cambió a Completado', 2, 1, '2026-02-17 16:16:26'),
(98, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-02-18 14:59:21'),
(99, 'producto', 'agregado', 'Producto agregado: Nodo', 21, 1, '2026-02-18 15:03:45'),
(100, 'producto', 'eliminado', 'Producto eliminado: Nodo', 21, 1, '2026-02-18 15:04:00'),
(101, 'levantamiento', 'cambio_estatus', 'Levantamiento LEV-00002 cambió a Completado', 2, 1, '2026-02-18 09:10:33'),
(102, 'cliente', 'actualizado', 'Cliente actualizado: Ruben Garsa / Taller de Mecanica', 3, 1, '2026-02-18 15:32:37'),
(103, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-02-18 15:42:16'),
(104, 'usuario', 'logout', 'Cierre de sesión: admin', 1, 1, '2026-02-18 15:43:51'),
(105, 'usuario', 'login', 'Inicio de sesión: Hector Vivero Martinez', 8, 8, '2026-02-18 15:44:08'),
(106, 'usuario', 'logout', 'Cierre de sesión: admin', 1, 1, '2026-02-18 16:49:43'),
(107, 'usuario', 'login', 'Inicio de sesión: Hector Vivero Martinez', 8, 8, '2026-02-18 16:49:51'),
(108, 'usuario', 'logout', 'Cierre de sesión: Hector Vivero Martinez', 8, 8, '2026-02-18 17:22:33'),
(109, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-02-18 17:22:40'),
(110, 'usuario', 'desactivado', 'Usuario desactivado: Hector Vivero Martinez', 8, 1, '2026-02-18 17:25:36'),
(111, 'usuario', 'logout', 'Cierre de sesión: admin', 1, 1, '2026-02-18 17:25:43'),
(112, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-02-18 17:26:38'),
(113, 'usuario', 'reactivado', 'Usuario reactivado: Hector Vivero Martinez', 8, 1, '2026-02-18 17:26:49'),
(114, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-02-18 19:51:06'),
(115, 'usuario', 'logout', 'Cierre de sesión: admin', 1, 1, '2026-02-18 19:57:42'),
(116, 'usuario', 'login', 'Inicio de sesión: Hector Vivero Martinez', 8, 8, '2026-02-18 19:57:52'),
(117, 'usuario', 'logout', 'Cierre de sesión: Hector Vivero Martinez', 8, 8, '2026-02-18 20:02:37'),
(118, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-02-18 20:02:54'),
(119, 'usuario', 'logout', 'Cierre de sesión: admin', 1, 1, '2026-02-18 21:20:58'),
(120, 'usuario', 'login', 'Inicio de sesión: Hector Vivero Martinez', 8, 8, '2026-02-18 21:22:09'),
(121, 'usuario', 'logout', 'Cierre de sesión: Hector Vivero Martinez', 8, 8, '2026-02-18 21:26:03'),
(122, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-02-18 21:26:18'),
(123, 'usuario', 'login', 'Inicio de sesión: Hector Vivero Martinez', 8, 8, '2026-02-25 21:50:11'),
(124, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-03-03 19:43:11'),
(125, 'producto', 'agregado', 'Producto agregado: prueba', 22, 1, '2026-03-03 14:17:59'),
(126, 'producto', 'eliminado', 'Producto eliminado: prueba', 22, 1, '2026-03-03 20:18:31'),
(127, 'usuario', 'logout', 'Cierre de sesión: admin', 1, 1, '2026-03-03 20:20:51'),
(128, 'usuario', 'login', 'Inicio de sesión: Hector Vivero Martinez', 8, 8, '2026-03-03 20:20:59'),
(129, 'usuario', 'logout', 'Cierre de sesión: Hector Vivero Martinez', 8, 8, '2026-03-03 20:21:21'),
(130, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-03-03 20:21:31'),
(131, 'usuario', 'actualizado', 'Usuario actualizado: Antonio Betancourt', 9, 1, '2026-03-03 20:21:44'),
(132, 'usuario', 'logout', 'Cierre de sesión: admin', 1, 1, '2026-03-03 20:21:48'),
(133, 'usuario', 'login', 'Inicio de sesión: Antonio Betancourt', 9, 9, '2026-03-03 20:21:56'),
(134, 'usuario', 'logout', 'Cierre de sesión: Antonio Betancourt', 9, 9, '2026-03-03 20:23:14'),
(135, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-03-03 20:25:52'),
(136, 'levantamiento', 'actualizado', 'Levantamiento LEV-00002 actualizado', 2, 1, '2026-03-03 14:26:40'),
(137, 'levantamiento', 'cambio_estatus', 'Levantamiento LEV-00002 cambió a Completado', 2, 1, '2026-03-03 14:26:53'),
(138, 'usuario', 'logout', 'Cierre de sesión: admin', 1, 1, '2026-03-03 21:01:09'),
(139, 'usuario', 'login', 'Inicio de sesión: Hector Vivero Martinez', 8, 8, '2026-03-03 21:01:20'),
(140, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-03-09 22:53:48'),
(141, 'usuario', 'login', 'Inicio de sesión: Hector Vivero Martinez', 8, 8, '2026-03-10 17:54:31'),
(142, 'usuario', 'logout', 'Cierre de sesión: Hector Vivero Martinez', 8, 8, '2026-03-10 18:04:33'),
(143, 'usuario', 'login', 'Inicio de sesión: Hector Vivero Martinez', 8, 8, '2026-03-10 18:55:45'),
(144, 'usuario', 'logout', 'Cierre de sesión: Hector Vivero Martinez', 8, 8, '2026-03-10 18:55:52'),
(145, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-03-10 18:55:59'),
(146, 'producto', 'asociado', 'Artículo \"Nodo\" asociado a cliente', 11, 1, '2026-03-10 12:57:51'),
(147, 'usuario', 'logout', 'Cierre de sesión: admin', 1, 1, '2026-03-10 19:12:27'),
(148, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-03-10 19:27:34'),
(149, 'usuario', 'logout', 'Cierre de sesión: admin', 1, 1, '2026-03-10 19:28:18'),
(150, 'usuario', 'login', 'Inicio de sesión: Hector Vivero Martinez', 8, 8, '2026-03-10 19:28:28'),
(151, 'usuario', 'logout', 'Cierre de sesión: Hector Vivero Martinez', 8, 8, '2026-03-10 19:28:58'),
(152, 'usuario', 'login', 'Inicio de sesión: Hector Vivero Martinez', 8, 8, '2026-04-09 18:23:18'),
(153, 'usuario', 'logout', 'Cierre de sesión: Hector Vivero Martinez', 8, 8, '2026-04-09 18:23:49'),
(154, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-04-09 18:23:57'),
(155, 'usuario', 'logout', 'Cierre de sesión: admin', 1, 1, '2026-04-09 19:11:27'),
(156, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-04-09 19:12:34'),
(157, 'usuario', 'logout', 'Cierre de sesión: admin', 1, 1, '2026-04-09 19:27:25'),
(158, 'usuario', 'login', 'Inicio de sesión: Hector Vivero Martinez', 8, 8, '2026-04-09 19:27:29'),
(159, 'usuario', 'logout', 'Cierre de sesión: Hector Vivero Martinez', 8, 8, '2026-04-09 19:27:39'),
(160, 'usuario', 'login', 'Inicio de sesión: Antonio Betancourt', 9, 9, '2026-04-09 19:28:05'),
(161, 'usuario', 'logout', 'Cierre de sesión: Antonio Betancourt', 9, 9, '2026-04-09 19:31:33'),
(162, 'usuario', 'login', 'Inicio de sesión: admin', 1, 1, '2026-04-09 19:31:41'),
(163, 'usuario', 'permisos_revocados', 'Permisos especiales revocados para: Antonio Betancourt', 9, 1, '2026-04-09 19:31:52'),
(164, 'usuario', 'logout', 'Cierre de sesión: admin', 1, 1, '2026-04-09 19:31:55'),
(165, 'usuario', 'login', 'Inicio de sesión: Antonio Betancourt', 9, 9, '2026-04-09 19:32:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividad_reciente`
--

DROP TABLE IF EXISTS `actividad_reciente`;
CREATE TABLE IF NOT EXISTS `actividad_reciente` (
  `id_actividad` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `tipo_actividad` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `detalles` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_actividad` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `leida` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_actividad`),
  KEY `idx_usuario` (`id_usuario`),
  KEY `idx_fecha` (`fecha_actividad`),
  KEY `idx_tipo` (`tipo_actividad`),
  KEY `idx_leida` (`leida`),
  KEY `idx_usuario_fecha` (`id_usuario`,`fecha_actividad` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de actividades recientes de los usuarios';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articulos`
--

DROP TABLE IF EXISTS `articulos`;
CREATE TABLE IF NOT EXISTS `articulos` (
  `Id_Articulos` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Descripcion` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Id_Marca` int NOT NULL,
  `Id_Modelo` int DEFAULT NULL,
  `modelo_por_definir` tinyint(1) DEFAULT '0',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `veces_solicitado` int DEFAULT '0',
  PRIMARY KEY (`Id_Articulos`),
  KEY `Id_Marca` (`Id_Marca`),
  KEY `Id_Modelo` (`Id_Modelo`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `articulos`
--

INSERT INTO `articulos` (`Id_Articulos`, `Nombre`, `Descripcion`, `Id_Marca`, `Id_Modelo`, `modelo_por_definir`, `fecha_creacion`, `veces_solicitado`) VALUES
(4, '[Audio Bidireccional + Dual Light + ColorVu] Bala TURBOHD 3K (5 Megapixel) / Lente 2.8 mm Angulo de vision 112° / 30 mts IR EXIR + 20 mts Luz Blanca / Micrófono y Bocina Integrado / Exterior IP67 / Metal / dWDR', 'Resolución 3K (2960x1665) con 5 Megapíxeles\r\nAudio bidireccional integrado para comunicación a 5-8 metros\r\nDual Light: 30 m IR EXIR + 20 m Luz Blanca\r\nÁngulo de visión 112° con lente 2.8 mm.\r\nProtección IP67 para uso exterior resistente a agua y polvo\r\nInfrarrojo inteligente con alcance 30 metros en noche completa', 2, 2, 0, '2026-02-17 12:36:31', 2),
(11, 'Nodo', NULL, 1, 5, 0, '2026-02-17 13:37:13', 0),
(12, 'Nodo', NULL, 1, 1, 0, '2026-02-17 13:38:13', 0),
(13, 'Fuente de Poder de 4 Salidas tipo Jack Macho de 12 Vcc / 5 Amper', 'Voltaje de entrada universal 100-240 V~ 50/60 Hz\r\nEficiencia energética del 85% con bajo consumo en espera\r\nFiltro de onda integrado para reducir interferencias\r\nProtección contra sobretensiones y descargas eléctricas\r\n4 salidas jack de 12 Vcc, 5 A, 60 W\r\nDiseño compacto de 117 x 62 x 34.2 mm', 3, 4, 0, '2026-02-17 20:32:00', 1),
(14, 'Disco Duro WD Purple Surveillance / 4TB / SATA 6Gb/s / 3.5\" / Tecnología AllFrame / 180TB año Workload / Soporte 64 Cámaras HD / 16 Bays / Componentes Anti-Corrosión / 3 Años Garantía', 'Capacidad hasta 8TB para almacenamiento de video\r\nTecnología AllFrame™ para reducir pérdida de frames\r\nWorkload anual de 180TB para operación 24/7\r\nSoporta hasta 64 cámaras HD simultáneas\r\nMTBF de 1,000,000 horas de confiabilidad\r\nInterfaz SATA 6Gb/s con cache de 256MB', 4, 6, 0, '2026-02-17 20:42:52', 1),
(15, 'DVR 16 Canales TurboHD + 16 Canales IP / 8 Megapíxel (4K) / Audio por Coaxitron / ACUSENSE Lite / 2 Bahías de Disco Duro / H.265+ / Salida en Video en 4K', 'Soporte HD-TVI/CVI/AHD/Analógico para máxima compatibilidad\r\nSoftware cliente gratuito iVMS-4200 y Hik-Connect P2P\r\n32 canales totales (16 TurboHD + 16 IP)\r\nCompresión H.265+/H.265/H.264 con ahorro de almacenamiento\r\nResolución 8 Megapíxel (4K) en cámaras compatibles\r\nAudio por Coaxitron en todos los canales TurboHD', 5, 7, 0, '2026-02-17 20:45:40', 1),
(16, '[Audio Bidireccional + Sirena & Estrobo + Dual Light + ColorVu] Bala TURBOHD 4K (8 Megapixel) / Lente 2.8 mm / 30 mts IR EXIR + 20 mts Luz Blanca / Micrófono y Bocina Integrado / Exterior IP67 / Metal / dWDR', 'Resolución 4K (8 MP) para detalles precisos\r\nLente 2.8 mm con ángulo 113°\r\nAudio bidireccional integrado hasta 5 metros\r\nDual Light: IR + 20 mts luz blanca\r\nSirena y estrobo LED rojo-azul integrado\r\nProtección IP67 para uso exterior', 2, 8, 0, '2026-02-17 20:46:48', 1),
(17, 'Kit de transceptores activos con conector para alimentación (12V/24Vcc/AC) TurboHD para aplicaciones de video por UTP Cat5e/6 en HD. Distancia de hasta 150 m en 4K', 'Transmisión 4K hasta 150 metros en tiempo real\r\nCompatible con HD-TVI, HD-CVI, AHD, CVBS\r\nSoporta voltaje de 12/24V CC/CA eficiente\r\nSupresor de sobretensión para protección del equipo\r\nCable mini-coaxial flexible para montaje rápido\r\nInmunidad al ruido y diafonía de 60 dB', 2, 9, 0, '2026-02-17 20:48:03', 1),
(18, 'Bobina de cable de 305 m, Cat5e, color negro, sin blindar, para aplicaciones de videovigilancia, redes de datos. Uso en intemperie', 'Videovigilancia IP megapíxel para máxima resolución\r\nConductor 100% cobre con protección UV\r\nAplicaciones 10BASE-T, 100BASE-T y Gigabit\r\nCompatible con estándares UL CMX, ANSI/TIA\r\nInstalaciones exteriores con cable UTP 24 AWG\r\nDiseño exterior CMX para uso en intemperie', 6, 10, 0, '2026-02-17 20:49:47', 1),
(19, 'Servicios Profesionales', 'Tendido de cableado, colocacion de camara, enfoque, configuracion de DVR, puesta en marcha con aplicacion en celular.', 1, 11, 0, '2026-02-17 20:53:22', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `connection` varchar(255) NOT NULL,
  `k` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`connection`,`k`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `Id_Cliente` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `Correo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Telefono` varchar(25) DEFAULT NULL,
  `Estatus` enum('Activo','Inactivo') NOT NULL,
  `Id_Articulos` int DEFAULT NULL,
  `Id_Direccion` int NOT NULL,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id_Cliente`),
  KEY `Id_Articulos` (`Id_Articulos`),
  KEY `Id_Direccion` (`Id_Direccion`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`Id_Cliente`, `Nombre`, `Correo`, `Telefono`, `Estatus`, `Id_Articulos`, `Id_Direccion`, `fecha_registro`) VALUES
(3, 'Ruben Garsa / Taller de Mecanica', NULL, NULL, 'Activo', 4, 3, '2026-02-17 20:17:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_articulos`
--

DROP TABLE IF EXISTS `cliente_articulos`;
CREATE TABLE IF NOT EXISTS `cliente_articulos` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Id_Cliente` int NOT NULL,
  `Id_Articulo` int NOT NULL,
  `Es_Principal` tinyint(1) DEFAULT '0',
  `Fecha_Agregado` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id`),
  KEY `Id_Cliente` (`Id_Cliente`),
  KEY `Id_Articulo` (`Id_Articulo`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `cliente_articulos`
--

INSERT INTO `cliente_articulos` (`Id`, `Id_Cliente`, `Id_Articulo`, `Es_Principal`, `Fecha_Agregado`) VALUES
(18, 3, 4, 1, '2026-02-18 15:32:37'),
(19, 3, 16, 0, '2026-02-18 15:32:37'),
(20, 3, 18, 0, '2026-02-18 15:32:37'),
(21, 3, 14, 0, '2026-02-18 15:32:37'),
(22, 3, 15, 0, '2026-02-18 15:32:37'),
(23, 3, 13, 0, '2026-02-18 15:32:37'),
(24, 3, 17, 0, '2026-02-18 15:32:37'),
(25, 3, 19, 0, '2026-02-18 15:32:37'),
(27, 3, 11, 0, '2026-03-10 12:57:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizaciones`
--

DROP TABLE IF EXISTS `cotizaciones`;
CREATE TABLE IF NOT EXISTS `cotizaciones` (
  `Id_Cotizacion` int NOT NULL AUTO_INCREMENT,
  `Folio` varchar(50) NOT NULL,
  `Id_Cliente` int NOT NULL,
  `Id_Levantamiento` int DEFAULT NULL,
  `Monto_Total` decimal(10,2) DEFAULT '0.00',
  `Estatus` enum('Borrador','Enviada','Autorizada','Rechazada','Cancelada') DEFAULT 'Borrador',
  `Fecha_Creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `Id_Usuario_Creador` int NOT NULL,
  PRIMARY KEY (`Id_Cotizacion`),
  UNIQUE KEY `Folio` (`Folio`),
  KEY `Id_Cliente` (`Id_Cliente`),
  KEY `Id_Levantamiento` (`Id_Levantamiento`),
  KEY `Id_Usuario_Creador` (`Id_Usuario_Creador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direccion`
--

DROP TABLE IF EXISTS `direccion`;
CREATE TABLE IF NOT EXISTS `direccion` (
  `Id_Direccion` int NOT NULL AUTO_INCREMENT,
  `Pais` varchar(100) NOT NULL,
  `Estado` varchar(100) NOT NULL,
  `Ciudad` varchar(100) DEFAULT NULL,
  `Colonia` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Codigo_Postal` int DEFAULT NULL,
  `calle` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `No_In` int DEFAULT NULL,
  `No_Ex` int DEFAULT NULL,
  `Municipio` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`Id_Direccion`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `direccion`
--

INSERT INTO `direccion` (`Id_Direccion`, `Pais`, `Estado`, `Ciudad`, `Colonia`, `Codigo_Postal`, `calle`, `No_In`, `No_Ex`, `Municipio`) VALUES
(2, 'México', 'MÉXICO', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'México', 'MÉXICO', NULL, NULL, 51400, NULL, NULL, NULL, 'Toluca');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `levantamientos`
--

DROP TABLE IF EXISTS `levantamientos`;
CREATE TABLE IF NOT EXISTS `levantamientos` (
  `Id_Levantamiento` int NOT NULL AUTO_INCREMENT,
  `Id_Tipo_Levantamiento` int DEFAULT NULL,
  `Cantidad_Articulos` int DEFAULT NULL,
  `Descripcion_Trabajo` text,
  `Herramientas_Necesarias` varchar(100) DEFAULT NULL,
  `Descripcion_Ubicacion_Nodos` varchar(255) DEFAULT NULL,
  `Cantidad_Nodos` int DEFAULT NULL,
  `Uso_Nodos` varchar(100) DEFAULT NULL,
  `Tipo_Canalizacion` varchar(255) DEFAULT NULL,
  `Img` varchar(255) DEFAULT NULL,
  `id_usuarios` int NOT NULL,
  `Id_Cliente` int NOT NULL,
  `Id_Articulos` int DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `estatus` enum('Pendiente','En Proceso','Completado','Cancelado') DEFAULT 'Pendiente',
  `modelo_por_definir` tinyint(1) DEFAULT '0',
  `Nodo` varchar(100) DEFAULT NULL,
  `Tipo_Nodo` varchar(100) DEFAULT NULL,
  `Precio_Unitario` decimal(10,2) DEFAULT '0.00',
  `Id_Marca` int DEFAULT NULL,
  `Id_Modelo` int DEFAULT NULL,
  `Id_ServicioProfecional` int DEFAULT NULL,
  PRIMARY KEY (`Id_Levantamiento`),
  KEY `id_usuarios` (`id_usuarios`),
  KEY `Id_Cliente` (`Id_Cliente`),
  KEY `Id_Articulos` (`Id_Articulos`),
  KEY `fk_lev_tipo` (`Id_Tipo_Levantamiento`),
  KEY `fk_lev_marca` (`Id_Marca`),
  KEY `fk_lev_modelo` (`Id_Modelo`),
  KEY `fk_lev_sp` (`Id_ServicioProfecional`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `levantamientos`
--

INSERT INTO `levantamientos` (`Id_Levantamiento`, `Id_Tipo_Levantamiento`, `Cantidad_Articulos`, `Descripcion_Trabajo`, `Herramientas_Necesarias`, `Descripcion_Ubicacion_Nodos`, `Cantidad_Nodos`, `Uso_Nodos`, `Tipo_Canalizacion`, `Img`, `id_usuarios`, `Id_Cliente`, `Id_Articulos`, `fecha_creacion`, `estatus`, `modelo_por_definir`, `Nodo`, `Tipo_Nodo`, `Precio_Unitario`, `Id_Marca`, `Id_Modelo`, `Id_ServicioProfecional`) VALUES
(2, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9, 3, NULL, '2026-02-17 20:59:06', 'Completado', 0, NULL, NULL, 0.00, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `levantamiento_articulos`
--

DROP TABLE IF EXISTS `levantamiento_articulos`;
CREATE TABLE IF NOT EXISTS `levantamiento_articulos` (
  `Id_Levantamiento_Articulo` int NOT NULL AUTO_INCREMENT,
  `Id_Levantamiento` int NOT NULL,
  `Id_Articulo` int NOT NULL,
  `Cantidad` int NOT NULL DEFAULT '1',
  `Precio_Unitario` decimal(10,2) NOT NULL DEFAULT '0.00',
  `Subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `Notas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `modelo_por_definir` tinyint(1) DEFAULT '0',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id_Levantamiento_Articulo`),
  KEY `idx_levantamiento` (`Id_Levantamiento`),
  KEY `idx_articulo` (`Id_Articulo`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `levantamiento_articulos`
--

INSERT INTO `levantamiento_articulos` (`Id_Levantamiento_Articulo`, `Id_Levantamiento`, `Id_Articulo`, `Cantidad`, `Precio_Unitario`, `Subtotal`, `Notas`, `modelo_por_definir`, `fecha_registro`) VALUES
(52, 2, 13, 1, 0.00, 0.00, NULL, 0, '2026-03-03 20:26:40'),
(53, 2, 4, 9, 0.00, 0.00, 'Perimetro del area, 2 en puerta principal, 2 en la calle lado izquierdo, 2 en el taller de mecanica, 2 en el taller de pintura, 1 en el area de lavado', 0, '2026-03-03 20:26:40'),
(54, 2, 14, 1, 0.00, 0.00, NULL, 0, '2026-03-03 20:26:40'),
(55, 2, 15, 3, 0.00, 0.00, NULL, 0, '2026-03-03 20:26:40'),
(56, 2, 16, 1, 0.00, 0.00, 'en el medio del taller', 0, '2026-03-03 20:26:40'),
(57, 2, 17, 10, 0.00, 0.00, NULL, 0, '2026-03-03 20:26:40'),
(58, 2, 18, 1, 0.00, 0.00, NULL, 0, '2026-03-03 20:26:40'),
(59, 2, 19, 10, 500.00, 5000.00, NULL, 0, '2026-03-03 20:26:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `levantamiento_valores_dinamicos`
--

DROP TABLE IF EXISTS `levantamiento_valores_dinamicos`;
CREATE TABLE IF NOT EXISTS `levantamiento_valores_dinamicos` (
  `Id_Valor` int NOT NULL AUTO_INCREMENT,
  `Id_Levantamiento` int NOT NULL,
  `Id_Campo` int NOT NULL,
  `Valor` text NOT NULL,
  `Fecha_Registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id_Valor`),
  KEY `idx_levantamiento` (`Id_Levantamiento`),
  KEY `idx_campo` (`Id_Campo`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `levantamiento_valores_dinamicos`
--

INSERT INTO `levantamiento_valores_dinamicos` (`Id_Valor`, `Id_Levantamiento`, `Id_Campo`, `Valor`, `Fecha_Registro`) VALUES
(27, 2, 1, 'Colocacion de CCTV', '2026-03-03 14:26:40'),
(28, 2, 2, 'Herramienta basica para la instalacion', '2026-03-03 14:26:40'),
(29, 2, 5, '6 metros de alto x 30 de largo x 20 de ancho', '2026-03-03 14:26:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

DROP TABLE IF EXISTS `marcas`;
CREATE TABLE IF NOT EXISTS `marcas` (
  `Id_Marca` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `Descripcion` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`Id_Marca`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `marcas`
--

INSERT INTO `marcas` (`Id_Marca`, `Nombre`, `Descripcion`) VALUES
(1, 'Ads', NULL),
(2, 'hikvision', ''),
(3, 'EPCOM POWERLINE', ''),
(4, 'Western Digital (WD)', NULL),
(5, 'HiLook by HIKVISION', NULL),
(6, 'LINKEDPRO BY EPCOM', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modelo`
--

DROP TABLE IF EXISTS `modelo`;
CREATE TABLE IF NOT EXISTS `modelo` (
  `Id_Modelo` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `Descripcion` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`Id_Modelo`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `modelo`
--

INSERT INTO `modelo` (`Id_Modelo`, `Nombre`, `Descripcion`) VALUES
(1, 'De Planta', NULL),
(2, 'DS-2CE16K0T-LTS', NULL),
(4, 'PLK12DC4CH', ''),
(5, 'de Oficina', ''),
(6, 'WD44PURZ', NULL),
(7, 'DVR-216U-M2(C)', NULL),
(8, 'DS-2CE16U0T-LXTS', NULL),
(9, 'TT-101-PV-TURBO', NULL),
(10, 'PRO-CAT5-EXT-LITES', NULL),
(11, 'Residencial', NULL),
(13, 'prueba', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
CREATE TABLE IF NOT EXISTS `notificaciones` (
  `Id_Notificacion` int NOT NULL AUTO_INCREMENT,
  `Id_Usuario` int DEFAULT NULL,
  `Titulo` varchar(100) NOT NULL,
  `Mensaje` varchar(255) NOT NULL,
  `Tipo` enum('info','success','warning','error') DEFAULT 'info',
  `Leida` tinyint(1) DEFAULT '0',
  `Fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id_Notificacion`),
  KEY `Id_Usuario` (`Id_Usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`Id_Notificacion`, `Id_Usuario`, `Titulo`, `Mensaje`, `Tipo`, `Leida`, `Fecha`) VALUES
(1, 1, 'Nuevo Tipo de Levantamiento', 'Se ha creado el tipo: Camaras', 'success', 0, '2026-02-17 17:17:55'),
(2, 1, 'Nuevo Cliente', 'Se ha registrado el cliente Ruben Garsa / Taller de Mecanica', 'info', 0, '2026-02-17 17:31:53'),
(3, 1, 'Nuevo Cliente', 'Se ha registrado el cliente Ruben Garsa / Taller de Mecanica', 'info', 0, '2026-02-17 17:35:00'),
(4, 1, 'Nuevo Cliente', 'Se ha registrado el cliente Ruben Garsa / Taller de Mecanica', 'info', 0, '2026-02-17 20:17:11'),
(5, 1, 'Nuevo Levantamiento', 'Se ha creado el levantamiento LEV-00002', 'info', 0, '2026-02-17 20:59:06'),
(6, 1, 'Nuevo Usuario', 'Se ha registrado el usuario Mario Ladrillero', 'info', 0, '2026-02-17 21:35:10'),
(7, 9, 'Nuevo Usuario', 'Se ha registrado el usuario Mario Ladrillero', 'info', 0, '2026-02-17 21:35:10'),
(8, 1, 'Usuario Desactivado', 'Se ha desactivado el usuario Hector Vivero Martinez', 'warning', 0, '2026-02-18 17:25:36'),
(9, 9, 'Usuario Desactivado', 'Se ha desactivado el usuario Hector Vivero Martinez', 'warning', 0, '2026-02-18 17:25:36'),
(10, 1, 'Usuario Reactivado', 'Se ha reactivado el usuario Hector Vivero Martinez', 'success', 0, '2026-02-18 17:26:49'),
(11, 9, 'Usuario Reactivado', 'Se ha reactivado el usuario Hector Vivero Martinez', 'success', 0, '2026-02-18 17:26:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('Z5GT6h6JFEQ7l4baH4g3B16ScxY8wXyLtKIp3mro', 9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiak1BZ3ZqNDNqVkY1ZHFMVG1qanZBUFNkTVA2ZWFlVkNYM1YyMk5XeiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC91c3VhcmlvL2Rhc2hib2FyZCI7czo1OiJyb3V0ZSI7czoxNzoidXN1YXJpby5kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo5O30=', 1775763125);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_levantamiento`
--

DROP TABLE IF EXISTS `tipos_levantamiento`;
CREATE TABLE IF NOT EXISTS `tipos_levantamiento` (
  `Id_Tipo_Levantamiento` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `Descripcion` varchar(255) DEFAULT NULL,
  `Icono` varchar(50) DEFAULT 'fa-clipboard-list',
  `Activo` tinyint(1) DEFAULT '1',
  `Fecha_Creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id_Tipo_Levantamiento`),
  KEY `idx_tipo_lev_activo` (`Activo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tipos_levantamiento`
--

INSERT INTO `tipos_levantamiento` (`Id_Tipo_Levantamiento`, `Nombre`, `Descripcion`, `Icono`, `Activo`, `Fecha_Creacion`) VALUES
(1, 'Camaras', NULL, 'fa-video', 1, '2026-02-17 17:17:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_levantamiento_campos`
--

DROP TABLE IF EXISTS `tipo_levantamiento_campos`;
CREATE TABLE IF NOT EXISTS `tipo_levantamiento_campos` (
  `Id_Campo` int NOT NULL AUTO_INCREMENT,
  `Id_Tipo_Levantamiento` int NOT NULL,
  `Nombre_Campo` varchar(100) NOT NULL,
  `Etiqueta` varchar(100) NOT NULL,
  `Tipo_Input` enum('text','number','textarea','select','checkbox','date','email','tel') NOT NULL DEFAULT 'text',
  `Es_Requerido` tinyint(1) DEFAULT '0',
  `Placeholder` varchar(255) DEFAULT NULL,
  `Valor_Default` text,
  `Opciones_Select` text COMMENT 'JSON con opciones si es select',
  `Orden` int DEFAULT '0',
  `Activo` tinyint(1) DEFAULT '1',
  `Fecha_Creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id_Campo`),
  KEY `idx_tipo_lev` (`Id_Tipo_Levantamiento`),
  KEY `idx_campo_orden` (`Orden`),
  KEY `idx_campo_activo` (`Activo`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tipo_levantamiento_campos`
--

INSERT INTO `tipo_levantamiento_campos` (`Id_Campo`, `Id_Tipo_Levantamiento`, `Nombre_Campo`, `Etiqueta`, `Tipo_Input`, `Es_Requerido`, `Placeholder`, `Valor_Default`, `Opciones_Select`, `Orden`, `Activo`, `Fecha_Creacion`) VALUES
(1, 1, 'Descripcion', 'Descripción del Trabajo', 'textarea', 0, 'descripción del trabajo', NULL, NULL, 1, 1, '2026-02-17 17:18:40'),
(2, 1, 'herramienta_extra', 'Describa la Herramienta extra', 'textarea', 0, 'Describa la Herramienta Extra', NULL, NULL, 2, 1, '2026-02-17 17:19:05'),
(5, 1, 'medidas_perimetro', 'Medidas del Perimetro', 'text', 0, 'Altura - Ancho - Largo', NULL, NULL, 3, 1, '2026-02-17 21:02:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_servicioprofecional`
--

DROP TABLE IF EXISTS `tipo_servicioprofecional`;
CREATE TABLE IF NOT EXISTS `tipo_servicioprofecional` (
  `Id_ServicioProfecional` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  PRIMARY KEY (`Id_ServicioProfecional`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuarios` int NOT NULL AUTO_INCREMENT,
  `Nombres` varchar(100) NOT NULL,
  `ApellidosPat` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ApellidoMat` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Telefono` varchar(25) DEFAULT NULL,
  `Correo` varchar(255) NOT NULL,
  `Contrasena` varchar(100) NOT NULL,
  `Rol` enum('Admin','Usuario') NOT NULL,
  `Estatus` enum('Activo','Inactivo') NOT NULL,
  `Permisos` enum('si','no') NOT NULL,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  `ultima_actividad` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuarios`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuarios`, `Nombres`, `ApellidosPat`, `ApellidoMat`, `Telefono`, `Correo`, `Contrasena`, `Rol`, `Estatus`, `Permisos`, `fecha_registro`, `ultima_actividad`) VALUES
(1, 'admin', '', '', '2147483647', 'soporte@reactorads.com', '$2y$10$gq0oFTdxiL125K2.GtdRje12F.gaCtgUwRxxmARvPzenbFAbJajIC', 'Admin', 'Activo', 'si', '2026-01-15 12:12:27', '2026-04-09 19:31:41'),
(8, 'Hector', 'Vivero', 'Martinez', '2147483647', 'hectorvivero031@gmail.com', '$2y$12$Fq5DPpGlAFA.wFOHrQa3kuoTu1QUKwr.J.GTunHmnsOFlBd/COmf.', 'Usuario', 'Activo', 'si', '2026-01-15 13:20:56', '2026-04-09 19:27:29'),
(9, 'Antonio', 'Betancourt', NULL, NULL, 'antonio@reactorads.com', '$2y$12$yqHqqyel9lKyApP/t1h1E.8kEx5IuagZEd.Wv0VhnVM3yAxZeQCyS', 'Usuario', 'Activo', 'no', '2026-02-05 19:21:03', '2026-04-09 19:32:05'),
(10, 'Mario', 'Ladrillero', NULL, NULL, 'mario@reactorads.com', '$2y$12$RoJCHB21K/kz4t6AEVsSAec.JCOmgzi5QvA0PlfUEj3F7BiR5vwX6', 'Usuario', 'Activo', 'no', '2026-02-17 21:35:10', '2026-02-17 21:35:10');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD CONSTRAINT `actividades_ibfk_1` FOREIGN KEY (`Id_Usuario`) REFERENCES `usuarios` (`id_usuarios`) ON DELETE SET NULL;

--
-- Filtros para la tabla `actividad_reciente`
--
ALTER TABLE `actividad_reciente`
  ADD CONSTRAINT `actividad_reciente_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuarios`) ON DELETE CASCADE;

--
-- Filtros para la tabla `articulos`
--
ALTER TABLE `articulos`
  ADD CONSTRAINT `articulos_ibfk_1` FOREIGN KEY (`Id_Marca`) REFERENCES `marcas` (`Id_Marca`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `articulos_ibfk_2` FOREIGN KEY (`Id_Modelo`) REFERENCES `modelo` (`Id_Modelo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`Id_Articulos`) REFERENCES `articulos` (`Id_Articulos`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `clientes_ibfk_2` FOREIGN KEY (`Id_Direccion`) REFERENCES `direccion` (`Id_Direccion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cliente_articulos`
--
ALTER TABLE `cliente_articulos`
  ADD CONSTRAINT `cliente_articulos_ibfk_1` FOREIGN KEY (`Id_Cliente`) REFERENCES `clientes` (`Id_Cliente`) ON DELETE CASCADE,
  ADD CONSTRAINT `cliente_articulos_ibfk_2` FOREIGN KEY (`Id_Articulo`) REFERENCES `articulos` (`Id_Articulos`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  ADD CONSTRAINT `cotizaciones_ibfk_1` FOREIGN KEY (`Id_Cliente`) REFERENCES `clientes` (`Id_Cliente`) ON DELETE CASCADE,
  ADD CONSTRAINT `cotizaciones_ibfk_2` FOREIGN KEY (`Id_Levantamiento`) REFERENCES `levantamientos` (`Id_Levantamiento`) ON DELETE SET NULL,
  ADD CONSTRAINT `cotizaciones_ibfk_3` FOREIGN KEY (`Id_Usuario_Creador`) REFERENCES `usuarios` (`id_usuarios`);

--
-- Filtros para la tabla `levantamientos`
--
ALTER TABLE `levantamientos`
  ADD CONSTRAINT `fk_lev_marca` FOREIGN KEY (`Id_Marca`) REFERENCES `marcas` (`Id_Marca`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_lev_modelo` FOREIGN KEY (`Id_Modelo`) REFERENCES `modelo` (`Id_Modelo`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_lev_sp` FOREIGN KEY (`Id_ServicioProfecional`) REFERENCES `tipo_servicioprofecional` (`Id_ServicioProfecional`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_lev_tipo` FOREIGN KEY (`Id_Tipo_Levantamiento`) REFERENCES `tipos_levantamiento` (`Id_Tipo_Levantamiento`) ON DELETE SET NULL,
  ADD CONSTRAINT `levantamientos_ibfk_1` FOREIGN KEY (`id_usuarios`) REFERENCES `usuarios` (`id_usuarios`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `levantamientos_ibfk_2` FOREIGN KEY (`Id_Cliente`) REFERENCES `clientes` (`Id_Cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `levantamientos_ibfk_3` FOREIGN KEY (`Id_Articulos`) REFERENCES `articulos` (`Id_Articulos`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `levantamiento_articulos`
--
ALTER TABLE `levantamiento_articulos`
  ADD CONSTRAINT `fk_lev_art_articulo` FOREIGN KEY (`Id_Articulo`) REFERENCES `articulos` (`Id_Articulos`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lev_art_levantamiento` FOREIGN KEY (`Id_Levantamiento`) REFERENCES `levantamientos` (`Id_Levantamiento`) ON DELETE CASCADE;

--
-- Filtros para la tabla `levantamiento_valores_dinamicos`
--
ALTER TABLE `levantamiento_valores_dinamicos`
  ADD CONSTRAINT `fk_valor_campo` FOREIGN KEY (`Id_Campo`) REFERENCES `tipo_levantamiento_campos` (`Id_Campo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_valor_levantamiento` FOREIGN KEY (`Id_Levantamiento`) REFERENCES `levantamientos` (`Id_Levantamiento`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`Id_Usuario`) REFERENCES `usuarios` (`id_usuarios`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tipo_levantamiento_campos`
--
ALTER TABLE `tipo_levantamiento_campos`
  ADD CONSTRAINT `fk_campo_tipo_lev` FOREIGN KEY (`Id_Tipo_Levantamiento`) REFERENCES `tipos_levantamiento` (`Id_Tipo_Levantamiento`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
