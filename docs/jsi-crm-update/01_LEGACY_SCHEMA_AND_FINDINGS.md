# JSI CRM Update – Legacy Schema & Findings

Stand: 2026-09-19

Dieses Dokument fasst die bisher bestätigten Legacy-CRM-Strukturen und Analysebefunde zusammen. Es trennt bestätigte Funde von Punkten, die noch verifiziert werden müssen.

---

# 1. Repository

Repository:

```text
dsactivi-2/Cursor-old-Crm_Refactor
```

Wichtige Bereiche:

```text
src/crm
src/idktime
src/jobstep-partner
src/join
src/messenger
src/online-viza
src/website
```

Auffällig groß und wahrscheinlich geschäftslogiklastig:

```text
src/crm/ajax.php
src/crm/ajax_data.php
```

Die Repo-Analyse dient dazu, den tatsächlichen Legacy-Workflow zu rekonstruieren, bevor ein neues Modell festgelegt wird.

---

# 2. Datenbanklandschaft

## 2.1 Supabase CRM

Projekt:

```text
JSI Base
```

Projekt-Ref:

```text
oohbgrajwxdotalijmih
```

Region:

```text
eu-west-1
```

Das eigentliche CRM liegt im Schema:

```text
crm
```

Nicht primär in `public`.

### Größere Tabellen

```text
crm.idk_kandidati                  122,004
crm.idk_nd_kandidata               81,330
crm.idk_dak_kandidati                 670
crm.idk_kandidat_edukacija        149,473
crm.idk_kandidat_jezici            82,474
crm.idk_kandidat_radno_iskustvo    87,844
crm.idk_documents                  23,614
crm.idk_logs                    ~2,997,184
crm.idk_nd_kandidata_biljeske     928,882
crm.idk_task_force                 452,568
crm.idk_project_kandidati          207,679
idk_nd_cron_export                  7,581
idk_nd_cron_settings                   13
idk_triggerurl                          10
```

### Neuere Agent-/MCP-nahe Tabellen

```text
crm.status_project_name_rules               17
crm.candidate_status_transition_audit        2
crm.candidate_field_fallbacks                 1
crm.candidate_field_change_audit              2
crm.agent_action_audit                        0
crm.candidate_documents                       0
crm.candidate_document_text                   0
crm.candidate_document_embeddings             0
crm.candidate_status_transition_rules         14
crm.occupation                               702
crm.occupation_alias                        1,239
crm.job_occupation_map                     87,995
crm.occupation_remap_audit                     4
crm.pozicija_occupations                    123
```

### Auth / Audit

```text
crm_auth.user_employee_map      1
roles                           18
permissions                     21
role_permissions                47
user_roles                       1
user_scopes                      0
crm_audit.agent_action_log       1
```

Viele `crm.*`-Tabellen haben RLS aktiviert. RLS nicht blind ändern oder deaktivieren.

---

## 2.2 Neon CRM

Projekt:

```text
CRM
```

Project ID:

```text
green-voice-30543034
```

Region:

```text
aws-eu-central-1
```

PostgreSQL:

```text
18
```

Default branch:

```text
prod-copy
br-steep-sound-b1qnvllq
```

Weitere Branches:

```text
production
vercel-dev
mcp-dev
```

Installierte Extensions:

```text
pg_stat_statements 1.12
neon 1.14
```

---

# 3. Performance-Befunde

Auffällige Sequential Scans:

```text
search_synonyms                 ~93,825 seq scans
idk_kandidat_jezici             ~52,661 seq scans
idk_kandidat_radno_iskustvo     ~29,360 seq scans
mcp_tokens                       ~6,328 seq scans
idk_kandidat_edukacija           ~1,325 seq scans
idk_kandidati                      694 seq scans
```

Größte Tabellen/Relationen grob:

```text
idk_logs                     ~499 MB
idk_nd_kandidata_biljeske    ~129 MB
idk_nd_cron_export            ~77 MB
idk_task_force                ~67 MB
idk_kandidati                 ~36 MB
idk_notes                     ~33 MB
```

Ein vorhandener PostgreSQL-Trigger:

```text
kandidat_status_change
```

auf:

```text
idk_kandidati.kandidat_status
```

AFTER UPDATE, Funktion:

```text
trg_kandidat_status_change()
```

Dabei werden u. a. `notification_settings` geprüft und `status_notifications` geschrieben.

---

# 4. Bestehende Kandidatenstatus-Verteilungen

## 4.1 kandidat_status

```text
0      3,999
1      8,432
2      3,804
3      4,446
4     24,545
5        461
6     74,164
7      1,960
8        193
NULL       0
```

