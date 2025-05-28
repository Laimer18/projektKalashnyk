-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Час створення: Трв 28 2025 р., 16:35
-- Версія сервера: 10.4.32-MariaDB
-- Версія PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База даних: `photosite1`
--

-- --------------------------------------------------------

--
-- Структура таблиці `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `question` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `contacts`
--

INSERT INTO `contacts` (`id`, `first_name`, `last_name`, `email`, `phone`, `question`, `created_at`) VALUES
(6, 'Yehor', 'Kalashnyk', 'faizer654@gmail.com', '0952082425', 'xzxz', '2025-05-18 17:39:47'),
(12, 'Yehor', 'Kalashnyk', 'faizetgr654@gmail.com', '0952082425', 'wewee', '2025-05-19 10:18:09'),
(13, 'Yehor', 'Kalashnyk', 'faizer654@gmail.com', '0952082425', 'впвв', '2025-05-25 19:15:23'),
(14, 'Yehor', 'Kalashnyk', 'faizer654@gmail.com', '0952082425', 'впвв', '2025-05-25 19:15:24'),
(15, 'Yehor', 'Kalashnyk', 'faizer654@gmail.com', '0952082425', 'впвв', '2025-05-25 19:15:24'),
(16, 'Yehor', 'Kalashnyk', 'faizer654@gmail.com', '0952082425', 'впвв', '2025-05-25 19:15:54'),
(17, 'Yehor', 'Kalashnyk', 'faizer654@gmail.com', '0952082425', '1122', '2025-05-26 14:26:35'),
(18, 'Yehor', 'Kalashnyk', 'faizer654@gmail.com', '0952082425', '2esqs', '2025-05-26 15:48:59');

-- --------------------------------------------------------

--
-- Структура таблиці `photosessions`
--

CREATE TABLE `photosessions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `date` date NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `photosessions`
--

INSERT INTO `photosessions` (`id`, `name`, `email`, `phone`, `date`, `details`, `created_at`) VALUES
(1, 'Yehor Kalashnyk', 'faizer654@gmail.com', '0952082425', '2025-05-21', '122', '2025-05-18 14:42:00'),
(2, 'Yehor Kalashnyk', 'faizer654@gmail.com', '0952082425', '2025-06-02', '1223', '2025-05-18 14:42:08'),
(3, 'Yehor Kalashnyk', 'faizer654@gmail.com', '0952082425', '2025-05-01', '122333', '2025-05-18 16:08:06'),
(4, 'Yehor Kalashnyk', 'faizer654@gmail.com', '0952082425', '2025-05-08', '122223', '2025-05-18 16:37:30'),
(5, 'Yehor Kalashnyk', 'faizer654@gmail.com', '0952082425', '2025-05-01', '12222', '2025-05-18 16:48:50'),
(6, 'Yehor Kalashnyk', 'faizer652224@gmail.com', '+4210952082425', '2025-05-07', '1222', '2025-05-18 16:54:51'),
(7, 'Yehor Kalashnyk', 'faizer6354@gmail.com', '0952082425', '2025-05-09', 'WWW', '2025-05-18 20:21:21'),
(8, 'Yehor Kalashnyk', 'faizer63354@gmail.com', '+4210952082425', '2025-05-21', 'іііііі', '2025-05-18 21:16:10'),
(9, 'Yehor Kalashnyk', 'faiz654@gmail.com', '0952082425', '2025-05-21', 'іііф', '2025-05-23 21:43:46'),
(10, 'Yehor Kalashnyk', 'faizer654@gmail.com', '+4210952082425', '2025-05-07', '1434334', '2025-05-24 10:54:30'),
(11, 'Yehor', 'faizer654@gmail.com', '0952082425', '2025-05-01', 'йцйц', '2025-05-26 14:18:03'),
(12, 'Yehor Kalashnyk', 'faizer654qqq@gmail.com', '1111111111111111', '2025-05-22', '2121', '2025-05-26 15:27:04'),
(13, 'Yehor', 'faizer654@gmail.com', '+380 (63) 652-51-95', '2025-05-09', '12122121223', '2025-05-26 15:50:55'),
(14, 'Yehor Kalashnyk', 'faizer654@gmail.com', '0952082425', '2025-05-29', '1222', '2025-05-27 16:12:12');

-- --------------------------------------------------------

--
-- Структура таблиці `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone`, `password`) VALUES
(2, 'Yehor', 'Kalashnyk', 'faizer6524@gmail.com', '0952082425', '$2y$10$oO3K4lZY9ZezOqeFwN4wzeCu7sNLCLgSi8m7fZ6L/TWALB6UO3clC'),
(4, 'Yehor', 'Kalashnyk', 'faizer6545@gmail.com', '0952082425', '$2y$10$VyyzgmzjkwNkxXIXBKlepOSyQYLzY9VtG77qmUh3sSysU0BprRQnW'),
(8, 'Yehor', 'Kalashnyk', 'faizer6354@gmail.com', '0952082425', '$2y$10$JafeiTSEwnmNxnaCCRaVr.0tn9mAybEwHxZZQhdB4JXrsSm643Su.'),
(9, 'Yehor', 'Kalashnyk', 'faizer622254@gmail.com', '0952082425', '$2y$10$ELuj5E8cZg4F67BmxfhIUultc1AYH19Jkvc80Xh9BS/HKRmkFEdxS'),
(10, 'Yehor', 'Kalashnyk', 'faizer63354@gmail.com', '0952082425', '$2y$10$YSMcWSjiRYWKhhhaVtO7xuBTqQdHALK5Q3gReC6dQGJoLUaig3k3i'),
(11, 'NIKITA', 'Kalashnyk', 'faiz654@gmail.com', '0952082425', '$2y$10$AhOOLrua/99hrPy0.oMf4uTFOCYTyTc5dS1996WYUiObxaQyMmTVO'),
(12, 'Yehor', 'Kalashnyk', 'faizer@gmail.com', '0952082425', '$2y$10$vUcsphRCftl.ZPyXgxX/5efTCvcKjMcNsdmoh0OzaBBQPUGh6sjm.'),
(13, 'Yehor', 'Kalashnyk', 'faize@gmail.com', '0952082425', '$2y$10$yoXJmKxsBawz8EDOnWRPPOYGfr4RivtrzjelNlidZmciNaUGsyxQi'),
(16, 'Yehor1', 'Kalashnyk', 'faizer654qqq@gmail.com', '0952082425', '$2y$10$5VW8Z4p8tqwiy33e/U2.dO0ApKes5WclvzCj/qmMjlia5kLuBOg.C'),
(17, 'Yehor', 'Kalashnyk', 'faizer654@gmail.com', '0952082425', '$2y$10$RtnLUKDKz3CWTLO8oiEPSuBLjHi3nlKNN2IXaRrRFkwnMWcg4jA/O');

--
-- Індекси збережених таблиць
--

--
-- Індекси таблиці `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `photosessions`
--
ALTER TABLE `photosessions`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для збережених таблиць
--

--
-- AUTO_INCREMENT для таблиці `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT для таблиці `photosessions`
--
ALTER TABLE `photosessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблиці `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
