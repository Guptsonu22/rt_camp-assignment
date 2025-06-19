#!/bin/bash
# This script should set up a CRON job to run cron.php every 5 minutes.
# You need to implement the CRON setup logic here.


CRON_JOB="*/5 * * * * php $(pwd)/cron.php > /dev/null 2>&1"

# Check if the cron job already exists
crontab -l | grep -F "$CRON_JOB" >/dev/null 2>&1

if [ $? -eq 0 ]; then
    echo "CRON job already exists."
else
    (crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -
    echo "CRON job added successfully."
fi
