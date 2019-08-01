FROM mkante/nginx-php7.2

RUN apt-get update && apt-get -y install redis-server
COPY supervisor.conf /etc/supervisor/supervisord.conf

EXPOSE 80
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
