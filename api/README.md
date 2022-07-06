# LibreTime API

This API provides access to LibreTime's database via a Django application.

## Deploying

Deploying in a production environment is done in the [`install`](../install)
script which installs LibreTime. This is how the API is installed in the Vagrant
development images too. This method does not automatically reflect changes to
this API. After any changes, the `libretime-api` systemd service needs
restarting:

```bash
sudo systemctl restart libretime-api
```

Connections to the API are proxied through the Apache web server by default.
Endpoint exploration and documentation is available from
`http://example.com/api/v2/schema/swagger-ui/`, where `example.com` is the URL
for the LibreTime instance.

## Development

For development, you can install all required dependencies and loading the environment using the following command:

```bash
make install
source .venv/bin/activate
```

You should be able to lint or format the code or run api commands:

```bash
make format
make lint

libretime-api help
```

In order to work with the database and message queue, you need to start the docker based
development stack present at the project root:

```bash
pushd ..
docker-compose up -d
popd
```

You can now run the api tests:

```bash
make test
```

### Inside Vagrant

You can develop the api using a live reloading version within Vagrant:

```bash
vagrant up bullseye
vagrant ssh bullseye

cd /vagrant/api
sudo pip3 install -e .

sudo systemctl stop libretime-api
sudo -u libretime LIBRETIME_DEBUG=True libretime-api runserver 0.0.0.0:8081
```

## 3rd Party Licences

`libretime_api/tests/resources/song.mp3`: Steps - Tears On The Dancefloor (Album
Teaser) by mceyedol. Downloaded from
https://soundcloud.com/mceyedol/steps-tears-on-the-dancefloor-album-teaser
released under a Creative Commons Licence
([cc-by-sa-nc-sa](https://creativecommons.org/licenses/by-nc-sa/3.0/))
