-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-10-2026 a las 00:15:59
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
-- Base de datos: `login_register_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividad_usuarios`
--

CREATE TABLE `actividad_usuarios` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `tipo_actividad` varchar(50) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `actividad_usuarios`
--

INSERT INTO `actividad_usuarios` (`id`, `usuario_id`, `tipo_actividad`, `descripcion`, `fecha`) VALUES
(1, NULL, 'LOGIN', 'Inicio de sesión en la plataforma', '2026-09-25 13:28:22'),
(2, NULL, 'LOGIN', 'Inicio de sesión en la plataforma', '2026-09-25 13:29:35'),
(3, NULL, 'COMPRA', 'Realizó la compra del Pedido #8 por $0.00', '2026-09-25 13:30:15'),
(4, 22, 'LOGIN', 'Inicio de sesión en la plataforma', '2026-09-25 14:23:19'),
(5, NULL, 'COMPRA', 'Realizó la compra del Pedido #9 por $0.00', '2026-09-25 14:23:55'),
(6, 22, 'LOGIN', 'Inicio de sesión en la plataforma', '2026-09-25 14:38:42'),
(7, 22, 'LOGIN', 'Inicio de sesión en la plataforma', '2026-09-25 14:45:59'),
(8, 22, 'LOGIN', 'Inicio de sesión en la plataforma', '2026-09-25 14:48:26'),
(9, 22, 'LOGIN', 'Inicio de sesión en la plataforma', '2026-09-25 14:57:02'),
(10, 22, 'LOGIN', 'Inicio de sesión en la plataforma', '2026-09-25 15:08:14'),
(11, 22, 'LOGIN', 'Inicio de sesión en la plataforma', '2026-09-25 15:09:41'),
(12, 22, 'LOGIN', 'Inicio de sesión en la plataforma', '2026-09-25 15:12:30'),
(13, 22, 'LOGIN', 'Inicio de sesión en la plataforma', '2026-09-25 15:19:30'),
(14, 22, 'LOGIN', 'Inicio de sesión en la plataforma', '2026-09-25 15:25:32'),
(15, 22, 'LOGIN', 'Inicio de sesión en la plataforma', '2026-09-25 15:37:48'),
(16, 22, 'LOGIN', 'Inicio de sesión en la plataforma', '2026-09-25 15:41:48'),
(17, NULL, 'COMPRA', 'Realizó la compra del Pedido #10 por $0.00', '2026-09-25 15:41:56'),
(18, 22, 'LOGIN', 'Inicio de sesión en la plataforma', '2026-09-25 15:48:11'),
(19, 22, 'COMPRA', 'Realizó la compra del Pedido #11 por $70.49', '2026-09-25 15:48:27'),
(20, 27, 'LOGIN', 'Inicio de sesión en la plataforma', '2026-09-25 15:50:03'),
(21, 27, 'COMPRA', 'Realizó la compra del Pedido #12 por $946.00', '2026-09-25 15:50:18'),
(22, 22, 'LOGIN', 'Inicio de sesión en la plataforma', '2026-09-25 16:02:16'),
(23, 22, 'LOGIN', 'Inicio de sesión en la plataforma', '2026-09-25 16:07:22'),
(24, 22, 'LOGIN', 'Inicio de sesión en la plataforma', '2026-09-25 16:27:04'),
(25, 22, 'LOGIN', 'Inicio de sesión en la plataforma', '2026-09-25 16:46:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividad_visitantes`
--

CREATE TABLE `actividad_visitantes` (
  `id` int(11) NOT NULL,
  `token_visitante` varchar(64) NOT NULL,
  `libro_id` int(11) DEFAULT NULL,
  `accion` varchar(50) NOT NULL COMMENT 'ver_preview, intento_favoritos, intento_carrito, explorar_catalogo',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores`
--

CREATE TABLE `administradores` (
  `id` int(11) NOT NULL,
  `nombre_completo` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `cargo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id`, `nombre_completo`, `email`, `usuario`, `password`, `cargo`) VALUES
(1, 'Carlos Alberto Ruiz', 'carlos.ruiz@aurora.com', 'cruiz_admin', 'c7ad4411ac917c4461d00c04869ad61112fe5552a4b35b1285038084a7d6e42b8214f5261175e52c875d9c2f6d0a7a3048967919869687483664722889260655', 'Gerente General'),
(2, 'Elena Sofía Mendoza', 'elena.mendoza@aurora.com', 'emendoza_lib', '39690180905e940176378e99443657662c5b3695270275811796f7c70c18d18b2c608f65422894371465e9271649938b814df22722f462a74c7cf4c93f0b2401', 'Bibliotecaria Principal'),
(3, 'Jorge Luis Herrera', 'jorge.herrera@aurora.com', 'jherrera_sys', '44b933662584984534a65a6e0c657a87588373b9830598851f50e82f7c089279568779603527a2066c61f2f01f464a2f8b5a0374e50d5e16548f08f877f0a7d5', 'Administrador de Sistemas'),
(4, 'Lucía Fernanda Gómez', 'lucia.gomez@aurora.com', 'lgomez_inv', 'e6c1e57c63df33a2d36f1c484f9b88cf2925a1762c47672221b6a7e58a25c6171d17d12226226164282862d5f66292261e416246e6264d6262422e6261622262', 'Jefa de Inventario'),
(5, 'Roberto Ignacio Díaz', 'roberto.diaz@aurora.com', 'rdiaz_ventas', 'b3671236894562c2f6236b367d36a362368d3632616b364236a36236462c36236b3672364236b3626136d36e36b36236b36b36a36236162362368d36b367236f', 'Supervisor de Ventas'),
(6, '432', '432', '432', 'bb6c3a3202ec74557dd754859d992e69a6825c5bb5ea968c288350a08094c8304f4f273b164f805610d08f008c802885ae4431fb9bde5fe2481c38bfd9a039b4', '432');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `libro_id` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT 1,
  `fecha_agregado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedidos`
--

CREATE TABLE `detalle_pedidos` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `libro_id` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_pedidos`
--

INSERT INTO `detalle_pedidos` (`id`, `pedido_id`, `libro_id`, `precio_unitario`, `cantidad`) VALUES
(1, 1, 3, 12.99, 1),
(2, 1, 17, 1000000.00, 1),
(3, 2, 7, 15.00, 1),
(4, 2, 18, 666.00, 1),
(5, 2, 19, 80.00, 1),
(6, 3, 19, 80.00, 1),
(7, 3, 19, 80.00, 1),
(8, 3, 19, 80.00, 1),
(9, 3, 19, 80.00, 1),
(10, 4, 3, 12.99, 1),
(11, 4, 3, 12.99, 1),
(12, 4, 3, 12.99, 1),
(13, 4, 3, 12.99, 1),
(14, 4, 3, 12.99, 1),
(15, 5, 21, 200.00, 1),
(16, 5, 18, 666.00, 1),
(17, 5, 18, 666.00, 1),
(18, 5, 7, 15.00, 1),
(19, 6, 6, 19.90, 1),
(20, 7, 7, 15.00, 1),
(21, 7, 18, 666.00, 1),
(22, 7, 18, 666.00, 1),
(23, 7, 19, 80.00, 1),
(24, 7, 19, 80.00, 1),
(25, 8, 21, 200.00, 1),
(26, 8, 18, 666.00, 1),
(27, 9, 1, 25.50, 1),
(28, 9, 3, 12.99, 1),
(29, 9, 4, 28.00, 1),
(30, 9, 6, 19.90, 1),
(31, 9, 17, 1000000.00, 1),
(32, 10, 9, 24.00, 1),
(33, 10, 2, 32.00, 1),
(34, 11, 1, 25.50, 1),
(35, 11, 2, 32.00, 1),
(36, 11, 3, 12.99, 1),
(37, 12, 21, 200.00, 1),
(38, 12, 19, 80.00, 1),
(39, 12, 18, 666.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `favoritos`
--

CREATE TABLE `favoritos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `libro_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `favoritos`
--

INSERT INTO `favoritos` (`id`, `usuario_id`, `libro_id`) VALUES
(24, 22, 1),
(29, 22, 3),
(30, 22, 4),
(26, 22, 6),
(14, 22, 17),
(27, 22, 18),
(18, 22, 19),
(4, 23, 10),
(16, 23, 18),
(33, 31, 2),
(32, 31, 18),
(31, 31, 21);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libros`
--

CREATE TABLE `libros` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `stock` int(11) DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`id`, `titulo`, `author`, `category`, `price`, `image`, `description`, `stock`) VALUES
(1, 'Cien años de soledad', 'Gabriel García Márquez', 'Literatura', 25.50, '1789759277.jpg', 'La épica historia de la familia Buendía en el remoto pueblo de Macondo.', 15),
(2, 'Sapiens: De animales a dioses', 'Yuval Noah Harari', 'Académicos', 32.00, '1789759259.jpg', 'Un recorrido por toda la historia de la humanidad, desde los primeros humanos hasta hoy.', 10),
(3, 'El Principito', 'Antoine de Saint-Exupéry', 'Infantiles', 12.99, '1789759238.jpg', 'Un piloto perdido en el desierto encuentra a un pequeño príncipe de otro planeta.', 20),
(4, 'Dune', 'Frank Herbert', 'Ciencia Ficción', 28.00, '1789759220.png', 'En el planeta desértico Arrakis, comienza la lucha por el control de la especia.', 12),
(5, 'El Resplandor', 'Stephen King', 'Misterio', 22.50, '1789759201.png', 'Un hombre y su familia se quedan aislados en un hotel con un pasado oscuro y sangriento.', 8),
(6, 'Hábitos Atómicos', 'James Clear', 'Literatura', 19.90, '1789759171.png', 'Una guía sencilla para crear buenos hábitos y romper los malos mediante pequeños cambios.', 25),
(7, 'Sin nombre', 'George Orwell', 'Literatura', 15.00, '1789759157.jpeg', 'Una visión aterradora de un mundo donde el Gran Hermano te vigila en todo momento.', 30),
(8, 'Breve historia del tiempo', 'Stephen Hawking', 'Académicos', 18.00, '1789759120.png', 'El científico más famoso explica los misterios del universo y el tiempo.', 7),
(9, 'El Código Da Vinci', 'Dan Brown', 'Misterio', 24.00, '1789759100.webp', 'Un asesinato en el Louvre revela una conspiración que ha estado oculta por siglos.', 5),
(10, 'Historia de la Belleza', 'Umberto Eco', 'Misterio', 99.00, '1774586064.png', 'Un análisis profundo sobre cómo ha cambiado el concepto de lo bello a través de la historia.', 3),
(17, 'LIBRO3', 'Spellbook', 'Académicos', 1000000.00, '1789759086.webp', 'descripción cualquiera abc', 9),
(18, 'LIBRO2', 'sin autor', 'Literatura', 666.00, '1774583117.png', 'zxczxc', 6),
(19, 'LIBRO1', 'Endymion', 'Literatura', 80.00, '1774586267.png', 'Mago de la profecía ', 4),
(21, 'cualquiera', '200', 'Literatura', 200.00, '1789759051.jpg', 'descripcion', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `total`, `fecha`) VALUES
(1, 22, 1000012.99, '2026-03-27 10:00:03'),
(2, 22, 761.00, '2026-03-27 10:17:27'),
(3, 23, 320.00, '2026-03-27 10:46:04'),
(4, 22, 64.95, '2026-03-27 10:57:24'),
(5, 22, 1547.00, '2026-03-27 16:53:31'),
(6, 22, 19.90, '2026-04-26 15:55:06'),
(7, 22, 1507.00, '2026-09-25 12:17:01'),
(8, 31, 866.00, '2026-09-25 13:30:15'),
(9, 22, 1000086.39, '2026-09-25 14:23:55'),
(10, 22, 56.00, '2026-09-25 15:41:56'),
(11, 22, 70.49, '2026-09-25 15:48:27'),
(12, 27, 946.00, '2026-09-25 15:50:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre_completo` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_completo`, `email`, `usuario`, `password`) VALUES
(22, '123', '123', '123', '$2y$10$vLQg8e4SF8XiFxHlbPg0ZekWCgYogpGANoI5LcReyNdcTySgmzbzW'),
(23, '456', '456', '456', 'f6b07b6c1340e947b861def5f8b092d8ee710826dc56bd175bdc8f3a16b0b8acf853c64786a710dedf9d1524d61e32504e27d60de159af110bc3941490731578'),
(27, 'davidmartinez', 'juan@martinez', 'jdmartz', '$2y$10$oh336NFCJIe935cHVNGWSesXOe0GRBCJPzxlvufu8PNdQMmRgPJTy'),
(28, 'david martinez', 'david@david', 'jdmaral0001', '$2y$10$lSrPTZI4cu9vHNwzbUOlq.2qbd0DiR64RS8Dym..6ppd1MiOh3EJC'),
(30, 'prueba1', 'brueba1@correo', 'brueba1', '$2y$10$9jweaJyhC.fwnt9jjp2tMunQtHufRIpVWNoHje2F7.H3SB0xxTcUy'),
(31, 'juan2', 'juan2', 'juan2', '$2y$10$nbPtgBIjiqPSnYnTAROF1OjPO8bCa/a6M7ghNRxKvSyS0NO5vd9ia');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividad_usuarios`
--
ALTER TABLE `actividad_usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_actividad_usuario` (`usuario_id`);

--
-- Indices de la tabla `actividad_visitantes`
--
ALTER TABLE `actividad_visitantes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_visitante` (`token_visitante`),
  ADD KEY `idx_libro` (`libro_id`),
  ADD KEY `idx_accion` (`accion`);

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `libro_id` (`libro_id`);

--
-- Indices de la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `libro_id` (`libro_id`);

--
-- Indices de la tabla `favoritos`
--
ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`,`libro_id`),
  ADD KEY `libro_id` (`libro_id`);

--
-- Indices de la tabla `libros`
--
ALTER TABLE `libros`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividad_usuarios`
--
ALTER TABLE `actividad_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `actividad_visitantes`
--
ALTER TABLE `actividad_visitantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT de la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `favoritos`
--
ALTER TABLE `favoritos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `libros`
--
ALTER TABLE `libros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividad_usuarios`
--
ALTER TABLE `actividad_usuarios`
  ADD CONSTRAINT `fk_actividad_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carrito_ibfk_2` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  ADD CONSTRAINT `detalle_pedidos_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_pedidos_ibfk_2` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`);

--
-- Filtros para la tabla `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favoritos_ibfk_2` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
