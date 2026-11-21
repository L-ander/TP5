-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-11-2026 a las 05:31:04
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
  `categoria` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `cedula` varchar(50) NOT NULL,
  `sexo` varchar(15) NOT NULL,
  `id_parroquia` int(11) NOT NULL,
  `direccion` varchar(250) NOT NULL COMMENT 'Dirección específica (Calle, casa, local)',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_modificacion` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--


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


--
-- Disparadores `detalle_pedido`
--
DELIMITER $$
CREATE TRIGGER `tg_calcular_total_pedido` AFTER INSERT ON `detalle_pedido` FOR EACH ROW BEGIN
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


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `linea_producto`
--

CREATE TABLE `linea_producto` (
  `id` int(11) NOT NULL,
  `id_subcategoria` int(11) NOT NULL,
  `nombre` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `linea_producto`
--


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


--
-- Disparadores `pedido`
--
DELIMITER $$
CREATE TRIGGER `tg_registrar_fecha_entrega` BEFORE UPDATE ON `pedido` FOR EACH ROW BEGIN
    IF NEW.id_estatus = 2 AND OLD.id_estatus != 2 THEN
        SET NEW.fecha_entrega = CURDATE();
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


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentacion`
--

CREATE TABLE `presentacion` (
  `id` int(11) NOT NULL,
  `contenido` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `presentacion`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id` int(11) NOT NULL,
  `nombre` varchar(40) NOT NULL,
  `id_linea` int(11) NOT NULL,
  `presentacion` int(5) NOT NULL DEFAULT 0,
  `id_medida` int(11) NOT NULL DEFAULT 0,
  `precio` decimal(18,6) NOT NULL DEFAULT 0.000000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `ID` int(5) NOT NULL,
  `nombre` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subcategoria`
--

CREATE TABLE `subcategoria` (
  `id` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `sub_categoria` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `subcategoria`
--


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


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidad_medida`
--

CREATE TABLE `unidad_medida` (
  `id` int(11) NOT NULL,
  `medida` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `unidad_medida`
--


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


--
-- Datos nativos
INSERT INTO `roles` (`ID`, `nombre`) VALUES (1, 'Admin');
INSERT INTO `tipo_personal` (`id`, `nombre`) VALUES (1, 'Admin'), (2, 'Gerente'), (3, 'Vendedor');
INSERT INTO `estatus` (`id`, `nombre`) VALUES (1, 'Pendiente'), (2, 'Entregado/Listo'), (3, 'Cancelado');
INSERT INTO `modulo` (`id`, `Modulo`) VALUES (2, 'Personal'), (3, 'Clientes'), (4, 'Productos'), (5, 'Pedidos'), (7, 'reportes'), (8, 'Permisos');
INSERT INTO `personal` (`id`, `cedula`, `nombre`, `apellido`, `telefono`, `id_tipo_personal`, `status`) VALUES (1, 10000000, 'Administrador', 'Sistema', NULL, 1, 1);
INSERT INTO `usuario` (`id`, `id_personal`, `username`, `password`, `cod_rol`, `status`) VALUES (1, 1, 'admin', 'Admin123*', 1, 1);
INSERT INTO `permisos` (`id`, `cod_rol`, `cod_modulo`) VALUES (1, 1, 2), (2, 1, 3), (3, 1, 4), (4, 1, 5), (5, 1, 7), (6, 1, 8);
INSERT INTO `estado` (`id`, `nombre`) VALUES (1, 'Amazonas'), (2, 'Anzoátegui'), (3, 'Apure'), (4, 'Aragua'), (5, 'Barinas'), (6, 'Bolívar'), (7, 'Carabobo'), (8, 'Cojedes'), (9, 'Delta Amacuro'), (10, 'Falcón'), (11, 'Guárico'), (12, 'Lara'), (13, 'Mérida'), (14, 'Miranda'), (15, 'Monagas'), (16, 'Nueva Esparta'), (17, 'Portuguesa'), (18, 'Sucre'), (19, 'Táchira'), (20, 'Trujillo'), (21, 'Vargas'), (22, 'Yaracuy'), (23, 'Zulia'), (24, 'Distrito Capital');
INSERT INTO `municipio` (`id`, `id_estado`, `nombre`) VALUES (1, 1, 'Alto Orinoco'), (2, 1, 'Atabapo'), (3, 1, 'Atures'), (4, 1, 'Autana'), (5, 1, 'Manapiare'), (6, 1, 'Maroa'), (7, 1, 'Río Negro'), (8, 2, 'Anaco'), (9, 2, 'Aragua'), (10, 2, 'Bolívar'), (11, 2, 'Bruzual'), (12, 2, 'Cajigal'), (13, 2, 'Carvajal'), (14, 2, 'Diego Bautista Urbaneja'), (15, 2, 'Freites'), (16, 2, 'Guanipa'), (17, 2, 'Guanta'), (18, 2, 'Independencia'), (19, 2, 'Libertad'), (20, 2, 'McGregor'), (21, 2, 'Miranda'), (22, 2, 'Monagas'), (23, 2, 'Peñalver'), (24, 2, 'Píritu'), (25, 2, 'San Juan de Capistrano'), (26, 2, 'Santa Ana'), (27, 2, 'Simón Rodríguez'), (28, 2, 'Sotillo'), (29, 3, 'Achaguas'), (30, 3, 'Biruaca'), (31, 3, 'Muñoz'), (32, 3, 'Páez'), (33, 3, 'Pedro Camejo'), (34, 3, 'Rómulo Gallegos'), (35, 3, 'San Fernando'), (36, 4, 'Bolívar'), (37, 4, 'Camatagua'), (38, 4, 'Francisco Linares Alcántara'), (39, 4, 'Girardot'), (40, 4, 'José Ángel Lamas'), (41, 4, 'José Félix Ribas'), (42, 4, 'José Rafael Revenga'), (43, 4, 'Libertador'), (44, 4, 'Mario Briceño Iragorry'), (45, 4, 'Ocumare de la Costa de Oro'), (46, 4, 'San Casimiro'), (47, 4, 'San Sebastián'), (48, 4, 'Santiago Mariño'), (49, 4, 'Santos Michelena'), (50, 4, 'Sucre'), (51, 4, 'Tovar'), (52, 4, 'Urdaneta'), (53, 4, 'Zamora'), (54, 5, 'Alberto Arvelo Torrealba'), (55, 5, 'Andrés Eloy Blanco'), (56, 5, 'Antonio José de Sucre'), (57, 5, 'Arismendi'), (58, 5, 'Barinas'), (59, 5, 'Bolívar'), (60, 5, 'Cruz Paredes'), (61, 5, 'Ezequiel Zamora'), (62, 5, 'Obispos'), (63, 5, 'Pedraza'), (64, 5, 'Rojas'), (65, 5, 'Sosa'), (66, 6, 'Angostura'), (67, 6, 'Caroní'), (68, 6, 'Cedeño'), (69, 6, 'El Callao'), (70, 6, 'Gran Sabana'), (71, 6, 'Heres'), (72, 6, 'Píar'), (73, 6, 'Roscio'), (74, 6, 'Sifontes'), (75, 6, 'Sucre'), (76, 6, 'Padre Pedro Chien'), (77, 7, 'Bejuma'), (78, 7, 'Carlos Arvelo'), (79, 7, 'Diego Ibarra'), (80, 7, 'Guacara'), (81, 7, 'Mora'), (82, 7, 'Libertador'), (83, 7, 'Los Guayos'), (84, 7, 'Miranda'), (85, 7, 'Montalbán'), (86, 7, 'Naguanagua'), (87, 7, 'Puerto Cabello'), (88, 7, 'San Diego'), (89, 7, 'San Joaquín'), (90, 7, 'Valencia'), (91, 8, 'Anzoátegui'), (92, 8, 'Tinaquillo'), (93, 8, 'Girardot'), (94, 8, 'Lima Blanco'), (95, 8, 'Pao de San Juan Bautista'), (96, 8, 'Ricaurte'), (97, 8, 'Rómulo Gallegos'), (98, 8, 'San Carlos'), (99, 8, 'Tinaco'), (100, 9, 'Antonio Díaz'), (101, 9, 'Casacoima'), (102, 9, 'Pedernales'), (103, 9, 'Tucupita'), (104, 10, 'Acosta'), (105, 10, 'Bolívar'), (106, 10, 'Buchivacoa'), (107, 10, 'Carirubana'), (108, 10, 'Colina'), (109, 10, 'Dabajuro'), (110, 10, 'Democracia'), (111, 10, 'Falcón'), (112, 10, 'Federación'), (113, 10, 'Jacura'), (114, 10, 'Los Taques'), (115, 10, 'Manaure'), (116, 10, 'Mauroa'), (117, 10, 'Miranda'), (118, 10, 'Monseñor Iturriza'), (119, 10, 'Palmasola'), (120, 10, 'Petit'), (121, 10, 'Píritu'), (122, 10, 'San Francisco'), (123, 10, 'Sucre'), (124, 10, 'Silva'), (125, 10, 'Tocópero'), (126, 10, 'Unión'), (127, 10, 'Urumaco'), (128, 10, 'Zamora'), (129, 11, 'Camaguán'), (130, 11, 'Chaguaramas'), (131, 11, 'El Socorro'), (132, 11, 'Infante'), (133, 11, 'Las Mercedes'), (134, 11, 'Mellado'), (135, 11, 'Miranda'), (136, 11, 'Monagas'), (137, 11, 'Ortiz'), (138, 11, 'Ribas'), (139, 11, 'Roscio'), (140, 11, 'San Gerónimo de Guayabal'), (141, 11, 'San José de Guaribe'), (142, 11, 'Santa María de Ipire'), (143, 11, 'Zaraza'), (144, 12, 'Andrés Eloy Blanco'), (145, 12, 'Crespo'), (146, 12, 'Iribarren'), (147, 12, 'Jiménez'), (148, 12, 'Morán'), (149, 12, 'Palavecino'), (150, 12, 'Simón Planas'), (151, 12, 'Torres'), (152, 12, 'Urdaneta'), (153, 13, 'Alberto Adriani'), (154, 13, 'Andrés Bello'), (155, 13, 'Antonio Pinto Salinas'), (156, 13, 'Aricagua'), (157, 13, 'Arzobispo Chacón'), (158, 13, 'Campo Elías'), (159, 13, 'Caracciolo Parra Olmedo'), (160, 13, 'Cardenal Quintero'), (161, 13, 'Guaraque'), (162, 13, 'Julio César Salas'), (163, 13, 'Justo Briceño'), (164, 13, 'Libertador'), (165, 13, 'Miranda'), (166, 13, 'Obispo Ramos de Lora'), (167, 13, 'Padre Noguera'), (168, 13, 'Pueblo Llano'), (169, 13, 'Rangel'), (170, 13, 'Rivas Dávila'), (171, 13, 'Santos Marquina'), (172, 13, 'Sucre'), (173, 13, 'Tovar'), (174, 13, 'Tulio Febres Cordero'), (175, 13, 'Zea'), (176, 14, 'Acevedo'), (177, 14, 'Andrés Bello'), (178, 14, 'Baruta'), (179, 14, 'Brión'), (180, 14, 'Buroz'), (181, 14, 'Carrizal'), (182, 14, 'Chacao'), (183, 14, 'Cristóbal Rojas'), (184, 14, 'El Hatillo'), (185, 14, 'Guaicaipuro'), (186, 14, 'Independencia'), (187, 14, 'Lander'), (188, 14, 'Los Salias'), (189, 14, 'Páez'), (190, 14, 'Paz Castillo'), (191, 14, 'Pedro Gual'), (192, 14, 'Plaza'), (193, 14, 'Simón Bolívar'), (194, 14, 'Sucre'), (195, 14, 'Urdaneta'), (196, 14, 'Zamora'), (197, 15, 'Acosta'), (198, 15, 'Aguasay'), (199, 15, 'Bolívar'), (200, 15, 'Caripe'), (201, 15, 'Cedeño'), (202, 15, 'Ezequiel Zamora'), (203, 15, 'Libertador'), (204, 15, 'Maturín'), (205, 15, 'Piar'), (206, 15, 'Punceres'), (207, 15, 'Santa Bárbara'), (208, 15, 'Sotillo'), (209, 15, 'Uracoa'), (210, 16, 'Antolín del Campo'), (211, 16, 'Arismendi'), (212, 16, 'Díaz'), (213, 16, 'García'), (214, 16, 'Gómez'), (215, 16, 'Maneiro'), (216, 16, 'Marcano'), (217, 16, 'Mariño'), (218, 16, 'Península de Macanao'), (219, 16, 'Tubores'), (220, 16, 'Villalba'), (221, 17, 'Agua Blanca'), (222, 17, 'Araure'), (223, 17, 'Esteller'), (224, 17, 'Guanare'), (225, 17, 'Guanarito'), (226, 17, 'Monseñor José Vicente de Unda'), (227, 17, 'Ospino'), (228, 17, 'Páez'), (229, 17, 'Papelón'), (230, 17, 'San Genaro de Boconoíto'), (231, 17, 'San Rafael de Onoto'), (232, 17, 'Santa Rosalía'), (233, 17, 'Sucre'), (234, 17, 'Turén'), (235, 18, 'Andrés Eloy Blanco'), (236, 18, 'Andrés Mata'), (237, 18, 'Arismendi'), (238, 18, 'Benítez'), (239, 18, 'Bermúdez'), (240, 18, 'Bolívar'), (241, 18, 'Cajigal'), (242, 18, 'Cruz Salmerón Acosta'), (243, 18, 'Libertador'), (244, 18, 'Mariño'), (245, 18, 'Mejía'), (246, 18, 'Montes'), (247, 18, 'Ribero'), (248, 18, 'Sucre'), (249, 18, 'Valdez'), (250, 19, 'Andrés Bello'), (251, 19, 'Antonio Rómulo Costa'), (252, 19, 'Ayacucho'), (253, 19, 'Bolívar'), (254, 19, 'Cárdenas'), (255, 19, 'Córdoba'), (256, 19, 'Fernández Feo'), (257, 19, 'Francisco de Miranda'), (258, 19, 'García de Hevia'), (259, 19, 'Guásimos'), (260, 19, 'Independencia'), (261, 19, 'Jáuregui'), (262, 19, 'José María Vargas'), (263, 19, 'Junín'), (264, 19, 'Libertad'), (265, 19, 'Libertador'), (266, 19, 'Lobatera'), (267, 19, 'Michelena'), (268, 19, 'Panamericano'), (269, 19, 'Pedro María Ureña'), (270, 19, 'Rafael Urdaneta'), (271, 19, 'Samuel Darío Maldonado'), (272, 19, 'San Cristóbal'), (273, 19, 'Seboruco'), (274, 19, 'Simón Rodríguez'), (275, 19, 'Sucre'), (276, 19, 'Torbes'), (277, 19, 'Uribante'), (278, 19, 'San Judas Tadeo'), (279, 20, 'Andrés Bello'), (280, 20, 'Boconó'), (281, 20, 'Bolívar'), (282, 20, 'Candelaria'), (283, 20, 'Carache'), (284, 20, 'Escuque'), (285, 20, 'José Felipe Márquez Cañizalez'), (286, 20, 'Juan Vicente Campos Elías'), (287, 20, 'La Ceiba'), (288, 20, 'Miranda'), (289, 20, 'Monte Carmelo'), (290, 20, 'Motatán'), (291, 20, 'Pampán'), (292, 20, 'Pampanito'), (293, 20, 'Rafael Rangel'), (294, 20, 'San Rafael de Carvajal'), (295, 20, 'Sucre'), (296, 20, 'Trujillo'), (297, 20, 'Urdaneta'), (298, 20, 'Valera'), (299, 21, 'Vargas'), (300, 22, 'Arístides Bastidas'), (301, 22, 'Bolívar'), (302, 22, 'Bruzual'), (303, 22, 'Cocorote'), (304, 22, 'Independencia'), (305, 22, 'José Antonio Páez'), (306, 22, 'La Trinidad'), (307, 22, 'Manuel Monge'), (308, 22, 'Nirgua'), (309, 22, 'Peña'), (310, 22, 'San Felipe'), (311, 22, 'Sucre'), (312, 22, 'Urachiche'), (313, 22, 'Veroes'), (314, 23, 'Almirante Padilla'), (315, 23, 'Baralt'), (316, 23, 'Cabimas'), (317, 23, 'Catatumbo'), (318, 23, 'Colón'), (319, 23, 'Francisco Javier Pulgar'), (320, 23, 'Páez'), (321, 23, 'Jesús Enrique Lossada'), (322, 23, 'Jesús María Semprún'), (323, 23, 'La Cañada de Urdaneta'), (324, 23, 'Lagunillas'), (325, 23, 'Machiques de Perijá'), (326, 23, 'Mara'), (327, 23, 'Maracaibo'), (328, 23, 'Miranda'), (329, 23, 'Rosario de Perijá'), (330, 23, 'San Francisco'), (331, 23, 'Santa Rita'), (332, 23, 'Simón Bolívar'), (333, 23, 'Sucre'), (334, 23, 'Valmore Rodríguez'), (335, 24, 'Libertador');
INSERT INTO `parroquia` (`id`, `id_municipio`, `nombre`, `id_estado`) VALUES (1, 1, 'La Esmeralda', 1), (2, 1, 'Alto', 1), (3, 1, 'Huachamacare', 1), (4, 1, 'Marawaka', 1), (5, 1, 'Mavaka', 1), (6, 1, 'Sierra', 1), (7, 2, 'San Fernando de Atabapo', 1), (8, 2, 'Ucata', 1), (9, 2, 'Yapacana', 1), (10, 2, 'Caname', 1), (11, 3, 'Puerto Ayacucho', 1), (12, 3, 'Fernando', 1), (13, 3, 'Luis', 1), (14, 3, 'Pahueña', 1), (15, 3, 'Platanillal', 1), (16, 4, 'Isla Ratón', 1), (17, 4, 'Samariapo', 1), (18, 4, 'Sipapo', 1), (19, 4, 'Munduapo', 1), (20, 4, 'Guayapo', 1), (21, 5, 'San Juan de Manapiare', 1), (22, 5, 'Alto', 1), (23, 5, 'Medio', 1), (24, 5, 'Bajo', 1), (25, 6, 'Maroa', 1), (26, 6, 'Victorino', 1), (27, 6, 'Comunidad', 1), (28, 7, 'San Carlos de Río Negro', 1), (29, 7, 'Casiquiare', 1), (30, 7, 'Cocuy', 1), (31, 7, 'San', 1), (32, 7, 'Solano', 1), (33, 8, 'Anaco', 2), (34, 8, 'San', 2), (35, 9, 'Aragua de Barcelona', 2), (36, 9, 'Cachipo', 2), (37, 9, 'Aragua', 2), (38, 10, 'Barcelona', 2), (39, 10, 'Bergatín', 2), (40, 10, 'Caigua', 2), (41, 10, 'El', 2), (42, 10, 'Naricual.', 2), (43, 10, 'San', 2), (44, 11, 'Clarines', 2), (45, 11, 'Guanape', 2), (46, 11, 'Sabana', 2), (47, 12, 'Onoto', 2), (48, 12, 'San', 2), (49, 13, 'Valle de Guanape', 2), (50, 13, 'Valle', 2), (51, 13, 'Santa', 2), (52, 14, 'Lechería', 2), (53, 14, 'El', 2), (54, 15, 'Cantaura', 2), (55, 15, 'Libertador', 2), (56, 15, 'Santa', 2), (57, 15, 'Urica', 2), (58, 16, 'San José de Guanipa', 2), (59, 16, 'San', 2), (60, 17, 'Guanta', 2), (61, 17, 'Chorrerón', 2), (62, 18, 'Soledad', 2), (63, 18, 'Mamo', 2), (64, 19, 'San Mateo', 2), (65, 19, 'San', 2), (66, 19, 'El', 2), (67, 19, 'Santa', 2), (68, 19, 'La', 2), (69, 20, 'El Chaparro', 2), (70, 20, 'El', 2), (71, 20, 'Tomás', 2), (72, 20, 'Calatrava', 2), (73, 21, 'Pariaguán', 2), (74, 21, 'Atapirire', 2), (75, 21, 'Boca', 2), (76, 21, 'El', 2), (77, 22, 'Mapire', 2), (78, 22, 'Piar', 2), (79, 22, 'Santa', 2), (80, 22, 'San', 2), (81, 22, 'Uverito', 2), (82, 22, 'Zuata', 2), (83, 23, 'Puerto Píritu', 2), (84, 23, 'Puerto', 2), (85, 23, 'San', 2), (86, 23, 'Sucre', 2), (87, 24, 'Píritu', 2), (88, 24, 'San', 2), (89, 25, 'Boca de Uchire', 2), (90, 25, 'Boca', 2), (91, 26, 'Santa Ana', 2), (92, 26, 'Pueblo', 2), (93, 26, 'Santa', 2), (94, 27, 'El Tigre', 2), (95, 27, 'Edmundo', 2), (96, 27, 'Miguel', 2), (97, 28, 'Puerto La Cruz', 2), (98, 28, 'Puerto', 2), (99, 28, 'Pozuelos', 2), (100, 29, 'Achaguas', 3), (101, 29, 'Apurito', 3), (102, 29, 'El', 3), (103, 29, 'Guachara', 3), (104, 29, 'Mucuritas', 3), (105, 29, 'Queseras', 3), (106, 30, 'Biruaca', 3), (107, 31, 'Bruzual', 3), (108, 31, 'Mantecal', 3), (109, 31, 'Quintero', 3), (110, 31, 'Rincón', 3), (111, 31, 'San', 3), (112, 32, 'Guasdualito', 3), (113, 32, 'Aramendi', 3), (114, 32, 'El', 3), (115, 32, 'San', 3), (116, 32, 'Urdaneta', 3), (117, 33, 'San Juan de Payara', 3), (118, 33, 'San', 3), (119, 33, 'Codazzi', 3), (120, 33, 'Cunaviche', 3), (121, 34, 'Elorza', 3), (122, 34, 'La', 3), (123, 35, 'San Fernando de Apure', 3), (124, 35, 'San', 3), (125, 35, 'El', 3), (126, 35, 'Peñalver', 3), (127, 36, 'San Mateo', 4), (128, 36, 'Bolívar', 4), (129, 37, 'Camatagua', 4), (130, 37, 'Carmen', 4), (131, 38, 'Santa Rita', 4), (132, 38, 'Santa', 4), (133, 38, 'Francisco', 4), (134, 38, 'Moseñor', 4), (135, 39, 'Maracay', 4), (136, 39, 'Pedro', 4), (137, 39, 'Joaquín', 4), (138, 39, 'José', 4), (139, 39, 'Madre', 4), (140, 39, 'Andrés', 4), (141, 39, 'Los', 4), (142, 39, 'Las', 4), (143, 39, 'Choroní', 4), (144, 40, 'Santa Cruz de Aragua', 4), (145, 40, 'Santa', 4), (146, 41, 'La Victoria', 4), (147, 41, 'José', 4), (148, 41, 'Castor', 4), (149, 41, 'Las', 4), (150, 41, 'Pao', 4), (151, 41, 'Zuata', 4), (152, 42, 'El Consejo', 4), (153, 42, 'José', 4), (154, 43, 'Palo Negro', 4), (155, 43, 'Palo', 4), (156, 43, 'San', 4), (157, 44, 'El Limón', 4), (158, 44, 'El', 4), (159, 44, 'Caña', 4), (160, 45, 'Ocumare de la Costa', 4), (161, 45, 'Ocumare', 4), (162, 46, 'San Casimiro', 4), (163, 46, 'San', 4), (164, 46, 'Güiripa', 4), (165, 46, 'Ollas', 4), (166, 46, 'Valle', 4), (167, 47, 'San Sebastián de Los Reyes', 4), (168, 47, 'San', 4), (169, 48, 'Turmero', 4), (170, 48, 'Arevalo', 4), (171, 48, 'Chuao', 4), (172, 48, 'Samán', 4), (173, 48, 'Alfredo', 4), (174, 49, 'Las Tejerías', 4), (175, 49, 'Santos', 4), (176, 49, 'Tiara', 4), (177, 50, 'Cagua', 4), (178, 50, 'Bella', 4), (179, 51, 'Colonia Tovar', 4), (180, 51, 'Tovar', 4), (181, 52, 'Barbacoas', 4), (182, 52, 'Urdaneta', 4), (183, 52, 'Las', 4), (184, 52, 'San', 4), (185, 52, 'Taguay', 4), (186, 53, 'Villa de Cura', 4), (187, 53, 'Zamora', 4), (188, 53, 'Magdaleno', 4), (189, 53, 'San', 4), (190, 53, 'Valles', 4), (191, 53, 'Augusto', 4), (192, 54, 'Sabaneta', 5), (193, 54, 'Juan', 5), (194, 55, 'El Cantón', 5), (195, 55, 'El', 5), (196, 55, 'Santa', 5), (197, 55, 'Puerto', 5), (198, 56, 'Socopó', 5), (199, 56, 'Ticoporo', 5), (200, 56, 'Nicolás', 5), (201, 56, 'Andrés', 5), (202, 57, 'Arismendi', 5), (203, 57, 'Guadarrama', 5), (204, 57, 'La', 5), (205, 57, 'San', 5), (206, 58, 'Barinas', 5), (207, 58, 'Alberto', 5), (208, 58, 'San', 5), (209, 58, 'Santa', 5), (210, 58, 'Torunos', 5), (211, 58, 'El', 5), (212, 58, 'Rómulo', 5), (213, 58, 'Corazón', 5), (214, 58, 'Ramón', 5), (215, 58, 'Alto', 5), (216, 58, 'Manuel', 5), (217, 58, 'Juan', 5), (218, 58, 'Dominga', 5), (219, 59, 'Barinitas', 5), (220, 59, 'Altamira', 5), (221, 59, 'Calderas', 5), (222, 60, 'Barrancas', 5), (223, 60, 'El', 5), (224, 60, 'Mazparrito', 5), (225, 61, 'Santa Bárbara', 5), (226, 61, 'Santa', 5), (227, 61, 'Pedro', 5), (228, 61, 'Ramón', 5), (229, 61, 'José', 5), (230, 62, 'Obispos', 5), (231, 62, 'Los', 5), (232, 62, 'El', 5), (233, 62, 'La', 5), (234, 63, 'Ciudad Bolivia', 5), (235, 63, 'Ciudad', 5), (236, 63, 'José', 5), (237, 63, 'Páez', 5), (238, 64, 'Libertad', 5), (239, 64, 'Dolores', 5), (240, 64, 'Santa', 5), (241, 64, 'Palacio', 5), (242, 64, 'Simón', 5), (243, 65, 'Ciudad de Nutrias', 5), (244, 65, 'Ciudad', 5), (245, 65, 'El', 5), (246, 65, 'Puerto', 5), (247, 65, 'Santa', 5), (248, 65, 'Simón', 5), (249, 66, 'Ciudad Píar', 6), (250, 66, 'Raúl', 6), (251, 66, 'Barceloneta', 6), (252, 66, 'Santa', 6), (253, 66, 'San', 6), (254, 67, 'Ciudad Guayana', 6), (255, 67, 'Cachamay', 6), (256, 67, 'Chirica', 6), (257, 67, 'Dalla', 6), (258, 67, 'Once', 6), (259, 67, 'Simón', 6), (260, 67, 'Unare', 6), (261, 67, 'Universidad', 6), (262, 67, 'Vista', 6), (263, 67, 'Pozo', 6), (264, 67, 'Yocoima', 6), (265, 67, '5', 6), (266, 68, 'Caicara del Orinoco', 6), (267, 68, 'Cedeño', 6), (268, 68, 'Altagracia', 6), (269, 68, 'Ascensión', 6), (270, 68, 'Guaniamo', 6), (271, 68, 'La', 6), (272, 68, 'Pijiguaos', 6), (273, 69, 'El Callao', 6), (274, 69, 'El', 6), (275, 70, 'Santa Elena de Uairén', 6), (276, 70, 'Gran', 6), (277, 70, 'Ikabarú', 6), (278, 71, 'Ciudad Bolívar', 6), (279, 71, 'Catedral', 6), (280, 71, 'Zea', 6), (281, 71, 'Orinoco', 6), (282, 71, 'José', 6), (283, 71, 'Marhuanta', 6), (284, 71, 'Agua', 6), (285, 71, 'Vista', 6), (286, 71, 'La', 6), (287, 71, 'Panapana', 6), (288, 72, 'Upata', 6), (289, 72, 'Andrés', 6), (290, 72, 'Pedro', 6), (291, 73, 'Guasipati', 6), (292, 73, 'Roscio', 6), (293, 73, 'Salóm', 6), (294, 74, 'El Dorado', 6), (295, 74, 'Sifontes', 6), (296, 74, 'Dalla', 6), (297, 74, 'San', 6), (298, 75, 'Maripa', 6), (299, 75, 'Sucre', 6), (300, 75, 'Aripao', 6), (301, 75, 'Guarataro', 6), (302, 75, 'Las', 6), (303, 75, 'Moitaco', 6), (304, 76, 'El Palmar', 6), (305, 76, 'Padre', 6), (306, 76, 'Río', 6), (307, 77, 'Bejuma', 7), (308, 77, 'Canoabo', 7), (309, 77, 'Simón', 7), (310, 78, 'Güigüe', 7), (311, 78, 'Carabobo', 7), (312, 78, 'Tacarigua', 7), (313, 79, 'Mariara', 7), (314, 79, 'Aguas', 7), (315, 80, 'Guacara', 7), (316, 80, 'Ciudad', 7), (317, 80, 'Yagua', 7), (318, 81, 'Morón', 7), (319, 81, 'Urama', 7), (320, 82, 'Tocuyito', 7), (321, 82, 'Valencia', 7), (322, 82, 'Campo', 7), (323, 83, 'Los Guayos', 7), (324, 83, 'Los', 7), (325, 84, 'Miranda', 7), (326, 85, 'Montalbán', 7), (327, 86, 'Naguanagua', 7), (328, 87, 'Puerto Cabello', 7), (329, 87, 'Bartolomé', 7), (330, 87, 'Democracia', 7), (331, 87, 'Fraternidad', 7), (332, 87, 'Goaigoaza', 7), (333, 87, 'Juan', 7), (334, 87, 'Unión', 7), (335, 87, 'Borburata', 7), (336, 87, 'Patanemo', 7), (337, 88, 'San Diego', 7), (338, 88, 'San', 7), (339, 89, 'San Joaquín', 7), (340, 89, 'San', 7), (341, 90, 'Valencia', 7), (342, 90, 'Urbana', 7), (343, 90, 'No', 7), (344, 91, 'Cojedes', 8), (345, 91, 'Juan', 8), (346, 92, 'Tinaquillo', 8), (347, 93, 'El Baúl', 8), (348, 93, 'El', 8), (349, 93, 'Sucre', 8), (350, 94, 'Macapo', 8), (351, 94, 'La', 8), (352, 95, 'El Pao', 8), (353, 95, 'El', 8), (354, 96, 'Libertad', 8), (355, 96, 'El', 8), (356, 97, 'Las Vegas', 8), (357, 97, 'Rómulo', 8), (358, 98, 'San Carlos', 8), (359, 98, 'San', 8), (360, 98, 'Juan', 8), (361, 98, 'Manuel', 8), (362, 99, 'Tinaco', 8), (363, 99, 'General', 8), (364, 100, 'Curiapo', 9), (365, 100, 'Almirante', 9), (366, 100, 'Francisco', 9), (367, 100, 'Manuel', 9), (368, 100, 'Padre', 9), (369, 100, 'Santos', 9), (370, 101, 'Sierra Imataca', 9), (371, 101, 'Imataca', 9), (372, 101, 'Cinco', 9), (373, 101, 'Juan', 9), (374, 101, 'Manuel', 9), (375, 101, 'Rómulo', 9), (376, 102, 'Pedernales', 9), (377, 102, 'Luis', 9), (378, 103, 'Tucupita', 9), (379, 103, 'San', 9), (380, 103, 'José', 9), (381, 103, 'Juan', 9), (382, 103, 'Leonardo', 9), (383, 103, 'Mariscal', 9), (384, 103, 'Monseñor', 9), (385, 103, 'Virgen', 9), (386, 104, 'San Juan de los Cayos', 10), (387, 104, 'Capadare', 10), (388, 104, 'La', 10), (389, 104, 'Libertador', 10), (390, 104, 'San', 10), (391, 105, 'San Luis', 10), (392, 105, 'Aracua', 10), (393, 105, 'La', 10), (394, 105, 'San', 10), (395, 106, 'Capatárida', 10), (396, 106, 'Bariro', 10), (397, 106, 'Borojó', 10), (398, 106, 'Guajiro', 10), (399, 106, 'Seque', 10), (400, 106, 'Zazárida', 10), (401, 106, 'Valle', 10), (402, 107, 'Punto Fijo', 10), (403, 107, 'Norte', 10), (404, 107, 'Carirubana', 10), (405, 107, 'Santa', 10), (406, 107, 'Urbana', 10), (407, 108, 'La Vela de Coro', 10), (408, 108, 'La', 10), (409, 108, 'Acurigua', 10), (410, 108, 'Guaibacoa', 10), (411, 108, 'Las', 10), (412, 108, 'Macoruca', 10), (413, 109, 'Dabajuro', 10), (414, 110, 'Pedregal', 10), (415, 110, 'Agua', 10), (416, 110, 'Avaria', 10), (417, 110, 'Piedra', 10), (418, 110, 'Purureche', 10), (419, 111, 'Pueblo Nuevo', 10), (420, 111, 'Adaure', 10), (421, 111, 'Adícora', 10), (422, 111, 'Baraived', 10), (423, 111, 'Buena', 10), (424, 111, 'Jadacaquiva', 10), (425, 111, 'El', 10), (426, 111, 'Moruy', 10), (427, 111, 'Pueblo', 10), (428, 112, 'Churuguara', 10), (429, 112, 'Agua', 10), (430, 112, 'El', 10), (431, 112, 'Independencia', 10), (432, 112, 'Mapararí', 10), (433, 113, 'Jacura', 10), (434, 113, 'Agua', 10), (435, 113, 'Araurima', 10), (436, 114, 'Santa Cruz de Los Taques', 10), (437, 114, 'Los', 10), (438, 114, 'Judibana', 10), (439, 115, 'Yaracal', 10), (440, 115, 'Cacique', 10), (441, 116, 'Mene de Mauroa', 10), (442, 116, 'Mene', 10), (443, 116, 'San', 10), (444, 116, 'Casigua', 10), (445, 117, 'Santa Ana de Coro', 10), (446, 117, 'Guzmán', 10), (447, 117, 'Mitare', 10), (448, 117, 'Río', 10), (449, 117, 'Sabaneta', 10), (450, 117, 'San', 10), (451, 117, 'Santa', 10), (452, 118, 'Chichiriviche', 10), (453, 118, 'Boca', 10), (454, 118, 'Tocuyo', 10), (455, 119, 'Palmasola', 10), (456, 120, 'Cabure', 10), (457, 120, 'Colina', 10), (458, 120, 'Curimagua', 10), (459, 121, 'Píritu', 10), (460, 121, 'San', 10), (461, 122, 'Mirimire', 10), (462, 122, 'Capital', 10), (463, 123, 'La Cruz de Taratara', 10), (464, 123, 'Sucre', 10), (465, 123, 'Pecaya', 10), (466, 124, 'Tucacas', 10), (467, 124, 'Boca', 10), (468, 125, 'Tocópero', 10), (469, 126, 'Santa Cruz de Bucaral', 10), (470, 126, 'El', 10), (471, 126, 'Las', 10), (472, 126, 'Santa', 10), (473, 127, 'Urumaco', 10), (474, 127, 'Bruzual', 10), (475, 128, 'Puerto Cumarebo', 10), (476, 128, 'Puerto', 10), (477, 128, 'La', 10), (478, 128, 'Pueblo', 10), (479, 128, 'Zazárida', 10), (480, 129, 'Camaguán', 11), (481, 129, 'Puerto', 11), (482, 129, 'Uverito', 11), (483, 130, 'Chaguaramas', 11), (484, 131, 'El Socorro', 11), (485, 131, 'El', 11), (486, 132, 'Valle de La Pascua', 11), (487, 132, 'Valle', 11), (488, 132, 'Espino', 11), (489, 133, 'Las Mercedes', 11), (490, 133, 'Las', 11), (491, 133, 'Cabruta', 11), (492, 133, 'Santa', 11), (493, 134, 'El Sombrero', 11), (494, 134, 'El', 11), (495, 134, 'Sosa', 11), (496, 135, 'Calabozo', 11), (497, 135, 'El', 11), (498, 135, 'Guardatinajas', 11), (499, 135, 'Capital', 11), (500, 136, 'Altagracia de Orituco', 11), (501, 136, 'Altagracia', 11), (502, 136, 'San', 11), (503, 136, 'Paso', 11), (504, 136, 'Carlos', 11), (505, 136, 'Libertad', 11), (506, 137, 'Ortiz', 11), (507, 137, 'San', 11), (508, 138, 'Tucupido', 11), (509, 138, 'San', 11), (510, 139, 'San Juan de Los Morros', 11), (511, 139, 'Cantagallo', 11), (512, 139, 'San', 11), (513, 139, 'Parapara', 11), (514, 140, 'Guayabal', 11), (515, 140, 'Cazorla', 11), (516, 141, 'San José de Guaribe', 11), (517, 141, 'San', 11), (518, 141, 'Uveral', 11), (519, 142, 'Santa María de Ipire', 11), (520, 142, 'Santa', 11), (521, 142, 'Altamira', 11), (522, 143, 'Zaraza', 11), (523, 143, 'Unare', 11), (524, 144, 'Sanare', 12), (525, 144, 'Quebrada', 12), (526, 144, 'Pío', 12), (527, 144, 'Yacambú', 12), (528, 145, 'Duaca', 12), (529, 145, 'Freitez', 12), (530, 145, 'José', 12), (531, 146, 'Barquisimeto', 12), (532, 146, 'Catedral', 12), (533, 146, 'Concepción', 12), (534, 146, 'El', 12), (535, 146, 'Juan', 12), (536, 146, 'Santa', 12), (537, 146, 'Tamaca', 12), (538, 146, 'Unión', 12), (539, 146, 'Aguedo', 12), (540, 146, 'Buena', 12), (541, 146, 'Juárez', 12), (542, 147, 'Quibor', 12), (543, 147, 'Juan', 12), (544, 147, 'Cuara', 12), (545, 147, 'Diego', 12), (546, 147, 'Paraíso', 12), (547, 147, 'San', 12), (548, 147, 'Tintorero', 12), (549, 147, 'José', 12), (550, 147, 'Coronel', 12), (551, 148, 'El Tocuyo', 12), (552, 148, 'Anzoátegui', 12), (553, 148, 'Bolívar', 12), (554, 148, 'Guárico', 12), (555, 148, 'Hilario', 12), (556, 148, 'Humocaro', 12), (557, 148, 'La', 12), (558, 148, 'Morán', 12), (559, 149, 'Cabudare', 12), (560, 149, 'José', 12), (561, 149, 'Agua', 12), (562, 150, 'Sarare', 12), (563, 150, 'Buría', 12), (564, 150, 'Gustavo', 12), (565, 151, 'Carora', 12), (566, 151, 'Altagracia', 12), (567, 151, 'Antonio', 12), (568, 151, 'Camacaro', 12), (569, 151, 'Castañeda', 12), (570, 151, 'Cecilio', 12), (571, 151, 'Chiquinquira', 12), (572, 151, 'El', 12), (573, 151, 'Espinoza', 12), (574, 151, 'Heriberto', 12), (575, 151, 'Lara', 12), (576, 151, 'Las', 12), (577, 151, 'Manuel', 12), (578, 151, 'Montaña', 12), (579, 151, 'Montes', 12), (580, 151, 'Reyes', 12), (581, 151, 'Torres', 12), (582, 151, 'Trinidad', 12), (583, 152, 'Siquisique', 12), (584, 152, 'San', 12), (585, 152, 'Moroturo', 12), (586, 152, 'Xaguas', 12), (587, 153, 'El Vigía', 13), (588, 153, 'Presidente', 13), (589, 153, 'Gabriel', 13), (590, 153, 'Héctor', 13), (591, 153, 'José', 13), (592, 153, 'Pulido', 13), (593, 154, 'La Azulita', 13), (594, 154, 'La', 13), (595, 155, 'Santa Cruz de Mora', 13), (596, 155, 'Santa', 13), (597, 155, 'Mesa', 13), (598, 156, 'Aricagua', 13), (599, 156, 'San', 13), (600, 157, 'Canaguá', 13), (601, 157, 'Canagua', 13), (602, 157, 'Capurí', 13), (603, 157, 'Chacantá', 13), (604, 157, 'El', 13), (605, 157, 'Guaimaral', 13), (606, 157, 'Mucutuy', 13), (607, 157, 'Mucuchachí', 13), (608, 158, 'Ejido', 13), (609, 158, 'Fernández', 13), (610, 158, 'Matriz', 13), (611, 158, 'Montalbán', 13), (612, 158, 'Acequias', 13), (613, 158, 'Jají', 13), (614, 158, 'La', 13), (615, 158, 'San', 13), (616, 159, 'Tucaní', 13), (617, 159, 'Florencio', 13), (618, 160, 'Santo Domingo', 13), (619, 160, 'Santo', 13), (620, 160, 'Las', 13), (621, 161, 'Guaraque', 13), (622, 161, 'Mesa', 13), (623, 161, 'Río', 13), (624, 162, 'Arapuey', 13), (625, 162, 'Palmira', 13), (626, 163, 'Torondoy', 13), (627, 163, 'San', 13), (628, 164, 'Mérida', 13), (629, 164, 'Antonio', 13), (630, 164, 'Arias', 13), (631, 164, 'Caracciolo', 13), (632, 164, 'Domingo', 13), (633, 164, 'El', 13), (634, 164, 'Gonzalo', 13), (635, 164, 'Jacinto', 13), (636, 164, 'Juan', 13), (637, 164, 'Lasso', 13), (638, 164, 'Mariano', 13), (639, 164, 'Milla', 13), (640, 164, 'Osuna', 13), (641, 164, 'Sagrario', 13), (642, 164, 'Los', 13), (643, 165, 'Timotes', 13), (644, 165, 'Andrés', 13), (645, 165, 'La', 13), (646, 165, 'Piñango', 13), (647, 166, 'Santa Elena de Arenales', 13), (648, 166, 'Eloy', 13), (649, 166, 'San', 13), (650, 166, 'Santa', 13), (651, 167, 'Santa María de Caparo', 13), (652, 167, 'Santa', 13), (653, 168, 'Pueblo Llano', 13), (654, 168, 'Pueblo', 13), (655, 169, 'Mucuchíes', 13), (656, 169, 'Cacute', 13), (657, 169, 'La', 13), (658, 169, 'Mucurubá', 13), (659, 169, 'San', 13), (660, 170, 'Bailadores', 13), (661, 170, 'Gerónimo', 13), (662, 171, 'Tabay', 13), (663, 172, 'Lagunillas', 13), (664, 172, 'Chiguará', 13), (665, 172, 'Estánquez', 13), (666, 172, 'La', 13), (667, 172, 'Pueblo', 13), (668, 172, 'San', 13), (669, 173, 'Tovar', 13), (670, 173, 'El', 13), (671, 173, 'San', 13), (672, 174, 'Nueva Bolivia', 13), (673, 174, 'Independencia', 13), (674, 174, 'María', 13), (675, 174, 'Nueva', 13), (676, 174, 'Santa', 13), (677, 175, 'Zea', 13), (678, 175, 'Caño', 13), (679, 176, 'Caucagua', 14), (680, 176, 'Aragüita', 14), (681, 176, 'Arévalo', 14), (682, 176, 'Capaya', 14), (683, 176, 'Panaquire', 14), (684, 176, 'Ribas', 14), (685, 176, 'El', 14), (686, 176, 'Marizapa', 14), (687, 177, 'San José de Barlovento', 14), (688, 177, 'Cumbo', 14), (689, 177, 'San', 14), (690, 178, 'Baruta', 14), (691, 178, 'El', 14), (692, 178, 'Las', 14), (693, 178, 'Nuestra', 14), (694, 179, 'Higuerote', 14), (695, 179, 'Curiepe', 14), (696, 179, 'Tacarigua', 14), (697, 180, 'Mamporal', 14), (698, 181, 'Carrizal', 14), (699, 182, 'Chacao', 14), (700, 183, 'Charallave', 14), (701, 183, 'Las', 14), (702, 184, 'Santa Rosalía de Palermo', 14), (703, 184, 'El', 14), (704, 185, 'Los Teques', 14), (705, 185, 'Altagracia', 14), (706, 185, 'Cecilio', 14), (707, 185, 'Los', 14), (708, 185, 'El', 14), (709, 185, 'San', 14), (710, 185, 'Tácata', 14), (711, 185, 'Paracotos', 14), (712, 186, 'Santa Teresa del Tuy', 14), (713, 186, 'Cartanal', 14), (714, 186, 'Santa', 14), (715, 187, 'Ocumare del Tuy', 14), (716, 187, 'La', 14), (717, 187, 'Ocumare', 14), (718, 187, 'Santa', 14), (719, 188, 'San Antonio de los Altos', 14), (720, 188, 'San', 14), (721, 189, 'Río Chico', 14), (722, 189, 'Río', 14), (723, 189, 'El', 14), (724, 189, 'Tacarigua', 14), (725, 189, 'Paparo', 14), (726, 189, 'San', 14), (727, 190, 'Santa Lucía', 14), (728, 190, 'Santa', 14), (729, 191, 'Cúpira', 14), (730, 191, 'Machurucuto', 14), (731, 192, 'Guarenas', 14), (732, 193, 'San Francisco de Yare', 14), (733, 193, 'San', 14), (734, 194, 'Petare', 14), (735, 194, 'Leoncio', 14), (736, 194, 'Caucagüita', 14), (737, 194, 'Filas', 14), (738, 194, 'La', 14), (739, 195, 'Cúa', 14), (740, 195, 'Nueva', 14), (741, 196, 'Guatire', 14), (742, 196, 'Bolívar', 14), (743, 197, 'San Antonio de Capayacuar', 15), (744, 197, 'San', 15), (745, 198, 'Aguasay', 15), (746, 199, 'Caripito', 15), (747, 200, 'Caripe', 15), (748, 200, 'El', 15), (749, 200, 'La', 15), (750, 200, 'Sabana', 15), (751, 200, 'San', 15), (752, 200, 'Teresen', 15), (753, 201, 'Caicara de Maturín', 15), (754, 201, 'Areo', 15), (755, 201, 'Capital', 15), (756, 201, 'San', 15), (757, 201, 'Viento', 15), (758, 202, 'Punta de Mata', 15), (759, 202, 'El', 15), (760, 202, 'Punta', 15), (761, 203, 'Temblador', 15), (762, 203, 'Chaguaramas', 15), (763, 203, 'Las', 15), (764, 203, 'Tabasca', 15), (765, 204, 'Maturín', 15), (766, 204, 'Alto', 15), (767, 204, 'Boquerón', 15), (768, 204, 'Las', 15), (769, 204, 'La', 15), (770, 204, 'San', 15), (771, 204, 'El', 15), (772, 204, 'Jusepín', 15), (773, 205, 'Aragua de Maturín', 15), (774, 205, 'Aparicio', 15), (775, 205, 'Aragua', 15), (776, 205, 'Chaguamal', 15), (777, 205, 'El', 15), (778, 205, 'Guanaguana', 15), (779, 205, 'La', 15), (780, 205, 'Taguaya', 15), (781, 206, 'Quiriquire', 15), (782, 206, 'Cachipo', 15), (783, 207, 'Santa Bárbara', 15), (784, 207, 'Santa', 15), (785, 208, 'Barrancas del Orinoco', 15), (786, 208, 'Barrancas', 15), (787, 208, 'Los', 15), (788, 209, 'Uracoa', 15), (789, 210, 'Paraguachí', 16), (790, 210, 'Antolín', 16), (791, 211, 'La Asunción', 16), (792, 211, 'Arismendi', 16), (793, 212, 'San Juan Bautista', 16), (794, 212, 'San', 16), (795, 212, 'Zabala', 16), (796, 213, 'El Valle', 16), (797, 213, 'García', 16), (798, 213, 'Francisco', 16), (799, 214, 'Santa Ana', 16), (800, 214, 'Bolívar', 16), (801, 214, 'Guevara', 16), (802, 214, 'Cerro', 16), (803, 214, 'Santa', 16), (804, 214, 'Sucre', 16), (805, 215, 'Pampatar', 16), (806, 215, 'Aguirre', 16), (807, 215, 'Maneiro', 16), (808, 216, 'Juan Griego', 16), (809, 216, 'Adrián', 16), (810, 216, 'Juan', 16), (811, 216, 'Yaguaraparo', 16), (812, 217, 'Porlamar', 16), (813, 218, 'Boca de Río', 16), (814, 218, 'San', 16), (815, 218, 'Boca', 16), (816, 219, 'Punta de Piedras', 16), (817, 219, 'Tubores', 16), (818, 219, 'Los', 16), (819, 220, 'San Pedro de Coche', 16), (820, 220, 'Vicente', 16), (821, 220, 'Villalba', 16), (822, 221, 'Agua Blanca', 17), (823, 221, 'Agua', 17), (824, 222, 'Araure', 17), (825, 222, 'Río', 17), (826, 223, 'Píritu', 17), (827, 223, 'Uveral', 17), (828, 224, 'Guanare', 17), (829, 224, 'Cordova', 17), (830, 224, 'San', 17), (831, 224, 'Virgen', 17), (832, 225, 'Guanarito', 17), (833, 225, 'Trinidad', 17), (834, 225, 'Divina', 17), (835, 226, 'Paraíso de Chabasquén', 17), (836, 226, 'Peña', 17), (837, 227, 'Ospino', 17), (838, 227, 'Aparición', 17), (839, 227, 'La', 17), (840, 228, 'Acarigua', 17), (841, 228, 'Payara', 17), (842, 228, 'Pimpinela', 17), (843, 228, 'Ramón', 17), (844, 229, 'Papelón', 17), (845, 229, 'Caño', 17), (846, 230, 'Boconoíto', 17), (847, 230, 'Antolín', 17), (848, 231, 'San Rafael de Onoto', 17), (849, 231, 'Santa', 17), (850, 231, 'San', 17), (851, 231, 'Thermo', 17), (852, 232, 'El Playón', 17), (853, 232, 'Florida', 17), (854, 232, 'El', 17), (855, 233, 'Biscucuy', 17), (856, 233, 'Concepción', 17), (857, 233, 'San', 17), (858, 233, 'Uvencio', 17), (859, 233, 'Villa', 17), (860, 234, 'Villa Bruzual', 17), (861, 234, 'Canelones', 17), (862, 234, 'Santa', 17), (863, 234, 'San', 17), (864, 235, 'Casanay', 18), (865, 235, 'Mariño', 18), (866, 235, 'Rómulo', 18), (867, 236, 'San José de Aerocuar', 18), (868, 236, 'San', 18), (869, 236, 'Tavera', 18), (870, 237, 'Río Caribe', 18), (871, 237, 'Río', 18), (872, 237, 'Antonio', 18), (873, 237, 'El', 18), (874, 237, 'Puerto', 18), (875, 237, 'San', 18), (876, 238, 'El Pilar', 18), (877, 238, 'El', 18), (878, 238, 'General', 18), (879, 238, 'Guaraúnos', 18), (880, 238, 'Tunapuicito', 18), (881, 238, 'Unión', 18), (882, 239, 'Carúpano', 18), (883, 239, 'Santa', 18), (884, 239, 'Bolívar', 18), (885, 239, 'Maracapana', 18), (886, 240, 'Marigüitar', 18), (887, 241, 'Yaguaraparo', 18), (888, 241, 'Libertad', 18), (889, 241, 'El', 18), (890, 242, 'Araya', 18), (891, 242, 'Cruz', 18), (892, 242, 'Chacopata', 18), (893, 242, 'Manicuare', 18), (894, 243, 'Tunapuy', 18), (895, 243, 'Campo', 18), (896, 244, 'Irapa', 18), (897, 244, 'Campo', 18), (898, 244, 'Maraval', 18), (899, 244, 'San', 18), (900, 244, 'Soro', 18), (901, 245, 'San Antonio del Golfo', 18), (902, 245, 'Mejía', 18), (903, 246, 'Cumanacoa', 18), (904, 246, 'Arenas', 18), (905, 246, 'Aricagua', 18), (906, 246, 'Cocollar', 18), (907, 246, 'San', 18), (908, 247, 'Cariaco', 18), (909, 247, 'Villa', 18), (910, 247, 'Catuaro', 18), (911, 247, 'Rendón', 18), (912, 247, 'San', 18), (913, 247, 'Santa', 18), (914, 248, 'Cumaná', 18), (915, 248, 'Altagracia', 18), (916, 248, 'Santa', 18), (917, 248, 'Valentín', 18), (918, 248, 'Ayacucho', 18), (919, 248, 'San', 18), (920, 248, 'Raúl', 18), (921, 248, 'Gran', 18), (922, 249, 'Güiria', 18), (923, 249, 'Cristóbal', 18), (924, 249, 'Bideau', 18), (925, 249, 'Punta', 18), (926, 250, 'Cordero', 19), (927, 250, 'Andrés', 19), (928, 251, 'Las Mesas', 19), (929, 251, 'Antonio', 19), (930, 252, 'Colón', 19), (931, 252, 'Ayacucho', 19), (932, 252, 'Rivas', 19), (933, 252, 'San', 19), (934, 253, 'San Antonio del Táchira', 19), (935, 253, 'Bolívar', 19), (936, 253, 'Palotal', 19), (937, 253, 'General', 19), (938, 253, 'Isaías', 19), (939, 254, 'Táriba', 19), (940, 254, 'Cárdenas', 19), (941, 254, 'Amenodoro', 19), (942, 254, 'La', 19), (943, 255, 'Santa Ana de Táchira', 19), (944, 255, 'Córdoba', 19), (945, 256, 'San Rafael del Piñal', 19), (946, 256, 'Fernández', 19), (947, 256, 'Alberto', 19), (948, 256, 'Santo', 19), (949, 257, 'San José de Bolívar', 19), (950, 257, 'Francisco', 19), (951, 258, 'La Fría', 19), (952, 258, 'García', 19), (953, 258, 'Boca', 19), (954, 258, 'José', 19), (955, 259, 'Palmira', 19), (956, 259, 'Guásimos', 19), (957, 260, 'Capacho Nuevo', 19), (958, 260, 'Independencia', 19), (959, 260, 'Juan', 19), (960, 260, 'Román', 19), (961, 261, 'La Grita', 19), (962, 261, 'Jáuregui', 19), (963, 261, 'Emilio', 19), (964, 261, 'Monseñor', 19), (965, 262, 'El Cobre', 19), (966, 262, 'José', 19), (967, 263, 'Rubio', 19), (968, 263, 'Junín', 19), (969, 263, 'La', 19), (970, 263, 'Quinimarí', 19), (971, 263, 'Bramón', 19), (972, 264, 'Capacho Viejo', 19), (973, 264, 'Libertad', 19), (974, 264, 'Cipriano', 19), (975, 264, 'Manuel', 19), (976, 265, 'Abejales', 19), (977, 265, 'Capital', 19), (978, 265, 'Doradas', 19), (979, 265, 'Emeterio', 19), (980, 265, 'San', 19), (981, 266, 'Lobatera', 19), (982, 266, 'Constitución', 19), (983, 267, 'Michelena', 19), (984, 268, 'Coloncito', 19), (985, 268, 'Panamericano', 19), (986, 268, 'La', 19), (987, 269, 'Ureña', 19), (988, 269, 'Pedro', 19), (989, 269, 'Nueva', 19), (990, 270, 'Delicias', 19), (991, 270, 'Pecaya', 19), (992, 271, 'La Tendida', 19), (993, 271, 'Samuel', 19), (994, 271, 'Boconó', 19), (995, 271, 'Hernández', 19), (996, 272, 'San Cristóbal', 19), (997, 272, 'La', 19), (998, 272, 'San', 19), (999, 272, 'Pedro', 19), (1000, 272, 'Dr.', 19), (1001, 273, 'Seboruco', 19), (1002, 274, 'San Simón', 19), (1003, 274, 'Simón', 19), (1004, 275, 'Queniquea', 19), (1005, 275, 'Sucre', 19), (1006, 275, 'Eleazar', 19), (1007, 275, 'San', 19), (1008, 276, 'San Josecito', 19), (1009, 276, 'San', 19), (1010, 277, 'Pregonero', 19), (1011, 277, 'Uribante', 19), (1012, 277, 'Cárdenas', 19), (1013, 277, 'Juan', 19), (1014, 277, 'Potosí', 19), (1015, 278, 'Umuquena', 19), (1016, 278, 'San', 19), (1017, 279, 'Santa Isabel', 20), (1018, 279, 'Araguaney', 20), (1019, 279, 'El', 20), (1020, 279, 'La', 20), (1021, 279, 'Santa', 20), (1022, 280, 'Boconó', 20), (1023, 280, 'El', 20), (1024, 280, 'Mosquey', 20), (1025, 280, 'Ayacucho', 20), (1026, 280, 'Burbusay', 20), (1027, 280, 'General', 20), (1028, 280, 'Guaramacal', 20), (1029, 280, 'Vega', 20), (1030, 280, 'Monseñor', 20), (1031, 280, 'Rafael', 20), (1032, 280, 'San', 20), (1033, 281, 'Sabana Grande', 20), (1034, 281, 'Sabana', 20), (1035, 281, 'Cheregüé', 20), (1036, 281, 'Granados', 20), (1037, 282, 'Chejendé', 20), (1038, 282, 'Arnoldo', 20), (1039, 282, 'Bolivia', 20), (1040, 282, 'Carrillo', 20), (1041, 282, 'Cegarra', 20), (1042, 282, 'Manuel', 20), (1043, 282, 'San', 20), (1044, 283, 'Carache', 20), (1045, 283, 'La', 20), (1046, 283, 'Cuicas', 20), (1047, 283, 'Panamericana', 20), (1048, 283, 'Santa', 20), (1049, 284, 'Escuque', 20), (1050, 284, 'La', 20), (1051, 284, 'Santa', 20), (1052, 284, 'Sabana', 20), (1053, 285, 'El Paradero', 20), (1054, 285, 'El', 20), (1055, 285, 'Los', 20), (1056, 285, 'Antonio', 20), (1057, 286, 'Campo Elías', 20), (1058, 286, 'Campo', 20), (1059, 286, 'Arnoldo', 20), (1060, 287, 'Santa Apolonia', 20), (1061, 287, 'Santa', 20), (1062, 287, 'El', 20), (1063, 287, 'La', 20), (1064, 287, 'Tres', 20), (1065, 288, 'El Dividive', 20), (1066, 288, 'El', 20), (1067, 288, 'Agua', 20), (1068, 288, 'Valerita', 20), (1069, 289, 'Monte Carmelo', 20), (1070, 289, 'Monte', 20), (1071, 289, 'Buena', 20), (1072, 289, 'Santa', 20), (1073, 290, 'Motatán', 20), (1074, 290, 'El', 20), (1075, 290, 'Jalisco', 20), (1076, 291, 'Pampán', 20), (1077, 291, 'Flor', 20), (1078, 291, 'La', 20), (1079, 291, 'Santa', 20), (1080, 292, 'Pampanito', 20), (1081, 292, 'La', 20), (1082, 293, 'Betijoque', 20), (1083, 293, 'José', 20), (1084, 293, 'La', 20), (1085, 293, 'Los', 20), (1086, 294, 'Carvajal', 20), (1087, 294, 'Campo', 20), (1088, 294, 'Antonio', 20), (1089, 294, 'José', 20), (1090, 295, 'Sabana de Mendoza', 20), (1091, 295, 'Sabana', 20), (1092, 295, 'Junín', 20), (1093, 295, 'Valmore', 20), (1094, 295, 'El', 20), (1095, 296, 'Trujillo', 20), (1096, 296, 'Andrés', 20), (1097, 296, 'Chiquinquirá', 20), (1098, 296, 'Cristóbal', 20), (1099, 296, 'Cruz', 20), (1100, 296, 'Matriz', 20), (1101, 296, 'Monseñor', 20), (1102, 296, 'Tres', 20), (1103, 297, 'La Quebrada', 20), (1104, 297, 'Cabimbú', 20), (1105, 297, 'Jajó', 20), (1106, 297, 'La', 20), (1107, 297, 'Santiago', 20), (1108, 297, 'Tuñame', 20), (1109, 298, 'Valera', 20), (1110, 298, 'Juan', 20), (1111, 298, 'La', 20), (1112, 298, 'Mendoza', 20), (1113, 298, 'Mercedes', 20), (1114, 298, 'San', 20), (1115, 299, 'Vargas', 21), (1116, 299, 'Caraballeda', 21), (1117, 299, 'Carayaca', 21), (1118, 299, 'Carlos', 21), (1119, 299, 'Caruao', 21), (1120, 299, 'Catia', 21), (1121, 299, 'El', 21), (1122, 299, 'La', 21), (1123, 299, 'Macuto', 21), (1124, 299, 'Maiquetía', 21), (1125, 299, 'Naiguatá', 21), (1126, 299, 'Urimare', 21), (1127, 300, 'San Pablo', 22), (1128, 300, 'Arístides', 22), (1129, 301, 'Aroa', 22), (1130, 301, 'Bolívar', 22), (1131, 302, 'Chivacoa', 22), (1132, 302, 'Campo', 22), (1133, 303, 'Cocorote', 22), (1134, 304, 'Independencia', 22), (1135, 305, 'Sabana de Parra', 22), (1136, 305, 'José', 22), (1137, 306, 'Boraure', 22), (1138, 306, 'La', 22), (1139, 307, 'Yumare', 22), (1140, 307, 'Manuel', 22), (1141, 308, 'Nirgua', 22), (1142, 308, 'Salóm', 22), (1143, 308, 'Temerla', 22), (1144, 309, 'Yaritagua', 22), (1145, 309, 'San', 22), (1146, 310, 'San Felipe', 22), (1147, 310, 'San', 22), (1148, 310, 'Albarico', 22), (1149, 311, 'Guama', 22), (1150, 311, 'Sucre', 22), (1151, 312, 'Urachiche', 22), (1152, 313, 'Farriar', 22), (1153, 313, 'El', 22), (1154, 314, 'El Toro', 23), (1155, 314, 'Isla', 23), (1156, 314, 'Monagas', 23), (1157, 315, 'San Timoteo', 23), (1158, 315, 'General', 23), (1159, 315, 'Libertador', 23), (1160, 315, 'Marcelino', 23), (1161, 315, 'Pueblo', 23), (1162, 315, 'Manuel', 23), (1163, 316, 'Cabimas', 23), (1164, 316, 'Ambrosio', 23), (1165, 316, 'Carmen', 23), (1166, 316, 'La', 23), (1167, 316, 'Germán', 23), (1168, 316, 'San', 23), (1169, 316, 'Rómulo', 23), (1170, 316, 'Jorge', 23), (1171, 316, 'Punta', 23), (1172, 316, 'Arístides', 23), (1173, 317, 'Encontrados', 23), (1174, 317, 'Udón', 23), (1175, 318, 'San Carlos del Zulia', 23), (1176, 318, 'Moralito', 23), (1177, 318, 'San', 23), (1178, 318, 'Santa', 23), (1179, 318, 'Urribarrí', 23), (1180, 319, 'Pueblo Nuevo - El Chivo', 23), (1181, 319, 'Carlos', 23), (1182, 319, 'Francisco', 23), (1183, 319, 'Simón', 23), (1184, 319, 'Guamo-Gavilanes', 23), (1185, 320, 'Sinamaica', 23), (1186, 320, 'Alta', 23), (1187, 320, 'Elías', 23), (1188, 320, 'Guajira', 23), (1189, 321, 'La Concepción', 23), (1190, 321, 'La', 23), (1191, 321, 'San', 23), (1192, 321, 'Mariano', 23), (1193, 321, 'José', 23), (1194, 322, 'Casigua - El Cubo', 23), (1195, 322, 'Jesús', 23), (1196, 322, 'Barí', 23), (1197, 323, 'La Concepción', 23), (1198, 323, 'Concepción', 23), (1199, 323, 'Andrés', 23), (1200, 323, 'Chiquinquirá', 23), (1201, 323, 'El', 23), (1202, 323, 'Potreritos', 23), (1203, 324, 'Ciudad Ojeda', 23), (1204, 324, 'Libertad', 23), (1205, 324, 'Alonso', 23), (1206, 324, 'Venezuela', 23), (1207, 324, 'Eleazar', 23), (1208, 324, 'Campo', 23), (1209, 325, 'Machiques', 23), (1210, 325, 'Bartolomé', 23), (1211, 325, 'Libertad', 23), (1212, 325, 'Río', 23), (1213, 325, 'San', 23), (1214, 326, 'San Rafael de El Moján', 23), (1215, 326, 'San', 23), (1216, 326, 'La', 23), (1217, 326, 'Las', 23), (1218, 326, 'Luis', 23), (1219, 326, 'Monseñor', 23), (1220, 326, 'Ricaurte', 23), (1221, 326, 'Tamare', 23), (1222, 327, 'Maracaibo', 23), (1223, 327, 'Antonio', 23), (1224, 327, 'Bolívar', 23), (1225, 327, 'Cacique', 23), (1226, 327, 'Carracciolo', 23), (1227, 327, 'Cecilio', 23), (1228, 327, 'Cristo', 23), (1229, 327, 'Coquivacoa', 23), (1230, 327, 'Chiquinquirá', 23), (1231, 327, 'Francisco', 23), (1232, 327, 'Idelfonzo', 23), (1233, 327, 'Juana', 23), (1234, 327, 'Luis', 23), (1235, 327, 'Manuel', 23), (1236, 327, 'Olegario', 23), (1237, 327, 'Raúl', 23), (1238, 327, 'Santa', 23), (1239, 327, 'Venancio', 23), (1240, 327, 'San', 23), (1241, 328, 'Los Puertos de Altagracia', 23), (1242, 328, 'Altagracia', 23), (1243, 328, 'Faría', 23), (1244, 328, 'Ana', 23), (1245, 328, 'San', 23), (1246, 329, 'La Villa del Rosario', 23), (1247, 329, 'Donaldo', 23), (1248, 329, 'El', 23), (1249, 329, 'Sixto', 23), (1250, 330, 'San Francisco', 23), (1251, 330, 'San', 23), (1252, 330, 'El', 23), (1253, 330, 'Domitila', 23), (1254, 330, 'Francisco', 23), (1255, 330, 'Los', 23), (1256, 330, 'Marcial', 23), (1257, 331, 'Santa Rita', 23), (1258, 331, 'Santa', 23), (1259, 331, 'El', 23), (1260, 331, 'Pedro', 23), (1261, 331, 'José', 23), (1262, 332, 'Tía Juana', 23), (1263, 332, 'Rafael', 23), (1264, 332, 'Manuel', 23), (1265, 333, 'Bobures', 23), (1266, 333, 'Gibraltar', 23), (1267, 333, 'Heras', 23), (1268, 333, 'Monseñor', 23), (1269, 333, 'Rómulo', 23), (1270, 333, 'El', 23), (1271, 334, 'Bachaquero', 23), (1272, 334, 'Rafael', 23), (1273, 334, 'La', 23), (1274, 334, 'Raúl', 23), (1275, 335, 'Caracas', 24), (1276, 335, '23', 24), (1277, 335, 'Altagracia', 24), (1278, 335, 'Antímano', 24), (1279, 335, 'Caricuao', 24), (1280, 335, 'Catedral', 24), (1281, 335, 'Coche', 24), (1282, 335, 'El', 24), (1283, 335, 'Candelaria', 24), (1284, 335, 'La', 24), (1285, 335, 'Macarao', 24), (1286, 335, 'San', 24), (1287, 335, 'Santa', 24), (1288, 335, 'Sucre', 24);

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
  ADD KEY `fk_medida` (`id_medida`),
  ADD KEY `fk_pres` (`presentacion`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de la tabla `estado`
--
ALTER TABLE `estado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `estatus`
--
ALTER TABLE `estatus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `linea_producto`
--
ALTER TABLE `linea_producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de la tabla `modulo`
--
ALTER TABLE `modulo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `municipio`
--
ALTER TABLE `municipio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=336;

--
-- AUTO_INCREMENT de la tabla `parroquia`
--
ALTER TABLE `parroquia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1289;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `personal`
--
ALTER TABLE `personal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `presentacion`
--
ALTER TABLE `presentacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `ID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `subcategoria`
--
ALTER TABLE `subcategoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de la tabla `tipo_personal`
--
ALTER TABLE `tipo_personal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `unidad_medida`
--
ALTER TABLE `unidad_medida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  ADD CONSTRAINT `fk_medida` FOREIGN KEY (`id_medida`) REFERENCES `unidad_medida` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pres` FOREIGN KEY (`presentacion`) REFERENCES `presentacion` (`id`) ON UPDATE CASCADE;

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

