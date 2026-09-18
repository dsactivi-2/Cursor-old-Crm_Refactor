#!/bin/bash
# No need to check if production, since the sendRequest script does that for us

for i in {1..19}
do
  sendRequest "$JS_URL/cron_reminders.php?reminder_type=$i"
done

