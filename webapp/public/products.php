<?php
// ============================================================
// products.php — Λίστα προϊόντων + φόρμα προσθήκης νέου
// CRUD: Create + Read. Edit/Delete στο products_edit.php.
// ============================================================

require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'PAPI-net — Προϊόντα';
$msg = '';

// --- POST: δημιουργία νέου προϊόντος ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    // Συλλογή τιμών από τη φόρμα
    $sku   = trim($_POST['sku']   ?? '');
    $name  = trim($_POST['name']  ?? '');
    // Μετατροπή σε αριθμούς (αν αποτύχει → 0)
    $price = (float)str_replace(',', '.', $_POST['price'] ?? '0');  // δεχόμαστε και κόμμα (ελληνική γραφή)
    $stock = (int)($_POST['stock'] ?? 0);

    if ($sku === '' || $name === '') {
        $msg = '<div class="err">SKU και όνομα είναι υποχρεωτικά.</div>';
    } else {
        try {
            // Prepared INSERT — ασφαλές
            $stmt = $pdo->prepare('INSERT INTO products (sku, name, price, stock) VALUES (?, ?, ?, ?)');
            $stmt->execute([$sku, $name, $price, $stock]);
            $msg = '<div class="ok">Το προϊόν προστέθηκε.</div>';
        } catch (PDOException $e) {
            // Πιθανότερο σφάλμα: διπλό SKU (UNIQUE constraint)
            $msg = '<div class="err">Σφάλμα: ' . h($e->getMessage()) . '</div>';
        }
    }
}

// --- READ: όλα τα προϊόντα ---
$products = $pdo->query('SELECT id, sku, name, price, stock FROM products ORDER BY id DESC')->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<h2>Προϊόντα</h2>
<?= $msg ?>

<!-- Φόρμα προσθήκης -->
<form method="post" class="form-inline">
    <input type="hidden" name="action" value="create">
    <input type="text"   name="sku"   placeholder="SKU"   required>
    <input type="text"   name="name"  placeholder="Όνομα" required>
    <input type="text"   name="price" placeholder="Τιμή (€)" value="0,00">
    <input type="number" name="stock" placeholder="Στοκ"  value="0" min="0">
    <button type="submit">Προσθήκη</button>
</form>

<!-- Πίνακας προϊόντων -->
<table class="data">
    <thead>
        <tr><th>#</th><th>SKU</th><th>Όνομα</th><th>Τιμή</th><th>Στοκ</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach ($products as $p): ?>
        <tr>
            <td><?= (int)$p['id'] ?></td>
            <td><?= h($p['sku']) ?></td>
            <td><?= h($p['name']) ?></td>
            <td><?= number_format((float)$p['price'], 2, ',', '.') ?> &euro;</td>
            <td><?= (int)$p['stock'] ?></td>
            <td><a href="products_edit.php?id=<?= (int)$p['id'] ?>">Επεξεργασία</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