Historisch bekannte Bezeichnungen aus Cron-/Legacy-Kontext:

```text
2 = Obrađen
3 = Arhiviran
4 = Kontrola
5 = Dopuna
```

Andere Werte müssen noch sauber aus Repo + Lookup-Tabellen verifiziert werden.

## 4.2 kandidat_status_prijave

```text
NULL  41,297
0        106
1     54,276
2     22,817
3        170
4        660
5        298
6      2,175
7          6
9         43
10       104
12        17
15         1
18        18
27        16
```

Im Repo werden mehrere dieser Werte für Recruiting-/Visa-/Projektprozesse verwendet.

## 4.3 kandidat_tf_status

```text
NULL 120,300
2        171
8        960
12       193
14       206
16       117
18         1
20         1
22        25
25        11
27        14
35         2
36         1
37         1
39         1
```

Diese Verteilung wird NICHT automatisch in das neue Statusmodell übernommen.

---

# 5. Taskforce / Reservation / Kommunikation

Wichtige Legacy-Tabelle:

```text
crm.idk_task_force
```

Spalten u. a.:

```text
tf_id
tf_candidate_id
tf_agent_id
tf_nalog_id
tf_project_id
tf_casting_id
tf_status_id
tf_call_appointment
tf_note
tf_important_note
tf_last_active_task
tf_brojac_neuspjela_komunikacija
tf_files
tf_doe
tf_vrsta_id
```

Unterstützende Tabellen:

```text
idk_tf_agent_nalog
idk_tf_reservations
idk_tf_stats_reservations
idk_tf_statusi
idk_tf_transfered_candidates_logs
idk_tf_vrste
```

Repo-Befund: Taskforce-Queries kombinieren `tf_status_id`, `tf_reserved_agent`, `kandidat_pogresan_broj`, `kandidat_nedostupan` und Reservations-Tabellen.

Interpretation:

> Reservation/Taskforce ist historisch kein einzelner sauberer Prozess, sondern über mehrere Felder und Tabellen verteilt.

Für das neue System muss zuerst fachlich entschieden werden, was überhaupt davon erhalten bleibt.

---

# 6. Wichtige Korrektur: „ND“ ist nicht eindeutig

Frühere Annahmen, dass `idk_nd_kandidata` automatisch „Nedostupan“ bedeutet, sind durch die Repo-Analyse NICHT bestätigt.

## 6.1 Tatsächliche „Nedostupan“ / nicht erreichbar-Logik

Bekannte Elemente:

```text
idk_kandidati.kandidat_nedostupan
idk_nedostupan_log
brojac
status
doe
```

Taskforce-Queues schließen `kandidat_nedostupan = 1` aus.

In `candidateTransferToTF/getCandidateTransferFilter.php` existiert eine Ausschlusslogik auf Basis von `idk_nedostupan_log`, `brojac`, `status`, `doe` und Auftrag.

Noch offen:

- exakter Write-Pfad
- exakte Zählerlogik
- exakte Schwelle für fehlgeschlagene Kontaktversuche
- Zusammenhang zu `tf_brojac_neuspjela_komunikacija`

Die Erinnerung „nach mehr als 5 Fehlversuchen“ ist noch zu verifizieren und darf nicht als bestätigter Codefakt behandelt werden.

## 6.2 `idk_nd_kandidata` wirkt im Repo diploma-/anerkennungsbezogen

Im Repo wird `idk_nd_kandidata` mit Diplom-/Anerkennungslogik verbunden, unter anderem zusammen mit:

```text
kandidat_dipl_id
status_nd_kandidata
pstatus_nd_kandidata
idk_nostrifikovane_diplome
full_recognition
```

Daraus folgt:

> `idk_nd_kandidata` darf nicht automatisch als „Nedostupan-Tabelle“ behandelt werden.

Das muss beim neuen Datenmodell sauber getrennt werden.

---

# 7. Messenger als separater historischer Prozess

Es gibt eine eigene Zustandslogik über:

```text
kandidat_status_messenger
```

Repo-Verwendung u. a. in:

```text
send_dipl.php
ssdata_search.php
cron_bot_instal.php
cron_bot_obrada.php
src/messenger/app/Idk_kandidati.php
serversidedata2.php
vibersms.php
viber_marketing.php
ajax.php
hammer_prijave.php
dak.php
do_dipl.php
```

Legacy-Queries kombinieren teilweise gleichzeitig:

```text
kandidat_status
kandidat_status_prijave
kandidat_status_messenger
```

Interpretation:

