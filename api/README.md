# LibreTime API

This API provides access to LibreTime's database via a Django application. This
API supersedes the [PHP API](../legacy/application/controllers/ApiController.php).

## Deploying

Deploying in a production environment is done in the [`install`](../install)
script which installs LibreTime. This is how the API is installed in the Vagrant
development images too. This method does not automatically reflect changes to
this API. After any changes, the `libretime-api` systemd service needs
restarting:

    sudo systemctl restart libretime-api

Connections to the API are proxied through the Apache web server by default.
Endpoint exploration and documentation is available from
`http://example.com/api/v2/`, where `example.com` is the URL for the LibreTime
instance.

### Development

For a live reloading version within Vagrant:

```
vagrant up debian-buster
# Run through the web setup http://localhost:8080
vagrant ssh debian-buster
sudo systemctl stop libretime-api
sudo systemctl restart libretime-analyzer libretime-celery libretime-liquidsoap libretime-playout
cd /vagrant/api
sudo pip3 install -e .
sudo -u www-data LIBRETIME_DEBUG=True python3 bin/libretime-api runserver 0.0.0.0:8081
```

Unit tests can be run in the vagrant environment using

```
sudo -u www-data LIBRETIME_DEBUG=True python3 bin/libretime-api test libretimeapi
```

## 3rd Party Licences

`libretimeapi/tests/resources/song.mp3`: Steps - Tears On The Dancefloor (Album
Teaser) by mceyedol. Downloaded from
https://soundcloud.com/mceyedol/steps-tears-on-the-dancefloor-album-teaser
released under a Creative Commons Licence
([cc-by-sa-nc-sa](https://creativecommons.org/licenses/by-nc-sa/3.0/))
