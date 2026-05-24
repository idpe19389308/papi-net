<?php
// ============================================================
// config.php — Κεντρική ρύθμιση σύνδεσης βάσης (PDO)
// Φορτώνεται από ΟΛΕΣ τις σελίδες της εφαρμογής με require_once.
// Παρέχει: $pdo (αντικείμενο PDO), σταθερές βάσης, διαχείριση σφαλμάτων.
// ============================================================

// Παράμετροι σύνδεσης — προεπιλογές XAMPP (Apache+MariaDB local)
define('DB_HOST', '127.0.0.1');          // Hostname της MariaDB (localhost)
define('DB_NAME', 'papinet');            // Όνομα βάσης (όπως στο schema.sql)
define('DB_USER', 'root');               // XAMPP default χρήστης
define('DB_PASS', '');                   // XAMPP default κενός κωδικός — αλλάξτε σε production!
define('DB_CHARSET', 'utf8mb4');         // Κωδικοποίηση για πλήρη υποστήριξη ελληνικών

// Κατασκευή του DSN string που χρειάζεται το PDO
$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

// Επιλογές PDO — κρίσιμες για ασφάλεια και σωστή λειτουργία
$options = [
    // Πέτα Exceptions στα SQL errors (αντί για silent failure)
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // Επιστροφή σαν associative arrays (πιο εύχρηστο)
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // Πραγματικά prepared statements (όχι emulated) — πιο ασφαλές
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Προσπάθεια σύνδεσης — αν αποτύχει, τερματίζουμε με μήνυμα στα ελληνικά
try {
    // Δημιουργία του global $pdo που χρησιμοποιούν όλες οι σελίδες
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Σε production μην δείχνετε ποτέ getMessage() στον χρήστη — log it instead.
    http_response_code(500);
    die('Σφάλμα σύνδεσης στη βάση: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

// Ενημέρωση του browser ότι στέλνουμε HTML σε UTF-8 (πριν από οποιοδήποτε echo)
header('Content-Type: text/html; charset=utf-8');

// Βοηθητική συνάρτηση: ασφαλές output ελληνικών (escape για XSS)
// Χρήση: echo h($row['fullname']);
function h(?string $s): string {
    // htmlspecialchars με ENT_QUOTES και UTF-8 — η σωστή πρακτική για ελληνικά
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}
