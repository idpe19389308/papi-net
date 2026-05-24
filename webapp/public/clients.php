<?php
// ============================================================
// clients.php — Λίστα πελατών + φόρμα προσθήκης νέου πελάτη
// CRUD (Create + Read). Edit/Delete γίνονται στο clients_edit.php
// Σημείωση: σε production θα προσθέταμε CSRF token σε κάθε POST.
// ============================================================

// Φόρτωση ρυθμίσεων και PDO
require_once __DIR__ . '/../includes/config.php';

// Τίτλος καρτέλας browser
$pageTitle = 'PAPI-net — Πελάτες';

// Μεταβλητή για μηνύματα feedback προς τον χρήστη (success/error)
$msg = '';

// --- ΔΗΜΙΟΥΡΓΙΑ νέου πελάτη μέσω POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    // trim για να καθαρίσουμε whitespace από τις τιμές
    $fullname = trim($_POST['fullname'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $phone    = trim($_POST['phone']    ?? '');
    $city     = trim($_POST['city']     ?? '');

    // Έλεγχος υποχρεωτικού πεδίου
    if ($fullname === '') {
        $msg = '<div class="err">Το ονοματεπώνυμο είναι υποχρεωτικό.</div>';
    } else {
        // Prepared statement — αποτρέπει SQL injection
        $stmt = $pdo->prepare('INSERT INTO clients (fullname, email, phone, city) VALUES (?, ?, ?, ?)');
        // Εκτέλεση με τις τιμές ως array — αυτόματο escaping
        $stmt->execute([$fullname, $email ?: null, $phone ?: null, $city ?: null]);
        $msg = '<div class="ok">Ο πελάτης προστέθηκε επιτυχώς.</div>';
    }
}

// --- ΑΝΑΖΗΤΗΣΗ (παράμετρος GET ?q=...) ---
// trim για να αγνοούνται μόνο spaces· κενό q => εμφάνιση όλων
$q = trim($_GET['q'] ?? '');

if ($q !== '') {
    // Prepared statement με wildcards — προστασία από SQL injection
    // Αναζητά case-insensitive σε fullname Ή email (LIKE είναι case-insensitive σε utf8mb4_unicode_ci)
    $stmt = $pdo->prepare(
        'SELECT id, fullname, email, phone, city, created_at
         FROM clients
         WHERE fullname LIKE :q OR email LIKE :q
         ORDER BY id DESC'
    );
    // Το %...% γίνεται bind ως τιμή του placeholder — όχι concatenation στο SQL
    $stmt->execute([':q' => '%' . $q . '%']);
    $clients = $stmt->fetchAll();
} else {
    // Κενό q → όλοι οι πελάτες
    $clients = $pdo->query('SELECT id, fullname, email, phone, city, created_at FROM clients ORDER BY id DESC')->fetchAll();
}

// Φόρτωση header
require_once __DIR__ . '/../includes/header.php';
?>

<h2>Πελάτες</h2>
<?= $msg /* feedback μήνυμα — ήδη HTML-safe από εμάς */ ?>

<!-- Φόρμα αναζήτησης (GET ώστε να μπορεί ο χρήστης να κάνει bookmark) -->
<form method="get" class="form-inline" style="margin-bottom:10px;">
    <input type="text" name="q" value="<?= h($q) ?>" placeholder="Αναζήτηση σε όνομα ή email…" size="30">
    <button type="submit">Αναζήτηση</button>
    <?php if ($q !== ''): ?>
        <a href="clients.php" style="margin-left:8px;">Καθαρισμός</a>
        <span style="margin-left:8px; color:#666;">(<?= count($clients) ?> αποτέλεσμα/τα για «<?= h($q) ?>»)</span>
    <?php endif; ?>
</form>

<!-- Φόρμα προσθήκης νέου πελάτη -->
<form method="post" class="form-inline">
    <input type="hidden" name="action" value="create">
    <input type="text"  name="fullname" placeholder="Ονοματεπώνυμο" required>
    <input type="email" name="email"    placeholder="Email">
    <input type="text"  name="phone"    placeholder="Τηλέφωνο">
    <input type="text"  name="city"     placeholder="Πόλη">
    <button type="submit">Προσθήκη</button>
</form>

<!-- Πίνακας με όλους τους πελάτες -->
<table class="data">
    <thead>
        <tr><th>#</th><th>Ονοματεπώνυμο</th><th>Email</th><th>Τηλέφωνο</th><th>Πόλη</th><th>Δημιουργία</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach ($clients as $c): ?>
        <tr>
            <td><?= (int)$c['id'] ?></td>
            <td><?= h($c['fullname']) ?></td>
            <td><?= h($c['email']) ?></td>
            <td><?= h($c['phone']) ?></td>
            <td><?= h($c['city']) ?></td>
            <td><?= h($c['created_at']) ?></td>
            <td><a href="clients_edit.php?id=<?= (int)$c['id'] ?>">Επεξεργασία</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php
// Φόρτωση footer
require_once __DIR__ . '/../includes/footer.php';
