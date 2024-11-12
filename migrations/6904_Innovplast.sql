-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 19-09-2024 a las 22:53:58
-- Versión del servidor: 10.1.48-MariaDB-0+deb9u2
-- Versión de PHP: 8.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `6904`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Innovplast__carrito`
--

CREATE TABLE `Innovplast__carrito` (
  `ID_CARRITO` int(11) NOT NULL,
  `activo` tinyint(4) NOT NULL,
  `ID_USUARIO` int(11) NOT NULL,
  `date_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Innovplast__color`
--

CREATE TABLE `Innovplast__color` (
  `ID_COLOR` int(11) NOT NULL,
  `color` varchar(64) NOT NULL,
  `valor_rgb` varchar(128) NOT NULL,
  `date_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Innovplast__empleado`
--

CREATE TABLE `Innovplast__empleado` (
  `ID_EMPLEADO` int(11) NOT NULL,
  `dni` varchar(16) NOT NULL,
  `ID_USUARIO` int(11) NOT NULL,
  `date_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Innovplast__estado`
--

CREATE TABLE `Innovplast__estado` (
  `ID_ESTADO` int(11) NOT NULL,
  `activo` tinyint(4) NOT NULL,
  `validado` tinyint(4) NOT NULL,
  `admin` tinyint(4) NOT NULL,
  `ID_USUARIO` int(11) NOT NULL,
  `date_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Innovplast__informe`
--

CREATE TABLE `Innovplast__informe` (
  `ID_INFORME` int(11) NOT NULL,
  `token` text NOT NULL,
  `ID_USUARIO` int(11) NOT NULL,
  `date_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Innovplast__log`
--

CREATE TABLE `Innovplast__log` (
  `ID_LOG` int(11) NOT NULL,
  `titulo` varchar(128) NOT NULL,
  `descripcion` text NOT NULL,
  `ID_USUARIO` int(11) NOT NULL,
  `date_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Innovplast__metodo_pago`
--

CREATE TABLE `Innovplast__metodo_pago` (
  `ID_METODO_PAGO` int(11) NOT NULL,
  `metodo` varchar(128) NOT NULL,
  `date_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Innovplast__producto`
--

CREATE TABLE `Innovplast__producto` (
  `ID_PRODUCTO` int(11) NOT NULL,
  `token` text NOT NULL,
  `nombre` text NOT NULL,
  `descripcion` text NOT NULL,
  `precio_unitario` double NOT NULL,
  `stock` int(11) NOT NULL,
  `imagen` text NOT NULL,
  `date_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Innovplast__producto_carrito`
--

CREATE TABLE `Innovplast__producto_carrito` (
  `ID_PRODUCTO_CARRITO` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `ID_CARRITO` int(11) NOT NULL,
  `ID_PRODUCTO` int(11) NOT NULL,
  `date_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Innovplast__tapa`
--

CREATE TABLE `Innovplast__tapa` (
  `ID_TAPA` int(11) NOT NULL,
  `token` text NOT NULL,
  `descripcion` text NOT NULL,
  `precio` double NOT NULL,
  `imagen` text NOT NULL,
  `ID_COLOR` int(11) NOT NULL,
  `date_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Innovplast__ticket`
--

CREATE TABLE `Innovplast__ticket` (
  `ID_TICKET` int(11) NOT NULL,
  `token` text NOT NULL,
  `ticket` text NOT NULL,
  `ID_CARRITO` int(11) NOT NULL,
  `ID_METODO_PAGO` int(11) NOT NULL,
  `date_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Innovplast__token`
--

CREATE TABLE `Innovplast__token` (
  `ID_TOKEN` int(11) NOT NULL,
  `token_email` text NOT NULL,
  `ID_USUARIO` int(11) NOT NULL,
  `date_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Innovplast__usuario`
--

CREATE TABLE `Innovplast__usuario` (
  `ID_USUARIO` int(11) NOT NULL,
  `token` text NOT NULL,
  `nombre` text NOT NULL,
  `apellido` text NOT NULL,
  `email` text NOT NULL,
  `password` text NOT NULL,
  `ip` varchar(32) NOT NULL,
  `avatar` text NOT NULL,
  `date_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Innovplast__usuario_informe`
--

CREATE TABLE `Innovplast__usuario_informe` (
  `ID_USUARIO_INFORME` int(11) NOT NULL,
  `ID_USUARIO` int(11) NOT NULL,
  `ID_INFORME` int(11) NOT NULL,
  `date_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `Innovplast__carrito`
