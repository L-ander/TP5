-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-07-2026 a las 20:19:17
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
-- Base de datos: `prueba`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `categoria` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id`, `categoria`) VALUES
(1, 'Bebidas'),
(2, 'Lacteos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `sexo` varchar(15) NOT NULL,
  `id_parroquia` int(11) NOT NULL,
  `direccion` varchar(250) NOT NULL COMMENT 'Dirección específica (Calle, casa, local)',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_modificacion` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id`, `nombre`, `apellido`, `sexo`, `id_parroquia`, `direccion`, `fecha_creacion`, `fecha_modificacion`) VALUES
(1, 'yeye', 'yayo', 'masculino', 1, 'gfgfgfgf', '2026-06-09 20:20:15', NULL),
(3, 'Robin', 'nose', 'Femenino', 2, 'dsd', '2026-06-10 23:58:57', '2026-07-22 14:15:59'),
(6, 'TestClienteJS', 'Ajax', 'Masculino', 3, 'Direccion Prueba AJAX', '2026-07-07 21:00:32', '2026-07-22 14:16:11'),
(8, 'CLICliente', 'Prueba', 'Masculino', 4, 'Dir CLI', '2026-07-07 21:49:23', '2026-07-22 14:16:58'),
(9, 'Andres ', 'Hernandez', 'Masculino', 7, 'Tocuyo al final', '2026-07-15 15:21:09', '2026-07-22 14:17:39'),
(10, 'Asly', 'Sanchez', 'Femenino', 1, 'Al lado de babilon', '2026-07-01 15:25:10', '2026-07-22 14:18:10'),
(11, 'Maria', 'Campo', 'Femenino', 1, 'Carrera 21\r\n', '2026-07-06 15:27:37', NULL),
(12, 'Aitor', 'Tilla', 'Masculino', 5, 'Al lado del cementerio viejo', '2026-07-06 15:30:01', '2026-07-22 14:18:31'),
(13, 'Eva', 'Fina Segura', 'Femenino', 8, 'Atras de farmatodo de la 53\r\n', '2026-06-30 15:31:53', '2026-07-22 14:18:40'),
(14, 'Dolores', 'Fuertes de Barriga', 'Femenino', 4, 'catedral, tres cuadras a la izquierda.\r\n', '2026-06-25 15:36:08', '2026-07-22 14:18:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(18,6) NOT NULL DEFAULT 0.000000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_pedido`
--

INSERT INTO `detalle_pedido` (`id`, `id_pedido`, `id_producto`, `cantidad`, `precio_unitario`) VALUES
(2, 7, 1, 1, 0.000000),
(3, 8, 4, 10, 1.600000),
(4, 9, 4, 1, 1.600000),
(5, 10, 1, 1, 0.000000),
(6, 11, 4, 2, 1.600000),
(7, 11, 2, 1, 9.000000),
(8, 11, 12, 1, 1.000000),
(9, 12, 42, 1, 8.000000),
(10, 12, 12, 2, 0.000000);

--
-- Disparadores `detalle_pedido`
--
DELIMITER $$
CREATE TRIGGER `tg_calcular_total_pedido` AFTER INSERT ON `detalle_pedido` FOR EACH ROW BEGIN
    -- Actualiza el total del pedido sumando (cantidad * precio_unitario) del nuevo item
    UPDATE `pedido` 
    SET `total` = `total` + (NEW.cantidad * NEW.precio_unitario) 
    WHERE `id` = NEW.id_pedido;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado`
--

CREATE TABLE `estado` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado`
--

INSERT INTO `estado` (`id`, `nombre`) VALUES
(1, 'Lara'),
(2, 'Zulia'),
(3, 'Distrito capital'),
(4, 'Portuguesa'),
(5, 'Falcón'),
(6, 'Mérida'),
(7, 'Yaracuy '),
(8, 'Bolivar '),
(9, 'Carabobo'),
(10, 'Miranda');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estatus`
--

CREATE TABLE `estatus` (
  `id` int(1) NOT NULL,
  `nombre` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estatus`
--

