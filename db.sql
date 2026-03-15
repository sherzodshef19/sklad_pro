-- =============================================
-- Sklad.pro - Database Schema
-- Date: 2026-03-15
-- =============================================

CREATE DATABASE IF NOT EXISTS sklad_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sklad_db;

-- ----------------------------
-- Users
-- ----------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id`       INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50)  NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin (password: admin123)
INSERT IGNORE INTO `users` (`username`, `password`)
VALUES ('admin', 'admin123');

-- ----------------------------
-- Products
-- ----------------------------
CREATE TABLE IF NOT EXISTS `products` (
    `id`             INT AUTO_INCREMENT PRIMARY KEY,
    `name`           VARCHAR(255)   NOT NULL,
    `quantity`       INT            DEFAULT 0,
    `expiry_date`    DATE           NULL,
    `purchase_price` DECIMAL(10,2)  NOT NULL,
    `selling_price`  DECIMAL(10,2)  NOT NULL,
    `created_at`     TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Customers
-- ----------------------------
CREATE TABLE IF NOT EXISTS `customers` (
    `id`         INT AUTO_INCREMENT PRIMARY KEY,
    `name`       VARCHAR(255) NOT NULL,
    `phone`      VARCHAR(20)  NULL,
    `created_at` TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Income (Warehouse Incoming)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `income` (
    `id`         INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT            NOT NULL,
    `quantity`   INT            NOT NULL,
    `price`      DECIMAL(10,2)  NOT NULL,
    `date`       DATETIME       DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Sales (Grouped Transactions)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `sales` (
    `id`           INT AUTO_INCREMENT PRIMARY KEY,
    `customer_id`  INT            NULL,
    `total_amount` DECIMAL(10,2)  NOT NULL DEFAULT 0,
    `date`         DATETIME       DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Outcome (Sales Line Items)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `outcome` (
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `sale_id`     INT            NULL,
    `product_id`  INT            NOT NULL,
    `customer_id` INT            NULL,
    `quantity`    INT            NOT NULL,
    `price`       DECIMAL(10,2)  NOT NULL,
    `date`        DATETIME       DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`sale_id`)     REFERENCES `sales`(`id`)     ON DELETE CASCADE,
    FOREIGN KEY (`product_id`)  REFERENCES `products`(`id`)  ON DELETE CASCADE,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
