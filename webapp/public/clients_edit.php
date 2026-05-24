<?php
// ============================================================
// clients_edit.php — Επεξεργασία ή διαγραφή πελάτη
// GET ?id=N → εμφανίζει φόρμα με τα τρέχοντα στοιχεία
// POST action=update → σώζει αλλαγές
// POST action=delete → διαγράφει (CASCADE: φεύγουν και οι παραγγελίες του)
// ============================================================

require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'PAPI-net — Επεξεργασία Πελάτη';

// Λήψη id είτε από GET είτε από POST (filter για ακέραιο)
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$msg = '';

// Αν δεν έχουμε έγκυρο id, πάμε πίσω στη λίστα
if ($id <= 0) {
    header('Location: clients.php');
    exit;
}

// --- Χειρισμός POST ενεργειών ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update') {
        // Συλλογή και καθαρισμός εισόδων
        $fullname = trim($_POST['fullname'] ?? '');
        $email    = trim($_POST['email']    ?? '');
        $phone    = trim($_POST['phone']    ?? '');
        $city     = trim($_POST['city']     ?? '');

        if ($fullname === '') {
            $msg = '<div class="err">Το ονοματεπώνυμο είναι υποχρεωτικό.</div>';
        } else {
            // Prepared UPDATE — placeholders αντί για concatenation
            $stmt = $pdo->prepare('UPDATE clients SET fullname=?, email=?, phone=?, city=? WHERE id=?');
            $stmt->execute([$fullname, $email ?: null, $phone ?: null, $city ?: null, $id]);
            $msg = '<div class="ok">Οι αλλαγές αποθηκεύτηκαν.</div>';
        }
    } elseif ($action === 'delete') {
        // Διαγραφή πελάτη — οι παραγγελίες του διαγράφονται μέσω FK ON DELETE CASCADE
        $stmt = $pdo->prepare('DELETE FROM clients WHERE id=?');
        $stmt->execute([$id]);
        // Επιστροφή στη λίστα μετά τη διαγραφή
        header('Location: clients.php');
        exit;
    }
}

// --- Φόρτωση τρεχόντων στοιχείων πελάτη ---
$stmt = $pdo->prepare('SELECT * FROM clients WHERE id=?');
$stmt->execute([$id]);
$client = $stmt->fetch();

// Αν δεν βρέθηκε, επιστροφή στη λίστα
if (!$client) {
    header('Location: clients.php');
    exit;
}

require_once __DIR__ . '/../includes/header.php';
?>

<h2>Επεξεργασία πελάτη #<?= (int)$client['id'] ?></h2>
<?= $msg ?>

<!-- Φόρμα ενημέρωσης -->
<form method="post" class="form-stack">
    <input type="hidden" name="id" value="<?= (int)$client['id'] ?>">
    <input type="hidden" name="action" value="update">
    <label>Ονοματεπώνυμο <input type="text"  name="fullname" value="<?= h($client['fullname']) ?>" required></label>
    <label>Email         <input type="email" name="email"    value="<?= h($client['email'])    ?>"></label>
    <label>Τηλέφωνο      <input type="text"  name="phone"    value="<?= h($client['phone'])    ?>"></label>
    <label>Πόλη          <input type="text"  name="city"     value="<?= h($client['city'])     ?>"></label>
    <button type="submit">Αποθήκευση</button>
</form>

<!-- Φόρμα διαγραφής σε ξεχωριστό form για να μην μπερδευτεί με το update -->
<form method="post" onsubmit="return confirm('Διαγραφή πελάτη; Οι παραγγελίες του θα διαγραφούν επίσης (CASCADE).');" class="form-stack">
    <input type="hidden" name="id" value="<?= (int)$client['id'] ?>">
    <input type="hidden" name="action" value="delete">
    <button type="submit" class="danger">Διαγραφή</button>
</form>

<p><a href="clients.php">&larr; Πίσω στη λίστα</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
