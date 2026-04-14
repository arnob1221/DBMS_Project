-- ============================================================
--  E-COMMERCE DATABASE  |  DBMS Course Project
--  Import this file in phpMyAdmin
-- ============================================================

CREATE DATABASE IF NOT EXISTS ecommerce_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE ecommerce_db;

-- ─────────────────────────────────────────
-- TABLE: categories
-- ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS categories (
  category_id   INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL UNIQUE,
  description   TEXT,
  created_at    DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ─────────────────────────────────────────
-- TABLE: products
-- ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS products (
  product_id    INT AUTO_INCREMENT PRIMARY KEY,
  category_id   INT NOT NULL,
  name          VARCHAR(200) NOT NULL,
  description   TEXT,
  price         DECIMAL(10,2) NOT NULL CHECK (price >= 0),
  stock         INT NOT NULL DEFAULT 0,
  image_url     VARCHAR(500),
  created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE RESTRICT
);

-- ─────────────────────────────────────────
-- TABLE: customers
-- ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS customers (
  customer_id   INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(150) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  phone         VARCHAR(20),
  address       TEXT,
  created_at    DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ─────────────────────────────────────────
-- TABLE: orders
-- ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS orders (
  order_id      INT AUTO_INCREMENT PRIMARY KEY,
  customer_id   INT NOT NULL,
  total_amount  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  status        ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE RESTRICT
);

-- ─────────────────────────────────────────
-- TABLE: order_items
-- ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS order_items (
  item_id       INT AUTO_INCREMENT PRIMARY KEY,
  order_id      INT NOT NULL,
  product_id    INT NOT NULL,
  quantity      INT NOT NULL CHECK (quantity > 0),
  unit_price    DECIMAL(10,2) NOT NULL,
  subtotal      DECIMAL(10,2) GENERATED ALWAYS AS (quantity * unit_price) STORED,
  FOREIGN KEY (order_id)   REFERENCES orders(order_id)   ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT
);

-- ─────────────────────────────────────────
-- SEED DATA
-- ─────────────────────────────────────────

INSERT INTO categories (name, description) VALUES
  ('Electronics',   'Gadgets, phones, laptops and accessories'),
  ('Clothing',      'Fashion for men, women and kids'),
  ('Books',         'Academic, fiction and non-fiction books'),
  ('Home & Living', 'Furniture, decor and kitchen items');

INSERT INTO products (category_id, name, description, price, stock, image_url) VALUES
  (1, 'Wireless Earbuds',   'Bluetooth earbuds with noise cancellation', 2499.00, 50,  'https://images.unsplash.com/photo-1572536147248-ac59a8abfa4b?w=400'),
  (1, 'Smartphone Stand',   'Adjustable aluminum phone holder',           699.00, 120, 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?w=400'),
  (2, 'Cotton T-Shirt',     'Soft breathable 100% cotton, all sizes',     399.00, 200, 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400'),
  (2, 'Denim Jacket',       'Classic denim jacket, slim fit',            1899.00,  75, 'https://images.unsplash.com/photo-1551537482-f2075a1d41f2?w=400'),
  (3, 'Clean Code',         'A Handbook of Agile Software Craftsmanship',  850.00,  40, 'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=400'),
  (3, 'Database Systems',   'Silberschatz – perfect for DBMS course',    1200.00,  30, 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400'),
  (4, 'Ceramic Mug Set',    'Set of 4 hand-painted ceramic mugs',          599.00,  90, 'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=400'),
  (4, 'LED Desk Lamp',      'Adjustable LED lamp with USB charging port', 1499.00,  60, 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=400');

INSERT INTO customers (name, email, phone, address) VALUES
  ('Rahim Uddin',   'rahim@example.com',   '01711-111111', 'Dhaka, Mirpur'),
  ('Suma Akter',    'suma@example.com',    '01812-222222', 'Chittagong, Agrabad'),
  ('Karim Hossain', 'karim@example.com',   '01611-333333', 'Sylhet, Zindabazar');
