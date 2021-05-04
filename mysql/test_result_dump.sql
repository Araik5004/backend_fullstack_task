-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Май 04 2021 г., 16:33
-- Версия сервера: 8.0.19
-- Версия PHP: 7.3.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `test_task`
--

-- --------------------------------------------------------

--
-- Структура таблицы `boosterpack`
--

CREATE TABLE `boosterpack` (
  `id` int NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bank` decimal(10,2) NOT NULL DEFAULT '0.00',
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `boosterpack`
--

INSERT INTO `boosterpack` (`id`, `price`, `bank`, `time_created`) VALUES
(1, '5.00', '1.00', '2020-03-30 00:17:28'),
(2, '20.00', '5.00', '2020-03-30 00:17:28'),
(3, '50.00', '0.00', '2020-03-30 00:17:28');

-- --------------------------------------------------------

--
-- Структура таблицы `boosterpack_analytics`
--

CREATE TABLE `boosterpack_analytics` (
  `id` int NOT NULL,
  `id_booster_pack` int NOT NULL,
  `price_booster_pack` decimal(10,2) NOT NULL,
  `user_id` int NOT NULL,
  `like_count` int NOT NULL,
  `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `boosterpack_analytics`
--

INSERT INTO `boosterpack_analytics` (`id`, `id_booster_pack`, `price_booster_pack`, `user_id`, `like_count`, `time_created`) VALUES
(1, 1, '5.00', 1, 13, '2021-05-04 07:17:16'),
(2, 1, '5.00', 1, 12, '2021-05-04 07:17:16'),
(3, 2, '20.00', 3, 13, '2021-05-04 07:17:57'),
(4, 2, '5.00', 3, 12, '2021-05-04 07:17:57'),
(5, 2, '20.00', 1, 4, '2021-05-04 07:18:26'),
(6, 2, '20.00', 3, 4, '2021-05-04 07:18:26');

-- --------------------------------------------------------

--
-- Структура таблицы `comment`
--

CREATE TABLE `comment` (
  `id` int NOT NULL,
  `parent_id` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'if not 0 then repost ',
  `user_id` int UNSIGNED NOT NULL,
  `assign_id` int UNSIGNED NOT NULL COMMENT 'post id ',
  `type` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `public` tinyint UNSIGNED NOT NULL,
  `text` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `comment`
--

INSERT INTO `comment` (`id`, `parent_id`, `user_id`, `assign_id`, `type`, `public`, `text`, `time_created`) VALUES
(1, 0, 1, 1, 'post', 1, 'Ну чо ассигн проверим', '2020-03-27 21:39:44'),
(2, 0, 1, 1, 'post', 1, 'Второй коммент', '2020-03-27 21:39:55'),
(3, 0, 2, 1, 'post', 1, 'Второй коммент от второго человека', '2020-03-27 21:40:22'),
(6, 0, 1, 1, 'post', 1, 'Смеркалось!!!!', '2021-05-01 18:18:45'),
(7, 0, 1, 2, 'post', 1, 'Вечерело!!!', '2021-05-01 18:19:20'),
(17, 0, 3, 1, 'post', 1, 'Первый комментарий )', '2021-05-04 11:41:10');

-- --------------------------------------------------------

--
-- Структура таблицы `post`
--

CREATE TABLE `post` (
  `id` int NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `likes` int NOT NULL DEFAULT '0',
  `text` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `img` varchar(1024) DEFAULT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `post`
--

INSERT INTO `post` (`id`, `user_id`, `likes`, `text`, `img`, `time_created`) VALUES
(1, 1, 130, 'Тестовый постик 1', '/images/posts/1.png', '2018-08-30 13:31:14'),
(2, 1, 48, 'Печальный пост', '/images/posts/2.png', '2018-10-11 01:33:27');

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id` int UNSIGNED NOT NULL,
  `email` varchar(60) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `personaname` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `avatarfull` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `rights` tinyint NOT NULL DEFAULT '0',
  `likes` int UNSIGNED NOT NULL DEFAULT '0',
  `wallet_balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `wallet_total_refilled` decimal(10,2) NOT NULL DEFAULT '0.00',
  `wallet_total_withdrawn` decimal(10,2) NOT NULL DEFAULT '0.00',
  `time_created` datetime NOT NULL,
  `time_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `personaname`, `avatarfull`, `rights`, `likes`, `wallet_balance`, `wallet_total_refilled`, `wallet_total_withdrawn`, `time_created`) VALUES
(1, 'admin@niceadminmail.pl', '123', 'AdminProGod', 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/96/967871835afdb29f131325125d4395d55386c07a_full.jpg', 0, 0, '95.00', '150.00', '55.00', '2019-07-26 01:53:54'),
(2, 'simpleuser@niceadminmail.pl', '456', 'simpleuser', 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/86/86a0c845038332896455a566a1f805660a13609b_full.jpg', 0, 0, '100.00', '150.00', '50.00', '2019-07-26 01:53:54'),
(3, 'test@mail.ru', '123', 'Araik', 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/96/967871835afdb29f131325125d4395d55386c07a_full.jpg', 0, 34, '0.00', '237.00', '237.00', '2019-07-26 01:53:54');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `boosterpack`
--
ALTER TABLE `boosterpack`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `boosterpack_analytics`
--
ALTER TABLE `boosterpack_analytics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_booster_pack` (`id_booster_pack`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `assign_id` (`assign_id`),
  ADD KEY `type` (`type`),
  ADD KEY `public` (`public`);

--
-- Индексы таблицы `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `likes` (`likes`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `time_created` (`time_created`),
  ADD KEY `time_updated` (`time_updated`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `boosterpack`
--
ALTER TABLE `boosterpack`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `boosterpack_analytics`
--
ALTER TABLE `boosterpack_analytics`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT для таблицы `post`
--
ALTER TABLE `post`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
