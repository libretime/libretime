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

Download the docker compose files from the repository:

```bash
# Load LIBRETIME_VERSION variable
source .env

wget "https://raw.githubusercontent.com/libretime/libretime/$LIBRETIME_VERSION/docker-compose.yml"
wget "https://raw.githubusercontent.com/libretime/libretime/$LIBRETIME_VERSION/docker/nginx.conf"
wget "https://raw.githubusercontent.com/libretime/libretime/$LIBRETIME_VERSION/docker/config.template.yml"
```

:::info

The `config.template.yml` configuration file you downloaded already contains specific values required by the container setup, you shouldn't change them:

```yaml
database:
  host: "postgres"
  password: ${POSTGRES_PASSWORD} # The value will be substituted
rabbitmq:
  host: "rabbitmq"
  password: ${RABBITMQ_DEFAULT_PASS} # The value will be substituted
playout:
  liquidsoap_host: "liquidsoap"
liquidsoap:
  server_listen_address: "0.0.0.0"
stream:
  outputs:
    .default_icecast_output:
      host: "icecast"
      source_password: ${ICECAST_SOURCE_PASSWORD} # The value will be substituted
      admin_password: ${ICECAST_ADMIN_PASSWORD} # The value will be substituted
```

:::

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
```

Generate a [configuration file](../configuration.md) from the `./config.template.yml` template with the previously generated passwords:

```bash
bash -a -c "source .env; envsubst < config.template.yml > config.yml"
```

:::note

On Debian based systems, if the `envsubst` command isn't found you can install it with:

```bash
sudo apt install gettext-base
```

:::

Next, edit the [configuration file](../configuration.md) at `./config.yml` to fill required information and to match your needs.

:::info

You can find more details in the `docker-compose.yml` file or on the external services docker specific documentation:

- [Postgres](https://hub.docker.com/_/postgres)
- [RabbitMQ](https://hub.docker.com/_/rabbitmq)
- [Icecast](https://github.com/libretime/icecast-docker#readme)

:::

Next, run the following commands to setup the database:

```bash
docker compose run --rm api libretime-api migrate
```

Finally, start the services, and check that they're running using the following commands:

```bash
docker compose up -d

docker compose ps
docker compose logs -f
```

## Securing LibreTime

Once LibreTime is running, it's recommended to [install a reverse proxy](./reverse-proxy.md) to setup SSL termination and secure your installation.

## First login

Once the setup is completed, log in the interface (with the default user `admin` and password `admin`), and edit the project settings (go to **Settings** > **General**) to match your needs.

:::warning

Remember to change your password.

:::
