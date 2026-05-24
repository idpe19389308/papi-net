================================================================================
README — Configs για Cisco Packet Tracer
Απαλλακτική: Διαμάντη Ελένη Βασιλική (Α.Μ. 19389308)
================================================================================

ΠΕΡΙΕΧΟΜΕΝΑ ΦΑΚΕΛΟΥ
--------------------
  R-IOA.txt   — Δρομολογητής Ιωαννίνων (2 WAN: προς THE & PAT)
  R-THE.txt   — Δρομολογητής Θεσσαλονίκης (2 WAN: προς IOA & ATH)
  R-ATH.txt   — Δρομολογητής Αθήνας (2 WAN: προς THE & CHA)
  R-PAT.txt   — Δρομολογητής Πάτρας (2 WAN: προς IOA & CHA)
  R-CHA.txt   — Δρομολογητής Χανίων (2 WAN: προς ATH & PAT)
  SW-IOA.txt  — Switch Ιωαννίνων (3 VLANs)
  SW-THE.txt  — Switch Θεσσαλονίκης
  SW-ATH.txt  — Switch Αθήνας
  SW-PAT.txt  — Switch Πάτρας
  SW-CHA.txt  — Switch Χανίων

ΕΞΟΠΛΙΣΜΟΣ ΣΤΟ PACKET TRACER
----------------------------
  Routers: 5 × Cisco PT8200 (4 × GigabitEthernet built-in)
  Switches: 5 × Cisco 2960-24TT
  PCs    : 15 × Generic PC-PT (3 ανά παράρτημα — ένα PC ανά τμήμα)
  Servers: 2 × Generic PC-PT στο παράρτημα Ιωαννίνων (WebServer + FileServer)

  Κάθε router χρησιμοποιεί:
    GigabitEthernet0/0/0  → LAN trunk προς το switch (router-on-a-stick με VLAN subinterfaces .10/.20/.30)
    GigabitEthernet0/0/1  → WAN προς γείτονα Α
    GigabitEthernet0/0/2  → WAN προς γείτονα Β
    GigabitEthernet0/0/3  → εφεδρικό (ασύνδετο)

ΠΩΣ ΦΟΡΤΩΝΟΥΜΕ ΤΟ CONFIG ΣΕ ΜΙΑ ΣΥΣΚΕΥΗ
---------------------------------------
  1. Κάντε διπλό κλικ πάνω στη συσκευή (router ή switch).
  2. Πηγαίνετε στην καρτέλα "CLI".
  3. Πατήστε ENTER αν χρειάζεται για να εμφανιστεί το prompt.
  4. Στην ερώτηση "Continue with configuration dialog?" απαντήστε "no".
  5. Πληκτρολογήστε:
        enable
        configure terminal
  6. Κάντε COPY ολόκληρο το αντίστοιχο .txt αρχείο (από hostname έως end)
     και PASTE στο CLI παράθυρο. Περιμένετε να εκτελεστούν όλες οι γραμμές.
  7. Όταν τελειώσει, πληκτρολογήστε:
        copy running-config startup-config
        [ENTER για επιβεβαίωση]
     έτσι ώστε να αποθηκευτεί και να επιβιώσει σε reboot.

ΣΕΙΡΑ ΡΥΘΜΙΣΗΣ (προτεινόμενη)
-----------------------------
  1. Όλα τα switches πρώτα (SW-IOA, SW-THE, SW-ATH, SW-PAT, SW-CHA).
  2. Όλα τα routers μετά (R-IOA, R-THE, R-ATH, R-PAT, R-CHA).
  3. Στους clients (PC) βάλτε IP/Mask/Gateway χειροκίνητα από
     Desktop > IP Configuration.

VLAN ΣΧΗΜΑ (ίδιο σε όλα τα παραρτήματα)
---------------------------------------
  VLAN 10 = Λογιστήριο        → υποδίκτυο .0/26   (router IP .1)
  VLAN 20 = Διεύθυνση         → υποδίκτυο .64/26  (router IP .65)
  VLAN 30 = SoftwareDevelopers→ υποδίκτυο .128/26 (router IP .129)

ΠΑΡΑΔΕΙΓΜΑ IP ΓΙΑ PC ΣΕ ΑΘΗΝΑ (LAN 200.1.100.0/24)
--------------------------------------------------
  PC-Λογιστήριο-ATH : IP 200.1.100.10  Mask 255.255.255.192  GW 200.1.100.1
  PC-Διεύθυνση-ATH  : IP 200.1.100.74  Mask 255.255.255.192  GW 200.1.100.65
  PC-SWDev-ATH      : IP 200.1.100.138 Mask 255.255.255.192  GW 200.1.100.129