> Der alte Kandidatendatensatz trägt mehrere unabhängige Prozesszustände direkt am Kandidaten. Diese Vermischung soll im neuen Modell aufgebrochen werden.

---

# 8. Bewerbung / Projektstatus

`kandidat_status_prijave` wird in vielen Bereichen verwendet, z. B.:

```text
send_dipl.php
cron_partnerpayment.php
odlasci.php
dashboardNaloga/utils.php
dashboardRemindera/utils.php
serverside_partner.php
viber_marketing.php
dashboardNaloga/API_Viziranje.php
candidateTransferToTF/updateSP.php
dashboardNaloga/API_Recruiting.php
ssdata_search.php
```

Einige im Code sichtbare Zuordnungen:

```text
2  → u_projektu_nr
3  → casting
4  → zaposlen (in einem Reporting-Kontext)
15 → ceka_termin
18 → ceka_vizu
```

Historische DB-/Lookup-Zuordnungen, die noch verifiziert werden müssen:

```text
1  Slobodan
2  U projektu NR
3  Casting
4  Završen / möglicherweise in einzelnen Reports „zaposlen“
5  Odbijen
6  U projektu RZ
7  Čeka ugovor
8  Poslan ugovor
9  Potpisan ugovor
10 Početak rada
12 Prikupljanje dokumentacije
15 Čeka termin
18 Čeka vizu
21 Dopuna dokumenata
24 Odbijena viza
27 Dobio vizu
```

Nicht als endgültiges neues Statusmodell verwenden.

---

# 9. Candidate Transfer / Projektzuordnung

Repo-Befund aus `candidateTransferToTF/updateSP.php`:

Beim Transfer werden am Kandidaten gleichzeitig u. a. gesetzt:

```text
kandidat_status_prijave = 6
kandidat_nalog_id
kandidat_latest_reserved_time
```

Interpretation:

> Bewerbungsstatus, Auftrag/Projekt und Reservierungszeitpunkt sind im Legacy-Modell technisch miteinander gekoppelt.

Das ist ein zentraler Grund, diese Bereiche im neuen Modell als getrennte Domänen/Relationen zu bauen.

---

# 10. Candidate Search / Legacy-Filter

Die alte Suche kombiniert viele Bedingungen, unter anderem:

- Archivstatus
- falsche Nummer
- nicht erreichbar
- Bewerbungsstatus
- Messengerstatus
- Auftrag/Projekt
- Beruf
- Sprache
- Alter
- Diploma-/Anerkennungsstatus

Das bestätigt, dass die neue Suche strukturiert aufgebaut werden sollte, ohne alte Statusfelder direkt zu kopieren.

---

# 11. Berufs- und Kandidatenquellen

Bekannte Kandidaten-/Berufsdaten liegen nicht nur in einer Tabelle.

Wichtige Tabellen:

```text
idk_kandidati
idk_kandidat_edukacija
idk_kandidat_radno_iskustvo
idk_kandidat_jezici
idk_nd_kandidata
idk_dak_kandidati
idk_project_kandidati
occupation
occupation_alias
job_occupation_map
pozicija_occupations
```

Bekannte Verknüpfungen zwischen `idk_kandidati` und `idk_nd_kandidata`:

```text
idk_kandidati.kandidat_dipl_id → idk_nd_kandidata.id_broj_nd_kandidata
```

ca. 77,616 Hauptkandidaten sind darüber verknüpft.

Weitere Rückverknüpfung:

```text
idk_nd_kandidata.kandidat_idd → Hauptkandidat
```

ca. 5,756 Treffer.

Zusätzlich existieren Überschneidungen über normalisierte E-Mail und wenige belastbare JMBG-Werte.

Wichtige Konsequenz:

> Die Migration darf Person und Beruf nicht nur anhand des Hauptdatensatzes beurteilen. Ausbildung, Berufserfahrung, Diploma-/Anerkennung und andere Kandidatenquellen müssen gemeinsam ausgewertet werden.

---

# 12. Duplicate-/Qualitätsbefunde

## Main candidates

```text
E-Mail gefüllt:        42,523
Duplicate groups:       2,034
excess rows grob:       3,075

Telefon gefüllt:      119,116
Duplicate groups:       3,692
excess rows grob:       4,172

Name + DOB:            73,734
Duplicate groups:       3,309
excess rows grob:       3,700
```

JMBG ist in der Haupttabelle als Identitätsschlüssel in der aktuellen Form nicht verlässlich, weil viele Werte auf `0` normalisieren.

## idk_nd_kandidata

```text
E-Mail:                28,636
Duplicate groups:       1,004
excess rows grob:       1,708

Telefon:               80,252
Duplicate groups:       1,698
excess rows grob:       1,933

JMBG:                   6,548
Duplicate groups:          64
excess rows grob:         460
```

