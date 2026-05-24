<?php
// ============================================================
// orders_view.php — Προβολή μιας παραγγελίας + γραμμές της
// GET ?id=N → φόρτωση παραγγελίας + order_items
// POST add_item → προσθήκη γραμμής (product_id + qty)
// POST del_item → αφαίρεση γραμμής
// POST set_status → αλλαγή κατάστασης παραγγελίας
// ============================================================

require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'PAPI-net — Παραγγελία';

// Λήψη id είτε από GET είτε από POST
$id = (int)($_GET['id'] ?? $_POST['order_id'] ?? 0);
$msg = '';

if ($id <= 0) {
    header('Location: orders.php');
    exit;
}

// --- POST: χειρισμός ενεργειών στην παραγγελία ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_item') {
        $product_id = (int)($_POST['product_id'] ?? 0);
        $qty        = max(1, (int)($_POST['qty'] ?? 1));   // ελάχιστο 1

        // Διαβάζουμε την τρέχουσα τιμή του προϊόντος για snapshot στο order_items
        $stmt = $pdo->prepare('SELECT price FROM products WHERE id=?');
        $stmt->execute([$product_id]);
        $p = $stmt->fetch();

        if ($p) {
            // Prepared INSERT — με την τιμή την στιγμή της παραγγελίας
            $stmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, qty, unit_price) VALUES (?, ?, ?, ?)');
            $stmt->execute([$id, $product_id, $qty, $p['price']]);
            $msg = '<div class="ok">Η γραμμή προστέθηκε.</div>';
        } else {
            $msg = '<div class="err">Άγνωστο προϊόν.</div>';
        }
    } elseif ($action === 'del_item') {
        // Διαγραφή συγκεκριμένης γραμμής (όχι όλης της παραγγελίας)
        $item_id = (int)($_POST['item_id'] ?? 0);
        $stmt = $pdo->prepare('DELETE FROM order_items WHERE id=? AND order_id=?');
        $stmt->execute([$item_id, $id]);
        $msg = '<div class="ok">Η γραμμή αφαιρέθηκε.</div>';
    } elseif ($action === 'update_qty') {
        // Ενημέρωση ποσότητας μιας υπάρχουσας γραμμής
        // — δεν αλλάζει η unit_price (παραμένει snapshot της αρχικής τιμής)
        $item_id = (int)($_POST['item_id'] ?? 0);
        // Επιτρέπουμε μόνο θετική ποσότητα ≥ 1
        $new_qty = max(1, (int)($_POST['qty'] ?? 1));
        if ($item_id > 0) {
            // Prepared UPDATE — με έλεγχο order_id για ασφάλεια (δεν αλλάζουμε ξένες γραμμές)
            $stmt = $pdo->prepare('UPDATE order_items SET qty=? WHERE id=? AND order_id=?');
            $stmt->execute([$new_qty, $item_id, $id]);
            $msg = '<div class="ok">Η ποσότητα ενημερώθηκε.</div>';
        }
    } elseif ($action === 'set_status') {
        // Αλλαγή κατάστασης — whitelist έλεγχος
        $status = $_POST['status'] ?? 'NEW';
        $allowed = ['NEW','PAID','SHIPPED','CANCELLED'];
        if (in_array($status, $allowed, true)) {
            $stmt = $pdo->prepare('UPDATE orders SET status=? WHERE id=?');
            $stmt->execute([$status, $id]);
            $msg = '<div class="ok">Η κατάσταση ενημερώθηκε.</div>';
        }
    }
}

