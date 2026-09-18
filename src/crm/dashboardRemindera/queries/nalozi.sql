        SELECT
            nalog_id,
            nalog_naziv,
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
        (
            SELECT
                company_name,
                nalog_naziv,
                nalog_id,
                poslanPuta,
                COUNT(*) AS levelCount
            FROM
        (
            SELECT
                *,
                COUNT(*) AS poslanPuta
            FROM
        (
            SELECT
                activeReminders.pr_reminder_setting_id,
                activeReminders.pr_candidate_id,
                activeReminders.company_name,
                activeReminders.nalog_naziv,
                activeReminders.nalog_id
            FROM
        (
            SELECT
                *
            FROM
        (
            SELECT
                *
            FROM
        (
            SELECT
                *
            FROM
        (
        SELECT
            *
        FROM
            `idk_pp_reminders`
        ) as reminders 
            JOIN `idk_pp_reminder_settings` ON reminders.pr_reminder_setting_id = `idk_pp_reminder_settings`.`prs_id`
            $whereTypes
        )as remindersWithSettings
            INNER JOIN `idk_nalozi` ON remindersWithSettings.prs_nalog_id = `idk_nalozi`.`nalog_id`
            INNER JOIN `idk_companies` ON `idk_companies`.`company_id` = `idk_nalozi`.`kompanija_id`
            WHERE
                `idk_companies`.`company_id` = :id
        )as remindersWithCompany 
            WHERE
                pr_status NOT IN(3, 4)
        )as activeReminders
            INNER JOIN `idk_pp_reminders` ON `idk_pp_reminders`.`pr_reminder_setting_id` = activeReminders.`pr_reminder_setting_id` AND `idk_pp_reminders`.`pr_candidate_id` = activeReminders.`pr_candidate_id`
        )as joinedReminders 
            GROUP BY
                pr_reminder_setting_id,
                pr_candidate_id
        )as remindersSentTimes
            GROUP BY
                poslanPuta,
                company_name,
                nalog_id
        )as levelsCounted 
        GROUP BY
            nalog_naziv