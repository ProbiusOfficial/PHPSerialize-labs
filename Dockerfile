FROM php:8.2-apache
COPY . /var/www/html

# 目录列表与字符集(与 main 分支行为一致)
RUN printf '<Directory /var/www/html>\n    Options +FollowSymLinks +Indexes\n</Directory>\n' > /etc/apache2/conf-enabled/lab-dir.conf \
    && printf 'AddDefaultCharset UTF-8\n' > /etc/apache2/conf-enabled/lab-charset.conf

# 关闭页面报错输出(deprecation 提示会干扰关卡显示),错误记录到日志
RUN printf 'display_errors=Off\nlog_errors=On\nerror_reporting=E_ALL & ~E_DEPRECATED & ~E_STRICT\n' > /usr/local/etc/php/conf.d/lab.ini

EXPOSE 80
