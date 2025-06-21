-- Set the SQL mode to disable auto-increment reset behavior
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

-- Start a new transaction
START TRANSACTION;

-- Set the time zone for the database operations
SET time_zone = "+08:00";

-- Create the database if it doesn't already exist
CREATE DATABASE IF NOT EXISTS `julies_db`;

-- Use the created database for subsequent operations
USE `julies_db`;

-- Table structure for the category_list table
CREATE TABLE `category_list` (
  `category_id` int(30) NOT NULL,                      -- Unique identifier for the category, using a large integer
  `name` text NOT NULL,                                -- Name of the category, stored as text
  `description` text NOT NULL,                         -- Description of the category, stored as text
  `status` tinyint(1) NOT NULL DEFAULT 1,              -- Status flag (active/inactive), defaulting to active
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,         -- Soft delete flag, defaulting to not deleted
  `date_created` datetime NOT NULL DEFAULT current_timestamp(), -- Date when the record was created, with default current timestamp
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp() -- Date when the record was last updated, with auto-update
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;               -- InnoDB engine with utf8mb4 character set for wide character support

-- Table structure for the product_list table
CREATE TABLE `product_list` (
  `product_id` int(30) NOT NULL,                       -- Unique identifier for the product, using a large integer
  `product_code` text NOT NULL,                        -- Product code, stored as text
  `category_id` int(30) NOT NULL,                      -- Foreign key referencing category_id in category_list
  `name` text NOT NULL,                                -- Name of the product, stored as text
  `description` text NOT NULL,                         -- Description of the product, stored as text
  `price` double NOT NULL DEFAULT 0,                   -- Price of the product, defaulting to 0
  `alert_restock` double NOT NULL DEFAULT 0,           -- Stock level alert threshold, defaulting to 0
  `status` tinyint(1) NOT NULL DEFAULT 1,              -- Status flag (active/inactive), defaulting to active
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,         -- Soft delete flag, defaulting to not deleted
  `date_created` datetime NOT NULL DEFAULT current_timestamp(), -- Date when the record was created, with default current timestamp
  `image_path` VARCHAR(255) DEFAULT NULL,              -- Path to the product image, stored as variable-length string
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp() -- Date when the record was last updated, with auto-update
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;               -- InnoDB engine with utf8mb4 character set for wide character support

-- Table structure for the stock_list table
CREATE TABLE `stock_list` (
  `stock_id` int(30) NOT NULL,                         -- Unique identifier for the stock, using a large integer
  `product_id` int(30) NOT NULL,                       -- Foreign key referencing product_id in product_list
  `quantity` double NOT NULL DEFAULT 0,                -- Quantity of stock, defaulting to 0
  `expiry_date` datetime NOT NULL,                     -- Expiry date of the stock
  `date_added` timestamp NOT NULL DEFAULT current_timestamp() -- Date when the stock was added, with default current timestamp
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;               -- InnoDB engine with utf8mb4 character set for wide character support

-- Table structure for the transaction_items table
CREATE TABLE `transaction_items` (
  `transaction_id` int(30) NOT NULL,                   -- Foreign key referencing transaction_id in transaction_list
  `product_id` int(30) NOT NULL,                       -- Foreign key referencing product_id in product_list
  `quantity` double NOT NULL DEFAULT 0,                -- Quantity of product in the transaction, defaulting to 0
  `price` double NOT NULL DEFAULT 0,                   -- Price of the product in the transaction, defaulting to 0
  `date_added` timestamp NOT NULL DEFAULT current_timestamp() -- Date when the transaction item was added, with default current timestamp
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;               -- InnoDB engine with utf8mb4 character set for wide character support

-- Table structure for the transaction_list table
CREATE TABLE `transaction_list` (
  `transaction_id` int(30) NOT NULL,                   -- Unique identifier for the transaction, using a large integer
  `receipt_no` text NOT NULL,                          -- Receipt number, stored as text
  `total` double NOT NULL DEFAULT 0,                   -- Total amount of the transaction, defaulting to 0
  `tendered_amount` double NOT NULL DEFAULT 0,         -- Amount tendered by the customer, defaulting to 0
  `change` double NOT NULL DEFAULT 0,                  -- Change given to the customer, defaulting to 0
  `user_id` int(30) DEFAULT 1,                         -- Foreign key referencing user_id in user_list, defaulting to 1
  `date_added` timestamp NOT NULL DEFAULT current_timestamp() -- Date when the transaction was added, with default current timestamp
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;               -- InnoDB engine with utf8mb4 character set for wide character support

-- Table structure for the user_list table
CREATE TABLE `user_list` (
  `user_id` int(30) NOT NULL,                          -- Unique identifier for the user, using a large integer
  `fullname` text NOT NULL,                            -- Full name of the user, stored as text
  `username` text NOT NULL,                            -- Username, stored as text
  `password` text NOT NULL,                            -- Password, stored as text (hashed)
  `type` ENUM('1', '0') NOT NULL DEFAULT '0',          -- User type (e.g., admin or regular user), defaulting to regular
  `status` tinyint(1) NOT NULL DEFAULT 1,              -- Status flag (active/inactive), defaulting to active
  `email` VARCHAR(255),                                -- User's email address, stored as variable-length string
  `phone_number` VARCHAR(20),                          -- User's phone number, stored as variable-length string
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(), -- Date when the user record was created, with default current timestamp
  INDEX `idx_user_id` (`user_id`)                      -- Index on user_id for faster lookups
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;               -- InnoDB engine with utf8mb4 character set for wide character support

-- Table structure for the cashier_shifts table
CREATE TABLE `cashier_shifts` (
  `shift_id` int(11) NOT NULL AUTO_INCREMENT,          -- Unique identifier for the shift, using a medium integer with auto-increment
  `cashier_id` int(30) NOT NULL,                       -- Foreign key referencing user_id in user_list
  `starting_cash` decimal(10,2) NOT NULL,              -- Starting cash amount, with 2 decimal places
  `ending_cash` decimal(10,2) DEFAULT NULL,            -- Ending cash amount, with 2 decimal places
  `starting_inventory` int(11) NOT NULL,               -- Starting inventory count
  `ending_inventory` int(11) DEFAULT NULL,             -- Ending inventory count
  `sales` decimal(10,2) DEFAULT NULL,                  -- Total sales amount, with 2 decimal places
  `notes` text,                                        -- Additional notes about the shift, stored as text
  `shift_date` date NOT NULL,                          -- Date of the shift
  `time_in` timestamp NOT NULL DEFAULT current_timestamp(), -- Time when the shift started, with default current timestamp
  `time_out` timestamp NULL DEFAULT NULL,              -- Time when the shift ended, nullable
  PRIMARY KEY (`shift_id`),                            -- Primary key on shift_id
  KEY `fk_cashier_id` (`cashier_id`)                   -- Foreign key on cashier_id
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;               -- InnoDB engine with utf8mb4 character set for wide character support

-- Add primary key to category_list
ALTER TABLE `category_list`
  ADD PRIMARY KEY (`category_id`);

-- Add primary key and category_id key to product_list
ALTER TABLE `product_list`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

-- Add primary key and product_id key to stock_list
ALTER TABLE `stock_list`
  ADD PRIMARY KEY (`stock_id`),
  ADD KEY `product_id` (`product_id`);

-- Add product_id and transaction_id keys to transaction_items
ALTER TABLE `transaction_items`
  ADD KEY `product_id` (`product_id`),
  ADD KEY `transaction_id` (`transaction_id`);

-- Add primary key and user_id key to transaction_list
ALTER TABLE `transaction_list`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `user_id` (`user_id`);

-- Add primary key to user_list
ALTER TABLE `user_list`
  ADD PRIMARY KEY (`user_id`);

-- Auto-increment settings for category_list
ALTER TABLE `category_list`
  MODIFY `category_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

-- Auto-increment settings for product_list
ALTER TABLE `product_list`
  MODIFY `product_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

-- Auto-increment settings for stock_list
ALTER TABLE `stock_list`
  MODIFY `stock_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

-- Auto-increment settings for transaction_list
ALTER TABLE `transaction_list`
  MODIFY `transaction_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

-- Auto-increment settings for user_list
ALTER TABLE `user_list`
  MODIFY `user_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

-- Foreign key constraints for product_list
ALTER TABLE `product_list`
  ADD CONSTRAINT `product_list_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category_list` (`category_id`) ON DELETE CASCADE;

-- Foreign key constraints for stock_list
ALTER TABLE `stock_list`
  ADD CONSTRAINT `stock_list_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`product_id`) ON DELETE CASCADE;

-- Foreign key constraints for transaction_items
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_items_ibfk_2` FOREIGN KEY (`transaction_id`) REFERENCES `transaction_list` (`transaction_id`) ON DELETE CASCADE;

-- Foreign key constraints for transaction_list
ALTER TABLE `transaction_list`
  ADD CONSTRAINT `transaction_list_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_list` (`user_id`) ON DELETE SET NULL;

-- Foreign key constraints for cashier_shifts
ALTER TABLE `cashier_shifts`
  ADD CONSTRAINT `fk_cashier_id` FOREIGN KEY (`cashier_id`) REFERENCES `user_list` (`user_id`) ON DELETE CASCADE;

-- Commit the transaction to apply all changes
COMMIT;