--
ALTER TABLE `Innovplast__carrito`
  ADD PRIMARY KEY (`ID_CARRITO`),
  ADD UNIQUE KEY `ID_USUARIO` (`ID_USUARIO`);

--
-- Indices de la tabla `Innovplast__color`
--
ALTER TABLE `Innovplast__color`
  ADD PRIMARY KEY (`ID_COLOR`);

--
-- Indices de la tabla `Innovplast__empleado`
--
ALTER TABLE `Innovplast__empleado`
  ADD PRIMARY KEY (`ID_EMPLEADO`),
  ADD UNIQUE KEY `ID_USUARIO` (`ID_USUARIO`),
  ADD UNIQUE KEY `ID_USUARIO_2` (`ID_USUARIO`);

--
-- Indices de la tabla `Innovplast__estado`
--
ALTER TABLE `Innovplast__estado`
  ADD PRIMARY KEY (`ID_ESTADO`),
  ADD UNIQUE KEY `ID_USUARIO` (`ID_USUARIO`);

--
-- Indices de la tabla `Innovplast__informe`
--
ALTER TABLE `Innovplast__informe`
  ADD PRIMARY KEY (`ID_INFORME`),
  ADD UNIQUE KEY `ID_USUARIO` (`ID_USUARIO`);

--
-- Indices de la tabla `Innovplast__log`
--
ALTER TABLE `Innovplast__log`
  ADD PRIMARY KEY (`ID_LOG`),
  ADD UNIQUE KEY `ID_USUARIO` (`ID_USUARIO`);

--
-- Indices de la tabla `Innovplast__metodo_pago`
--
ALTER TABLE `Innovplast__metodo_pago`
  ADD PRIMARY KEY (`ID_METODO_PAGO`);

--
-- Indices de la tabla `Innovplast__producto`
--
ALTER TABLE `Innovplast__producto`
  ADD PRIMARY KEY (`ID_PRODUCTO`);

--
-- Indices de la tabla `Innovplast__producto_carrito`
--
ALTER TABLE `Innovplast__producto_carrito`
  ADD PRIMARY KEY (`ID_PRODUCTO_CARRITO`),
  ADD KEY `ID_CARRITO` (`ID_CARRITO`,`ID_PRODUCTO`),
  ADD KEY `ID_PRODUCTO` (`ID_PRODUCTO`);

--
-- Indices de la tabla `Innovplast__tapa`
--
ALTER TABLE `Innovplast__tapa`
  ADD PRIMARY KEY (`ID_TAPA`),
  ADD KEY `ID_COLOR` (`ID_COLOR`);

--
-- Indices de la tabla `Innovplast__ticket`
--
ALTER TABLE `Innovplast__ticket`
  ADD KEY `ID_CARRITO` (`ID_CARRITO`,`ID_METODO_PAGO`),
  ADD KEY `ID_METODO_PAGO` (`ID_METODO_PAGO`);

--
-- Indices de la tabla `Innovplast__token`
--
ALTER TABLE `Innovplast__token`
  ADD PRIMARY KEY (`ID_TOKEN`),
  ADD KEY `ID_USUARIO` (`ID_USUARIO`),
  ADD KEY `ID_USUARIO_2` (`ID_USUARIO`);

--
-- Indices de la tabla `Innovplast__usuario`
--
ALTER TABLE `Innovplast__usuario`
  ADD PRIMARY KEY (`ID_USUARIO`);

