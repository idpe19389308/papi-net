-- ============================================================
-- schema.sql — Σχήμα βάσης δεδομένων για το PAPI-net
-- Σενάριο: Διαχείριση Παραγγελιών (clients, products, orders, order_items)
-- Στόχος: εκπαιδευτική web εφαρμογή πάνω σε XAMPP (Apache + PHP + MariaDB)
-- Κωδικοποίηση: UTF8MB4 για πλήρη υποστήριξη ελληνικών χαρακτήρων + emoji
-- Μηχανή αποθήκευσης: InnoDB για foreign keys + transactions
-- ============================================================

-- Δημιουργία της βάσης (αν δεν υπάρχει ήδη) με ελληνική συλλογή
CREATE DATABASE IF NOT EXISTS papinet
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Επιλογή της βάσης για όλες τις επόμενες εντολές
USE papinet;

-- ------------------------------------------------------------
-- Πίνακας πελατών (clients): οι αγοραστές που κάνουν παραγγελίες
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS clients (
  -- Πρωτεύον κλειδί με αυτόματη αρίθμηση
  id INT AUTO_INCREMENT PRIMARY KEY,
  -- Ονοματεπώνυμο πελάτη (υποχρεωτικό)
  fullname VARCHAR(120) NOT NULL,
  -- Email επικοινωνίας (προαιρετικό, μπορεί να είναι NULL)
  email VARCHAR(120),
  -- Τηλέφωνο (προαιρετικό)
  phone VARCHAR(30),
  -- Πόλη κατοικίας (προαιρετικό)
  city VARCHAR(60),
  -- Χρονοσήμανση δημιουργίας — γεμίζει αυτόματα
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Πίνακας προϊόντων (products): ο κατάλογος που πωλείται
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS products (
  -- Πρωτεύον κλειδί
  id INT AUTO_INCREMENT PRIMARY KEY,
  -- SKU (Stock Keeping Unit) — μοναδικός κωδικός προϊόντος
  sku VARCHAR(40) UNIQUE NOT NULL,
  -- Εμπορική ονομασία προϊόντος
  name VARCHAR(150) NOT NULL,
  -- Τιμή μονάδας σε ευρώ (10 ψηφία, 2 δεκαδικά)
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  -- Διαθέσιμο απόθεμα σε τεμάχια
  stock INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Πίνακας παραγγελιών (orders): η κεφαλίδα κάθε παραγγελίας
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
  -- Πρωτεύον κλειδί
  id INT AUTO_INCREMENT PRIMARY KEY,
  -- Ξένο κλειδί προς τον πελάτη που έκανε την παραγγελία
  client_id INT NOT NULL,
  -- Ημερομηνία παραγγελίας
  order_date DATE NOT NULL,
  -- Κατάσταση παραγγελίας (απαρίθμηση με σταθερές τιμές)
  status ENUM('NEW','PAID','SHIPPED','CANCELLED') DEFAULT 'NEW',
  -- Foreign key: αν διαγραφεί ο πελάτης, διαγράφονται και οι παραγγελίες του (CASCADE)
  CONSTRAINT fk_orders_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Πίνακας γραμμών παραγγελίας (order_items): τι αγοράστηκε ανά παραγγελία
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS order_items (
  -- Πρωτεύον κλειδί
  id INT AUTO_INCREMENT PRIMARY KEY,
  -- Ξένο κλειδί προς την παραγγελία
  order_id INT NOT NULL,
  -- Ξένο κλειδί προς το προϊόν
  product_id INT NOT NULL,
  -- Ποσότητα του προϊόντος στην συγκεκριμένη παραγγελία
  qty INT NOT NULL DEFAULT 1,
  -- Τιμή μονάδας τη στιγμή της παραγγελίας (snapshot — δεν αλλάζει αν αλλάξει η τιμή του προϊόντος αργότερα)
  unit_price DECIMAL(10,2) NOT NULL,
  -- Foreign key προς orders με CASCADE διαγραφή (διαγραφή παραγγελίας => διαγραφή γραμμών)
  CONSTRAINT fk_oi_order   FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
  -- Foreign key προς products χωρίς CASCADE — δεν διαγράφουμε προϊόν αν υπάρχει σε ιστορικές παραγγελίες
  CONSTRAINT fk_oi_product FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Τέλος schema.sql
-- ============================================================
