# JSI CRM Update – Target Architecture & Migration Plan

Stand: 2026-09-19

Dieses Dokument beschreibt die vorgeschlagene Zielarchitektur auf Basis der bisherigen Diskussion. Es ist ein Arbeitsentwurf und wird nach der Status- und Workflow-Besprechung weiter reduziert.

---

# 1. Architekturprinzip

```text
New UI
   │
API / Service Layer
   │
MCP Servers
   │
PostgreSQL
   │
Structured CRM data + Vector search
   │
Audit / History / Automation
```

Grundregel:

> Eine Person = ein kanonischer Kandidat. Keine Kopien derselben Person je Modul oder Prozess.

---

# 2. Datenmodell – Kern

## 2.1 Candidate

```text
candidates
----------
id
external_reference
first_name
last_name
date_of_birth
lifecycle_code
created_at
updated_at
```

Nur wirklich kandidatenbezogene Kerndaten gehören direkt hierhin.

Weitere Daten über Relationen:

```text
candidate_contacts
candidate_addresses
candidate_sources
candidate_occupations
candidate_skills
candidate_languages
candidate_education
candidate_work_experience
candidate_documents
candidate_notes
candidate_communications
```

---

# 3. Occupations

## 3.1 Kanonischer Beruf

```text
occupations
-----------
id
code
name_en
active
created_at
updated_at
```

Beispiel:

```text
471 | AUTO_MECHANIC | Automotive Mechanic
```

Keine dauerhafte Speicherung hunderter historischer Schreibvarianten als eigenständige Berufe.

## 3.2 Candidate occupation relation

```text
candidate_occupations
---------------------
id
candidate_id
occupation_id
source_type
source_id
is_primary
confidence
verified
created_at
```

`source_type` kann z. B. zeigen, woher die Berufszuordnung kam:

```text
profile
education
work_experience
diploma
legacy_import
manual
```

Dadurch bleibt nachvollziehbar, warum eine Person einem Beruf zugeordnet wurde.

---

# 4. Ausbildung

```text
candidate_education
-------------------
id
candidate_id
occupation_id nullable
qualification_id nullable
institution
country_code
start_date
end_date
graduated
raw_legacy_text nullable
notes nullable
```

Wichtig:

Wenn ein Ausbildungsdatensatz fachlich auf einen Beruf zeigt, wird dieselbe zentrale `occupation_id` verwendet wie im Kandidatenprofil.

---

# 5. Berufserfahrung

```text
candidate_work_experience
-------------------------
id
candidate_id
occupation_id nullable
company_name
country_code
start_date
end_date
experience_months nullable
level_code nullable
raw_legacy_text nullable
details nullable
```

Zusätzliche Skills können separat verknüpft werden:

```text
candidate_work_experience_skills
```

So kann eine Person mehrere Berufserfahrungen sauber besitzen, ohne alles in ein Freitextfeld zu drücken.

---

# 6. Skills

```text
skills
------
id
code
name_en
category_id nullable
active
```

```text
candidate_skills
----------------
candidate_id
skill_id
level_code nullable
verified
source_type
```

Beispiele:

```text
CABLE_INSTALLATION
DRILLING
ROUTER_INSTALLATION
WELDING
PLC_PROGRAMMING
CUSTOMER_SERVICE
```

---

# 7. Sprache

```text
languages
---------
id
code
name_en
```

```text
candidate_languages
-------------------
candidate_id
language_id
level_code
verified
```

Sprachniveau als kontrollierter Wert, z. B. CEFR:

```text
A1 A2 B1 B2 C1 C2 NATIVE
```

---

# 8. Kunden und Stellen

```text
customers
---------
id
name
status_code
created_at
updated_at
```

```text
jobs
----
id
customer_id
title_en
description
headcount_requested
status_code
created_at
updated_at
```

Eine Stelle ist nicht automatisch ein standardisierter Beruf.

---

# 9. Job Profiles / Einsatzprofile

Für nicht-standardisierte Tätigkeiten:

```text
job_profiles
------------
id
code
name_en
description
```

Beispiel:

```text
INTERNET_INSTALLATION_TECHNICIAN
```

Dazu geeignete Berufe:

```text
job_profile_occupations
-----------------------
job_profile_id
occupation_id
weight nullable
```

Dazu relevante Skills:

```text
job_profile_skills
------------------
job_profile_id
skill_id
required_level nullable
required boolean
```

---

# 10. Gespeicherte Suchprofile für Kunden/Stellen

```text
job_search_profiles
-------------------
id
customer_id
job_id nullable
name
active
created_by
created_at
updated_at
```

Feste Kriterien werden relational gespeichert:

```text
job_search_profile_occupations
job_search_profile_skills
job_search_profile_languages
job_search_profile_qualifications
```

Weitere strukturierte Regeln können als typisierte Filter gespeichert werden, sofern das relational nicht sinnvoll ist.

