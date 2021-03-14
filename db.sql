
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_slovak_ci NOT NULL,
  `surname` varchar(50) COLLATE utf8_slovak_ci NOT NULL,
  `username` varchar(50) COLLATE utf8_slovak_ci NOT NULL,
  `password` varchar(64) COLLATE utf8_slovak_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_slovak_ci NOT NULL,
  `last_logon` datetime NOT NULL,
  `author_id` int(11) NOT NULL DEFAULT 1,
  `is_tutor` tinyint(1) NOT NULL DEFAULT 0,
  `is_accountant` tinyint(1) NOT NULL DEFAULT 0,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`id`, `name`, `surname`, `username`, `password`, `email`, `last_logon`, `author_id`, `is_tutor`, `is_accountant`, `is_admin`) VALUES
(1, 'SYSTEM', '', 'system', '', '', '0000-00-00 00:00:00', 1, 1, 1, 1),
(2, 'Admin', 'Adminovic', 'admin', '0bf7cb8f6b9d2ea7c2e0144070dc0c833696e6ce50dc7a0e688e3ca82c65e2e9', 'email@admin.sk', '2021-03-10 09:49:45', 1, 1, 1, 1),
(3, 'Lektor1_meno', 'Lektor1_surname', 'lector1', '4bc2ef0648cdf275032c83bb1e87dd554d47f4be293670042212c8a01cc2ccbe', 'lector1@uniba.sk', '2021-03-10 18:29:43', 2, 1, 0, 0),
(4, 'Lektor2_meno', 'Lektor2_surname', 'lector2', '274efeaa827a33d7e35be9a82cd6150b7caf98f379a4252aa1afce45664dcbe1', 'lector2@uniba.sk', '2021-03-11 20:46:29', 2, 1, 0, 0),
(5, 'Uctovnik_meno', 'Uctovnik_priezvisko', 'uctovnik', '56b1db8133d9eb398aabd376f07bf8ab5fc584ea0b8bd6a1770200cb613ca005', 'uctovnik@tia.sk', '2021-03-14 13:52:39', 1, 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_slovak_ci NOT NULL,
  `surname` varchar(50) COLLATE utf8_slovak_ci NOT NULL,
  `username` varchar(50) COLLATE utf8_slovak_ci NOT NULL,
  `password` varchar(64) COLLATE utf8_slovak_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_slovak_ci NOT NULL,
  `birth_date` date NOT NULL,
  `credit` decimal(10,0) NOT NULL DEFAULT 0,
  `last_logon` datetime NOT NULL,
  `author_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`id`, `name`, `surname`, `username`, `password`, `email`, `birth_date`, `credit`, `last_logon`, `author_id`) VALUES
(1, 'User1_name', 'User1_surname', 'user1', '4bc2ef0648cdf275032c83bb1e87dd554d47f4be293670042212c8a01cc2ccbe', 'user1@email.com', '2000-03-02', '0', '2021-03-10 11:41:10', 2),
(2, 'User2_name', 'User2_surname', 'user2', '274efeaa827a33d7e35be9a82cd6150b7caf98f379a4252aa1afce45664dcbe1', 'user2@system.sk', '2002-03-09', '200', '2021-03-10 20:47:38', 2),
(3, 'User3_name', 'User3_surname', 'user3', '05af533c6614544a704c4cf51a45be5c10ff19bd10b7aa1dfe47efc0fd059ede', 'user3@system.sk', '2002-02-05', '100', '2021-03-10 20:47:38', 2),
(4, 'User4_name', 'User4_surname', 'user4', 'e806a0c49839320161a6cd6bd8057722eb330fa3c5937642d56e727b92e8a4c1', 'user4@system.sk', '2001-09-27', '120', '2021-03-31 23:37:38', 2);

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_slovak_ci NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `delay` int(11) NOT NULL DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`id`, `name`, `price`, `unit_id`, `start_date`, `end_date`, `author_id`, `delay`) VALUES
(1, 'Ponozka_lektora_1', '10', NULL, NULL, NULL, 3, 10),
(2, 'Ponozka_lektora_2', '20', NULL, NULL, NULL, 4, 10),
(3, 'Event1_september', '10', 3, '2020-09-01', '2020-09-30', 1, 10),
(4, 'Event1_oktober', '10', 3, '2020-10-01', '2020-10-31', 1, 10),
(5, 'Event1_november', '10', 3, '2020-11-01', '2020-11-30', 1, 10);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `create_datetime` datetime NOT NULL,
  `pay_datetime` datetime DEFAULT NULL,
  `due_datetime` datetime NOT NULL,
  `amount` decimal(10,0) NOT NULL,
  `type` set('cash','credit','ib') COLLATE utf8_slovak_ci DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `registrar_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`id`, `client_id`, `item_id`, `create_datetime`, `pay_datetime`, `due_datetime`, `amount`, `type`, `author_id`, `registrar_id`) VALUES
