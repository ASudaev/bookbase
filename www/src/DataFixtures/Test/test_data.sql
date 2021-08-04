--
-- Test data for table `author`
--

INSERT INTO `author` (`id`, `name`) VALUES
(1, 'Терри Пратчетт'),
(2, 'Нил Гейман');

--
-- Test data for table `book`
--

INSERT INTO `book` (`id`, `name_en`, `name_ru`) VALUES
(1, 'The Colour of Magic', 'Цвет волшебства'),
(2, 'Good Omens', 'Благие знамения'),
(3, 'American Gods', 'Американские боги');

--
-- Test data for table `book_author`
--

INSERT INTO `book_author` (`book_id`, `author_id`) VALUES
(1, 1),
(2, 1),
(2, 2),
(3, 2);
