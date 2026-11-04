-- Estructura Limpia sin Triggers para InfinityFree
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `categoria` (`id` int(11) NOT NULL AUTO_INCREMENT, `categoria` varchar(10) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `categoria` (`id`, `categoria`) VALUES (1, 'Bebidas'), (2, 'Lacteos');

CREATE TABLE `cliente` (`id` int(11) NOT NULL AUTO_INCREMENT, `nombre` varchar(50) NOT NULL, `apellido` varchar(50) NOT NULL, `sexo` varchar(15) NOT NULL, `id_parroquia` int(11) NOT NULL, `direccion` varchar(250) NOT NULL, `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(), `fecha_modificacion` datetime DEFAULT NULL ON UPDATE current_timestamp(), PRIMARY KEY (`id`), KEY `fk_cliente_parroquia` (`id_parroquia`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `cliente` (`id`, `nombre`, `apellido`, `sexo`, `id_parroquia`, `direccion`, `fecha_creacion`, `fecha_modificacion`) VALUES (1, 'Eleander', 'Teran', 'Masculino', 1, 'aaaaaaaaa', '2026-09-09 23:55:55', NULL);

CREATE TABLE `detalle_pedido` (`id` int(11) NOT NULL AUTO_INCREMENT, `id_pedido` int(11) NOT NULL, `id_producto` int(11) NOT NULL, `cantidad` int(11) NOT NULL, `precio_unitario` decimal(18,6) NOT NULL DEFAULT 0.000000, PRIMARY KEY (`id`), KEY `fk_pedido` (`id_pedido`), KEY `fk_detalle_producto` (`id_producto`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `detalle_pedido` (`id`, `id_pedido`, `id_producto`, `cantidad`, `precio_unitario`) VALUES (2, 1, 134040, 12, 0.850000);

CREATE TABLE `estado` (`id` int(11) NOT NULL AUTO_INCREMENT, `nombre` varchar(50) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `estado` (`id`, `nombre`) VALUES (1, 'Lara'), (2, 'Zulia'), (3, 'Distrito capital'), (4, 'Portuguesa'), (5, 'Falcón'), (6, 'Mérida'), (7, 'Yaracuy'), (8, 'Bolivar'), (9, 'Carabobo'), (10, 'Miranda');

CREATE TABLE `estatus` (`id` int(1) NOT NULL AUTO_INCREMENT, `nombre` varchar(20) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `estatus` (`id`, `nombre`) VALUES (1, 'Pendiente'), (2, 'Entregado/Listo'), (3, 'Cancelado');

CREATE TABLE `linea_producto` (`id` int(11) NOT NULL AUTO_INCREMENT, `id_subcategoria` int(11) NOT NULL, `nombre` varchar(30) NOT NULL, PRIMARY KEY (`id`), KEY `fk_subcategoria` (`id_subcategoria`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `linea_producto` (`id`, `id_subcategoria`, `nombre`) VALUES (2, 1, 'Te de Jamaica'), (3, 2, 'Naranja'), (4, 2, 'Nectar de Manzana'), (5, 2, 'Nectar de Pera'), (6, 2, 'Nectar de Duraz'), (7, 5, 'Suero de Leche'), (8, 5, 'Suero Cremoso'), (9, 8, 'Leche'), (10, 8, 'Chicha'), (11, 6, 'Crema de Leche'), (12, 6, 'Queso Crema'), (13, 6, 'Natilla'), (17, 4, 'Cheddar'), (18, 4, 'prueba'), (19, 8, 'Leche Pasteurizada');

CREATE TABLE `modulo` (`id` int(5) NOT NULL AUTO_INCREMENT, `Modulo` varchar(20) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `modulo` (`id`, `Modulo`) VALUES (1, 'Home'), (2, 'vendedores'), (3, 'clientes'), (4, 'productos'), (5, 'pedidos'), (6, 'inventario'), (7, 'reportes');

CREATE TABLE `municipio` (`id` int(11) NOT NULL AUTO_INCREMENT, `id_estado` int(11) NOT NULL, `nombre` varchar(50) NOT NULL, PRIMARY KEY (`id`), KEY `fk_municipio_estado` (`id_estado`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `municipio` (`id`, `id_estado`, `nombre`) VALUES (1, 1, 'Iribarren'), (2, 2, 'Catatumbo'), (3, 3, 'Caracas'), (4, 4, 'Guanare'), (5, 5, 'Coro'), (6, 6, 'Mérida'), (7, 7, 'San Felipe'), (8, 8, 'Ciudad Bolivar'), (9, 9, 'Valencia'), (10, 10, 'Los Teques');

CREATE TABLE `parroquia` (`id` int(11) NOT NULL AUTO_INCREMENT, `id_municipio` int(11) DEFAULT NULL, `nombre` varchar(26) NOT NULL, `id_estado` int(11) NOT NULL, PRIMARY KEY (`id`), KEY `PK id_parroquia` (`id_municipio`), KEY `PK id_parrroquia` (`id_estado`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `parroquia` (`id`, `id_municipio`, `nombre`, `id_estado`) VALUES (1, 3, 'Calendaria', 3), (2, 2, 'Chiquinquirá', 2), (3, 1, 'El Cují', 1), (4, 5, 'Río Seco', 5), (5, 8, 'Simón Bolívar', 8), (6, 4, 'Córdoba', 4), (7, 9, 'El Socorro', 9), (8, 7, 'San Javier', 7), (9, 10, 'El Café', 10);

CREATE TABLE `pedido` (`id` int(11) NOT NULL AUTO_INCREMENT, `id_vendedor` int(11) NOT NULL, `id_cliente` int(11) NOT NULL, `fecha` date NOT NULL, `fecha_entrega` date DEFAULT NULL, `total` decimal(18,6) NOT NULL DEFAULT 0.000000, `id_estatus` int(1) NOT NULL DEFAULT 1, PRIMARY KEY (`id`), KEY `fk_vendedor` (`id_vendedor`), KEY `fk_cliente` (`id_cliente`), KEY `fk_pedido_estatus` (`id_estatus`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `pedido` (`id`, `id_vendedor`, `id_cliente`, `fecha`, `fecha_entrega`, `total`, `id_estatus`) VALUES (1, 127, 1, '2026-09-10', '2026-09-10', 10.200000, 2);

CREATE TABLE `permisos` (`id` int(5) NOT NULL AUTO_INCREMENT, `cod_rol` int(5) NOT NULL, `cod_modulo` int(5) NOT NULL, PRIMARY KEY (`id`), KEY `fk_rol` (`cod_rol`), KEY `fk_modulo` (`cod_modulo`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `permisos` (`id`, `cod_rol`, `cod_modulo`) VALUES (1, 1, 1), (2, 1, 2), (9, 10, 2);

CREATE TABLE `personal` (`id` int(11) NOT NULL AUTO_INCREMENT, `cedula` int(11) NOT NULL, `nombre` varchar(50) NOT NULL, `apellido` varchar(50) NOT NULL, `telefono` varchar(20) DEFAULT NULL, `id_tipo_personal` int(11) NOT NULL, `status` tinyint(1) NOT NULL DEFAULT 1, PRIMARY KEY (`id`), UNIQUE KEY `cedula` (`cedula`), KEY `fk_personal_tipo` (`id_tipo_personal`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `personal` (`id`, `cedula`, `nombre`, `apellido`, `telefono`, `id_tipo_personal`, `status`) VALUES (127, 28621145, 'ENGELBERG', 'Osuna', NULL, 1, 1), (128, 30125995, 'Enyelberth', 'Gutierrez', '04125780436', 1, 1), (129, 13881540, 'Yezenia', 'Vasquez', '04245648303', 3, 1), (130, 32156425, 'Daniela', 'Vasquez', '04124865236', 2, 1);

CREATE TABLE `presentacion` (`id` int(11) NOT NULL AUTO_INCREMENT, `contenido` varchar(50) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `presentacion` (`id`, `contenido`) VALUES (1, '400'), (2, '900');

CREATE TABLE `producto` (`id` int(11) NOT NULL AUTO_INCREMENT, `nombre` varchar(40) NOT NULL, `id_linea` int(11) NOT NULL, `presentacion` int(5) NOT NULL DEFAULT 0, `id_medida` int(11) NOT NULL DEFAULT 0, `precio` decimal(18,6) NOT NULL DEFAULT 0.000000, PRIMARY KEY (`id`), KEY `fk_cont` (`id_linea`), KEY `fk_medida` (`id_medida`), KEY `fk_pres` (`presentacion`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `producto` (`id`, `nombre`, `id_linea`, `presentacion`, `id_medida`, `precio`) VALUES (134040, 'NECTAR DE MANZANA 400CC ', 4, 1, 2, 0.850000);

CREATE TABLE `roles` (`ID` int(5) NOT NULL AUTO_INCREMENT, `nombre` varchar(20) NOT NULL, PRIMARY KEY (`ID`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `roles` (`ID`, `nombre`) VALUES (1, 'Adminsd'), (8, 'Admin'), (9, 'Admin'), (10, 'wee'), (11, 'ssa');

CREATE TABLE `subcategoria` (`id` int(11) NOT NULL AUTO_INCREMENT, `id_categoria` int(11) NOT NULL, `sub_categoria` varchar(10) NOT NULL, PRIMARY KEY (`id`), KEY `fk_categoria` (`id_categoria`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `subcategoria` (`id`, `id_categoria`, `sub_categoria`) VALUES (1, 1, 'Te'), (2, 1, 'Nectares'), (3, 2, 'Yogurt'), (4, 2, 'Queso'), (5, 2, 'Suero'), (6, 2, 'Crema'), (8, 2, 'Bebidas La');

CREATE TABLE `tipo_personal` (`id` int(11) NOT NULL AUTO_INCREMENT, `nombre` varchar(10) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `tipo_personal` (`id`, `nombre`) VALUES (1, 'Administra'), (2, 'Gerente'), (3, 'Vendedor');

CREATE TABLE `unidad_medida` (`id` int(11) NOT NULL AUTO_INCREMENT, `medida` varchar(2) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `unidad_medida` (`id`, `medida`) VALUES (1, 'Lt'), (2, 'cc'), (3, 'kg'), (4, 'Gr');

CREATE TABLE `usuario` (`id` int(11) NOT NULL AUTO_INCREMENT, `id_personal` int(11) NOT NULL, `username` varchar(50) NOT NULL, `password` varchar(255) NOT NULL, `cod_rol` int(5) NOT NULL, `status` int(1) NOT NULL DEFAULT 1, PRIMARY KEY (`id`), UNIQUE KEY `id_personal` (`id_personal`), UNIQUE KEY `username` (`username`), KEY `fk_usuario_rol` (`cod_rol`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `usuario` (`id`, `id_personal`, `username`, `password`, `cod_rol`, `status`) VALUES (11, 127, 'engelberg', '12345678', 1, 1);

--
-- Restricciones
--
ALTER TABLE `cliente` ADD CONSTRAINT `fk_cliente_parroquia` FOREIGN KEY (`id_parroquia`) REFERENCES `parroquia` (`id`) ON UPDATE CASCADE;
ALTER TABLE `detalle_pedido` ADD CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`) ON UPDATE CASCADE, ADD CONSTRAINT `fk_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id`) ON UPDATE CASCADE;
ALTER TABLE `linea_producto` ADD CONSTRAINT `fk_subcategoria` FOREIGN KEY (`id_subcategoria`) REFERENCES `subcategoria` (`id`) ON UPDATE CASCADE;
ALTER TABLE `municipio` ADD CONSTRAINT `fk_municipio_estado` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id`) ON UPDATE CASCADE;
ALTER TABLE `parroquia` ADD CONSTRAINT `PK id_parroquia` FOREIGN KEY (`id_municipio`) REFERENCES `municipio` (`id`) ON UPDATE CASCADE, ADD CONSTRAINT `PK id_parrroquia` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id`) ON UPDATE CASCADE;
ALTER TABLE `pedido` ADD CONSTRAINT `fk_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id`), ADD CONSTRAINT `fk_pedido_estatus` FOREIGN KEY (`id_estatus`) REFERENCES `estatus` (`id`) ON UPDATE CASCADE, ADD CONSTRAINT `fk_pedido_personal` FOREIGN KEY (`id_vendedor`) REFERENCES `personal` (`id`) ON UPDATE CASCADE;
ALTER TABLE `permisos` ADD CONSTRAINT `fk_modulo` FOREIGN KEY (`cod_modulo`) REFERENCES `modulo` (`id`) ON UPDATE CASCADE, ADD CONSTRAINT `fk_rol` FOREIGN KEY (`cod_rol`) REFERENCES `roles` (`ID`) ON UPDATE CASCADE;
ALTER TABLE `personal` ADD CONSTRAINT `fk_personal_tipo` FOREIGN KEY (`id_tipo_personal`) REFERENCES `tipo_personal` (`id`) ON UPDATE CASCADE;
ALTER TABLE `producto` ADD CONSTRAINT `fk_linea` FOREIGN KEY (`id_linea`) REFERENCES `linea_producto` (`id`) ON UPDATE CASCADE, ADD CONSTRAINT `fk_medida` FOREIGN KEY (`id_medida`) REFERENCES `unidad_medida` (`id`) ON UPDATE CASCADE, ADD CONSTRAINT `fk_pres` FOREIGN KEY (`presentacion`) REFERENCES `presentacion` (`id`) ON UPDATE CASCADE;
ALTER TABLE `subcategoria` ADD CONSTRAINT `fk_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id`) ON UPDATE CASCADE;
ALTER TABLE `usuario` ADD CONSTRAINT `fk_usuario_personal` FOREIGN KEY (`id_personal`) REFERENCES `personal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE, ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`cod_rol`) REFERENCES `roles` (`ID`) ON UPDATE CASCADE;
COMMIT;