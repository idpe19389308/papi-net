# Απαλλακτική Εργασία στο μάθημα «Τεχνολογία Διαδικτύου στην Ψηφιακή Βιομηχανία»

**PAPI-net** — Διαδικτυακή εφαρμογή διαχείρισης παραγγελιών και δικτυακή υποδομή πέντε παραρτημάτων.

| | |
|---|---|
| Σπουδάστρια | Ελένη Βασιλική Διαμάντη |
| Αριθμός Μητρώου | 19389308 |
| Διδάσκων | Δρ. Μιχάλης Ξευγένης |
| Ακαδημαϊκό έτος | 2025–2026 |
| Ημερομηνία παράδοσης | 5 Ιουνίου 2026 |

---

## Προβολή της εργασίας

Η πλήρης τεκμηρίωση, τα διαδραστικά διαγράμματα και η παρουσίαση διατίθενται στη διεύθυνση:

### https://idpe19389308.github.io/papi-net/

> *Σημείωση*: το παρόν repository (`github.com`) περιέχει τον πηγαίο κώδικα. Η ίδια η ιστοσελίδα της εργασίας (HTML, διαδραστικά διαγράμματα, τεχνική αναφορά) σερβίρεται από το παραπάνω URL.

### Άμεσοι σύνδεσμοι

| Παραδοτέο | Σύνδεσμος |
|---|---|
| Κεντρική σελίδα | [idpe19389308.github.io/papi-net/](https://idpe19389308.github.io/papi-net/) |
| Τεχνική αναφορά | [final-report/report.html](https://idpe19389308.github.io/papi-net/final-report/report.html) |
| Διαδραστικά διαγράμματα | [final-report/interactive/](https://idpe19389308.github.io/papi-net/final-report/interactive/) |
| Τεκμηρίωση διαδικτυακής εφαρμογής | [webapp/README.html](https://idpe19389308.github.io/papi-net/webapp/README.html) |
| Δικτυακές διαμορφώσεις (configs) | [packet-tracer/configs/README.html](https://idpe19389308.github.io/papi-net/packet-tracer/configs/README.html) |
| Παρουσίαση (PDF preview) | [final-report/papinet-preview.pdf](https://idpe19389308.github.io/papi-net/final-report/papinet-preview.pdf) |

---

## Περιεχόμενο εργασίας

### 1. Διαδικτυακή εφαρμογή (Μέρος Α)

Σύστημα διαχείρισης παραγγελιών υλοποιημένο σε PHP/MariaDB πάνω σε τοπικό περιβάλλον XAMPP. Περιλαμβάνει επτά δυναμικές σελίδες με λειτουργίες CRUD επί τεσσάρων πινάκων (πελάτες, προϊόντα, παραγγελίες, γραμμές παραγγελίας), χρήση προετοιμασμένων εντολών PDO, αναζήτηση πελατών και φίλτρο κατάστασης παραγγελιών.

| | |
|---|---|
| Πηγαίος κώδικας | [`webapp/`](webapp/) |
| Τεκμηρίωση | [`webapp/README.md`](webapp/README.md) |
| Σχήμα βάσης δεδομένων | [`webapp/sql/schema.sql`](webapp/sql/schema.sql) |
| Δοκιμαστικά δεδομένα | [`webapp/sql/seed.sql`](webapp/sql/seed.sql) |
| Άδεια χρήσης | [MIT License](webapp/LICENSE) |

### 2. Δικτυακή υποδομή (Μέρος Β)

Διασύνδεση πέντε παραρτημάτων (Αθήνα, Θεσσαλονίκη, Πάτρα, Ιωάννινα, Χανιά) σε δακτυλιοειδή τοπολογία WAN, υλοποιημένη στο Cisco Packet Tracer. Δυναμική δρομολόγηση μέσω OSPF (area 0), διαχωρισμός τριών τμημάτων ανά παράρτημα (Λογιστήριο, Διεύθυνση, Software Developers) με VLAN, inter-VLAN routing μέσω router-on-a-stick και απομόνωση τμημάτων μέσω access control lists.

| | |
|---|---|
| Διαμορφώσεις (πέντε routers + πέντε switches) | [`packet-tracer/configs/`](packet-tracer/configs/) |
| Αρχείο Cisco Packet Tracer | [`packet-tracer/papinet-V21.pkt`](packet-tracer/papinet-V21.pkt) |
| Στιγμιότυπα υλοποίησης (εννέα αρχεία) | [`packet-tracer/screenshots/`](packet-tracer/screenshots/) |

### 3. Τεκμηρίωση και παρουσίαση

| | |
|---|---|
| Τεχνική αναφορά (Markdown) | [`final-report/report.md`](final-report/report.md) |
| Τεχνική αναφορά (HTML) | [`final-report/report.html`](https://idpe19389308.github.io/papi-net/final-report/report.html) |
| Διαδραστικά διαγράμματα (αρχιτεκτονική, ER, τοπολογία, VLAN) | [διαδραστική προβολή](https://idpe19389308.github.io/papi-net/final-report/interactive/) |
| Παρουσίαση (PowerPoint) | [`presentation/papinet-presentation.pptx`](presentation/papinet-presentation.pptx) |
| Προεπισκόπηση παρουσίασης (PDF) | [`final-report/papinet-preview.pdf`](final-report/papinet-preview.pdf) |
| Κεντρική σελίδα | [idpe19389308.github.io/papi-net/](https://idpe19389308.github.io/papi-net/) |

---

## Δήλωση χρήσης εργαλείων Τεχνητής Νοημοσύνης

Η αναλυτική δήλωση χρήσης εργαλείων ΤΝ, σύμφωνα με τις απαιτήσεις της εκφώνησης, παρατίθεται στην ενότητα 3.7 της τεχνικής αναφοράς ([`final-report/report.md`](final-report/report.md))
