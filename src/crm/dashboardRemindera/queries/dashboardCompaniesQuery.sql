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
        activeReminders.company_name
    FROM
        activeReminders
    INNER JOIN `idk_pp_reminders` ON `idk_pp_reminders`.`pr_reminder_setting_id` = activeReminders.`pr_reminder_setting_id` AND `idk_pp_reminders`.`pr_candidate_id` = activeReminders.`pr_candidate_id`
),
remindersSentTimes AS(
    SELECT
        *,
        COUNT(*) AS poslanPuta
    FROM
        joinedReminders
    GROUP BY
        pr_reminder_setting_id,
        pr_candidate_id
),
levelsCounted AS(
    SELECT
        company_name,
        poslanPuta,
        COUNT(*) AS levelCount
    FROM
        remindersSentTimes
    GROUP BY
        poslanPuta,
        company_name
)
SELECT
    company_name,
    SUM(
        IF(poslanPuta = 1, levelCount, 0)
    ) AS level1,
    SUM(
        IF(poslanPuta = 2, levelCount, 0)
    ) AS level2,
    SUM(
        IF(poslanPuta = 3, levelCount, 0)
    ) AS level3,
    SUM(
        IF(poslanPuta = 4, levelCount, 0)
    ) AS level4,
    SUM(
        IF(poslanPuta > 5, levelCount, 0)
    ) AS level5plus
FROM
    levelsCounted
GROUP BY
    company_name