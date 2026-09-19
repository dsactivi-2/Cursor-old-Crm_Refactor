# JSI CRM Update – Master Summary

Stand: 2026-09-19

## Ziel

Das bestehende Legacy-CRM soll nicht 1:1 nachgebaut werden. Ziel ist eine neue, deutlich einfachere, zentrale und skalierbare CRM-Architektur für Kandidaten, Kunden, Stellen, Suche, MCPs und eine neue UI.

Grundprinzip:

> Eine Person = ein kanonischer Kandidatendatensatz. Prozesse, Module, Stellen, Bewerbungen, Kommunikation und weitere Funktionen werden als Relationen bzw. Prozessobjekte angebunden – nicht als Kopien derselben Person in verschiedenen Bereichen.

Die Reihenfolge der Neustrukturierung ist bewusst:

1. Datenbank-Grundstruktur festlegen
2. Statusmodell komplett neu besprechen
3. Berufe / Skills / Qualifikationen / Filterstruktur bereinigen
4. Suche und gespeicherte Filterprofile definieren
5. Workflows aus dem Legacy-CRM rekonstruieren und nur notwendige Funktionen übernehmen
6. MCPs und UI auf das neue Modell setzen
7. Migration erst nach fachlicher Freigabe durchführen

---

# 1. Zentrale Architekturentscheidungen

## 1.1 Standardsprache der Datenbank

Die neue Datenbank soll fachlich und technisch standardmäßig Englisch verwenden.

Beispiele:

- Tabellen- und Spaltennamen: Englisch
- kanonische Codes: Englisch / sprachneutral
- kanonische fachliche Werte: Englisch
- technische Beziehungen immer über IDs / Codes, nicht über angezeigten Freitext

Beispiel:

```text
occupation_id = 471
code = AUTO_MECHANIC
name_en = Automotive Mechanic
```

Die UI kann Deutsch, Englisch, Bosnisch und Serbisch anzeigen. Die UI-Übersetzung ist Darstellung, nicht die fachliche Identität des Datensatzes.

Wichtige Regel:

> Der englische Wert ist die fachliche Referenz für Menschen; die ID bzw. der Code ist die technische Referenz für Datenbank, API und MCP.

---

## 1.2 Keine dauerhaften 50 Schreibvarianten im neuen Kernsystem

Die vorhandenen Berufsbezeichnungen sind über 10–12 Jahre in Freitext entstanden. Für einzelne Berufe existieren sehr viele Schreibweisen, Tippfehler, Übersetzungen, Abkürzungen und fachlich ähnliche Begriffe.

Ziel:

- gleiche Bedeutungen einmalig erkennen
- auf einen kanonischen Beruf mergen
- alle Kandidaten- und Prozessreferenzen auf den neuen kanonischen Beruf umhängen
- alte Varianten nur während der Migration als Mapping-/Auditinformation benutzen
- im neuen produktiven Kernsystem keine dauerhafte Synonym-Sammlung als Primärmodell führen

Beispiel:

```text
Automehanicar
Auto mehanicar
KFZ Mechaniker
Kfz-Mechaniker
Automechaniker
Automotive Mechanic

→ AUTO_MECHANIC
→ occupation_id = 471
```

Nach dem Merge zeigen alle betroffenen Personen und abhängigen Objekte auf dieselbe `occupation_id`.

---

## 1.3 Beruf ist nicht dasselbe wie Einsatzprofil

Das Legacy-CRM enthält auch künstlich angelegte „Berufe“, die in Wirklichkeit Such- oder Einsatzprofile sind.

Beispiel:

Ein Kunde sucht Personen für Internetanschlüsse. Dafür gibt es nicht zwingend einen einzelnen anerkannten Ausbildungsberuf. Geeignet könnten z. B. Elektriker, Installateure, Mechatroniker oder andere handwerklich geeignete Personen sein.

Deshalb trennen:

```text
occupation   = echter Beruf / fachliche Berufszuordnung
skill        = konkrete Fähigkeit
job_profile  = Einsatzprofil / Zielrolle für eine Suche oder Stelle
```

Ein `job_profile` darf mehrere geeignete Berufe und Skills referenzieren.

---

# 2. Kandidatenbezogene Berufs- und Qualifikationsdaten

Ein Beruf darf nicht nur aus einem Haupt-Berufsfeld abgeleitet werden.

Berufsinformationen können im Legacy-System an mehreren Stellen vorkommen:

- Haupt-Kandidatenprofil
- Ausbildung / `idk_kandidat_edukacija`
- Berufserfahrung / `idk_kandidat_radno_iskustvo`
- Diplom-/Anerkennungsbereich
- Dippel/NP bzw. weitere historische Kandidatenbereiche
- Job-/Positions-Mappings
- ggf. weitere Freitextfelder

