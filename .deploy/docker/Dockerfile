FROM mkante/nginx-php7.2

COPY ./ /srv
COPY ./.deploy/supervisor.conf /etc/supervisor/conf.d/supervisor.conf
COPY ./.deploy/www.conf /etc/php/7.2/fpm/pool.d/www.conf

EXPOSE 80
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
