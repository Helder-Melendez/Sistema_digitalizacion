-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-05-2026 a las 02:51:59
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
-- Base de datos: `saneamiento_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_calidad`
--

CREATE TABLE `auditoria_calidad` (
  `id_auditoria` int(11) NOT NULL,
  `id_expediente` int(11) NOT NULL,
  `estado_revision` enum('APROBADO','OBSERVADO') DEFAULT 'APROBADO',
  `error_nomenclatura` tinyint(1) DEFAULT 0,
  `error_legibilidad` tinyint(1) DEFAULT 0,
  `error_folios` tinyint(1) DEFAULT 0,
  `observaciones` text DEFAULT NULL,
  `auditor_id` int(11) DEFAULT NULL,
  `fecha_auditoria` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditoria_calidad`
--

INSERT INTO `auditoria_calidad` (`id_auditoria`, `id_expediente`, `estado_revision`, `error_nomenclatura`, `error_legibilidad`, `error_folios`, `observaciones`, `auditor_id`, `fecha_auditoria`) VALUES
(1, 1, 'APROBADO', 0, 0, 0, '', 1, '2026-05-15 20:04:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `expedientes_saneamiento`
--

CREATE TABLE `expedientes_saneamiento` (
  `id_expediente` int(11) NOT NULL,
  `codigo_expediente` varchar(50) NOT NULL,
  `tipo_procedimiento` varchar(100) NOT NULL,
  `nombre_predio_comunidad` varchar(150) NOT NULL,
  `fecha_ingreso_fisico` date NOT NULL,
  `estado_digitalizacion` varchar(50) DEFAULT 'Pendiente',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `dni` varchar(8) DEFAULT NULL,
  `nombres` varchar(100) DEFAULT NULL,
  `apellido_paterno` varchar(100) DEFAULT NULL,
  `apellido_materno` varchar(100) DEFAULT NULL,
  `n_titulo` varchar(50) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `distrito` varchar(100) DEFAULT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `tipo_documento` varchar(100) DEFAULT NULL,
  `ruta_archivo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `expedientes_saneamiento`
--

INSERT INTO `expedientes_saneamiento` (`id_expediente`, `codigo_expediente`, `tipo_procedimiento`, `nombre_predio_comunidad`, `fecha_ingreso_fisico`, `estado_digitalizacion`, `fecha_registro`, `dni`, `nombres`, `apellido_paterno`, `apellido_materno`, `n_titulo`, `provincia`, `distrito`, `sector`, `tipo_documento`, `ruta_archivo`) VALUES
(1, '2026-00001', 'Titulación', 'LA COLMENA', '2026-05-15', 'Pendiente', '2026-05-15 20:00:03', '00000000', 'MATEO', 'SICCHA', 'DOMINGUEZ', '0068572', 'Chachapoyas', 'MARISCAL BENAVIDES', 'LA COLMENA', 'Expediente Digital', '1778875203_EXP1.pdf'),
(2, '2026-00002', 'Titulación', 'EUCALIPTO', '2026-05-15', 'Pendiente', '2026-05-15 20:09:06', '00000001', 'MARIA DOLORES', 'TAFUR', 'LOPEZ', '0068529', 'Chachapoyas', 'MARISCAL BENAVIDES', 'EUCALIPTO', 'Expediente Digital', '1778875746_EXP2.pdf');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitantes`
--

CREATE TABLE `solicitantes` (
  `id_solicitante` int(11) NOT NULL,
  `dni` varchar(8) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellido_paterno` varchar(100) NOT NULL,
  `apellido_materno` varchar(100) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitantes`
--

INSERT INTO `solicitantes` (`id_solicitante`, `dni`, `nombres`, `apellido_paterno`, `apellido_materno`, `fecha_registro`) VALUES
(1, '70128199', 'HELDER ALEXIS', 'MELENDEZ', 'GUADALUPE', '2026-05-15 19:00:45'),
(2, '40082134', 'Lesley ', 'Melendez', 'Villanueva', '2026-05-15 19:12:05'),
(6, '40373025', 'FLORIZA', 'GUADALUPE', 'MELENDEZ', '2026-05-15 19:30:31'),
(7, '00000000', 'MATEO', 'SICCHA', 'DOMINGUEZ', '2026-05-15 19:59:03'),
(8, '00000001', 'MARIA DOLORES', 'TAFUR', 'LOPEZ', '2026-05-15 20:08:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contraseña` varchar(255) DEFAULT NULL,
  `google_secret` varchar(255) DEFAULT NULL,
  `rol` enum('ADMINISTRADOR','AUDITOR','DIGITALIZADOR') DEFAULT 'DIGITALIZADOR',
  `estado` tinyint(4) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombres`, `apellidos`, `usuario`, `contraseña`, `google_secret`, `rol`, `estado`, `fecha_creacion`) VALUES
(5, 'Administrador', 'Principal', 'admin', '$2y$10$sNapPEKEus9y0Zdm3KMDjOSNpF5z48eMjYIcjC8to1xKVRKPsVNC2', 'X7S5LBJUYJNBVWJN', 'ADMINISTRADOR', 1, '2026-05-15 21:37:53'),
(10, 'helder ', 'melendez', 'helder', '$2y$10$9pr/QsqkvKUygixLs2YuO.Wm70Zgj0UpZ8FgHLH5a1cLShnHEDcOC', 'FSGTKZ5QMNFM2O7A', 'DIGITALIZADOR', 1, '2026-05-15 22:08:28'),
(11, 'patricia', 'zuñiga', 'patricia', '$2y$10$7MpxX9qhWZmodcUzk.gytOmTOwgm2xLmAGbqSyWPm2p/p/BWHF5RC', NULL, 'AUDITOR', 1, '2026-05-16 00:37:47');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditoria_calidad`
--
ALTER TABLE `auditoria_calidad`
  ADD PRIMARY KEY (`id_auditoria`),
  ADD KEY `id_expediente` (`id_expediente`);

--
-- Indices de la tabla `expedientes_saneamiento`
--
ALTER TABLE `expedientes_saneamiento`
  ADD PRIMARY KEY (`id_expediente`),
  ADD UNIQUE KEY `codigo_expediente` (`codigo_expediente`);

--
-- Indices de la tabla `solicitantes`
--
ALTER TABLE `solicitantes`
  ADD PRIMARY KEY (`id_solicitante`),
  ADD UNIQUE KEY `dni` (`dni`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditoria_calidad`
--
ALTER TABLE `auditoria_calidad`
  MODIFY `id_auditoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `expedientes_saneamiento`
--
ALTER TABLE `expedientes_saneamiento`
  MODIFY `id_expediente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `solicitantes`
--
ALTER TABLE `solicitantes`
  MODIFY `id_solicitante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `auditoria_calidad`
--
ALTER TABLE `auditoria_calidad`
  ADD CONSTRAINT `auditoria_calidad_ibfk_1` FOREIGN KEY (`id_expediente`) REFERENCES `expedientes_saneamiento` (`id_expediente`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
