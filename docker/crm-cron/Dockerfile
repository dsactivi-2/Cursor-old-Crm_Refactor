FROM alpine:latest

COPY ./docker/crm-cron/crontab /etc/crontabs/root
COPY ./docker/crm-cron/entry.sh /entry.sh
COPY ./docker/crm-cron/scripts/sendRequest.sh /usr/local/bin/sendRequest
COPY ./docker/crm-cron/scripts/runAllReminders.sh /usr/local/bin/runAllReminders
RUN chmod -R a+x /etc/crontabs/root \
    && chmod -R a+x /entry.sh \
    && chmod -R a+x /usr/local/bin/sendRequest \
    && chmod -R a+x /usr/local/bin/runAllReminders \
    && apk add dcron tzdata bash --no-cache

ENV TZ Europe/Berlin

CMD ["/entry.sh"]
