---
title: Install using docker
sidebar_position: 10
---

import Tabs from '@theme/Tabs';
import TabItem from '@theme/TabItem';
import CodeBlock from '@theme/CodeBlock';
import vars from '@site/vars';

This guide walk you though the steps required to install LibreTime on your system using docker.

## Before installing

Before installing LibreTime, you need to make sure that [Docker](https://docs.docker.com/engine/) is installed on your operating system and **up-to-date**.

## Download

First, set the version you want to install:

<CodeBlock language="bash">
echo LIBRETIME_VERSION="{vars.version}" > .env
</CodeBlock>

Download the docker-compose files from the repository:

```bash
# Load LIBRETIME_VERSION variable
source .env

wget "https://raw.githubusercontent.com/libretime/libretime/$LIBRETIME_VERSION/docker-compose.yml"
wget "https://raw.githubusercontent.com/libretime/libretime/$LIBRETIME_VERSION/docker/nginx.conf"
wget "https://raw.githubusercontent.com/libretime/libretime/$LIBRETIME_VERSION/docker/config.yml"
```

## Setup LibreTime

Once the files are downloaded, generate a set of random passwords for the different docker services used by LibreTime:

```bash
echo "# Postgres
POSTGRES_PASSWORD=$(openssl rand -hex 16)

# RabbitMQ
RABBITMQ_DEFAULT_PASS=$(openssl rand -hex 16)

# Icecast
ICECAST_SOURCE_PASSWORD=$(openssl rand -hex 16)
ICECAST_ADMIN_PASSWORD=$(openssl rand -hex 16)
ICECAST_RELAY_PASSWORD=$(openssl rand -hex 16)" >> .env
cat .env
```

:::info

You can find more details in the `docker-compose.yml` file or on the external services docker specific documentation:

- [Postgres](https://hub.docker.com/_/postgres)
- [RabbitMQ](https://hub.docker.com/_/rabbitmq)
- [Icecast](https://github.com/libretime/icecast-docker#readme)

:::

Next, edit the [configuration file](../configuration.md) at `./config.yml` to set the previously generated passwords, fill required information, and to match your needs.

:::info

The `docker/config.yml` configuration file you previously downloaded already contains specific values required by the container setup, you shouldn't change them:

```yaml
database:
  host: "postgres"
rabbitmq:
  host: "rabbitmq"
playout:
  liquidsoap_host: "liquidsoap"
liquidsoap:
  server_listen_address: "0.0.0.0"
stream:
  outputs:
    .default_icecast_output:
      host: "icecast"
```

:::

Next, run the following commands to setup the database:

```bash
docker-compose run --rm api libretime-api migrate
```

Finally, start the services, and check that they're running properly using the following commands:

```bash
docker-compose up -d

docker-compose ps
docker-compose logs -f
```

## Securing LibreTime

Once LibreTime is running, it's recommended to [install a reverse proxy](./reverse-proxy.md) to setup SSL termination and secure your installation.

## First login

Once the setup is completed, log in the interface (with the default user `admin` and password `admin`), and edit the project settings (go to **Settings** > **General**) to match your needs.