INSERT INTO `estatus` (`id`, `nombre`) VALUES
(1, 'Pendiente'),
(2, 'Entregado/Listo'),
(3, 'Cancelado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `linea_producto`
--

CREATE TABLE `linea_producto` (
  `id` int(11) NOT NULL,
  `id_subcategoria` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `linea_producto`
--

INSERT INTO `linea_producto` (`id`, `id_subcategoria`, `nombre`) VALUES
(1, 1, 'Te con Durazno4s5'),
(2, 1, 'Te de Jamaica'),
(3, 2, 'Naranja'),
(4, 2, 'Nectar de Manza'),
(5, 2, 'Nectar de Pera'),
(6, 2, 'Nectar de Duraz'),
(7, 5, 'Suero de Leche'),
(8, 5, 'Suero Cremoso'),
(9, 8, 'Leche'),
(10, 8, 'Chicha'),
(11, 6, 'Crema de Leche'),
(12, 6, 'Queso Crema'),
(13, 6, 'Natilla'),
(17, 4, 'Cheddar'),
(18, 4, 'prueba');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulo`
--

CREATE TABLE `modulo` (
  `id` int(5) NOT NULL,
  `Modulo` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modulo`
--

INSERT INTO `modulo` (`id`, `Modulo`) VALUES
(1, 'Home'),
(2, 'vendedores'),
(3, 'clientes'),
(4, 'productos'),
(5, 'pedidos'),
(6, 'inventario'),
(7, 'reportes');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `municipio`
--

CREATE TABLE `municipio` (
  `id` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `municipio`
--

INSERT INTO `municipio` (`id`, `id_estado`, `nombre`) VALUES
(1, 1, 'Iribarren'),
(2, 2, 'Catatumbo'),
(3, 3, 'Caracas'),
(4, 4, 'Guanare'),
(5, 5, 'Coro'),
(6, 6, 'Mérida'),
(7, 7, 'San Felipe'),
(8, 8, 'Ciudad Bolivar'),
(9, 9, 'Valencia'),
(10, 10, 'Los Teques');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nombre_producto`
--

CREATE TABLE `nombre_producto` (
  `id` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parroquia`
--

CREATE TABLE `parroquia` (
  `id` int(11) NOT NULL,
  `id_municipio` int(11) DEFAULT NULL,
  `nombre` varchar(26) NOT NULL,
  `id_estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `parroquia`
--

INSERT INTO `parroquia` (`id`, `id_municipio`, `nombre`, `id_estado`) VALUES
(1, 3, 'Calendaria', 3),
(2, 2, 'Chiquinquirá', 2),
(3, 1, 'El Cují', 1),
(4, 5, ' Río Seco', 5),
(5, 8, 'Simón Bolívar', 8),
(6, 4, 'Córdoba', 4),
(7, 9, 'El Socorro', 9),
(8, 7, 'San Javier', 7),
(9, 10, 'El Café', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `id` int(11) NOT NULL,
  `id_vendedor` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `total` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `id_estatus` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedido`
--

INSERT INTO `pedido` (`id`, `id_vendedor`, `id_cliente`, `fecha`, `fecha_entrega`, `total`, `id_estatus`) VALUES
(7, 131, 3, '2026-06-19', NULL, 0.000000, 1),
(8, 137, 3, '2026-06-19', '2026-06-19', 16.000000, 2),
(9, 137, 3, '2026-06-02', NULL, 1.600000, 2),
(10, 131, 3, '2026-06-20', NULL, 0.000000, 1),
(11, 137, 9, '2026-07-09', NULL, 13.200000, 2),
(12, 137, 12, '2026-07-09', NULL, 8.000000, 1);

--
-- Disparadores `pedido`
--
DELIMITER $$
CREATE TRIGGER `tg_registrar_fecha_entrega` BEFORE UPDATE ON `pedido` FOR EACH ROW BEGIN
    -- Si el estatus cambia a 2 (Entregado/Listo), ponemos la fecha actual
    IF NEW.id_estatus = 2 AND OLD.id_estatus != 2 THEN
        SET NEW.fecha_entrega = CURDATE();
        
    -- Si por error lo cambian de Entregado a Pendiente/Cancelado, limpiamos la fecha
    ELSEIF NEW.id_estatus != 2 THEN
        SET NEW.fecha_entrega = NULL;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(5) NOT NULL,
  `cod_rol` int(5) NOT NULL,
  `cod_modulo` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `cod_rol`, `cod_modulo`) VALUES
(1001, 1, 1),
(1002, 1, 2),
(1003, 1, 3),
(1004, 1, 4),
(1005, 1, 5),
(1006, 1, 6),
(2001, 2, 1),
(2003, 2, 3),
(2004, 2, 4),
(2005, 2, 5),
(2006, 2, 6),
(3001, 3, 1),
(3004, 3, 4),
(3006, 3, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal`
--

CREATE TABLE `personal` (
  `id` int(11) NOT NULL,
  `cedula` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `id_tipo_personal` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `personal`
--

INSERT INTO `personal` (`id`, `cedula`, `nombre`, `apellido`, `telefono`, `id_tipo_personal`, `status`) VALUES
(127, 28621145, 'ENGELBERG', 'Osuna', NULL, 1, 1),
(128, 29000000, 'Steve', 'Harrington', NULL, 2, 2),
(129, 30000000, 'Dustin', 'Henderson', NULL, 3, 0),
(131, 32000000, 'Eleven', 'Hopper', NULL, 3, 2),
(132, 33000000, 'Mike', 'Wheeler', NULL, 3, 2),
(134, 34000000, 'Joyce', 'Byers', NULL, 2, 2),
(135, 35000000, 'Jim', 'Hopper', NULL, 1, 0),
(136, 36000000, 'William', 'Byers', NULL, 2, 1),
(137, 21554521, 'Ash', 'Ketchum', '014511', 3, 1),
(194, 12345678, 'Test', 'ting', '04266454569', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentacion`
--

CREATE TABLE `presentacion` (
  `id` int(11) NOT NULL,
  `contenido` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `id_linea` int(11) NOT NULL,
  `presentacion` int(5) NOT NULL DEFAULT 0,
  `id_medida` int(11) NOT NULL DEFAULT 0,
  `precio` decimal(18,6) NOT NULL DEFAULT 0.000000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id`, `nombre`, `id_linea`, `presentacion`, `id_medida`, `precio`) VALUES
(1, 'CHICHA 400CC', 10, 4003, 3, 7.000000),
(2, 'CHICHA 900CC', 10, 900, 2, 9.000000),
(4, 'Crema 250cc', 11, 250, 2, 1.600000),
(12, 'Leche 900', 9, 900, 2, 0.000000),
(34, 'Manzana 400', 4, 400, 2, 0.600000),
(35, 'Manzana', 4, 900, 2, 0.950000),
(36, 'Manzana', 4, 1500, 2, 1.300000),
(37, 'Manzana', 4, 1500, 2, 1.300000),
(38, 'Te de Jamaica 1800cc', 2, 1800, 2, 2.100000),
(39, 'Te de Jamaica 400cc', 2, 400, 2, 0.650000),
(40, 'Crema de Leche Bolsa', 11, 450, 4, 1.600000),
(41, 'Crema de leche Tina ', 11, 450, 4, 1.400000),
(42, 'Producto Test', 18, 1, 3, 8.000000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `ID` int(5) NOT NULL,
  `roles` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`ID`, `roles`) VALUES
(1, 'Admin'),
(2, 'Vendedor'),
(3, 'Almacenista');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subcategoria`
--

CREATE TABLE `subcategoria` (
  `id` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `sub_categoria` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `subcategoria`
--

INSERT INTO `subcategoria` (`id`, `id_categoria`, `sub_categoria`) VALUES
(1, 1, 'Te'),
(2, 1, 'Nectares'),
(3, 2, 'Yogurt'),
(4, 2, 'Queso'),
(5, 2, 'Suero'),
(6, 2, 'Crema'),
(8, 2, 'Bebidas La');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_personal`
--

CREATE TABLE `tipo_personal` (
  `id` int(11) NOT NULL,
  `nombre` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_personal`
--

INSERT INTO `tipo_personal` (`id`, `nombre`) VALUES
(1, 'Administra'),
(2, 'Geremte'),
(3, 'Vendedor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidad_medida`
--

CREATE TABLE `unidad_medida` (
  `id` int(11) NOT NULL,
  `medida` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `unidad_medida`
--

INSERT INTO `unidad_medida` (`id`, `medida`) VALUES
(1, 'Lt'),
(2, 'cc'),
(3, 'kg'),
(4, 'Gr');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `id_personal` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `cod_rol` int(5) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `id_personal`, `username`, `password`, `cod_rol`, `status`) VALUES
(11, 127, 'engelberg', '12345678', 1, 1),
(12, 128, 'steve', '123456', 2, 2),
(13, 129, 'dustin', '123456', 2, 0),
(14, 131, 'eleven', '123456', 2, 2),
(15, 132, 'mike', '123456', 2, 2),
(16, 134, 'joyce', '123456', 2, 2),
(17, 135, 'jim', '123456', 2, 0),
(18, 136, 'william', '123456', 2, 1),
(22, 194, 'test', 'aA123456$', 1, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cliente_parroquia` (`id_parroquia`);

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pedido` (`id_pedido`),
  ADD KEY `fk_detalle_producto` (`id_producto`);

--
-- Indices de la tabla `estado`
--
ALTER TABLE `estado`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estatus`
--
ALTER TABLE `estatus`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `linea_producto`
--
ALTER TABLE `linea_producto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_subcategoria` (`id_subcategoria`);

--
-- Indices de la tabla `modulo`
--
ALTER TABLE `modulo`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `municipio`
--
ALTER TABLE `municipio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_municipio_estado` (`id_estado`);

--
-- Indices de la tabla `nombre_producto`
--
ALTER TABLE `nombre_producto`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `parroquia`
--
ALTER TABLE `parroquia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `PK id_parroquia` (`id_municipio`),
  ADD KEY `PK id_parrroquia` (`id_estado`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vendedor` (`id_vendedor`),
  ADD KEY `fk_cliente` (`id_cliente`),
  ADD KEY `fk_pedido_estatus` (`id_estatus`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rol` (`cod_rol`),
  ADD KEY `fk_modulo` (`cod_modulo`);

--
-- Indices de la tabla `personal`
--
ALTER TABLE `personal`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cedula` (`cedula`),
  ADD KEY `fk_personal_tipo` (`id_tipo_personal`);

--
-- Indices de la tabla `presentacion`
--
ALTER TABLE `presentacion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cont` (`id_linea`),
  ADD KEY `fk_nombre` (`nombre`),
  ADD KEY `fk_medida` (`id_medida`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `subcategoria`
--
ALTER TABLE `subcategoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_categoria` (`id_categoria`);

--
-- Indices de la tabla `tipo_personal`
--
ALTER TABLE `tipo_personal`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `unidad_medida`
--
ALTER TABLE `unidad_medida`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_personal` (`id_personal`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_usuario_rol` (`cod_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `estado`
--
ALTER TABLE `estado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `linea_producto`
--
ALTER TABLE `linea_producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `municipio`
--
ALTER TABLE `municipio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `parroquia`
--
ALTER TABLE `parroquia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `personal`
--
ALTER TABLE `personal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=195;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `subcategoria`
--
ALTER TABLE `subcategoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `tipo_personal`
--
ALTER TABLE `tipo_personal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `unidad_medida`
--
ALTER TABLE `unidad_medida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `fk_cliente_parroquia` FOREIGN KEY (`id_parroquia`) REFERENCES `parroquia` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `linea_producto`
--
ALTER TABLE `linea_producto`
  ADD CONSTRAINT `fk_subcategoria` FOREIGN KEY (`id_subcategoria`) REFERENCES `subcategoria` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `municipio`
--
ALTER TABLE `municipio`
  ADD CONSTRAINT `fk_municipio_estado` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `parroquia`
--
ALTER TABLE `parroquia`
  ADD CONSTRAINT `PK id_parroquia` FOREIGN KEY (`id_municipio`) REFERENCES `municipio` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `PK id_parrroquia` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `fk_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id`),
  ADD CONSTRAINT `fk_pedido_estatus` FOREIGN KEY (`id_estatus`) REFERENCES `estatus` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pedido_personal` FOREIGN KEY (`id_vendedor`) REFERENCES `personal` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD CONSTRAINT `fk_modulo` FOREIGN KEY (`cod_modulo`) REFERENCES `modulo` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rol` FOREIGN KEY (`cod_rol`) REFERENCES `roles` (`ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `personal`
--
ALTER TABLE `personal`
  ADD CONSTRAINT `fk_personal_tipo` FOREIGN KEY (`id_tipo_personal`) REFERENCES `tipo_personal` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `fk_linea` FOREIGN KEY (`id_linea`) REFERENCES `linea_producto` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_medida` FOREIGN KEY (`id_medida`) REFERENCES `unidad_medida` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `subcategoria`
--
ALTER TABLE `subcategoria`
  ADD CONSTRAINT `fk_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_personal` FOREIGN KEY (`id_personal`) REFERENCES `personal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`cod_rol`) REFERENCES `roles` (`ID`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
