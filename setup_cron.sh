#!/bin/bash
# This script should set up a CRON job to run cron.php every 24 hours.
# You need to implement the CRON setup logic here.

powershell.exe -Command "
\$action = New-ScheduledTaskAction -Execute 'C:\\php-8.3\\php.exe' -Argument 'C:\\php-assignment\\xkcd-SafuraAnam\\src\\cron.php';
\$trigger = New-ScheduledTaskTrigger -Daily -At 00:00;
\$settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries;
Register-ScheduledTask -TaskName 'XKCDComicJob' -Action \$action -Trigger \$trigger -Settings \$settings -Force;
"

if [ $? -eq 0 ]; then
  echo 'CRON job registered successfully to run cron.php every 24 hours at 00:00.'
else
  echo 'Failed to register scheduled task.'
fi
