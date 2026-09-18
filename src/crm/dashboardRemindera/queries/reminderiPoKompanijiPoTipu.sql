WITH
    reminders AS(
    SELECT
        *
    FROM
        `idk_pp_reminders`
),
remindersWithSettings AS(
    SELECT
        *
    FROM
        reminders
    JOIN `idk_pp_reminder_settings` ON reminders.pr_reminder_setting_id = `idk_pp_reminder_settings`.`prs_id`
),
remindersWithCompany AS(
    SELECT
        *
    FROM
        remindersWithSettings
    INNER JOIN `idk_nalozi` ON remindersWithSettings.prs_nalog_id = `idk_nalozi`.`nalog_id`
    INNER JOIN `idk_companies` ON `idk_companies`.`company_id` = `idk_nalozi`.`kompanija_id`
),
activeReminders AS(
    SELECT
        *
    FROM
        remindersWithCompany
    WHERE
        pr_status NOT IN(3, 4)
),
joinedReminders AS(
    SELECT
        activeReminders.pr_reminder_setting_id,
        activeReminders.pr_candidate_id,
        activeReminders.prs_reminder_type_id,
        activeReminders.company_name
    FROM
        activeReminders
    INNER JOIN `idk_pp_reminders` ON `idk_pp_reminders`.`pr_reminder_setting_id` = activeReminders.`pr_reminder_setting_id` AND `idk_pp_reminders`.`pr_candidate_id` = activeReminders.`pr_candidate_id`
),
byCompany AS(
    SELECT
        company_name,
        prs_reminder_type_id,
        COUNT(*) AS qty
    FROM
        joinedReminders
    GROUP BY
        company_name,
        prs_reminder_type_id
)
SELECT
    company_name,
	SUM(IF(prs_reminder_type_id = 1, qty, 0)) AS "1",
    SUM(IF(prs_reminder_type_id = 2, qty, 0)) AS "2",
    SUM(IF(prs_reminder_type_id = 3, qty, 0)) AS "3",
    SUM(IF(prs_reminder_type_id = 4, qty, 0)) AS "4",
    SUM(IF(prs_reminder_type_id = 5, qty, 0)) AS "5",
    SUM(IF(prs_reminder_type_id = 6, qty, 0)) AS "6"
FROM
    byCompany
GROUP BY
    company_name