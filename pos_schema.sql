-- POS Orders Table
CREATE TABLE IF NOT EXISTS `pos_orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('Cash','Card','GCash') NOT NULL DEFAULT 'Cash',
  `status` enum('Completed','Cancelled') NOT NULL DEFAULT 'Completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- POS Order Items Table
CREATE TABLE IF NOT EXISTS `pos_order_items` (
  `order_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_time` decimal(10,2) NOT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `order_id` (`order_id`),
  KEY `menu_item_id` (`menu_item_id`),
  CONSTRAINT `pos_order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `pos_orders` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `pos_order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Inventory Items Table (Raw Stock / Merchandise)
CREATE TABLE IF NOT EXISTS `inventory_items` (
  `inventory_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT 'General',
  `unit` varchar(50) DEFAULT 'pcs',
  `current_stock` int(11) NOT NULL DEFAULT 0,
  `reorder_level` int(11) NOT NULL DEFAULT 10,
  `cost_per_unit` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`inventory_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Financial Batches Table (Tracks "1st Batch" Investment vs Revenue)
CREATE TABLE IF NOT EXISTS `financial_batches` (
  `batch_id` int(11) NOT NULL AUTO_INCREMENT,
  `batch_name` varchar(255) NOT NULL DEFAULT 'Batch 1',
  `target_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Investment Goal',
  `collected_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Revenue from Sales',
  `status` enum('Active','Completed') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`batch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add stock_quantity column to menu table if it doesn't exist
-- We'll simulate this check in PHP or just run the ALTER and ignore error if exists,
-- but pure SQL requires a procedure for "IF NOT EXISTS column".
-- For this environment, I'll run a direct ALTER TABLE and catch the error in PHP if needed,
-- or use a safe approach.
-- Since I can run PHP, I'll execute this via a PHP script that handles the logic.
