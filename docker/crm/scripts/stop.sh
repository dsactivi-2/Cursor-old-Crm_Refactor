#!/bin/bash

# Sleep in order to give the ALB time to stop sending requests
sleep 20

# Execute 'supervisord status QUIT php-fpm'
supervisorctl signal QUIT php-fpm

# Wait until the process has exited
while pgrep php-fpm > /dev/null; do
    sleep 1
done

# Run 'supervisord stop apache'
apachectl -k graceful-stop

echo "Processes have been managed successfully." > /dev/stdout
