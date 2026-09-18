#!/bin/bash

# Print usage if no arguments are given
if [ $# -eq 0 ]; then
    echo "Usage: $0 <url>"
    exit 1
fi

# Exit early if not production
if [ "$APP_ENV" != "production" ]; then
    exit 0
fi

# Send HTTP request to the given URL
wget --user-agent='Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:47.0) Gecko/20100101 Firefox/47.0' -q -t 1 -T 7200 $1
