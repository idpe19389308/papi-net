<?php
// ============================================================
// header.php — Κοινή κορυφή για όλες τις σελίδες
// Περιέχει: <html>, <head>, navigation bar.
// Φορτώνεται με require_once από κάθε σελίδα στον public/.
// ============================================================
?>
<!doctype html>
<html lang="el">
<head>
    <!-- Κωδικοποίηση: UTF-8 για ελληνικά -->
    <meta charset="utf-8">
    <!-- Responsive viewport για κινητά -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Τίτλος καρτέλας: παίρνει $pageTitle από την σελίδα που μας κάλεσε, αλλιώς default -->
    <title><?= isset($pageTitle) ? h($pageTitle) : 'PAPI-net — Διαχείριση Παραγγελιών' ?></title>
    <!-- CSS της εφαρμογής (απλό, zero dependencies) -->
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <!-- Κορυφαία γραμμή πλοήγησης — εμφανίζεται σε όλες τις σελίδες -->
    <header class="topbar">
        <h1 class="brand">PAPI-net <span class="sub">Διαχείριση Παραγγελιών</span></h1>
        <nav class="mainnav">
            <!-- Σύνδεσμοι προς όλες τις δυναμικές σελίδες -->
            <a href="index.php">Αρχική</a>
            <a href="clients.php">Πελάτες</a>
            <a href="products.php">Προϊόντα</a>
            <a href="orders.php">Παραγγελίες</a>
        </nav>
    </header>
    <!-- Κύριο περιεχόμενο — γεμίζει από την κάθε σελίδα -->
    <main class="container">