Diese Zahlen dürfen nicht einfach addiert werden, weil sich Dublettenkriterien überschneiden können.

---

# 13. Notizen

Bekannte Tabelle:

```text
crm.idk_notes
```

Spalten:

```text
note_id
note_txt
note_files
note_datetime
note_group
note_dataid
note_employeeid
```

Noch zu analysieren:

- Bedeutung von `note_group`
- Bedeutung / Referenzmodell von `note_dataid`
- welche Notizen Kandidaten, Kunden, Projekte, Taskforce etc. betreffen

Große weitere Notiz-/Historientabelle:

```text
idk_nd_kandidata_biljeske
```

ca. 928,882 Zeilen.

Spalten u. a.:

```text
id_biljeska_nd
id_kandidata_biljeska_nd
status_biljeska_nd
tip_biljeska_nd
razlog_biljeska_nd
sadrzaj_biljeska_nd
vrijeme_dodavanja_biljeska_nd
employee
vrijeme_ponovnog_zvanja
payment/date/attachment fields
```

Da `idk_nd_kandidata` im Repo diploma-/anerkennungsbezogen wirkt, darf diese Tabelle nicht vorschnell als „Nedostupan-Notizen“ interpretiert werden.

---

# 14. Legacy Cron

Alte Tabellen:

```text
idk_nd_cron_settings
idk_nd_cron_export
idk_triggerurl
```

`idk_nd_cron_export` hat ca. 7,581 Zeilen; neueste bekannte Daten bis 2025-07-09.

Historische Regeln:

```text
status 2 Obrađen   → 15 Tage
status 3 Arhiviran → 45 Tage
status 4 Kontrola  → 5 Tage
status 5 Dopuna    → 10 Tage
```

Empfehlung:

> Alte Cron-Infrastruktur nicht automatisch wiederverwenden. Nur fachliche Regeln übernehmen, falls sie nach Prüfung weiterhin benötigt werden.

---

# 15. Search-Funktion / MCP-Fehler

Bekannte Funktion:

```text
crm_search_candidates(jsonb, text)
```

`pg_stat_statements` zeigte grob:

```text
112 calls
~79.54 s cumulative execution
~0.71 s mean
~91.6% des gemessenen Ausführungsanteils
```

Ein repräsentatives EXPLAIN lag bei ca. 29 Sekunden.

Wichtiger Schema-/Validierungsfehler:

```text
occupation_terms = ["Elektriker"]
→ korrekte Filterwirkung

occupation_terms = "Elektriker"
→ Filter kann faktisch ignoriert werden
```

Beobachtetes Beispiel:

```text
["Elektriker"] → 7,346
"Elektriker"   → 117,558
```

Konsequenz:

> Neue MCP-/API-Schemas müssen Typen strikt validieren. Ungültige Eingaben dürfen niemals stillschweigend zu einer breiteren Suche führen.

Auch `include_archive=true` wirkt in der bestehenden Logik potenziell missverständlich und muss beim Neubau klar definiert werden.

---

# 16. Mitarbeiter-/Aktionszahlen – nicht interpretieren

Vom Benutzer genannte Werte:

```text
Adil Salkicevic       173
Adis Toromanović       75
Benjamin Bender       134
Emir Bender             67
Faris Sabic            207
Sladjana Stoponja      461
```

Rechnerische Summe:

```text
1,117
```

WICHTIG:

> Die Bedeutung dieser Zahlen ist noch nicht bestätigt. Nicht als Mitarbeiteranzahl, Downloadanzahl oder Kandidatenanzahl interpretieren, solange die zugrunde liegende Query / Tabelle / Logik nicht verifiziert wurde.

---

# 17. Noch offene Untersuchungen

- vollständiger End-to-End-Kandidatenworkflow im Legacy-Repo
- exakte Bedeutungen aller Statuswerte
- Writes auf `kandidat_nedostupan`
- Writes/Increments in `idk_nedostupan_log`
- Verwendung von `tf_brojac_neuspjela_komunikacija`
- vollständige Taskforce-Statuslogik
- `note_group` / `note_dataid`
- genaue Rolle von Dippel/NP
- alle Tabellen/Felder mit Berufsbezug
- historische Bulk-Downloads / Exporte um ca. 2024 ± einige Monate
- Loginzeiten, IP-/Metadaten und mögliche versteckte Accounts/Rollen
- Verantwortlichkeit für konkrete Marketing-Berichtigungen

Diese Punkte dürfen erst nach Prüfung als Fakten dokumentiert werden.
