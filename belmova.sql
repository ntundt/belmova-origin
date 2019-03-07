-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Мар 07 2019 г., 18:58
-- Версия сервера: 5.7.24-0ubuntu0.18.04.1
-- Версия PHP: 7.2.10-0ubuntu0.18.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `belmova`
--

-- --------------------------------------------------------

--
-- Структура таблицы `bm_email_confirmation`
--

CREATE TABLE `bm_email_confirmation` (
  `id` int(11) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL,
  `confirmation_token` varchar(16) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `bm_exercises_basic_ru`
--

CREATE TABLE `bm_exercises_basic_ru` (
  `id` int(11) NOT NULL,
  `partition_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `topic_level` int(11) NOT NULL,
  `lesson_number` int(11) NOT NULL,
  `exercises` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `bm_exercises_basic_ru`
--

INSERT INTO `bm_exercises_basic_ru` (`id`, `partition_id`, `topic_id`, `topic_level`, `lesson_number`, `exercises`) VALUES
(1, 1, 1, 1, 1, '[{\"text\": \"\", \"type\": \"readTheRule\", \"title\": \"Употребление Ў\"}, {\"type\": \"makeTranslation\", \"words\": [\"a\", \"b\", \"c\", \"d\"], \"answer\": [\"a\", \"b\"], \"sentence\": \"а б\"}, {\"type\": \"makeTranslation\", \"words\": [\"Складзце\", \"пераклад\", \"гэтага\", \"сказа\", \"іншыя\", \"словы\"], \"answer\": [\"Складзце\", \"пераклад\", \"гэтага\", \"сказа\"], \"sentence\": \"Составьте перевод этого предложения\"}, {\"type\": \"writeTranslation\", \"sentence\": \"Cu la vetero estas varma en Usono?\", \"translation\": \"Ist der weter hatt in USdA?\"}, {\"type\": \"writeTranslation\", \"sentence\": \"Предложение\", \"translation\": \"Правильный перевод\"}, {\"type\": \"writeTranslation\", \"sentence\": \"Ght\", \"translation\": \"Гхт\"}]'),
(2, 2, 1, 1, 1, '[{}, {}, {}]'),
(3, 1, 1, 1, 2, '[{\"type\": \"makeTranslation\", \"words\": [\"ПРЕДЛОЖЖЕНИЕ\"], \"answer\": [\"ПРЕДЛОЖЖЕНИЕ\"], \"sentence\": \"THE SENTENCE\"}, [], []]'),
(4, 1, 2, 1, 1, '[{}, {}, {}, {}]'),
(5, 1, 1, 3, 1, '[{}]'),
(6, 3, 1, 1, 1, '[{}]'),
(7, 1, 2, 1, 2, '[{}]'),
(8, 1, 1, 2, 1, '[{}]');

-- --------------------------------------------------------

--
-- Структура таблицы `bm_exercises_partitions_ru`
--

CREATE TABLE `bm_exercises_partitions_ru` (
  `id` int(11) NOT NULL,
  `name` varchar(128) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `bm_exercises_partitions_ru`
--

INSERT INTO `bm_exercises_partitions_ru` (`id`, `name`) VALUES
(1, 'Фонетика'),
(2, 'Что-то еще'),
(3, 'Третий раздел');

-- --------------------------------------------------------

--
-- Структура таблицы `bm_exercises_topics_ru`
--

CREATE TABLE `bm_exercises_topics_ru` (
  `partition_id` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `name` varchar(128) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `bm_exercises_topics_ru`
--

INSERT INTO `bm_exercises_topics_ru` (`partition_id`, `id`, `name`) VALUES
(1, 1, 'Ў'),
(2, 1, 'Первый урок'),
(1, 2, 'ШЧ'),
(3, 1, 'И первый его топик');

-- --------------------------------------------------------

--
-- Структура таблицы `bm_feedbacks`
--

CREATE TABLE `bm_feedbacks` (
  `id` int(11) NOT NULL,
  `type` varchar(32) COLLATE utf8_bin NOT NULL,
  `from_id` int(11) NOT NULL,
  `reply_to` int(11) DEFAULT NULL,
  `title` text COLLATE utf8_bin,
  `time` int(11) NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `fact_result` text COLLATE utf8_bin,
  `needed_result` text COLLATE utf8_bin,
  `status` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `files` text COLLATE utf8_bin
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `bm_feedbacks`
--

INSERT INTO `bm_feedbacks` (`id`, `type`, `from_id`, `reply_to`, `title`, `time`, `description`, `fact_result`, `needed_result`, `status`, `files`) VALUES
(1, 'bug', 1, NULL, 'Пример отчёта', 11123124, 'Описание', 'Когда я совершаю действие А, происходит Б', 'Когда я совершаю действие А, должно происходить В', 'not_seen', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `bm_language_ru`
--

CREATE TABLE `bm_language_ru` (
  `pattern_key` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `text_pattern` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `bm_language_ru`
--

INSERT INTO `bm_language_ru` (`pattern_key`, `text_pattern`) VALUES
('INeedHelp', 'Помощь'),
('aboutUs', 'О нас'),
('allLessons', 'Все уроки'),
('allPosts', 'Все отчёты'),
('bugtracker', 'Баг-трекер'),
('constructor', 'Конструктор'),
('description', 'Описание'),
('entrance', 'Вход'),
('error_101', 'Нет пользователя с таким логином.'),
('error_102', 'Неправильный пароль.'),
('error_103', 'Логин или email, указанные Вами, уже используются.'),
('error_104', 'Нет пользователя с таким email.'),
('error_105', 'Сессия устарела, не существовала вовсе или не была передана.'),
('error_106', 'Токен устарел или не существовал.'),
('error_107', 'Логин или пароль не был передан.'),
('error_108', 'Один из обязательных для этого метода параметров не был передан или имеет недопустимое значение.'),
('error_109', 'У Вас недостаточно прав для выполнения этого действия.'),
('error_202', 'Урок не существует.'),
('error_301', 'Пост не существует.'),
('exit', 'Выйти'),
('factResult', 'Фактический результат'),
('incorrectIdentificator', 'Неправильный логин'),
('incorrectPassword', 'Неправильный пароль'),
('learn', 'Заниматься'),
('logIn', 'Войти'),
('loginOrEmail', 'Логин или email'),
('mainPage', 'Главная'),
('neededResult', 'Ожидаемый результат'),
('newProblemReport', 'Новый отчёт о проблеме'),
('newReport', 'Новый отчёт'),
('password', 'Пароль'),
('passwordResetConfirmMailText', 'Здравствуйте, %fname%!\\n\\nПохоже, что Вы запросили у нас сброс пароля Вашего аккаунта. Если это были не Вы, то проигнорируйте это письмо. Если Вы действительно запрашивали сброс, то вот Вам ссылка, перейдя по которой Вы сможете задать новый пароль для Вашего аккаунта.\\n\\n<a href=\"%link%\">%link%</a>\\n\\nНапоминаем также, что регистрировались Вы с логином: %login%.\\n\\nС уважением,\\nКоманда'),
('replaySteps', 'Шаги воспроизведения'),
('select', 'Выбрать'),
('selectLesson', 'Выбрать урок'),
('send', 'Отправить'),
('title', 'Заголовок');

-- --------------------------------------------------------

--
-- Структура таблицы `bm_sessions`
--

CREATE TABLE `bm_sessions` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `ip` varchar(16) COLLATE utf8_bin NOT NULL,
  `last_used` int(11) NOT NULL,
  `sid` varchar(16) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `bm_sessions`
--

INSERT INTO `bm_sessions` (`id`, `uid`, `ip`, `last_used`, `sid`) VALUES
(36, 1, '::1', 1549029512, 'example'),
(37, 1, '::1', 1549118458, '11972eba48078c1e'),
(38, 1, '::1', 1549118690, 'e6628cb808d74e22'),
(39, 1, '::1', 1549296749, '4e7c59b92ee67e8a'),
(40, 1, '::1', 1549296788, 'efe8e20780cf1c8a'),
(41, 1, '::1', 1549296906, 'cd121c89b91864e9'),
(42, 1, '::1', 1549297027, '84a23235b87b8388'),
(43, 1, '::1', 1549297069, '110c1eeeee7888a7'),
(44, 1, '::1', 1549297132, 'a63316338eca8306'),
(45, 1, '::1', 1549297288, '548480b684f1aa6b'),
(46, 1, '::1', 1549297340, 'ee6c14a235dcdbbe'),
(47, 1, '::1', 1549297684, '9ec3c5de37f8ab0b'),
(48, 1, '::1', 1549297721, 'de0156a4edebdde3'),
(49, 1, '::1', 1549297782, '76880cdc7871ffa5'),
(50, 1, '::1', 1549297821, '6693669692ee3c0d'),
(51, 1, '::1', 1549297913, '408eda7a1ff04f08'),
(52, 1, '::1', 1549298021, 'f9d7f193bb77e08e'),
(53, 1, '::1', 1549298067, '90eebb6dbfa3777a'),
(54, 1, '::1', 1549463933, 'a21aa0bfcb722261'),
(55, 1, '::1', 1549548299, '1126b693221c28d6'),
(56, 1, '::1', 1549640625, 'd6e6c396ff034b63'),
(57, 1, '::1', 1549640724, 'c4ce439c9382660f'),
(58, 165054978, '::1', 1549728702, '748a2c1c0cc9d865'),
(59, 12, '::1', 1549728912, 'eaa66c8e336d2504'),
(60, 12, '::1', 1549729094, '4a736fcb25f69566'),
(61, 12, '::1', 1549729230, '70ac69777b58b8fd'),
(62, 12, '::1', 1549730376, 'fe83f215b5e1ce97'),
(63, 12, '::1', 1549730574, '35e6f5d02adb5af2'),
(64, 12, '::1', 1549730678, 'a20675073e66fa49'),
(65, 12, '::1', 1549730903, 'e2d9e2e47faa8496'),
(66, 1, '::1', 1549791165, '45ead4ee1ddf9b8e'),
(67, 1, '::1', 1550146085, '0d12a1d28f32acb3'),
(68, 1, '::1', 1550674816, '0151f46f360086f9');

-- --------------------------------------------------------

--
-- Структура таблицы `bm_users`
--

CREATE TABLE `bm_users` (
  `id` int(8) NOT NULL,
  `fname` varchar(64) COLLATE utf8_bin NOT NULL,
  `lname` varchar(64) COLLATE utf8_bin NOT NULL,
  `login` varchar(64) COLLATE utf8_bin NOT NULL,
  `password_hash` varchar(64) COLLATE utf8_bin NOT NULL,
  `profile_picture` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT 'uploads/default_profile_picture.png',
  `email` varchar(64) COLLATE utf8_bin NOT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `bm_users`
--

INSERT INTO `bm_users` (`id`, `fname`, `lname`, `login`, `password_hash`, `profile_picture`, `email`, `email_verified`) VALUES
(1, 'Никита', 'Тихонович', 'mtsikhanovich', 'e8e329582fded4948bd574c2a7d39f91', '/work/ui/default_profile_picture150x150.png', 'nikita.tihonovich@gmail.com', 1),
(12, 'Никита', 'Тихонович', 'id165054978', 'not_set', '/work/ui/default_profile_picture150x150.png', '', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `bm_users_progress`
--

CREATE TABLE `bm_users_progress` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `partition_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `topic_level` int(11) NOT NULL,
  `lesson_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `bm_users_progress`
--

INSERT INTO `bm_users_progress` (`id`, `uid`, `partition_id`, `topic_id`, `topic_level`, `lesson_number`) VALUES
(1, 1, 1, 1, 1, 2),
(2, 1, 2, 1, 1, 1),
(3, 1, 1, 2, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `bm_users_rights`
--

CREATE TABLE `bm_users_rights` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `type` varchar(64) COLLATE utf8_bin NOT NULL,
  `has` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `bm_users_rights`
--

INSERT INTO `bm_users_rights` (`id`, `uid`, `type`, `has`) VALUES
(1, 1, 'createLessons', 1),
(2, 1, 'editLessons', 1),
(3, 1, 'deleteLessons', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `bm_users_xp`
--

CREATE TABLE `bm_users_xp` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `xp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `bm_users_xp`
--

INSERT INTO `bm_users_xp` (`id`, `uid`, `xp`) VALUES
(1, 1, 76);

-- --------------------------------------------------------

--
-- Структура таблицы `bm_vk_auth`
--

CREATE TABLE `bm_vk_auth` (
  `id` int(11) NOT NULL,
  `vk_user_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `access_token` varchar(100) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `bm_vk_auth`
--

INSERT INTO `bm_vk_auth` (`id`, `vk_user_id`, `user_id`, `access_token`) VALUES
(1, 165054978, 12, 'd71a7900f63866ba4170703a189282fd1e669f4955b80a3cfa9fa4a4d5f43e7fca92a293b0f5ae3ad0a5d'),
(2, 165054978, 12, 'c0c7c86f9e80b91f069cdb13cf2591ee89600bb53b9ce7312dd30c1a95a9db8932c7e598b6d693e016406'),
(3, 165054978, 12, '9c8c2e0ed006dbd9b2d89ef2dde13a9cb442576045c727b28926a4653ea99fd72b25011a83f92d66bbdee'),
(4, 165054978, 12, 'a343c3ab7cc1fe22e53c2cb955a2ff7e6eac91b09c0f5869a05fa25d800e388ece86aab6a0658bd4641d9');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `bm_email_confirmation`
--
ALTER TABLE `bm_email_confirmation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `confirmation_token` (`confirmation_token`);

--
-- Индексы таблицы `bm_exercises_basic_ru`
--
ALTER TABLE `bm_exercises_basic_ru`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `bm_exercises_partitions_ru`
--
ALTER TABLE `bm_exercises_partitions_ru`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `bm_feedbacks`
--
ALTER TABLE `bm_feedbacks`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `bm_language_ru`
--
ALTER TABLE `bm_language_ru`
  ADD UNIQUE KEY `pattern_key` (`pattern_key`);

--
-- Индексы таблицы `bm_sessions`
--
ALTER TABLE `bm_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sid` (`sid`);

--
-- Индексы таблицы `bm_users`
--
ALTER TABLE `bm_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Индексы таблицы `bm_users_progress`
--
ALTER TABLE `bm_users_progress`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `bm_users_rights`
--
ALTER TABLE `bm_users_rights`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `bm_users_xp`
--
ALTER TABLE `bm_users_xp`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `bm_vk_auth`
--
ALTER TABLE `bm_vk_auth`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `bm_email_confirmation`
--
ALTER TABLE `bm_email_confirmation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `bm_exercises_basic_ru`
--
ALTER TABLE `bm_exercises_basic_ru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `bm_exercises_partitions_ru`
--
ALTER TABLE `bm_exercises_partitions_ru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `bm_feedbacks`
--
ALTER TABLE `bm_feedbacks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `bm_sessions`
--
ALTER TABLE `bm_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT для таблицы `bm_users`
--
ALTER TABLE `bm_users`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `bm_users_progress`
--
ALTER TABLE `bm_users_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `bm_users_rights`
--
ALTER TABLE `bm_users_rights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `bm_users_xp`
--
ALTER TABLE `bm_users_xp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `bm_vk_auth`
--
ALTER TABLE `bm_vk_auth`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
