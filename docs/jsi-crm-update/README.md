# JSI CRM Update – Documentation Index

Stand: 2026-09-19

Dieser Ordner enthält den aktuellen konsolidierten Stand der CRM-Neustrukturierung.

## Dokumente

### `00_MASTER_SUMMARY.md`
Gesamtüberblick und fachliche Leitplanken:

- Zielbild
- zentrale Datenbankprinzipien
- Englisch als kanonische Standardsprache
- Berufs-/Skill-Bereinigung
- strukturierte Filter statt Freitext
- Kunden-/Stellen-Suchprofile
- Vector Search
- MCP- und UI-Grundsätze
- offene Entscheidungen

### `01_LEGACY_SCHEMA_AND_FINDINGS.md`
Bestätigte bzw. bisher beobachtete Legacy-Strukturen:

- Repository-Struktur
- Supabase-/Neon-Datenbanklandschaft
- große Tabellen und Row Counts
- Statusverteilungen
- Taskforce / Messenger / Application / Nedostupan
- wichtige Korrektur zu `idk_nd_kandidata`
- Kandidaten-/Berufsquellen
- Duplicate-/Qualitätsbefunde
- Legacy Cron
- Search-/MCP-Probleme
- noch offene forensische Untersuchungen

### `02_TARGET_ARCHITECTURE_AND_MIGRATION.md`
Arbeitsentwurf für das neue System:

- Kandidaten-Kernmodell
- Occupations / Skills / Education / Work Experience
- Customers / Jobs / Job Profiles
- gespeicherte Suchprofile
- strukturierte + semantische Suche
- Vector Search
- Mehrsprachigkeit
- Audit
- Occupation-Merge-Pipeline
- Candidate Identity / Deduplizierung
- MCP-Sicherheitsmodell
- Legacy-to-Target-Mapping
- empfohlene Umsetzungsreihenfolge

## Wichtige Arbeitsregel

Das Legacy-CRM ist eine Quelle zur Rekonstruktion des bisherigen Geschäftsprozesses, aber nicht die Zielarchitektur.

Bei jedem Modul wird entschieden:

```text
BEHALTEN
ÄNDERN
ENTFERNEN
```

Erst danach werden endgültige Zieltabellen, Statuswerte, Workflows und Migrationen implementiert.

## Nächster fachlicher Schritt

1. Datenbank-Domänen gemeinsam finalisieren
2. Statussystem vollständig durchgehen
3. alle Berufs-/Skill-/Qualifikationsquellen im Legacy-System inventarisieren
4. Kandidatenworkflow Ende-zu-Ende aus Repo rekonstruieren
5. danach Target Schema reduzieren und finalisieren
