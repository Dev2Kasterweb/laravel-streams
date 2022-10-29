## Instruções de uso

- ``git clone git@github.com:Dev2Kasterweb/laravel-streams.git``
- ``composer install``
- ``docker build -t laravel-streams-1 .`` (incluindo o ponto)
- ``docker run -dp 80:80 laravel-streams-1``
- dentro do container abrir o arquivo ``/etc/nginx/conf.d/default.conf`` (acessar é mais simples via [remote-container](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.vscode-remote-extensionpack)
- no lugar do ``default.conf`` original, substituir o conteúdo pelo novo contexto ``server`` abaixo
- no cli do container: ``service nginx restart`` 

## Novo texto do ``default.conf``
```
server {
    listen   80; ## listen for ipv4; this line is default and implied
    listen   [::]:80 default ipv6only=on; ## listen for ipv6

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    root /usr/share/nginx/html/laravel-streams/public;
    
    index index.php;

    # Make site accessible from http://localhost/
    server_name _;

    # Disable sendfile as per https://docs.vagrantup.com/v2/synced-folders/virtualbox.html
    sendfile off;

    # Security - Hide nginx version number in error pages and Server header
    server_tokens off;

    # Add stdout logging
    error_log /dev/stdout info;
    access_log /dev/stdout;

    # reduce the data that needs to be sent over network
    gzip off;

    location / {
        # First attempt to serve request as file, then
        # as directory, then fall back to index.php
        try_files $uri $uri/ /index.php?$query_string;
    }

    # redirect server error pages to the static page /50x.html
    #
    error_page   500 502 503 504 404  /index.php;

    # pass the PHP scripts to FastCGI server listening on socket
    #
    location ~ \.php$ {
        try_files $uri $uri/ /index.php?$query_string;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

        location ~* \.(jpg|jpeg|gif|png|css|js|ico|xml)$ {
                expires           5d;
        }

    # deny access to . files, for security
    #
    location ~ /\. {
            log_not_found off;
            deny all;
    }

}
```