(1, 1, 1, '2021-02-03 10:40:37', '2021-03-11 14:58:48', '2021-03-14 13:58:46', '2', 'ib', 3, 5),
(3, 2, 2, '2021-03-08 15:02:41', NULL, '2021-03-14 14:02:40', '3', NULL, 4, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `unit`
--

CREATE TABLE `unit` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_slovak_ci NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `venue` varchar(50) COLLATE utf8_slovak_ci NOT NULL DEFAULT 'udefined',
  `author_id` int(11) NOT NULL,
  `max_clients` int(11) NOT NULL,
  `create_date` date NOT NULL,
  `registration` set('open','close','invite','request') COLLATE utf8_slovak_ci NOT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `type` set('course','event','occurrence','singleevent') COLLATE utf8_slovak_ci NOT NULL,
  `attendance` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;

--
-- Dumping data for table `unit`
--

INSERT INTO `unit` (`id`, `name`, `price`, `venue`, `author_id`, `max_clients`, `create_date`, `registration`, `start_datetime`, `end_datetime`, `type`, `attendance`) VALUES
(1, 'Course1', '10', '', 3, 20, '2021-03-10', 'open', '2020-09-01 15:11:56', '2021-06-30 15:12:26', 'course', 1),
(2, 'Course2', '20', '', 4, 20, '2021-03-09', 'close', '2020-11-01 15:12:18', '2021-06-30 15:12:32', 'course', 1),
(3, 'Event1', '10', 'VenueEvent1', 3, 20, '2021-03-10', 'close', '2020-09-01 15:15:23', '2021-06-30 15:15:23', 'event', 1),
(4, 'Event2', '10', 'VenueEvent2', 4, 25, '2021-03-10', 'request', '2021-04-01 15:15:23', '2021-04-30 15:15:23', 'event', 1),
(5, 'Event4', '4', 'VenueEvent4', 4, 33, '2021-03-10', 'close', '2021-04-01 15:15:23', '2021-04-30 15:15:23', 'event', 1),
(6, 'Event3', '3', 'VenueEvent3', 4, 25, '2021-03-12', 'invite', '2021-04-01 15:15:23', '2021-04-30 15:15:23', 'event', 1);

-- --------------------------------------------------------

--
-- Table structure for table `unit_account`
--

CREATE TABLE `unit_account` (
  `id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `is_editor` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;

--
-- Dumping data for table `unit_account`
--

INSERT INTO `unit_account` (`id`, `unit_id`, `account_id`, `is_editor`) VALUES
(1, 1, 4, 0),
(2, 2, 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `unit_client`
--

CREATE TABLE `unit_client` (
  `id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `date_join` date DEFAULT NULL,
  `date_leave` date DEFAULT NULL,
  `status` set('request','approve','invite','accept','refuse','restrict','manual') COLLATE utf8_slovak_ci NOT NULL DEFAULT 'manual'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;

--
-- Dumping data for table `unit_client`
--

INSERT INTO `unit_client` (`id`, `unit_id`, `client_id`, `author_id`, `date_join`, `date_leave`, `status`) VALUES
(1, 1, 1, 3, '2020-09-01', NULL, 'manual'),
(2, 2, 1, 1, '2021-03-03', NULL, 'request'),
(3, 4, 2, 4, '2021-03-11', NULL, 'invite'),
(4, 5, 4, 3, '2021-03-12', NULL, 'manual');

-- --------------------------------------------------------

--
-- Table structure for table `unit_unit`
--

CREATE TABLE `unit_unit` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `create_date` date NOT NULL,
  `author_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;

--
-- Dumping data for table `unit_unit`
--

INSERT INTO `unit_unit` (`id`, `parent_id`, `child_id`, `create_date`, `author_id`) VALUES
(1, 1, 3, '2020-09-01', 3),
(2, 2, 2, '2021-03-07', 4),
(3, 1, 6, '2021-03-10', 2),
(4, 2, 6, '2021-03-09', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ACCOUNT_author_id_ACCOUNT_id` (`author_id`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id_account_id` (`author_id`) USING BTREE;

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ITEM_autor_id_ACCOUNT_id` (`author_id`),
  ADD KEY `ITEM_unit_id_UNIT_id` (`unit_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `PAYMENT_client_id_CLIENT_id` (`client_id`),
  ADD KEY `PAYMENT_created_account_id_ACCOUNT_id` (`author_id`),
  ADD KEY `PAYMENT_payed_account_id_ACCOUNT_id` (`registrar_id`),
  ADD KEY `PAYMENT_item_id_ITEM_id` (`item_id`);

--
-- Indexes for table `unit`
--
ALTER TABLE `unit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UNIT_author_id_ACCOUNT_id` (`author_id`);

--
-- Indexes for table `unit_account`
--
ALTER TABLE `unit_account`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UNIT_ACCOUNT_account_id_ACCOUNT_id` (`account_id`),
  ADD KEY `UNIT_ACCOUNT_unit_id_UNIT_id` (`unit_id`);

--
-- Indexes for table `unit_client`
--
ALTER TABLE `unit_client`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UNIT_CLIENT_author_id_ACCOUNT_id` (`author_id`),
  ADD KEY `UNIT_CLIENT_client_id_CLIENT_id` (`client_id`),
  ADD KEY `UNIT_CLIENT_unit_id_UNIT_id` (`unit_id`);

--
-- Indexes for table `unit_unit`
--
ALTER TABLE `unit_unit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UNIT_UNIT_author_id_ACCOUNT_id` (`author_id`),
  ADD KEY `UNIT_UNIT_parent_id_UNIT_id` (`parent_id`),
  ADD KEY `UNIT_UNIT_child_id_UNIT_id` (`child_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `unit`
--
ALTER TABLE `unit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `unit_account`
--
ALTER TABLE `unit_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `unit_client`
--
ALTER TABLE `unit_client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `unit_unit`
--
ALTER TABLE `unit_unit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account`
--
ALTER TABLE `account`
  ADD CONSTRAINT `ACCOUNT_author_id_ACCOUNT_id` FOREIGN KEY (`author_id`) REFERENCES `account` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `CLIENT_author_id_ACCOUNT_id` FOREIGN KEY (`author_id`) REFERENCES `account` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `ITEM_autor_id_ACCOUNT_id` FOREIGN KEY (`author_id`) REFERENCES `account` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ITEM_unit_id_UNIT_id` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `PAYMENT_client_id_CLIENT_id` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `PAYMENT_created_account_id_ACCOUNT_id` FOREIGN KEY (`author_id`) REFERENCES `account` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `PAYMENT_item_id_ITEM_id` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `PAYMENT_payed_account_id_ACCOUNT_id` FOREIGN KEY (`registrar_id`) REFERENCES `account` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `unit`
--
ALTER TABLE `unit`
  ADD CONSTRAINT `UNIT_author_id_ACCOUNT_id` FOREIGN KEY (`author_id`) REFERENCES `account` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `unit_account`
--
ALTER TABLE `unit_account`
  ADD CONSTRAINT `UNIT_ACCOUNT_account_id_ACCOUNT_id` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `UNIT_ACCOUNT_unit_id_UNIT_id` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `unit_client`
--
ALTER TABLE `unit_client`
  ADD CONSTRAINT `UNIT_CLIENT_author_id_ACCOUNT_id` FOREIGN KEY (`author_id`) REFERENCES `account` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `UNIT_CLIENT_client_id_CLIENT_id` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `UNIT_CLIENT_unit_id_UNIT_id` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `unit_unit`
--
ALTER TABLE `unit_unit`
  ADD CONSTRAINT `UNIT_UNIT_author_id_ACCOUNT_id` FOREIGN KEY (`author_id`) REFERENCES `account` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `UNIT_UNIT_child_id_UNIT_id` FOREIGN KEY (`child_id`) REFERENCES `unit` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `UNIT_UNIT_parent_id_UNIT_id` FOREIGN KEY (`parent_id`) REFERENCES `unit` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
