-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 17/01/2026 às 23:44
-- Versão do servidor: 11.8.3-MariaDB-log
-- Versão do PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `u326160813_escalas2`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `route` varchar(255) NOT NULL,
  `bus` varchar(100) NOT NULL,
  `driver` varchar(150) NOT NULL,
  `departure` time NOT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Despejando dados para a tabela `schedules`
--

INSERT INTO `schedules` (`id`, `date`, `route`, `bus`, `driver`, `departure`, `whatsapp`, `notes`) VALUES
(78, '2026-01-03', 'Palmas/TO 🔁 Araguaína/TO', '1010', 'Beto', '08:00:00', NULL, NULL),
(79, '2026-01-03', 'Palmas/TO 🔁 Araguaína/TO', '1100', 'Danilo', '09:00:00', NULL, NULL),
(80, '2026-01-03', 'Palmas/TO 🔁 Araguaína/TO', '1030', 'Jackson', '22:30:00', NULL, NULL),
(81, '2026-01-04', 'Palmas/TO 🔁 Araguaína/TO', '1010', 'Natanael', '08:00:00', NULL, NULL),
(82, '2026-01-04', 'Palmas/TO 🔁 Araguaína/TO', '1090', 'Iago', '08:30:00', NULL, NULL),
(83, '2026-01-04', 'Palmas/TO 🔁 Araguaína/TO', '1100', 'Rivelino', '09:00:00', NULL, NULL),
(84, '2026-01-04', 'Palmas/TO 🔁 Araguaína/TO', '1260', 'Torres', '21:30:00', NULL, NULL),
(85, '2026-01-04', 'Palmas/TO 🔁 Araguaína/TO', '1030', 'Ricardo', '22:30:00', NULL, NULL),
(86, '2026-01-05', 'Palmas/TO 🔁 Araguaína/TO', '1010', 'Renilton', '08:00:00', NULL, NULL),
(87, '2026-01-05', 'Palmas/TO 🔁 Araguaína/TO', '1090', 'Beto', '08:30:00', NULL, NULL),
(88, '2026-01-05', 'Palmas/TO 🔁 Araguaína/TO', '1100', 'Danilo', '09:00:00', NULL, NULL),
(89, '2026-01-05', 'Palmas/TO 🔁 Filadélfia/TO', '1250', 'Josué', '10:30:00', NULL, NULL),
(90, '2026-01-05', 'Palmas/TO 🔁 Araguaína/TO', '1260', 'Irislando', '21:30:00', NULL, NULL),
(91, '2026-01-05', 'Palmas/TO 🔁 Araguaína/TO', '1030', 'Jackson', '22:30:00', NULL, NULL),
(92, '2026-01-06', 'Palmas/TO 🔁 Araguaína/TO', '1010', 'Natanael', '08:00:00', NULL, NULL),
(93, '2026-01-06', 'Palmas/TO 🔁 Araguaína/TO', '1090', 'Iago', '08:30:00', NULL, NULL),
(94, '2026-01-06', 'Palmas/TO 🔁 Araguaína/TO', '1100', 'Rivelino', '09:00:00', NULL, NULL),
(95, '2026-01-06', 'Palmas/TO 🔁 Araguaína/TO', '1260', 'Torres', '21:30:00', NULL, NULL),
(96, '2026-01-06', 'Palmas/TO 🔁 Araguaína/TO', '1030', 'Ricardo', '22:30:00', NULL, NULL),
(97, '2026-01-07', 'Palmas/TO 🔁 Araguaína/TO', '1010', 'Renilton', '08:00:00', NULL, NULL),
(98, '2026-01-07', 'Palmas/TO 🔁 Araguaína/TO', '1090', 'Beto', '08:30:00', NULL, NULL),
(99, '2026-01-07', 'Palmas/TO 🔁 Araguaína/TO', '1100', 'Danilo', '09:00:00', NULL, NULL),
(100, '2026-01-07', 'Palmas/TO 🔁 Filadélfia/TO', '1250', 'Josué', '10:30:00', NULL, NULL),
(101, '2026-01-07', 'Palmas/TO 🔁 Araguaína/TO', '1260', 'Irislando', '21:30:00', NULL, NULL),
(102, '2026-01-07', 'Palmas/TO 🔁 Araguaína/TO', '1030', 'Jackson', '22:30:00', NULL, NULL),
(103, '2026-01-08', 'Palmas/TO 🔁 Araguaína/TO', '1010', 'Natanael', '08:00:00', NULL, NULL),
(104, '2026-01-08', 'Palmas/TO 🔁 Araguaína/TO', '1090', 'Iago', '08:30:00', NULL, NULL),
(105, '2026-01-08', 'Palmas/TO 🔁 Araguaína/TO', '1100', 'Rivelino', '09:00:00', NULL, NULL),
(106, '2026-01-08', 'Palmas/TO 🔁 Araguaína/TO', '1260', 'Torres', '21:30:00', NULL, NULL),
(107, '2026-01-08', 'Palmas/TO 🔁 Araguaína/TO', '1030', 'Ricardo', '22:30:00', NULL, NULL),
(108, '2026-01-09', 'Palmas/TO 🔁 Araguaína/TO', '1010', 'Renilton', '08:00:00', NULL, NULL),
(109, '2026-01-09', 'Palmas/TO 🔁 Araguaína/TO', '1090', 'Beto', '08:30:00', NULL, NULL),
(110, '2026-01-09', 'Palmas/TO 🔁 Araguaína/TO', '1100', 'Danilo', '09:00:00', NULL, NULL),
(111, '2026-01-09', 'Palmas/TO 🔁 Filadélfia/TO', '1250', 'Josué', '10:30:00', NULL, NULL),
(112, '2026-01-09', 'Palmas/TO 🔁 Araguaína/TO', '1260', 'Irislando', '21:30:00', NULL, NULL),
(113, '2026-01-09', 'Palmas/TO 🔁 Araguaína/TO', '1030', 'Jackson', '22:30:00', NULL, NULL),
(114, '2026-01-10', 'Palmas/TO 🔁 Araguaína/TO', '1010', 'Natanael', '08:00:00', NULL, NULL),
(115, '2026-01-10', 'Palmas/TO 🔁 Araguaína/TO', '1100', 'Rivelino', '09:00:00', NULL, NULL),
(116, '2026-01-10', 'Palmas/TO 🔁 Araguaína/TO', '1030', 'Antonio', '22:30:00', NULL, NULL),
(118, '2026-01-11', 'Palmas/TO 🔁 Araguaína/TO', '1090', 'Iago', '08:30:00', NULL, NULL),
(119, '2026-01-11', 'Palmas/TO 🔁 Araguaína/TO', '1100', 'Danilo', '09:00:00', NULL, NULL),
(120, '2026-01-11', 'Palmas/TO 🔁 Araguaína/TO', '1260', 'Torres', '21:30:00', NULL, NULL),
(121, '2026-01-11', 'Palmas/TO 🔁 Araguaína/TO', '1030', 'Ricardo', '22:30:00', NULL, NULL),
(132, '2026-01-12', 'Palmas/TO 🔁 Araguaína/TO', '1070', 'Mario', '08:30:00', '', ''),
(134, '2026-01-12', 'Palmas/TO 🔁 Araguaína/TO', '1100', 'Rivelino', '09:00:00', '', ''),
(135, '2026-01-12', 'Palmas/TO 🔁 Filadélfia/TO', '1080', 'Irislando', '10:30:00', '', ''),
(136, '2026-01-12', 'Palmas/TO 🔁 Araguaína/TO', '1260', 'Josué', '21:30:00', '', ''),
(137, '2026-01-12', 'Palmas/TO 🔁 Araguaína/TO', '1030', 'Jackson', '22:30:00', '', ''),
(142, '2026-01-12', 'Palmas/TO 🔁 Araguaína/TO', '1010', 'Natanael', '08:00:00', '', ''),
(145, '2026-01-11', 'Palmas/TO 🔁 Araguaína/TO', '1010', 'Renilton', '08:00:00', '', ''),
(149, '2026-01-13', 'Palmas/TO 🔁 Araguaína/TO', '1010', 'Renilton', '08:00:00', '', ''),
(150, '2026-01-13', 'Palmas/TO 🔁 Araguaína/TO', '1070', 'Beto', '08:30:00', '', ''),
(151, '2026-01-13', 'Palmas/TO 🔁 Araguaína/TO', '1100', 'Danilo', '09:00:00', '', ''),
(152, '2026-01-13', 'Palmas/TO 🔁 Araguaína/TO', '1260', 'Torres', '21:30:00', '', ''),
(153, '2026-01-13', 'Palmas/TO 🔁 Araguaína/TO', '1030', 'Ricardo', '22:30:00', '', ''),
(154, '2026-01-14', 'Palmas/TO 🔁 Araguaína/TO', '1010', 'Natanael', '08:00:00', '', ''),
(155, '2026-01-14', 'Palmas/TO 🔁 Araguaína/TO', '1070', 'Iago', '08:30:00', '', ''),
(156, '2026-01-14', 'Palmas/TO 🔁 Araguaína/TO', '1100', 'Rivelino', '09:00:00', '', ''),
(157, '2026-01-14', 'Palmas/TO 🔁 Filadélfia/TO', '1080', 'Irislando', '10:30:00', '', ''),
(158, '2026-01-14', 'Palmas/TO 🔁 Araguaína/TO', '1260', 'Josué', '21:30:00', '', ''),
(159, '2026-01-14', 'Palmas/TO 🔁 Araguaína/TO', '1030', 'Jackson', '22:30:00', '', ''),
(160, '2026-01-15', 'Palmas/TO 🔁 Araguaína/TO', '1010', 'Renilton', '08:00:00', '', ''),
(161, '2026-01-15', 'Palmas/TO 🔁 Araguaína/TO', '1070', 'Beto', '08:30:00', '', ''),
(162, '2026-01-15', 'Palmas/TO 🔁 Araguaína/TO', '1100', 'Danilo', '09:00:00', '', ''),
(163, '2026-01-15', 'Palmas/TO 🔁 Araguaína/TO', '1090', 'Torres', '21:30:00', '', ''),
(164, '2026-01-15', 'Palmas/TO 🔁 Araguaína/TO', '1030', 'Ricardo', '22:30:00', '', ''),
(165, '2026-01-16', 'Palmas/TO 🔁 Araguaína/TO', '1010', 'Natanael', '08:00:00', '', ''),
(166, '2026-01-16', 'Palmas/TO 🔁 Araguaína/TO', '1070', 'Iago', '08:30:00', '', ''),
(167, '2026-01-16', 'Palmas/TO 🔁 Araguaína/TO', '1100', 'Rivelino', '09:00:00', '', ''),
(168, '2026-01-16', 'Palmas/TO 🔁 Araguaína/TO', '1260', 'Josué', '21:30:00', '', ''),
(169, '2026-01-16', 'Palmas/TO 🔁 Araguaína/TO', '1030', 'Mario', '22:30:00', '', ''),
(170, '2026-01-16', 'Palmas/TO 🔁 Filadélfia/TO', '1080', 'Irislando', '10:30:00', '', ''),
(171, '2026-01-17', 'Palmas/TO 🔁 Araguaína/TO', '1010', 'Renilton', '08:00:00', '', ''),
(172, '2026-01-17', 'Palmas/TO 🔁 Araguaína/TO', '1100', 'Ok', '09:00:00', '', ''),
(173, '2026-01-17', 'Palmas/TO 🔁 Araguaína/TO', '1030', 'Jackson', '22:30:00', '', ''),
(174, '2026-01-18', 'Palmas/TO 🔁 Araguaína/TO', '1010', 'Natanael', '08:00:00', '', ''),
(175, '2026-01-18', 'Palmas/TO 🔁 Araguaína/TO', '1070', 'Beto', '08:30:00', '', ''),
(176, '2026-01-18', 'Palmas/TO 🔁 Araguaína/TO', '1100', 'Danilo', '09:00:00', '', ''),
(177, '2026-01-18', 'Palmas/TO 🔁 Araguaína/TO', '1260', 'Torres', '21:30:00', '', ''),
(178, '2026-01-18', 'Palmas/TO 🔁 Araguaína/TO', '1030', 'Ricardo', '22:30:00', '', ''),
(179, '2026-01-19', 'Palmas/TO 🔁 Araguaína/TO', '1010', 'Antônio', '08:00:00', '', ''),
(180, '2026-01-19', 'Palmas/TO 🔁 Araguaína/TO', '1090', 'Iago', '08:30:00', '', ''),
(181, '2026-01-19', 'Palmas/TO 🔁 Araguaína/TO', '1100', 'Rivelino', '09:00:00', '', ''),
(182, '2026-01-19', 'Palmas/TO 🔁 Araguaína/TO', '1080', 'Josué', '10:30:00', '', ''),
(183, '2026-01-19', 'Palmas/TO 🔁 Araguaína/TO', '1260', 'Irislando', '21:30:00', '', ''),
(184, '2026-01-19', 'Palmas/TO 🔁 Araguaína/TO', '1030', 'Jackson', '22:30:00', '', ''),
(185, '2026-01-20', 'Palmas/TO 🔁 Araguaína/TO', '1010', 'Renilton', '08:00:00', '', ''),
(186, '2026-01-20', 'Palmas/TO 🔁 Araguaína/TO', '1090', 'Beto', '08:30:00', '', ''),
(187, '2026-01-20', 'Palmas/TO 🔁 Araguaína/TO', '1100', 'Danilo', '09:00:00', '', ''),
(188, '2026-01-20', 'Palmas/TO 🔁 Araguaína/TO', '1260', 'Torres', '21:30:00', '', ''),
(189, '2026-01-20', 'Palmas/TO 🔁 Araguaína/TO', '1030', 'Ricardo', '22:30:00', '', ''),
(190, '2026-01-21', 'Palmas/TO 🔁 Araguaína/TO', '1010', 'Natanael', '08:00:00', '', ''),
(191, '2026-01-21', 'Palmas/TO 🔁 Araguaína/TO', '1090', 'Iago', '08:30:00', '', ''),
(192, '2026-01-21', 'Palmas/TO 🔁 Araguaína/TO', '1100', 'Rivelino', '09:00:00', '', ''),
(193, '2026-01-21', 'Palmas/TO 🔁 Filadélfia/TO', '1080', 'Josué', '10:30:00', '', ''),
(194, '2026-01-21', 'Palmas/TO 🔁 Araguaína/TO', '1260', 'Irislando', '21:30:00', '', ''),
(195, '2026-01-21', 'Palmas/TO 🔁 Araguaína/TO', '1030', 'Jackson', '22:30:00', '', ''),
(196, '2026-01-22', 'Palmas/TO 🔁 Araguaína/TO', '1010', 'Renilton', '08:00:00', '', ''),
(197, '2026-01-22', 'Palmas/TO 🔁 Araguaína/TO', '1090', 'Beto', '08:30:00', '', ''),
(198, '2026-01-22', 'Palmas/TO 🔁 Araguaína/TO', '1100', 'Danilo', '09:00:00', '', ''),
(199, '2026-01-22', 'Palmas/TO 🔁 Araguaína/TO', '1260', 'Torres', '21:30:00', '', ''),
(200, '2026-01-22', 'Palmas/TO 🔁 Araguaína/TO', '1030', 'Ricardo', '22:30:00', '', ''),
(201, '2026-01-23', 'Palmas/TO 🔁 Araguaína/TO', '1010', 'Natanael', '08:00:00', '', ''),
(202, '2026-01-23', 'Palmas/TO 🔁 Araguaína/TO', '1090', 'Iago', '08:30:00', '', ''),
(203, '2026-01-23', 'Palmas/TO 🔁 Araguaína/TO', '1100', 'Rivelino', '09:00:00', '', ''),
(204, '2026-01-23', 'Palmas/TO 🔁 Filadélfia/TO', '1080', 'Josué', '10:30:00', '', ''),
(205, '2026-01-23', 'Palmas/TO 🔁 Araguaína/TO', '1260', 'Irislando', '21:30:00', '', ''),
(206, '2026-01-23', 'Palmas/TO 🔁 Araguaína/TO', '1030', 'Jackson', '22:30:00', '', '');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=207;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
