<?php
// ============================================================
// products_edit.php — Επεξεργασία ή διαγραφή προϊόντος
// Παρόμοια λογική με clients_edit.php
// Προσοχή: αν το προϊόν χρησιμοποιείται σε παραγγελίες,
// η διαγραφή θα αποτύχει (δεν έχουμε CASCADE στο FK products).
// ============================================================

require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'PAPI-net — Επεξεργασία Προϊόντος';

$id  = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$msg = '';

if ($id <= 0) {
    header('Location: products.php');
    exit;
}

// --- POST: ενημέρωση ή διαγραφή ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update') {
        // Λήψη τιμών
        $sku   = trim($_POST['sku']   ?? '');
        $name  = trim($_POST['name']  ?? '');
        $price = (float)str_replace(',', '.', $_POST['price'] ?? '0');
        $stock = (int)($_POST['stock'] ?? 0);

        if ($sku === '' || $name === '') {
            $msg = '<div class="err">SKU και όνομα είναι υποχρεωτικά.</div>';
        } else {
            try {
                // Prepared UPDATE
                $stmt = $pdo->prepare('UPDATE products SET sku=?, name=?, price=?, stock=? WHERE id=?');
                $stmt->execute([$sku, $name, $price, $stock, $id]);
                $msg = '<div class="ok">Οι αλλαγές αποθηκεύτηκαν.</div>';
            } catch (PDOException $e) {
                $msg = '<div class="err">Σφάλμα: ' . h($e->getMessage()) . '</div>';
            }
        }
    } elseif ($action === 'delete') {
        try {
            // Διαγραφή — θα αποτύχει αν υπάρχουν order_items που το χρησιμοποιούν
            $stmt = $pdo->prepare('DELETE FROM products WHERE id=?');
            $stmt->execute([$id]);
            header('Location: products.php');
            exit;
        } catch (PDOException $e) {
            $msg = '<div class="err">Δεν διαγράφεται — υπάρχει σε παραγγελίες.</div>';
        }
    }
}

// --- Φόρτωση τρεχόντων στοιχείων ---
$stmt = $pdo->prepare('SELECT * FROM products WHERE id=?');
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php');
    exit;
}

require_once __DIR__ . '/../includes/header.php';
?>

<h2>Επεξεργασία προϊόντος #<?= (int)$product['id'] ?></h2>
<?= $msg ?>

<form method="post" class="form-stack">
    <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
    <input type="hidden" name="action" value="update">
    <label>SKU   <input type="text" name="sku"  value="<?= h($product['sku']) ?>" required></label>
    <label>Όνομα <input type="text" name="name" value="<?= h($product['name']) ?>" required></label>
    <label>Τιμή (€)
        <!-- Δείχνουμε με κόμμα δεκαδικού (ελληνική γραφή) -->
        <input type="text" name="price" value="<?= number_format((float)$product['price'], 2, ',', '') ?>">
    </label>
    <label>Στοκ  <input type="number" name="stock" value="<?= (int)$product['stock'] ?>" min="0"></label>
    <button type="submit">Αποθήκευση</button>
</form>

<form method="post" onsubmit="return confirm('Διαγραφή προϊόντος;');" class="form-stack">
    <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
    <input type="hidden" name="action" value="delete">
    <button type="submit" class="danger">Διαγραφή</button>
</form>

<p><a href="products.php">&larr; Πίσω στη λίστα</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