Wichtig:

> Ein Suchprofil ist nur eine gespeicherte Filterdefinition. Es ist keine Reservierung und keine feste Kandidatenliste.

Beim Laden eines Profils dürfen Filter temporär überschrieben oder ergänzt werden.

---

# 11. Kandidatensuche

Die neue Suche wird in Ebenen aufgebaut.

## Ebene 1: Strukturierte Filter

```text
occupation IDs
skill IDs
language + level
education
work experience
country / city
availability
process state
customer/job profile
```

## Ebene 2: Semantische Suche

Für unstrukturierte Inhalte:

```text
notes
conversation summaries
CV text
document text
legacy free text
```

## Ebene 3: Kombination

Beispiel:

```text
occupation_id IN (...)
AND German >= B1
AND driving_licence = B
AND semantic_match("open to Germany after language course")
```

---

# 12. Vector Search

Empfohlenes generisches Modell:

```text
semantic_documents
------------------
id
candidate_id nullable
entity_type
entity_id
content
content_language
source_type
created_at
```

```text
semantic_document_embeddings
----------------------------
document_id
embedding
embedding_model
created_at
```

Mit PostgreSQL/pgvector kann strukturierte Suche und Vektorsuche in derselben Datenplattform kombiniert werden.

Nicht jeden Datensatz blind vektorisieren. Nur Inhalte, bei denen semantisches Auffinden echten Nutzen bringt.

---

# 13. Mehrsprachigkeit

Die Datenbank bleibt kanonisch Englisch.

UI-Übersetzungen können in einer allgemeinen Localization-Schicht verwaltet werden.

Beispiel:

```text
localization_values
-------------------
entity_type
entity_id
field_name
locale
translated_value
```

Alternativ kann UI-Lokalisierung vollständig außerhalb der Kerndatenbank in i18n-Dateien/Services liegen, wenn nur feste Stammdaten betroffen sind.

Unterstützte Zielsprachen zu Beginn:

```text
German
English
Bosnian
Serbian
```

Für Serbisch bei Bedarf Latin + Cyrillic berücksichtigen.

MCP-Suche:

```text
User query in any supported language
→ semantic / controlled resolver
→ canonical ID/code
→ structured DB query
```

---

# 14. Statusmodell

Noch bewusst offen.

Nicht alle alten Status gehören an `candidates`.

Mögliche Domänen, die getrennt geprüft werden müssen:

```text
candidate lifecycle
application state
communication state
job/project relation
messenger/channel state
diploma/recognition process
```

Grundregel:

> Ein Status gehört zu dem Objekt, dessen Zustand er beschreibt.

Beispiel:

Ein Bewerbungsstatus gehört zu `application`, nicht automatisch direkt zu `candidate`.

---

# 15. Applications

Falls nach fachlicher Prüfung benötigt:

```text
applications
------------
id
candidate_id
customer_id nullable
job_id nullable
project_id nullable
status_code
created_at
updated_at
```

Dadurch kann ein Kandidat mehrere Bewerbungen parallel besitzen.

---

# 16. Kommunikation

```text
candidate_communications
------------------------
id
candidate_id
channel_code
direction_code
occurred_at
employee_id nullable
summary nullable
raw_text nullable
outcome_code nullable
```

Strukturierte Outcomes sollten kontrollierte Werte sein.

Unstrukturierter Gesprächsinhalt kann zusätzlich semantisch indexiert werden.

---

# 17. Notizen

```text
candidate_notes
---------------
id
candidate_id
author_user_id
note_type_code nullable
text
created_at
updated_at nullable
```

Notizen bleiben Freitext, werden aber für Vector Search indexierbar.

---

# 18. Audit

Jede kritische Migration oder fachliche Änderung sollte nachvollziehbar sein.

```text
audit_log
---------
id
actor_type
actor_id
action_code
entity_type
entity_id
before_data
after_data
created_at
```

Spezielle Occupation-Merges zusätzlich dokumentieren:

```text
occupation_merge_audit
----------------------
id
source_occupation_id
target_occupation_id
affected_candidate_count
affected_education_count
affected_work_experience_count
affected_job_count
affected_profile_count
approved_by
executed_at
```

---

# 19. Occupation Cleanup / Merge Pipeline

## Phase A – Discovery

Alle Berufsquellen scannen:

```text
main candidate profile
candidate education
candidate work experience
diploma/recognition
Dippel/NP
job mappings
project mappings
relevant legacy free text
```

## Phase B – Candidate terms sammeln

Alle unterschiedlichen Begriffe extrahieren und normalisieren.

Nur zur Analyse:

```text
lowercase
trim
unicode normalization
diacritics handling
punctuation cleanup
```

Der normalisierte technische Text ist nicht automatisch der neue Beruf.

