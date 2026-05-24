<?php
// ============================================================
// orders.php — Λίστα όλων των παραγγελιών + φόρμα νέας παραγγελίας
// Δείχνει: id, πελάτη (JOIN), ημερομηνία, κατάσταση, σύνολο γραμμών.
// Δημιουργία νέας παραγγελίας: επιλογή πελάτη + ημερομηνία (γραμμές μετά).
// ============================================================

require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'PAPI-net — Παραγγελίες';
$msg = '';

// --- POST: δημιουργία νέας παραγγελίας ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    // client_id έρχεται ως integer από select
    $client_id  = (int)($_POST['client_id'] ?? 0);
    // Ημερομηνία — αν κενή, σήμερα
    $order_date = $_POST['order_date'] ?? date('Y-m-d');
    // Κατάσταση — whitelist έλεγχος
    $status     = $_POST['status'] ?? 'NEW';
    $allowedStatuses = ['NEW','PAID','SHIPPED','CANCELLED'];
    if (!in_array($status, $allowedStatuses, true)) {
        $status = 'NEW';   // ασφαλές fallback
    }

    if ($client_id <= 0) {
        $msg = '<div class="err">Επιλέξτε πελάτη.</div>';
    } else {
        // Prepared INSERT — placeholders
        $stmt = $pdo->prepare('INSERT INTO orders (client_id, order_date, status) VALUES (?, ?, ?)');
        $stmt->execute([$client_id, $order_date, $status]);
        $newId = (int)$pdo->lastInsertId();
        // Redirect στη σελίδα προβολής για να προσθέσει γραμμές
        header('Location: orders_view.php?id=' . $newId);
        exit;
    }
}

// --- POST: διαγραφή ολόκληρης παραγγελίας ---
// FK ON DELETE CASCADE στις γραμμές παραγγελίας (order_items) → αυτόματη διαγραφή τους
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    // Λήψη id από κρυφό πεδίο φόρμας
    $del_id = (int)($_POST['id'] ?? 0);
    if ($del_id > 0) {
        // Prepared DELETE — προστασία από SQL injection
        $stmt = $pdo->prepare('DELETE FROM orders WHERE id=?');
        $stmt->execute([$del_id]);
        // PRG pattern — redirect για να αποτραπεί διπλή υποβολή με F5
        header('Location: orders.php?msg=deleted');
        exit;
    }
}
// Εμφάνιση μηνύματος επιτυχίας μετά από redirect (PRG pattern)
if (($_GET['msg'] ?? '') === 'deleted') {
    $msg = '<div class="ok">Η παραγγελία διαγράφηκε (μαζί με τις γραμμές της).</div>';
}

// --- ΦΙΛΤΡΟ ΚΑΤΑΣΤΑΣΗΣ (παράμετρος GET ?status=...) ---
// Επιτρεπτές τιμές — whitelist guard: αν έρθει οτιδήποτε άλλο, fallback σε 'ALL'
$filterStatus = $_GET['status'] ?? 'ALL';
$allowedFilter = ['ALL','NEW','PAID','SHIPPED','CANCELLED'];
if (!in_array($filterStatus, $allowedFilter, true)) {
    $filterStatus = 'ALL';   // anti-injection: μη έγκυρες τιμές αγνοούνται
}

