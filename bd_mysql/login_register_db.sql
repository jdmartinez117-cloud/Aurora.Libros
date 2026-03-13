-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-03-2026 a las 21:30:26
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
(14, 'erik', 'erik@gmail.com', 'erik123', 'faec748b2218a84f0d0cc3e5000657980b1a0586e7c0e71d3035c28bd5df1bf119ad6b8dd1dfbeeeb617914b73a4a857e52af3e84d8ee68a36156d1b0081b178'),
(15, 'juan', 'juan12345@hotmail.com', 'juan12345', '3881e09b2813de65d79f4bb2fea7fd1ff44d503ac878ad090a72f1d0d78c872afc732f165c4f90ed5c94e94fb4301ddb6a757e248fe9e7d3c56feb30fd3791ee'),
(16, 'Brandon Lozada Cárdenas', 'brandy@gmail.com', 'Brandon69', '4856345117e74c707f770fe22da6e3e80c0f60c3c63abf2be797e9ce2e98c3c2eea8fd466627e6e87992811f537623e9aa0b4f703f458c9c0edf45965ec64ed9'),
(17, '', '', '', 'cf83e1357eefb8bdf1542850d66d8007d620e4050b5715dc83f4a921d36ce9ce47d0d13c5d85f2b0ff8318d2877eec2f63b931bd47417a81a538327af927da3e'),
(18, 'pedro pascal', 'pedreo12@qwerty.com', 'pedro666', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413'),
(19, 'yeison brodi', 'drodipequerman@qwerty.com', 'brodi343', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413'),
(20, 'juan lopez', 'yabadaba@qwerty.com', 'pedropica', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413'),
(21, 'jonatan martinez', 'jonatanmartinez343@gmail.com', 'admin', '7a79054fd0e33b1a00085881a334f69654a38486f6d583200241286824c12689857b55a15f572a6b92cb3377b248a3e1c80d70da19853a53f4b6279dfdced076');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