## Phase C – Semantisches Clustering

Begriffe nach Bedeutung gruppieren.

Signale:

```text
text similarity
multilingual semantic similarity
education context
work experience context
skills
job/project context
historical co-occurrence
```

## Phase D – Canonical occupations

Je fachlicher Bedeutung genau einen kanonischen Zielberuf auswählen oder neu anlegen.

## Phase E – Mapping Review

Unsichere Cluster manuell prüfen.

Automatische Zuordnung nur bei hoher Sicherheit.

## Phase F – Relationen umhängen

Nicht nur Occupation-Namen ändern.

Alle Referenzen migrieren:

```text
candidate occupations
education
work experience
job mappings
search profiles
projects
other dependent tables
```

## Phase G – Referential Verification

Vor Entfernen alter Datensätze:

```text
old occupation reference count = 0
```

## Phase H – Cleanup

Legacy-Mappingdaten archivieren oder entfernen, wenn nicht mehr produktiv benötigt.

---

# 20. Personen-Merge / Candidate Identity

Berufsbereinigung und Personen-Deduplizierung sind zwei getrennte Probleme.

Mögliche Identitätssignale:

```text
legacy candidate IDs
source links
normalized email
normalized phone
JMBG where trustworthy
name + DOB
other source-specific links
```

E-Mail und Telefon dürfen nicht alleine als sichere Identität behandelt werden.

Empfehlung:

```text
canonical candidate ID
+
source_candidate_links
+
match confidence
+
manual review for ambiguous cases
```

---

# 21. MCPs

Empfohlene Trennung:

```text
MCP Internal Read
MCP Internal Controlled Write
MCP Internal Full Access (UI-controlled)
MCP Customer
MCP Candidate
```

Jeder MCP nutzt serverseitig definierte Policies.

Beispiele:

```text
allowed tools
allowed fields
allowed entities
allowed write operations
approval requirements
scope restrictions
```

Keine sensible Policy nur als Systemprompt implementieren.

---

# 22. API-/MCP-Schema-Validierung

Alle Tool-Parameter strikt typisieren.

Beispiel:

```text
occupation_ids: array<integer>
```

Ein String anstelle eines Arrays muss einen Fehler erzeugen und darf nicht stillschweigend zu einer ungefilterten Suche führen.

Das ist besonders wichtig wegen des bereits beobachteten Legacy-Fehlers in `crm_search_candidates`.

---

# 23. Legacy zu Target – Mapping-Arbeitsmatrix

Für jede Legacy-Funktion:

| Legacy | Fachliche Bedeutung | Ziel | Entscheidung |
|---|---|---|---|
| `idk_kandidati` | Kandidatenkern + vermischte Zustände | `candidates` + Relationen | prüfen |
| `idk_kandidat_edukacija` | Ausbildung | `candidate_education` | behalten/bereinigen |
| `idk_kandidat_radno_iskustvo` | Arbeitserfahrung | `candidate_work_experience` | behalten/bereinigen |
| `idk_kandidat_jezici` | Sprachen | `candidate_languages` | behalten/bereinigen |
| `idk_task_force` | Queue/Kommunikation/Projektmix | neu definieren | stark prüfen |
| `kandidat_status` | Legacy-Kandidatenstatus | neues Statusmodell | neu bauen |
| `kandidat_status_prijave` | Bewerbung/Projekt/Visum gemischt | pro Prozessobjekt | neu bauen |
| `kandidat_status_messenger` | Messengerprozess | Channel/Communication | prüfen |
| `kandidat_nedostupan` | Erreichbarkeit | Communication state | prüfen |
| `idk_nd_kandidata` | wahrscheinlich Diploma/Anerkennung | eigenes Modul | verifizieren |
| `idk_notes` | Notizen | zentralisierte Notes | behalten/mappen |
| cron-Strukturen | historische Automationen | neue Rules/Workers | nicht kopieren |

---

# 24. Reihenfolge für die tatsächliche Umsetzung

```text
1. Legacy workflow analysis
2. Data domains finalisieren
3. Status workshop / status redesign
4. Filter fields finalisieren
5. Canonical occupation/skill taxonomy
6. Target schema erstellen
7. Read-only migration dry run
8. Merge reports erzeugen
9. Manual review der unsicheren Zuordnungen
10. Data migration
11. Referential verification
12. Search API / MCP
13. Vector search
14. New UI
15. Controlled cutover
```

---

# 25. Noch nicht bauen / nicht vorschnell entscheiden

Bis zur fachlichen Besprechung nicht automatisch übernehmen:

```text
legacy reservation logic
legacy taskforce statuses
legacy messenger statuses
legacy cron workflows
legacy candidate lifecycle values
all legacy occupation aliases
all legacy tables
```

Die neue Struktur soll kleiner werden. Historische Komplexität ist kein Grund, sie erneut abzubilden.
