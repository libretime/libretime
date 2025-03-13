---
title: Uninstall
sidebar_position: 91
---

This guide provide some guidance to uninstall LibreTime from your system.

We recommend using **disposable devices** for your installations, so you can delete your old system and install on a fresh one easily without worrying about old files.

If you don't have a way use disposable devices, below are commands that should help you remove most of the LibreTime files from your system.

:::danger

Use these commands at your **own risk**, we can't guarantee that these commands are always up to date.

:::

Remove configuration directories:

```bash
sudo rm -Rf /etc/airtime
sudo rm -Rf /etc/libretime
```

Remove logs directories:

```bash
sudo rm -Rf /var/log/airtime
sudo rm -Rf /var/log/libretime
```

Remove runtime directories:

```bash
sudo rm -Rf /var/lib/airtime
sudo rm -Rf /var/lib/libretime
```

Remove shared directories:

```bash
sudo rm -Rf /usr/share/airtime
sudo rm -Rf /usr/share/libretime
```

Remove systemd services files:

```bash
sudo rm -f /{etc,usr/lib}/systemd/system/airtime*
sudo rm -f /{etc,usr/lib}/systemd/system/libretime*
```

Remove nginx configuration files:

```bash
sudo rm -f /etc/nginx/sites-{available,enabled}/airtime*
sudo rm -f /etc/nginx/sites-{available,enabled}/libretime*
```

Remove php-fpm configuration files:

```bash
sudo rm -f /etc/php/*/fpm/pool.d/airtime*
sudo rm -f /etc/php/*/fpm/pool.d/libretime*
```

Remove logrotate configuration files:

```bash
sudo rm -f /etc/logrotate.d/airtime*
sudo rm -f /etc/logrotate.d/libretime*
```

Remove python packages:

```bash
sudo pip3 uninstall \
    libretime-analyzer \
    libretime-api \
    libretime-api-client \
    libretime-celery \
    libretime-playout \
    libretime-shared \
    libretime-worker

# Check if we forgot old python packages.
# Remove packages that show up with this commands.
sudo pip3 freeze | grep libretime
sudo pip3 freeze | grep airtime
```

Delete the postgresql database and user:

```bash
sudo -u postgres dropdb airtime
sudo -u postgres dropdb libretime

sudo -u postgres dropuser airtime
sudo -u postgres dropuser libretime
```

Delete the rabbitmq vhost and user:

```bash
sudo rabbitmqctl delete_vhost airtime
sudo rabbitmqctl delete_vhost libretime

sudo rabbitmqctl delete_user airtime
sudo rabbitmqctl delete_user libretime
```

Delete the file storage (you probably don't want that):

```bash
sudo rm -Rf /srv/airtime
sudo rm -Rf /srv/libretime
```

Search for remaining files:

```bash
sudo find / -name "libretime*"
sudo find / -name "airtime*"
```