--
-- Indices de la tabla `Innovplast__usuario_informe`
--
ALTER TABLE `Innovplast__usuario_informe`
  ADD PRIMARY KEY (`ID_USUARIO_INFORME`),
  ADD KEY `ID_USUARIO` (`ID_USUARIO`,`ID_INFORME`),
  ADD KEY `ID_INFORME` (`ID_INFORME`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `Innovplast__carrito`
--
ALTER TABLE `Innovplast__carrito`
  MODIFY `ID_CARRITO` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Innovplast__color`
--
ALTER TABLE `Innovplast__color`
  MODIFY `ID_COLOR` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Innovplast__empleado`
--
ALTER TABLE `Innovplast__empleado`
  MODIFY `ID_EMPLEADO` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Innovplast__estado`
--
ALTER TABLE `Innovplast__estado`
  MODIFY `ID_ESTADO` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Innovplast__informe`
--
ALTER TABLE `Innovplast__informe`
  MODIFY `ID_INFORME` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Innovplast__log`
--
ALTER TABLE `Innovplast__log`
  MODIFY `ID_LOG` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Innovplast__metodo_pago`
--
ALTER TABLE `Innovplast__metodo_pago`
  MODIFY `ID_METODO_PAGO` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Innovplast__producto`
--
ALTER TABLE `Innovplast__producto`
  MODIFY `ID_PRODUCTO` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Innovplast__producto_carrito`
--
ALTER TABLE `Innovplast__producto_carrito`
  MODIFY `ID_PRODUCTO_CARRITO` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Innovplast__tapa`
--
ALTER TABLE `Innovplast__tapa`
  MODIFY `ID_TAPA` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Innovplast__token`
--
ALTER TABLE `Innovplast__token`
  MODIFY `ID_TOKEN` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Innovplast__usuario`
--
ALTER TABLE `Innovplast__usuario`
  MODIFY `ID_USUARIO` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Innovplast__usuario_informe`
--
ALTER TABLE `Innovplast__usuario_informe`
  MODIFY `ID_USUARIO_INFORME` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `Innovplast__carrito`
--
ALTER TABLE `Innovplast__carrito`
  ADD CONSTRAINT `Innovplast__carrito_ibfk_1` FOREIGN KEY (`ID_USUARIO`) REFERENCES `Innovplast__usuario` (`ID_USUARIO`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `Innovplast__empleado`
--
ALTER TABLE `Innovplast__empleado`
  ADD CONSTRAINT `Innovplast__empleado_ibfk_1` FOREIGN KEY (`ID_USUARIO`) REFERENCES `Innovplast__usuario` (`ID_USUARIO`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `Innovplast__estado`
--
ALTER TABLE `Innovplast__estado`
  ADD CONSTRAINT `Innovplast__estado_ibfk_1` FOREIGN KEY (`ID_USUARIO`) REFERENCES `Innovplast__usuario` (`ID_USUARIO`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `Innovplast__log`
--
ALTER TABLE `Innovplast__log`
  ADD CONSTRAINT `Innovplast__log_ibfk_1` FOREIGN KEY (`ID_USUARIO`) REFERENCES `Innovplast__usuario` (`ID_USUARIO`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `Innovplast__producto_carrito`
--
ALTER TABLE `Innovplast__producto_carrito`
  ADD CONSTRAINT `Innovplast__producto_carrito_ibfk_1` FOREIGN KEY (`ID_CARRITO`) REFERENCES `App-ACME__carrito` (`ID_CARRITO`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Innovplast__producto_carrito_ibfk_2` FOREIGN KEY (`ID_PRODUCTO`) REFERENCES `Innovplast__producto` (`ID_PRODUCTO`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `Innovplast__tapa`
--
ALTER TABLE `Innovplast__tapa`
  ADD CONSTRAINT `Innovplast__tapa_ibfk_1` FOREIGN KEY (`ID_COLOR`) REFERENCES `Innovplast__color` (`ID_COLOR`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `Innovplast__ticket`
--
ALTER TABLE `Innovplast__ticket`
  ADD CONSTRAINT `Innovplast__ticket_ibfk_1` FOREIGN KEY (`ID_CARRITO`) REFERENCES `Innovplast__carrito` (`ID_CARRITO`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Innovplast__ticket_ibfk_2` FOREIGN KEY (`ID_METODO_PAGO`) REFERENCES `Innovplast__metodo_pago` (`ID_METODO_PAGO`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `Innovplast__token`
--
ALTER TABLE `Innovplast__token`
  ADD CONSTRAINT `Innovplast__token_ibfk_1` FOREIGN KEY (`ID_USUARIO`) REFERENCES `Innovplast__usuario` (`ID_USUARIO`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `Innovplast__usuario_informe`
--
ALTER TABLE `Innovplast__usuario_informe`
  ADD CONSTRAINT `Innovplast__usuario_informe_ibfk_1` FOREIGN KEY (`ID_USUARIO`) REFERENCES `Innovplast__usuario` (`ID_USUARIO`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Innovplast__usuario_informe_ibfk_2` FOREIGN KEY (`ID_INFORME`) REFERENCES `Innovplast__informe` (`ID_INFORME`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