// Δυναμική κατασκευή SQL ανάλογα με το αν έχει επιλεγεί φίλτρο
if ($filterStatus === 'ALL') {
    // Καμία συνθήκη WHERE → όλες οι παραγγελίες
    $orders = $pdo->query("
        SELECT o.id, o.order_date, o.status, c.fullname,
               COALESCE(SUM(oi.qty * oi.unit_price), 0) AS total
        FROM orders o
        JOIN clients c ON c.id = o.client_id
        LEFT JOIN order_items oi ON oi.order_id = o.id
        GROUP BY o.id, o.order_date, o.status, c.fullname
        ORDER BY o.id DESC
    ")->fetchAll();
} else {
    // Prepared statement με placeholder — η τιμή έχει ήδη περάσει whitelist
    $stmt = $pdo->prepare("
        SELECT o.id, o.order_date, o.status, c.fullname,
               COALESCE(SUM(oi.qty * oi.unit_price), 0) AS total
        FROM orders o
        JOIN clients c ON c.id = o.client_id
        LEFT JOIN order_items oi ON oi.order_id = o.id
        WHERE o.status = :status
        GROUP BY o.id, o.order_date, o.status, c.fullname
        ORDER BY o.id DESC
    ");
    $stmt->execute([':status' => $filterStatus]);
    $orders = $stmt->fetchAll();
}

// Λίστα πελατών για το dropdown
$clients = $pdo->query('SELECT id, fullname FROM clients ORDER BY fullname')->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<h2>Παραγγελίες</h2>
<?= $msg ?>

<!-- Φόρμα φίλτρου κατάστασης (GET, ώστε ο χρήστης να μπορεί να κάνει bookmark) -->
<form method="get" class="form-inline" style="margin-bottom:10px;">
    <label for="status-filter">Φίλτρο κατάστασης:</label>
    <select name="status" id="status-filter" onchange="this.form.submit()">
        <option value="ALL"       <?= $filterStatus === 'ALL'       ? 'selected' : '' ?>>Όλες</option>
        <option value="NEW"       <?= $filterStatus === 'NEW'       ? 'selected' : '' ?>>Νέα</option>
        <option value="PAID"      <?= $filterStatus === 'PAID'      ? 'selected' : '' ?>>Πληρωμένη</option>
        <option value="SHIPPED"   <?= $filterStatus === 'SHIPPED'   ? 'selected' : '' ?>>Απεσταλμένη</option>
        <option value="CANCELLED" <?= $filterStatus === 'CANCELLED' ? 'selected' : '' ?>>Ακυρωμένη</option>
    </select>
    <noscript><button type="submit">Εφαρμογή</button></noscript>
    <?php if ($filterStatus !== 'ALL'): ?>
        <span style="margin-left:8px; color:#666;">(<?= count($orders) ?> παραγγελία/ες)</span>
        <a href="orders.php" style="margin-left:8px;">Καθαρισμός</a>
    <?php endif; ?>
</form>

<!-- Φόρμα δημιουργίας νέας παραγγελίας -->
<form method="post" class="form-inline">
    <input type="hidden" name="action" value="create">
    <select name="client_id" required>
        <option value="">— Επιλέξτε πελάτη —</option>
        <?php foreach ($clients as $c): ?>
            <option value="<?= (int)$c['id'] ?>"><?= h($c['fullname']) ?></option>
        <?php endforeach; ?>
    </select>
    <input type="date" name="order_date" value="<?= date('Y-m-d') ?>">
    <select name="status">
        <option value="NEW">Νέα</option>
        <option value="PAID">Πληρωμένη</option>
        <option value="SHIPPED">Απεσταλμένη</option>
        <option value="CANCELLED">Ακυρωμένη</option>
    </select>
    <button type="submit">Δημιουργία</button>
</form>

<!-- Πίνακας με τις παραγγελίες -->
<table class="data">
    <thead>
        <tr><th>#</th><th>Πελάτης</th><th>Ημερομηνία</th><th>Κατάσταση</th><th>Σύνολο</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach ($orders as $o): ?>
        <tr>
            <td><?= (int)$o['id'] ?></td>
            <td><?= h($o['fullname']) ?></td>
            <td><?= h($o['order_date']) ?></td>
            <td><?= h($o['status']) ?></td>
            <td><?= number_format((float)$o['total'], 2, ',', '.') ?> &euro;</td>
            <td>
                <a href="orders_view.php?id=<?= (int)$o['id'] ?>">Άνοιγμα</a>
                <!-- Inline form διαγραφής (PRG pattern). Confirm για να αποτραπεί ατύχημα. -->
                <form method="post" style="display:inline; margin-left:8px;" onsubmit="return confirm('Διαγραφή παραγγελίας #<?= (int)$o['id'] ?>; Θα διαγραφούν και οι γραμμές της.');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id"     value="<?= (int)$o['id'] ?>">
                    <button type="submit" class="danger">Διαγραφή</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
