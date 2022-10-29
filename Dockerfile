FROM wyveo/nginx-php-fpm:php81
COPY . /usr/share/nginx/html/laravel-streams
EXPOSE 80
