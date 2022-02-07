server {
    listen 80;
    server_name client;
    error_log  /var/log/nginx/error.log debug;
    location / {
        proxy_pass http://client:8080;
        proxy_set_header Host $host;

    }
    location /sockjs-node {
        proxy_pass http://client:8080;
        proxy_set_header Host $host;
        # below lines make ws://localhost/sockjs-node/... URLs work, enabling hot-reload
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
    }
    location /api/ {
        # on the backend side, the request URI will _NOT_ contain the /api prefix,
        # which is what we want for a pure-api project
        proxy_pass http://api:8000/;
        proxy_set_header Host localhost;
    }
}
