# Στιγμιότυπα οθόνης από το Cisco Packet Tracer

Φάκελος με τα στιγμιότυπα που τεκμηριώνουν την υλοποίηση της δικτυακής τοπολογίας και την επαλήθευση της λειτουργίας της.

## Περιεχόμενα

| Αρχείο | Περιγραφή |
|---|---|
| [`01-topology.png`](01-topology.png) | Πλήρης τοπολογία στο workspace του Packet Tracer με ονόματα συσκευών |
| [`02-show-ip-int-brief.png`](02-show-ip-int-brief.png) | R-IOA: `show ip interface brief` — όλα τα subinterfaces ενεργά |
| [`03-show-ospf-neighbor.png`](03-show-ospf-neighbor.png) | R-THE: `show ip ospf neighbor` — γείτονες σε κατάσταση FULL |
| [`04-show-ip-route-ospf.png`](04-show-ip-route-ospf.png) | R-CHA: `show ip route ospf` — διαδρομές LAN `/26` ως κατηγορία `O` |
| [`05-show-vlan-brief.png`](05-show-vlan-brief.png) | SW-ATH: `show vlan brief` — VLAN 10/20/30 και αντίστοιχες θύρες |
| [`06-show-interfaces-trunk.png`](06-show-interfaces-trunk.png) | SW-PAT: `show interfaces trunk` — θύρα Fa0/24 με allowed VLANs 10, 20, 30 |
| [`07-ping-success-intra-vlan.png`](07-ping-success-intra-vlan.png) | Επιτυχημένο `ping` από σταθμό VLAN 10 Αθήνας προς σταθμό VLAN 10 Χανίων |
| [`08-ping-fail-inter-vlan.png`](08-ping-fail-inter-vlan.png) | Αποτυχημένο `ping` από σταθμό VLAN 10 Αθήνας προς σταθμό VLAN 20 Αθήνας (μπλοκάρισμα μέσω ACL) |
| [`09-ping-server.png`](09-ping-server.png) | Ping προς τον Web server του παραρτήματος Ιωαννίνων |

## Σχετικά αρχεία

Το αρχείο της τοπολογίας Cisco Packet Tracer φυλάσσεται στον πατρικό φάκελο: [`../papinet-V21.pkt`](../papinet-V21.pkt).
