---
title: Reverse Proxy
layout: article
category: install
---


In some deployments, the LibreTime server is deployed behind a reverse proxy,
for example in containerization use-cases such as Docker and LXC. LibreTime
makes extensive use of its API for some site functionality, which causes
[Cross-Origin Resource Sharing (CORS)](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS)
to occur. By default, CORS requests are blocked by your browser and the origins
need to be added to the **Allowed CORS URLs** block in
[**General Settings**](/docs/settings). These origins should include any
domains that will be used externally to connect to your reverse proxy that you
want handled by LibreTime. These URLS can also be set during the first run configuration
that is displayed when you first install LibreTime

### Reverse Proxy Basics

A reverse proxy allows the LibreTime server to not be connected to the open internet. In
this configuration, it is rather behind another server that proxies traffic to it from
users. This provides some advantages in the containerization space, as this means that
the containers can be on their own internal network, protected from outside access.

A reverse proxy also allows SSL to be terminated in a single location for multiple sites.
This means that all your traffic to the proxy from clients is encrypted, but the reverse
proxy's traffic to the containers on the internal network is not. All the SSL certificates
live on the reverse proxy and can be renewed there instead of on the individual
containers.

### Setup

There are known bugs when using LibreTime behind a reverse proxy ([#957](https://github.com/LibreTime/libretime/issues/957)
tracks the issue and contains a temporary workaround). For SSL redirection to work, you
need two domains: one for LibreTime and one for Icecast. Here, these will be
`libretime.example.com` and `icecast.example.com`.

You will also require two VMs, servers or containers. Alternatively the reverse proxy can
be located on the server, proxying connections to containers also on the host. Setting up
a containerization environment is beyond the scope of this guide. It assumes that you have
Nginx set up on `proxy` and LibreTime will be installed on `libretime`. You will need root
access on both. `libretime` also needs to be able to be accessed from `proxy`
(`ping libretime` on `proxy`).

On `libretime`, install LibreTime as described in the [install guide](/install). In short
this means run the following commands:

```
git clone https://github.com/LibreTime/libretime.git
cd libretime
sudo ./install -fiap
```

Once it has installed, replace `<hostname>localhost</hostname>` in
`/etc/icecast2/icecast.xml` with the following:

```
<hostname>icecast.example.com</hostname>
```

This is the hostname that people listening to your stream will connect to and what
LibreTime will use to stream out to them. You will then need to restart Icecast:

```
sudo systemctl restart icecast2
```

On `proxy`, run the following:

```
cat << EOF | sudo tee /etc/nginx/sites-available/libretime.conf
server {
    listen 80;
    server_name libretime.example.com;
    location / {
        rewrite ^ https://$server_name$request_uri? permanent;
    }
}
server {
    listen 443 ssl;
    server_name libretime.example.com;
    ssl_certificate /etc/letsencrypt/live/libretime.example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/libretime.example.com/privkey.pem;
    add_header Strict-Transport-Security "max-age=15552000;";
    add_header X-Frame-Options "SAMEORIGIN";
    client_max_body_size 512M;
    location / {
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_pass http://libretime/;
    }
}
EOF
```

This Nginx configuration ensures that all traffic uses SSL to the reverse proxy, and
traffic is proxied to `libretime`.

Next, the SSL certificate needs to be generated and the site activated.

```
sudo apt install certbot
sudo systemctl stop nginx
sudo certbot certonly -d libretime.example.com -a standalone
sudo systemctl start nginx
```

You can now go to *https://libretime.example.com* and go
through the installer. On `General Settings`, you need to change the Webserver Port to
`443` and add the following CORS URLs:

```
https://libretime.example.com
http://libretime.example.com
https://localhost
http://localhost
```

Finally, the configuration file needs updating. Under `[general]`, `force_ssl`
needs to be set to true:

```
[general]
...
force_ssl = true
```
