<?php
// ============================================================
// index.php — Αρχική σελίδα (dashboard)
// Δείχνει σύνολα: πλήθος πελατών, προϊόντων, παραγγελιών
// και συνολικά έσοδα από τιμολογημένες/αποσταλμένες παραγγελίες.
// ============================================================

// Φόρτωση ρυθμίσεων και αντικειμένου $pdo
require_once __DIR__ . '/../includes/config.php';

// Μεταβλητή για τίτλο της καρτέλας — διαβάζεται από header.php
$pageTitle = 'PAPI-net — Αρχική';

// COUNT queries — απλά και ασφαλή (καμία είσοδος χρήστη, αλλά παραμένουμε σε prepare style)
$totalClients  = (int)$pdo->query('SELECT COUNT(*) AS c FROM clients')->fetch()['c'];
$totalProducts = (int)$pdo->query('SELECT COUNT(*) AS c FROM products')->fetch()['c'];
$totalOrders   = (int)$pdo->query('SELECT COUNT(*) AS c FROM orders')->fetch()['c'];

// Υπολογισμός συνολικών εσόδων (sum από order_items όπου order.status IN ('PAID','SHIPPED'))
$revenueRow = $pdo->query("
    SELECT COALESCE(SUM(oi.qty * oi.unit_price), 0) AS revenue
    FROM order_items oi
    JOIN orders o ON o.id = oi.order_id
    WHERE o.status IN ('PAID','SHIPPED')
")->fetch();
$revenue = (float)$revenueRow['revenue'];

// Τελευταίες 5 παραγγελίες — JOIN με πελάτες για το όνομα
$lastOrders = $pdo->query("
    SELECT o.id, o.order_date, o.status, c.fullname
    FROM orders o
    JOIN clients c ON c.id = o.client_id
    ORDER BY o.id DESC
    LIMIT 5
")->fetchAll();

// Φόρτωση header (HTML + nav)
require_once __DIR__ . '/../includes/header.php';
?>

<h2>Καλωσορίσατε</h2>
<p>Αυτή είναι η αρχική σελίδα του συστήματος διαχείρισης παραγγελιών <strong>PAPI-net</strong>.</p>

<!-- Κάρτες με σύνολα -->
<section class="cards">
    <div class="card">
        <div class="card-num"><?= $totalClients ?></div>
        <div class="card-lbl">Πελάτες</div>
    </div>
    <div class="card">
        <div class="card-num"><?= $totalProducts ?></div>
        <div class="card-lbl">Προϊόντα</div>
    </div>
    <div class="card">
        <div class="card-num"><?= $totalOrders ?></div>
        <div class="card-lbl">Παραγγελίες</div>
    </div>
    <div class="card">
        <!-- number_format για ελληνική μορφή (κόμμα δεκαδικό, τελεία χιλιάδων) -->
        <div class="card-num"><?= number_format($revenue, 2, ',', '.') ?> &euro;</div>
        <div class="card-lbl">Έσοδα</div>
    </div>
</section>

<h3>Τελευταίες 5 παραγγελίες</h3>
<table class="data">
    <thead>
        <tr><th>#</th><th>Πελάτης</th><th>Ημερομηνία</th><th>Κατάσταση</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach ($lastOrders as $o): ?>
        <tr>
            <td><?= (int)$o['id'] ?></td>
            <td><?= h($o['fullname']) ?></td>
            <td><?= h($o['order_date']) ?></td>
            <td><?= h($o['status']) ?></td>
            <td><a href="orders_view.php?id=<?= (int)$o['id'] ?>">Άνοιγμα</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php
// Φόρτωση footer (κλείσιμο html)
require_once __DIR__ . '/../includes/footer.php';
