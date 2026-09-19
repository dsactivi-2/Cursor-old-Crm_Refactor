# Candidate ID – unveränderliche Identität

Stand: 2026-09-19

## Feste Regel

Die Kandidaten-ID ist die dauerhafte Identität eines Kandidaten.

> Sobald ein Kandidat angelegt wurde, behält er seine Kandidaten-ID für immer.

Die Kandidaten-ID darf NICHT verändert werden durch:

- Migration
- Datenbereinigung
- Merge von Berufsbezeichnungen
- Statusänderung
- Modulwechsel
- Bewerbung auf eine andere Stelle
- Zuordnung zu einem Kunden
- Import aus anderen Legacy-Bereichen
- Dublettenprüfung
- UI-Bearbeitung
- MCP-Aktionen
- Automationen
- Skripte

Auch bei der Migration in die neue Datenbank müssen die vorhandenen Kandidaten-IDs erhalten bleiben.

Neue Kandidaten erhalten genau eine neue Kandidaten-ID, die danach dauerhaft unveränderlich bleibt.

---

# Sicherheitsschutz

Die Unveränderlichkeit soll nicht nur eine Anwendungsregel sein, sondern auf mehreren Ebenen geschützt werden.

## 1. Datenbank-Schutz

Die Kandidaten-ID soll nach INSERT nicht mehr updatebar sein.

Empfohlene Schutzschichten:

- Primärschlüssel / UNIQUE Constraint
- Foreign Keys auf allen abhängigen Tabellen
- Trigger oder vergleichbarer DB-Schutz, der Änderungen an der Kandidaten-ID blockiert
- keine `ON UPDATE CASCADE`-Logik für die Kandidaten-ID
- keine normale API-Funktion zum Ändern der Kandidaten-ID

Sinngemäße Regel:

```text
OLD.candidate_id != NEW.candidate_id
→ UPDATE ABLEHNEN
```

## 2. API- und MCP-Schutz

Die Kandidaten-ID darf in normalen Update-Tools nicht als veränderbares Feld angeboten werden.

Beispiel:

Erlaubt:

```text
update_candidate(
  candidate_id=12345,
  phone=...,
  status=...
)
```

Nicht erlaubt:

```text
update_candidate(
  candidate_id=12345,
  new_candidate_id=67890
)
```

Ein MCP-Agent darf niemals Kandidaten-IDs neu nummerieren oder ändern.

## 3. UI-Schutz

Die Kandidaten-ID wird angezeigt, ist aber nicht editierbar.

Empfohlen:

```text
Candidate ID: 12345
[read only]
```

## 4. Migrations-Schutz

Vor einer Migration:

- vollständige Liste der bestehenden Kandidaten-IDs sichern
- Duplikate / ungültige IDs prüfen
- Mapping Alt-ID → Neu-ID erzeugen
- Zielregel: Alt-ID = Neu-ID

Nach der Migration:

- Anzahl vergleichen
- jede Kandidaten-ID gegen Quelle prüfen
- fehlende IDs melden
- unerwartete neue IDs melden
- geänderte IDs als kritischen Fehler behandeln

Beispiel Prüfregel:

```text
source candidate_id = 12345
new candidate_id    = 12345
→ OK

source candidate_id = 12345
new candidate_id    = 98765
→ CRITICAL ERROR
```

## 5. Merge-Regel bei Dubletten

Auch bei Dubletten darf nicht stillschweigend eine alte Kandidaten-ID überschrieben werden.

Wenn zwei Datensätze später als dieselbe Person erkannt werden, muss separat entschieden werden, wie mit beiden historischen IDs umgegangen wird.

Bis dafür eine fachliche Regel beschlossen ist:

> Keine automatische ID-Zusammenführung und keine automatische Löschung historischer Kandidaten-IDs.

Ein möglicher späterer Ansatz wäre eine separate Alias-/Legacy-ID-Relation, aber das wird erst nach fachlicher Entscheidung umgesetzt.

---

# Wichtig für Berufs-Merges

Wenn Berufsbezeichnungen zusammengeführt werden, wird ausschließlich die Berufszuordnung geändert.

Beispiel:

```text
Kandidat 12345
Beruf alt: KFZ-Mechaniker
Beruf neu: Automechaniker
```

Die Kandidaten-ID bleibt:

```text
12345 → 12345
```

Der Merge eines Berufs darf niemals eine Kandidaten-ID verändern.

---

# Architekturprinzip

```text
Candidate ID = permanente Identität

Berufe
Status
Skills
Stellen
Kunden
Bewerbungen
Notizen
Kommunikation
Dokumente
Module

→ dürfen sich ändern

Candidate ID
→ darf sich niemals ändern
```

Diese Regel ist als kritische Invariante der neuen CRM-Architektur zu behandeln.