// --- READ: στοιχεία παραγγελίας + όνομα πελάτη ---
$stmt = $pdo->prepare("
    SELECT o.id, o.order_date, o.status, c.id AS client_id, c.fullname
    FROM orders o
    JOIN clients c ON c.id = o.client_id
    WHERE o.id = ?
");
$stmt->execute([$id]);
$order = $stmt->fetch();

// Αν δεν υπάρχει η παραγγελία, επιστροφή στη λίστα
if (!$order) {
    header('Location: orders.php');
    exit;
}

// --- READ: γραμμές παραγγελίας με προϊόν ---
$stmt = $pdo->prepare("
    SELECT oi.id, oi.qty, oi.unit_price, p.sku, p.name
    FROM order_items oi
    JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = ?
    ORDER BY oi.id
");
$stmt->execute([$id]);
$items = $stmt->fetchAll();

// Υπολογισμός συνόλου (στην PHP — για να δείξουμε στο template)
$total = 0.0;
foreach ($items as $it) {
    $total += (float)$it['qty'] * (float)$it['unit_price'];
}

// Λίστα προϊόντων για το dropdown προσθήκης γραμμής
$products = $pdo->query('SELECT id, sku, name, price FROM products ORDER BY name')->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<h2>Παραγγελία #<?= (int)$order['id'] ?></h2>
<p>
    <strong>Πελάτης:</strong> <?= h($order['fullname']) ?> &middot;
    <strong>Ημερομηνία:</strong> <?= h($order['order_date']) ?> &middot;
    <strong>Κατάσταση:</strong> <?= h($order['status']) ?>
</p>
<?= $msg ?>

<!-- Αλλαγή κατάστασης -->
<form method="post" class="form-inline">
    <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
    <input type="hidden" name="action"   value="set_status">
    <select name="status">
        <?php foreach (['NEW'=>'Νέα','PAID'=>'Πληρωμένη','SHIPPED'=>'Απεσταλμένη','CANCELLED'=>'Ακυρωμένη'] as $k=>$v): ?>
            <option value="<?= h($k) ?>" <?= $order['status']===$k?'selected':'' ?>><?= h($v) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Αλλαγή κατάστασης</button>
</form>

<!-- Πίνακας γραμμών -->
<h3>Γραμμές</h3>
<table class="data">
    <thead>
        <tr><th>#</th><th>SKU</th><th>Προϊόν</th><th>Ποσότητα</th><th>Τιμή/τμχ</th><th>Υποσύνολο</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach ($items as $it):
        // Υποσύνολο γραμμής
        $sub = (float)$it['qty'] * (float)$it['unit_price']; ?>
        <tr>
            <td><?= (int)$it['id'] ?></td>
            <td><?= h($it['sku']) ?></td>
            <td><?= h($it['name']) ?></td>
            <td>
                <!-- Inline form για επεξεργασία ποσότητας — submit ενημερώνει qty -->
                <form method="post" style="display:inline">
                    <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                    <input type="hidden" name="action"   value="update_qty">
                    <input type="hidden" name="item_id"  value="<?= (int)$it['id'] ?>">
                    <input type="number" name="qty" value="<?= (int)$it['qty'] ?>" min="1" style="width:60px">
                    <button type="submit" title="Ενημέρωση ποσότητας">↻</button>
                </form>
            </td>
            <td><?= number_format((float)$it['unit_price'], 2, ',', '.') ?> &euro;</td>
            <td><?= number_format($sub, 2, ',', '.') ?> &euro;</td>
            <td>
                <!-- Inline form για αφαίρεση γραμμής -->
                <form method="post" style="display:inline" onsubmit="return confirm('Αφαίρεση γραμμής;');">
                    <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                    <input type="hidden" name="action"   value="del_item">
                    <input type="hidden" name="item_id"  value="<?= (int)$it['id'] ?>">
                    <button type="submit" class="danger">x</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <tr class="total-row">
        <td colspan="5" style="text-align:right"><strong>Σύνολο:</strong></td>
        <td colspan="2"><strong><?= number_format($total, 2, ',', '.') ?> &euro;</strong></td>
    </tr>
    </tbody>
</table>

<!-- Φόρμα προσθήκης γραμμής -->
<h3>Προσθήκη γραμμής</h3>
<form method="post" class="form-inline">
    <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
    <input type="hidden" name="action"   value="add_item">
    <select name="product_id" required>
        <option value="">— Επιλέξτε προϊόν —</option>
        <?php foreach ($products as $p): ?>
            <option value="<?= (int)$p['id'] ?>">
                <?= h($p['sku']) ?> — <?= h($p['name']) ?> (<?= number_format((float)$p['price'],2,',','.') ?> €)
            </option>
        <?php endforeach; ?>
    </select>
    <input type="number" name="qty" value="1" min="1" required>
    <button type="submit">Προσθήκη</button>
</form>

<p><a href="orders.php">&larr; Πίσω στις παραγγελίες</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