Wichtige Migrationsregel:

> Berufszuordnung niemals aus nur einer Tabelle ableiten. Alle relevanten Kandidatenquellen, Ausbildung, Berufserfahrung und beruflichen Zuordnungen werden gemeinsam ausgewertet.

Beispiel:

```text
Candidate 123

Main occupation: empty
Education: Automehaničar
Work experience: KFZ servis, 8 years
NP/Dippel: Automechaniker

→ canonical occupation: Automotive Mechanic
```

Eine Person kann mehrere Berufe haben.

Empfohlene Zielrelationen:

```text
candidate_occupations
candidate_education
candidate_work_experience
```

Alle referenzieren dieselbe zentrale Tabelle:

```text
occupations
```

Damit kann dieselbe kanonische Berufs-ID in Ausbildung, Arbeitserfahrung und aktuellem Berufsprofil wiederverwendet werden.

---

# 3. Strukturierte Datenerfassung statt Freitext

Alles, was später gesucht, gefiltert, verglichen oder automatisiert werden soll, soll möglichst strukturiert gespeichert werden.

Geeignete UI-Komponenten:

- Dropdown
- Multi-Select
- Checkboxen
- Choice / Radio
- Toggle
- definierte Kategorien

Freitext bleibt nur für Informationen, die nicht sinnvoll standardisiert werden können, z. B. Notizen oder ergänzende Beschreibungen.

Beispiel Berufserfahrung:

```text
Occupation: Electrician
Areas: House installation, Cabinet building, Cable laying
Experience: 5–10 years
Level: Independent
Country: Germany
Leadership: Yes/No
Driving licence: B
Skills: Multi-select
Details: optional free text
```

Wichtige Regel:

> Freitext ist Zusatzinformation, nicht die primäre Quelle für Filterlogik.

---

# 4. Kunden, Stellen und gespeicherte Filterprofile

Für einen Kunden kann es mehrere Stellen / Suchbedarfe geben.

Beispiel:

```text
Customer: XY GmbH
Job: Internet Installation Technician
Demand: 10 employees
```

Zu einer Stelle wird ein wiederverwendbares Such-/Filterprofil gespeichert.

Beispiel:

```text
Suitable occupations:
- Electrician
- Installer
- Mechatronics Technician
- Telecommunications Technician

Skills:
- Cable installation
- Drilling
- Router installation

Language:
- German B1+

Driving licence:
- B
```

Die Suche soll zwei Wege unterstützen:

1. normale manuelle Filterung
2. gespeichertes Stellen-/Kundenprofil per Klick laden

Das Profil ist nur eine Suchdefinition. Es ist keine Reservierung und keine feste Kandidatenliste.

Temporäre Filter dürfen bei einer Suche ergänzt oder geändert werden, ohne das gespeicherte Profil automatisch zu überschreiben.

Empfohlener Name:

```text
job_search_profile
```

oder

```text
candidate_search_profile
```

---

# 5. Vector Search / semantische Suche

Neben strukturierter Suche soll eine semantische Vektorsuche für unstrukturierte Inhalte vorgesehen werden.

Geeignete Inhalte:

- Notizen
- Gesprächsverläufe
- Call Summaries
- CV-Text
- Dokumenttext
- Freitext in Berufserfahrung
- historische Kommentare

Nicht als Vector-Primärsuche verwenden:

- IDs
- Status
- Datum
- Beruf
- Skills
- Sprachen
- strukturierte Qualifikationen

Empfohlenes Suchmodell:

```text
Structured filters
+ semantic/vector search
```

Beispiel:

```text
occupation = Electrician
language = German >= A2
semantic query = "interested in Germany later after improving language"
```

Technisch bietet sich PostgreSQL mit pgvector bzw. ein äquivalenter Vektorindex an.

---

# 6. Statusmodell – noch NICHT festgelegt

Das bestehende Statussystem ist historisch gewachsen und enthält mehrere sich überschneidende Statusarten.

Es soll NICHT 1:1 übernommen werden.

Vor Umsetzung müssen wir fachlich entscheiden:

- welche Status überhaupt noch benötigt werden
- welche alten Status doppelt sind
- welche Status eigentlich zu einer Bewerbung, Kommunikation, Stelle oder einem anderen Prozess gehören
- welche Status vollständig entfallen

Wichtig:

> Bestehende Begriffe wie „reserviert“ werden nicht automatisch in das neue Modell übernommen. Erst nach fachlicher Besprechung wird entschieden, ob sie erhalten, geändert oder entfernt werden.

