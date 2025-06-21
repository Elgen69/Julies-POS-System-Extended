SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+08:00";


CREATE DATABASE IF NOT EXISTS `julies_db`;


USE `julies_db`;

--
-- Table structure for table `category_list`
--

CREATE TABLE `category_list` (
  `category_id` int(30) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `category_list`
--

INSERT INTO `category_list` (`category_id`, `name`, `description`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(1, 'Baked Classics', 'Various types of cakes', 1, 0, '2022-02-14 09:16:23', '2022-02-14 09:18:40'),
(2, 'Baked Loaves', 'Delicious pastries', 1, 0, '2022-02-14 09:19:04', NULL),
(3, 'Baked Savouries', 'Freshly baked breads', 1, 0, '2022-02-14 09:19:11', NULL),
(4, 'Baked Sweets', 'Sweet and savory pies', 1, 0, '2022-02-14 09:19:18', NULL),
(5, 'Fried Breads', 'Assorted cookies', 1, 0, '2022-02-14 09:19:24', NULL),
(6, 'Pastries', 'Beverages', 1, 0, '2022-02-14 09:19:37', NULL),
(7, 'Snack Cakes', 'Sweet treats', 1, 0, '2022-02-14 09:19:43', NULL),
(8, 'Non Bread', 'Savoury snacks', 1, 0, '2022-02-14 09:19:49', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_list`
--

CREATE TABLE `product_list` (
  `product_id` int(30) NOT NULL,
  `product_code` text NOT NULL,
  `category_id` int(30) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `price` double NOT NULL DEFAULT 0,
  `alert_restock` double NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `image_path` VARCHAR(255) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `product_list`
-- 

INSERT INTO `product_list` (`product_id`, `product_code`, `category_id`, `name`, `description`, `price`, `alert_restock`, `status`, `delete_flag`, `date_created`, `date_updated`, `image_path`) VALUES
(1, '001', 1, 'Pandesal', 'Classic Pandesal.', 15, 5, 1, 0, '2022-02-14 09:42:00', '2022-02-14 11:52:23', 'images\\Classic\\pandesal.jpg'),
(2, '002', 1, 'Everlasting', 'flower-like appearance and topped with refined sugar.', 15, 5, 1, 0, '2022-02-14 09:42:00', '2022-02-14 11:52:23', 'images\\Classic\\Everlasting.jpg'),
(3, '003', 1, 'Mushroom Bread', 'Mushroom Looking bread.', 45, 5, 1, 0, '2022-02-14 09:42:00', '2022-02-14 11:52:32', 'images\\Classic\\mushroom.jpg'),
(4, '004', 1, 'Corn Bread', 'Corn Looking bread.', 20, 5, 1, 0, '2022-02-14 09:42:00', '2022-02-14 11:52:43', 'images\\Classic\\Corn-Bread.jpg'),
(5, '005', 1, 'Dinner Rolls', 'A soft creamy bread and perfect pairing for a cup of coffee.', 75, 5, 1, 0, '2022-02-14 09:46:59', '2022-02-14 11:52:50', 'images\\Classic\\Dinner-Rolls-1.jpg'),
(6, '006', 1, 'Pan de Julia', 'Julies take on the classic pandesal made more special.', 6, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Classic\\Pan-de-Julia-.jpg'),
(7, '007', 1, 'Pan de Sal Pugon', 'Special pandesal made special with the unique pugon taste.', 45, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Classic\\Pan_de_sal.jpg'),
(8, '008', 1, 'Butter Bun', 'Lean bread coated with bread crumbs, with a slit on top and piped with margarine.', 5, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Classic\\Butter-Bun-1.jpg'),
(9, '009', 2, 'Bayan Sliced Bread', 'Soft texture and slightly sweet flavor, making it ideal for sandwiches and everyday snacks.', 55, 5, 1, 0, '2022-02-14 09:42:00', '2022-02-14 11:52:16', 'images\\Baked Loaves\\Bayan-Sliced-Bread.jpg'),
(10, '010', 2, 'Graciosa Loaf', 'Soft bread and yellowish crumb topped with icing and grated cheese that will surely catch your taste.', 28, 5, 1, 0, '2022-02-14 09:42:00', '2022-02-14 11:52:23', 'images\\Baked Loaves\\Graciosa-Loaf.jpg'),
(11, '011', 2, 'Regular Sliced Bread', 'An all-time favorite bread that is soft and has a fine crumb. It has a moderately sweet taste that truly compliments any spread or dressing and can be consumed even without them.', 50, 5, 1, 0, '2022-02-14 09:42:00', '2022-02-14 11:52:32', 'images\\Baked Loaves\\Regular-Sliced-Bread.jpg'),
(12, '012', 2, 'Special Sliced Bread', 'Soft, fluffy texture and rich taste. Its designed to be more flavorful and satisfying than regular sliced bread, perfect for both sweet and savory spreads or as a base for gourmet sandwiches.', 55, 5, 1, 0, '2022-02-14 09:42:00', '2022-02-14 11:52:43', 'images\\Baked Loaves\\Special-Sliced-Bread.jpg'),
(13, '013', 2, 'Violet Cream Loaf', 'Intricate violet and white braided loaf, topped with sweet creamy icing and grated cheese.', 25, 5, 1, 0, '2022-02-14 09:46:59', '2022-02-14 11:52:50', 'images\\Baked Loaves\\Violet-Cream-Loaf.jpg'),
(14, '014', 3, 'Cheese Bread', 'Soft crumb with a slightly shiny golden brown crust and filled with a cheese strip.', 10, 5, 1, 0, '2022-02-14 09:42:00', '2022-02-14 11:52:16', 'images\\Baked Savouries\\cheese_bread.jpg'),
(15, '015', 3, 'Cheese Streusel', 'A soft, sweet bread with diced cheese filling and a creamy, gritty topping, featuring a unique cheese flavor.', 15, 5, 1, 0, '2022-02-14 09:42:00', '2022-02-14 11:52:23', 'images\\Baked Savouries\\cheese_streusel.jpg'),
(16, '016', 3, 'Cheese Bun', 'Bread with a lightly shiny golden brown crust and filled with a cheese strip inside.', 45, 5, 1, 0, '2022-02-14 09:42:00', '2022-02-14 11:52:32', 'images\\Baked Savouries\\cheese_bun.jpg'),
(17, '017', 3, 'Chicken Roll', 'Rounded bread coated with breadcrumbs, filled with sweet and sour chicken strips.', 20, 5, 1, 0, '2022-02-14 09:42:00', '2022-02-14 11:52:43', 'images\\Baked Savouries\\chicken_roll.jpg'),
(18, '018', 3, 'Siopao', 'A favorite Filipino snack; a steamed bun filled with a savory meat filling.', 28, 5, 1, 0, '2022-02-14 09:46:59', '2022-02-14 11:52:50', 'images\\Baked Savouries\\siopao.jpg'),
(19, '019', 4, 'Choco German', 'With a creamy choco filling and topped with a sugary-margarine topping to complete the taste.', 24, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Baked Sweets\\Choco-German-1.png'),
(20, '020', 4, 'Ube Cheese de Sal', 'Classic but with a little twist that makes you great to be around in small and big gatherings.', 30, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Baked Sweets\\Ube-Cheese-de-Sal-1.png'),
(21, '021', 4, 'Ensaymada', 'Twirl bread with rich golden brown crust and yellowish crumb with an intense butter note, and topped with sugar.', 6, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Baked Sweets\\Ensaymada.png'),
(22, '022', 4, 'Pan de Coco', 'The traditional afternoon sweet delight made from grated coco then cooked to a sugar concoction locally known as “bukayo” makes this bread unique and truly Pinoy. It has a distinct gritty mouthfeel imparted by the grated coco.', 5, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Baked Sweets\\Pan-de-Coco-1.png'),
(23, '023', 4, 'Spanish Bread', 'A crescent-shaped roll that is soft with a sweet-creamy, moist and rich mixture of filling and coated with bread crumbs.', 21, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Baked Sweets\\Spanish-Bread-1.png'),
(24, '024', 4, 'Choco Loco Loaf', 'Chocolate bread which has soft, bitter sweet chocolate taste.', 68, 5, 1, 1, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Baked Sweets\\Choco-Loco-Bread.png'),
(25, '025', 4, 'Ensaymada Cheese', 'Twirl bread with rich golden brown crust and yellowish crumb with an intense butter note, topped with sugar and grated cheese.', 10, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Baked Sweets\\Ensaymada.png'),
(26, '026', 4, 'Graciosa with Icing', 'Round bread pieces that form a big rectangular-shaped bread, brushed evenly with margarine sweetened with sprinkled with sugar.', 28, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Baked Sweets\\Graciosa-with-Icing-1.png'),
(27, '027', 5, 'Bicho-Bicho', 'Classic fried bread, elongated in shape and filled with cheese and coated with sugar.', 21, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Fried Breads\\Bicho-Bicho-1.png'),
(28, '028', 5, 'Binangkal', 'Round bread coated with sesame seeds. Crusty bread with a slightly sweet taste.', 21, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Fried Breads\\Binangkal.png'),
(29, '029', 5, 'Buchi', 'Round fried bread filled with ube/mongo and coated with refined sugar.', 10, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Fried Breads\\Buchi-1.png'),
(30, '030', 5, 'Doughnut', 'Wheel shaped fried bread dredged in sugar.', 5, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Fried Breads\\Doughnut-1.png'),
(31, '031', 5, 'Siacoy', 'A twisted doughnut coated with refined sugar. It has slightly fermented-creamy flavor and taste.', 5, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Fried Breads\\Siacoy.png'),
(32, '032', 5, 'Ube Fried Bread', 'Another variety of fried bread, triangular in shape filled with ube filling and coated with coarse bread crumbs.', 5, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Fried Breads\\Ube-Fried-Bread-1.png'),
(33, '033', 4, 'Kalihim', 'Filled with a sweet, vibrant red or pink filling made from breadcrumbs, sugar, and food coloring.', 18, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Fried Breads\\Ube-Fried-Bread-1.png'),
(34, '034', 4, 'Cinnamon Bun', 'Bread Bun filled with sugar and cinnamon which provide a robust and sweet flavor, topped with glazed icing.', 15, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Fried Breads\\Ube-Fried-Bread-1.png'),
(35, '035', 6, 'Chocolate Crinkles', 'Soft and moist, fudge-like chocolate cookie coated in confectioners sugar.', 6, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Pastries\\Chocolate-Crinkles.png'),
(36, '036', 6, 'Yoyo', ' Consists of two halves filled with a sugar paste and coated with sugar crystals.', 6, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Pastries\\Yoyo.png'),
(37, '037', 6, 'Choco Chip Cookies', 'Chewy cookie topped with chocolate chips.', 6, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Pastries\\Choco-Chip-Cookies.png'),
(38, '038', 6, 'Fig Pie', 'Bread with a flaky crust and enticingly sweet with a distinctive onion flavored filling.', 5, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Pastries\\Fig-Pie.png'),
(39, '039', 6, 'Hopia', 'The favorite flaky pastry treats with distinct onion flavor.', 18, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Pastries\\Hopia.png'),
(40, '040', 6, 'Pineapple Pie', 'A sweet treat filled with a rich pineapple filling.', 5, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Pastries\\Pineapple-Pie.png'),
(41, '041', 1, 'Elorde', 'Reddish-brown to light brown crust with one cut in the center. It has very fine, closed grains with smooth and cottony crumb with rich milky & creamy aroma.', 15, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Classic\\Elorde.png'),
(42, '042', 1, 'Francis', 'Nearly bland in taste with a soft honeycomb structure; semi-elongated in shape with pointed ends and a slit on top. Topped with freshly prepared bread crumbs.', 5, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Classic\\Francis.png'),
(43, '043', 1, 'King Roll', 'A lean type of bread with a crisp crust and a slight anise flavor and taste.', 6, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Classic\\King-Roll.png'),
(44, '044', 1, 'Monay', 'A round shaped creamy bread with a fine texture and creamy-sweet taste.', 5, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Classic\\Monay.png'),
(45, '045', 1, 'Pan de Leche', 'Reddish brown crust, rich in milky and creamy taste.', 10, 5, 1, 0, '2022-02-14 09:47:22', '2022-02-14 09:48:32', 'images\\Classic\\Pan-de-Leche.png'),
(46, '046', 8, 'Julie''s Kape', 'Coffee of Julies.', 10, 5, 1, 0, '2022-02-14 09:42:00', '2022-02-14 11:52:16', 'images\\Non-Bread\\kape.jpg'),
(47, '047', 8, 'Julie''s Peanut Butter 100g', 'Peanut Butter that meets your bread spread needs.', 15, 5, 1, 0, '2022-02-14 09:42:00', '2022-02-14 11:52:23', 'images\\Non-Bread\\peanut_butter_100g.jpg'),
(48, '048', 8, 'Julie''s Peanut Butter 230g', 'Peanut Butter that meets more of your bread spread needs.', 15, 5, 1, 0, '2022-02-14 09:42:00', '2022-02-14 11:52:23', 'images\\Non-Bread\\peanut_butter_230g.jpg'),
(49, '049', 8, 'Julie''s Peanut Butter 500g', 'Peanut Butter that meets all of your bread spread needs.', 45, 5, 1, 0, '2022-02-14 09:42:00', '2022-02-14 11:52:32', 'images\\Non-Bread\\peanut_butter_500g'),
(50, '050', 8, 'Coke Mismo', 'A Small Coke intended for snacks.', 20, 5, 1, 0, '2022-02-14 09:42:00', '2022-02-14 11:52:43', 'images\\Non-Bread\\coke_mismo.png'),
(51, '051', 8, 'Coke 1.5L', 'Bigger Coke suitable for group gatherings.', 75, 5, 1, 0, '2022-02-14 09:46:59', '2022-02-14 11:52:50', 'images\\Non-Bread\\Coke-1.5-L.png'),
(52, '052', 7, 'Cheesy Mamon', 'A soft and spongy treat with grated cheese, and delightfully rich butter aroma and cheese notes.', 10, 5, 1, 0, '2022-02-14 09:46:59', '2022-02-14 11:52:50', 'images\\Snack Cakes\\Cheesy-Mamon-1.png'),
(53, '053', 7, 'Torta with Raisin', 'With sweet creamy taste and flavor topped with raisin and sugar crystals to enhance the taste.', 10, 5, 1, 0, '2022-02-14 09:46:59', '2022-02-14 11:52:50', 'images\\Snack Cakes\\Torta-with-Raisin-1.png'),
(54, '054', 7, 'Chocolate Cupcake', 'A very soft, sweet type flavored-chocolate cupcake topped with peanuts.', 5, 5, 1, 0, '2022-02-14 09:46:59', '2022-02-14 11:52:50', 'images\\Snack Cakes\\chocolate-cupcake.png'),
(55, '055', 7, 'Torta', 'A sweet creamy taste and flavor topped raisin and sugar crystal to enhance the taste.', 5, 5, 1, 0, '2022-02-14 09:46:59', '2022-02-14 11:52:50', 'images\\Snack Cakes\\Torta-1.png'),
(56, '056', 7, 'Ube Bar', 'Soft, spongy cake with sweet, dairy ube coated with sugar and desiccated coconut.', 10, 5, 1, 0, '2022-02-14 09:46:59', '2022-02-14 11:52:50', 'images\\Snack Cakes\\Ube-Bar-1.png'),
(57, '057', 7, 'Cheese Cupcake', 'Soft and moist cake with sweet cheesy note, topped with condensed milk & grated cheese.', 5, 5, 1, 0, '2022-02-14 09:46:59', '2022-02-14 11:52:50', 'images\\Snack Cakes\\Cheese-Cupcake-1.png'),
(58, '058', 7, 'Marble Cupcake', 'Moist cupcake with a chocolate marbling and with a combination of sweet & slightly butter note', 5, 5, 1, 0, '2022-02-14 09:46:59', '2022-02-14 11:52:50', 'images\\Snack Cakes\\Marble-Cupcake-1.png');

--
-- Table structure for table `stock_list`
--

CREATE TABLE `stock_list` (
  `stock_id` int(30) NOT NULL,
  `product_id` int(30) NOT NULL,
  `quantity` double NOT NULL DEFAULT 0,
  `expiry_date` datetime NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `stock_list`
--

INSERT INTO `stock_list` (`stock_id`, `product_id`, `quantity`, `expiry_date`, `date_added`) VALUES
(1, 1, 50, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(2, 2, 50, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(3, 3, 50, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(4, 4, 50, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(5, 5, 50, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(6, 6, 50, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(7, 7, 50, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(8, 8, 50, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(9, 9, 50, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(10, 10, 50, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(11, 11, 50, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(12, 12, 35, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(13, 13, 15, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(14, 14, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(15, 15, 30, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(16, 16, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(17, 17, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(18, 18, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(19, 19, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(20, 20, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(21, 21, 20, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(22, 22, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(23, 23, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(24, 24, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(25, 25, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(26, 26, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(27, 27, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(28, 28, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(29, 29, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(30, 30, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(31, 31, 20, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(32, 32, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(33, 33, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(34, 34, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(35, 35, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(36, 36, 20, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(37, 37, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(38, 38, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(39, 39, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(40, 40, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(41, 41, 20, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(42, 42, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(43, 43, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(44, 44, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(45, 45, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(46, 46, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(47, 47, 20, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(48, 48, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(49, 49, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(50, 50, 25, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(51, 51, 20, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(52, 52, 20, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(53, 53, 20, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(54, 54, 20, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(55, 55, 20, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(56, 56, 20, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(57, 57, 20, '2025-12-25 00:00:00', '2024-06-25 02:05:45'),
(58, 58, 20, '2025-12-25 00:00:00', '2024-06-25 02:05:45');
-- --------------------------------------------------------

-- 
-- Table structure for table `transaction_items`
--

CREATE TABLE `transaction_items` (
  `transaction_id` int(30) NOT NULL,
  `product_id` int(30) NOT NULL,
  `quantity` double NOT NULL DEFAULT 0,
  `price` double NOT NULL DEFAULT 0,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transaction_items`
--

-- --------------------------------------------------------

--
-- Table structure for table `transaction_list`
--

CREATE TABLE `transaction_list` (
  `transaction_id` int(30) NOT NULL,
  `receipt_no` text NOT NULL,
  `total` double NOT NULL DEFAULT 0,
  `tendered_amount` double NOT NULL DEFAULT 0,
  `change` double NOT NULL DEFAULT 0,
  `user_id` int(30) DEFAULT 1,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transaction_list`
--


-- --------------------------------------------------------

--
-- Table structure for table `user_list`
-- // `type` int(30) NOT NULL DEFAULT 1,

CREATE TABLE `user_list` (
  `user_id` int(30) NOT NULL,
  `fullname` text NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `type` ENUM('1', '0') NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `email` VARCHAR(255),
  `phone_number` VARCHAR(20),
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  INDEX `idx_user_id` (`user_id`)  -- Create an index on user_id
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


--
-- Dumping data for table `user_list` Default temporary pass for admin is "admin123" 
--

INSERT INTO `user_list` (`user_id`, `fullname`, `username`, `password`, `type`, `status`, `date_created`, `email`, `phone_number`) VALUES
(1, 'Administrator', 'admin', '0192023a7bbd73250516f069df18b500', 1, 1, '2022-02-14 00:44:30', 'admin@example.com', '1234567890'), 
(2, 'Claire Blake', 'cblake', 'cd74fae0a3adf459f73bbf187607ccea', 0, 1, '2022-02-14 02:29:23', 'cblake@example.com', '0987654321'),
(3, 'Elgen Mar', 'elgen', 'cd74fae0a3adf459f73bbf187607ccea', 0, 1, '2022-02-14 02:29:23', 'elgen@example.com', '098312124123'),
(4, 'Mark Cooper', 'mcooper', '0c4635c5af0f173c26b0d85b6c9b398b', 1, 1, '2022-02-14 02:29:58', 'mcooper@example.com', '1122334455');


CREATE TABLE `cashier_shifts` (
  `shift_id` int(11) NOT NULL AUTO_INCREMENT,
  `cashier_id` int(30) NOT NULL,
  `starting_cash` decimal(10,2) NOT NULL,
  `ending_cash` decimal(10,2) DEFAULT NULL,
  `starting_inventory` int(11) NOT NULL,
  `ending_inventory` int(11) DEFAULT NULL,
  `sales` decimal(10,2) DEFAULT NULL,
  `notes` text,
  `shift_date` date NOT NULL,
  `time_in` timestamp NOT NULL DEFAULT current_timestamp(),
  `time_out` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`shift_id`),
  KEY `fk_cashier_id` (`cashier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for table `category_list`
--
ALTER TABLE `category_list`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `product_list`
--
ALTER TABLE `product_list`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `stock_list`
--
ALTER TABLE `stock_list`
  ADD PRIMARY KEY (`stock_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD KEY `product_id` (`product_id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `transaction_list`
--
ALTER TABLE `transaction_list`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_list`
ALTER TABLE `user_list`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for table `category_list`
--
ALTER TABLE `category_list`
  MODIFY `category_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `product_list`
--
ALTER TABLE `product_list`
  MODIFY `product_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `stock_list`
--
ALTER TABLE `stock_list`
  MODIFY `stock_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transaction_list`
--
ALTER TABLE `transaction_list`
  MODIFY `transaction_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_list`
--
ALTER TABLE `user_list`
  MODIFY `user_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_list`
--
ALTER TABLE `product_list`
  ADD CONSTRAINT `product_list_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category_list` (`category_id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_list`
--
ALTER TABLE `stock_list`
  ADD CONSTRAINT `stock_list_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_items_ibfk_2` FOREIGN KEY (`transaction_id`) REFERENCES `transaction_list` (`transaction_id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_list`
--
ALTER TABLE `transaction_list`
  ADD CONSTRAINT `transaction_list_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_list` (`user_id`) ON DELETE SET NULL;

ALTER TABLE `cashier_shifts`
  ADD CONSTRAINT `fk_cashier_id` FOREIGN KEY (`cashier_id`) REFERENCES `user_list` (`user_id`) ON DELETE CASCADE;

COMMIT;
