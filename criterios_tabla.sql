-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-12-2023 a las 19:28:18
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `criterios`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `criterios_tabla`
--

CREATE TABLE `criterios_tabla` (
  `id` int(11) NOT NULL,
  `numero_criterio` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `prioridad` varchar(100) NOT NULL,
  `sumatoria` int(11) NOT NULL,
  `ponderacion` int(11) NOT NULL,
  `porcentaje` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `criterios_tabla`
--

INSERT INTO `criterios_tabla` (`id`, `numero_criterio`, `nombre`, `prioridad`, `sumatoria`, `ponderacion`, `porcentaje`) VALUES
(27, 85, 'arroz', '53', 138, 69, 50),
(28, 85, 'arroz', '53', 138, 69, 50),
(29, 99, 'agua', '2', 101, 51, 50);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `criterios_tabla`
--
ALTER TABLE `criterios_tabla`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `criterios_tabla`
--
ALTER TABLE `criterios_tabla`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