---

# 7. MCP-Zielbild

Geplante MCP-Flächen:

1. interner MCP – read only
2. interner MCP – read/write mit Freigabeschalter und begrenzten Aktionen
3. interner vollständig offener MCP – über UI aktivierbar/deaktivierbar
4. Kunden-MCP – serverseitig streng eingeschränkte Felder und Aktionen
5. Kandidaten-MCP

Sicherheitsprinzip:

> Berechtigungen und Feldsichtbarkeit müssen serverseitig erzwungen werden. Nicht nur über Prompts.

Sprachprinzip:

Ein Benutzer kann in seiner Sprache suchen. Der MCP löst den Begriff auf einen kanonischen Code bzw. eine ID auf und führt die eigentliche Datenbankabfrage sprachneutral aus.

Beispiel:

```text
"KFZ-Mechaniker"
→ AUTO_MECHANIC
→ occupation_id 471
```

---

# 8. UI-Zielbild

Die neue UI soll vor allem kontrollierte Stammdaten und strukturierte Filter anbieten.

Bei neuen fachlichen Werten sollen Benutzer nicht beliebig neue Freitext-Berufe erzeugen können.

Neue Berufe / Skills / Kategorien werden kontrolliert in Stammdaten angelegt.

UI-Sprache ist nutzerspezifisch.

Beispiel:

```text
AUTO_MECHANIC
DE: KFZ-Mechaniker
EN: Automotive Mechanic
BS: Automehaničar
SR: Automehaničar
```

Die technische Relation bleibt immer dieselbe ID.

---

# 9. Empfohlene Zieltabellen – Arbeitsstand

Die endgültigen Tabellen werden erst nach Workflow- und Statusbesprechung festgelegt.

Arbeitsentwurf:

```text
candidates
candidate_contacts
candidate_occupations
candidate_education
candidate_work_experience
candidate_languages
candidate_skills
candidate_documents
candidate_notes
candidate_communications
candidate_sources

occupations
skills
qualifications
languages

customers
customer_contacts
jobs
job_profiles
job_search_profiles
job_search_profile_occupations
job_search_profile_skills

applications
projects
project_candidates

users
roles
permissions

automation_rules
automation_runs
audit_log
```

Nicht als final betrachten. Insbesondere Status-, Reservation-, Taskforce-, Messenger- und Diploma-Prozesse müssen zuerst fachlich besprochen werden.

---

# 10. Migration – Grundregeln

1. Legacy-Daten zunächst nur lesen und analysieren.
2. UI und echte Arbeitsabläufe rekonstruieren.
3. Entscheiden: behalten / ändern / entfernen.
4. Neues fachliches Datenmodell bauen.
5. Alte Freitextwerte clustern und kanonischen Werten zuordnen.
6. Kandidaten über alle relevanten Quellen und Untertabellen hinweg zusammenführen.
7. Alle Relationen auf neue IDs umhängen.
8. Erst nach vollständiger Referenzprüfung alte Datensätze entfernen oder archivieren.
9. Jede Migration auditieren.

Ein Merge bedeutet ausdrücklich nicht nur Umbenennen.

Beispiel:

```text
old occupation IDs: 12, 38, 91, 144
new occupation ID: 471
```

Alle betroffenen Relationen müssen auf 471 umgestellt werden.

Nach dem Update muss geprüft werden, ob noch Referenzen auf alte IDs existieren.

---

# 11. Noch offene fachliche Entscheidungen

- finaler Kandidaten-Lifecycle
- neues Statusmodell
- welche Legacy-Status entfallen
- Bedeutung und Zukunft von Taskforce
- Bedeutung und Zukunft von „reserviert“
- Messenger-Prozess
- Diploma/Anerkennungsprozess
- genaue Rolle von Dippel/NP und anderen historischen Quellen
- endgültiges Kunden-/Jobmodell
- welche Filter als feste Stammdaten geführt werden
- welche Felder tatsächlich frei bleiben dürfen
- endgültige Vector-Search-Quellen
- genaue MCP-Schreibrechte je Rolle

---

# 12. Arbeitsprinzip für die nächsten Schritte

Bei jedem alten Modul wird geprüft:

```text
Was macht es fachlich?
↓
Brauchen wir es weiterhin?
↓
BEHALTEN / ÄNDERN / ENTFERNEN
↓
Welche Daten braucht die neue UI dafür?
↓
Welche Relation / Tabelle braucht das neue Modell?
```

Das Legacy-CRM ist Referenz für den tatsächlichen bisherigen Ablauf, aber nicht die Zielarchitektur.
