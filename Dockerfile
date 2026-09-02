FROM php:5.5-apache
COPY . /var/www/html

# 目录列表与字符集(独立 conf 片段,兼容不同版本 Apache 布局)
RUN printf '<Directory /var/www/html>\n    Options +FollowSymLinks +Indexes\n</Directory>\n' > /etc/apache2/conf-enabled/lab-dir.conf \
    && printf 'AddDefaultCharset UTF-8\n' > /etc/apache2/conf-enabled/lab-charset.conf

EXPOSE 80
