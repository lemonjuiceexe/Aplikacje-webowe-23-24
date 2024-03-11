-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2024 at 01:16 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `h-rem`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `order_count` bigint(20) NOT NULL,
  `adult` tinyint(1) NOT NULL,
  `fee` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `order_count`, `adult`, `fee`) VALUES
(1, 'Krzysztof Stuglik', 2, 1, 19920),
(2, 'Maciej Egzystencja', 1, 0, 9960);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `directors`
--

CREATE TABLE `directors` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `movie_count` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `directors`
--

INSERT INTO `directors` (`id`, `name`, `movie_count`) VALUES
(1, 'Bartosz Walaszek', 1),
(2, 'George Lucas', 2);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `genre`
--

CREATE TABLE `genre` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `explicit` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genre`
--

INSERT INTO `genre` (`id`, `name`, `explicit`) VALUES
(1, 'Akcja', 0),
(2, 'Science Fiction', 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `movie - genre`
--

CREATE TABLE `movie - genre` (
  `id` bigint(20) NOT NULL,
  `movie_id` bigint(20) NOT NULL,
  `genre_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movie - genre`
--

INSERT INTO `movie - genre` (`id`, `movie_id`, `genre_id`) VALUES
(1, 2, 2),
(2, 1, 1),
(3, 2, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `movies`
--

CREATE TABLE `movies` (
  `id` bigint(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `year` smallint(6) NOT NULL,
  `length` time NOT NULL,
  `director_id` bigint(20) NOT NULL,
  `count` bigint(20) NOT NULL,
  `rating` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `title`, `year`, `length`, `director_id`, `count`, `rating`) VALUES
(1, 'Wściekłe Pięści Węża', 2006, '00:59:35', 1, 6, 10),
(2, 'Gwiezdne wojny: Część IV - Nowa nadzieja', 1977, '02:01:00', 2, 21, 9),
(3, 'Gwiezdne wojny: Część V - Imperium kontratakuje', 1980, '02:04:00', 2, 37, 8);

--
-- Wyzwalacze `movies`
--
DELIMITER $$
CREATE TRIGGER `recount_movies_delete` AFTER DELETE ON `movies` FOR EACH ROW UPDATE directors SET directors.movie_count = (SELECT COUNT(*) FROM movies WHERE movies.director_id=directors.id)
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `recount_movies_insert` AFTER INSERT ON `movies` FOR EACH ROW UPDATE directors SET directors.movie_count = (SELECT COUNT(*) FROM movies WHERE movies.director_id=directors.id)
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `recount_movies_update` AFTER UPDATE ON `movies` FOR EACH ROW UPDATE directors SET directors.movie_count = (SELECT COUNT(*) FROM movies WHERE movies.director_id=directors.id)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) NOT NULL,
  `movie_id` bigint(20) NOT NULL,
  `customer_id` bigint(20) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `movie_id`, `customer_id`, `timestamp`) VALUES
(1, 1, 2, '2024-02-29 14:34:39'),
(2, 2, 1, '2024-02-29 14:34:39'),
(3, 1, 1, '2024-02-29 14:34:39');

--
-- Wyzwalacze `orders`
--
DELIMITER $$
CREATE TRIGGER `recount_orders_delete` AFTER DELETE ON `orders` FOR EACH ROW UPDATE customers SET customers.order_count = (SELECT COUNT(*) FROM orders WHERE orders.customer_id = customers.id)
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `recount_orders_insert` AFTER INSERT ON `orders` FOR EACH ROW UPDATE customers SET customers.order_count = (SELECT COUNT(*) FROM orders WHERE orders.customer_id = customers.id)
$$
DELIMITER ;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `directors`
--
ALTER TABLE `directors`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `genre`
--
ALTER TABLE `genre`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `movie - genre`
--
ALTER TABLE `movie - genre`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie _ genre_genre_id_foreign` (`genre_id`),
  ADD KEY `movie _ genre_movie_id_foreign` (`movie_id`);

--
-- Indeksy dla tabeli `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movies_director_id_foreign` (`director_id`);

--
-- Indeksy dla tabeli `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_movie_id_foreign` (`movie_id`),
  ADD KEY `orders_customer_id_foreign` (`customer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `directors`
--
ALTER TABLE `directors`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `genre`
--
ALTER TABLE `genre`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `movie - genre`
--
ALTER TABLE `movie - genre`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `movie - genre`
--
ALTER TABLE `movie - genre`
  ADD CONSTRAINT `movie _ genre_genre_id_foreign` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`id`),
  ADD CONSTRAINT `movie _ genre_movie_id_foreign` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`);

--
-- Constraints for table `movies`
--
ALTER TABLE `movies`
  ADD CONSTRAINT `movies_director_id_foreign` FOREIGN KEY (`director_id`) REFERENCES `directors` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `orders_movie_id_foreign` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`);

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `calc_fee` ON SCHEDULE EVERY 1 DAY STARTS '2024-02-29 16:13:53' ON COMPLETION PRESERVE ENABLE DO UPDATE customers SET customers.fee = customers.fee + 120 * (SELECT COUNT(*) FROM orders WHERE customers.id = orders.customer_id AND TIMESTAMPDIFF(MONTH, NOW(), orders.timestamp) < 0)$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
