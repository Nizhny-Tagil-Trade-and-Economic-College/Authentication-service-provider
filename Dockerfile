FROM ubuntu/apache2:latest as server
ENV TZ=Asia/Yekaterinburg
RUN apt update -y
RUN apt upgrade -y
RUN apt install -y php php-xml php-json php-pdo php-mysqli composer
RUN rm -rf /etc/apache2/sites-available/000-default.conf /var/www/html/*
COPY ./html/ /var/www/html/
COPY ./assets/ /var/www/html/assets/
RUN chown -R root:root /var/www/html
RUN chmod -R 777 /var/www/html
COPY ./000-default.conf /etc/apache2/sites-available/
RUN a2enmod rewrite
