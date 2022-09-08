---
title: Configuration
sidebar_position: 20
---

To configure LibreTime, you need to edit the `/etc/libretime/config.yml` file. This page describe the available options to configure your installation.

Don't forget to restart the services after you made changes to the configuration file:

```
sudo systemctl restart libretime.target
```

## General

The `general` section configure anything related to the legacy and API services.

```yml
general:
  # The public url.
  # > this field is REQUIRED
  public_url: "https://example.com"
  # The internal API authentication key.
  # > this field is REQUIRED
  api_key: "some_random_generated_secret!"

  # List of origins allowed to access resources on the server,
  # the [general.public_url] origin is automatically included.
  # > default is []
  allowed_cors_origins: []

  # The server timezone, should be a lookup key in the IANA time zone database,
  # for example Europe/Berlin.
  # > default is UTC
  timezone: UTC

  # How many hours ahead Playout should cache scheduled media files.
  # > default is 1
  cache_ahead_hours: 1

  # Authentication adaptor to use for the legacy service, specify a class like
  # LibreTime_Auth_Adaptor_FreeIpa to replace the built-in adaptor.
  # > default is local
  auth: "local"
```

## Storage

The `storage` section configure the project storage.

```yml
storage:
  # Path of the storage directory.
  # > default is /srv/libretime
  path: "/srv/libretime"
```

## Database

The `database` section configure the PostgreSQL connection.

:::caution

Before editing this section be sure to update the PostgreSQL server with the desired values.

#### Changing a PostgreSQL user password

You can change the `libretime` PostgreSQL user password using:

```bash
sudo -u postgres psql -c "ALTER USER libretime PASSWORD 'new-password';"
```

:::

```yml
database:
  # The hostname of the PostgreSQL server.
  # > default is localhost
  host: "localhost"
  # The port of the PostgreSQL server.
  # > default is 5432
  port: 5432
  # The name of the PostgreSQL database.
  # > default is libretime
  name: "libretime"
  # The username of the PostgreSQL user.
  # > default is libretime
  user: "libretime"
  # The password of the PostgreSQL user.
  # > default is libretime
  password: "some_random_generated_secret!"
```

## RabbitMQ

The `rabbitmq` section configure the RabbitMQ connection.

:::caution

Before editing this section be sure to update the RabbitMQ server with the desired values.

#### Changing a RabbitMq user password

You can change the `libretime` RabbitMQ user password using:

```bash
sudo rabbitmqctl change_password "libretime" "new-password"
```

:::

```yml
rabbitmq:
  # The hostname of the RabbitMQ server.
  # > default is localhost
  host: "localhost"
  # The port of the RabbitMQ server.
  # > default is 5672
  port: 5672
  # The virtual host of RabbitMQ server.
  # > default is /libretime
  vhost: "/libretime"
  # The username of the RabbitMQ user.
  # > default is libretime
  user: "libretime"
  # The password of the RabbitMQ user.
  # > default is libretime
  password: "some_random_generated_secret!"
```

## Playout

The `playout` section configure anything related to the playout service.

:::caution

When changing the `playout.liquidsoap_*` entries, make sure to also configure the `liquidsoap.server_listen_*` entries accordingly.

:::

```yml
playout:
  # Liquidsoap connection host.
  # > default is localhost
  liquidsoap_host: "localhost"
  # Liquidsoap connection port.
  # > default is 1234
  liquidsoap_port: 1234

  # The format for recordings.
  # > must be one of (ogg, mp3)
  # > default is ogg
  record_file_format: ogg
  # The bitrate for recordings.
  # > default is 256
  record_bitrate: 256
  # The samplerate for recordings.
  # > default is 44100
  record_samplerate: 44100
  # The number of channels for recordings.
  # > default is 2
  record_channels: 2
  # The sample size for recordings.
  # > default is 16
  record_sample_size: 16
```

## Liquidsoap

The `liquidsoap` section configure anything related to the liquidsoap service.

:::caution

When changing the `liquidsoap.server_listen_*` entries, make sure to also configure the `playout.liquidsoap_*` entries accordingly.

:::

```yml
liquidsoap:
  # Liquidsoap server listen address.
  # > default is 127.0.0.1
  server_listen_address: "127.0.0.1"
  # Liquidsoap server listen port.
  # > default is 1234
  server_listen_port: 1234

  # Input harbor listen address.
  # > default is ["0.0.0.0"]
  harbor_listen_address: ["0.0.0.0"]
```

## Stream

The `stream` section configures anything related to the input and output streams.

```yml
stream:
  inputs: # See the [stream.inputs] section.
  outputs: # See the [stream.outputs] section.
```

:::info

To help you simplify your stream configuration, you can use yaml anchors to define a common properties and reuse them in your output definitions:

```yml
stream:
  outputs:
    # This can be reused to define multiple outputs without duplicating data
    .default_icecast_output: &default_icecast_output
      source_password: "hackme"
      admin_password: "hackme"
      name: "LibreTime!"
      description: "LibreTime Radio!"
      website: "https://libretime.org"
      genre: "various"

    icecast:
      - <<: *default_icecast_output
        enabled: true
        mount: "main.ogg"
        audio:
          format: "ogg"
          bitrate: 256

      - <<: *default_icecast_output
        enabled: true
        mount: "main.mp3"
        audio:
          format: "mp3"
          bitrate: 256
```

:::

### Inputs

The `stream.inputs` section configures anything related to the input streams.

```yml
stream:
  # Inputs sources.
  inputs:
    # Main harbor input.
    main:
      # Harbor input public url. If not defined, the value will be generated from
      # the [general.public_url] hostname, the input port and mount.
      public_url:
      # Mount point for the main harbor input.
      # > default is main
      mount: "main"
      # Listen port for the main harbor input.
      # > default is 8001
      port: 8001

    # Show harbor input.
    show:
      # Harbor input public url. If not defined, the value will be generated from
      # the [general.public_url] hostname, the input port and mount.
      public_url:
      # Mount point for the show harbor input.
      # > default is show
      mount: "show"
      # Listen port for the show harbor input.
      # > default is 8002
      port: 8002
```

### Outputs

The `stream.outputs` section configures anything related to the output streams.

```yml
stream:
  # Output streams.
  outputs:
    icecast: # See the [stream.outputs.icecast] section.
    shoutcast: # See the [stream.outputs.shoutcast] section.
    system: # See the [stream.outputs.system] section.
```

#### Icecast

The `stream.outputs.icecast` section configures the icecast output streams.

:::warning

If you configure more than 2 icecast stream on a **single icecast server**, make sure to raise the icecast sources limit:

```xml
<icecast>
  <limits>
    <sources>2</sources>
  </limits>
</icecast>
```

:::

```yml
stream:
  outputs:
    # Icecast output streams.
    # > max items is 3
    icecast:
      - # Whether the output is enabled.
        # > default is false
        enabled: false
        # Output public url, If not defined, the value will be generated from
        # the [general.public_url] hostname, the output port and mount.
        public_url:
        # Icecast server host.
        # > default is localhost
        host: "localhost"
        # Icecast server port.
        # > default is 8000
        port: 8000
        # Icecast server mount point.
        # > this field is REQUIRED
        mount: "main"
        # Icecast source user.
        # > default is source
        source_user: "source"
        # Icecast source password.
        # > this field is REQUIRED
        source_password: "hackme"
        # Icecast admin user.
        # > default is admin
        admin_user: "admin"
        # Icecast admin password. If not defined, statistics will not be collected.
        admin_password: "hackme"

        # Icecast output audio.
        audio:
          # Icecast output audio format.
          # > must be one of (aac, mp3, ogg, opus)
          # > this field is REQUIRED
          format: "ogg"
          # Icecast output audio bitrate.
          # > must be one of (32, 48, 64, 96, 128, 160, 192, 224, 256, 320)
          # > this field is REQUIRED
          bitrate: 256

          # format=ogg only field: Embed metadata (track title, artist, and show name)
          # in the output stream. Some bugged players will disconnect from the stream
          # after every songs when playing ogg streams that have metadata information
          # enabled.
          # > default is false
          enable_metadata: false

        # Icecast stream name.
        name: "LibreTime!"
        # Icecast stream description.
        description: "LibreTime Radio!"
        # Icecast stream website.
        website: "https://libretime.org"
        # Icecast stream genre.
        genre: "various"
```

#### Shoutcast

The `stream.outputs.shoutcast` section configures the shoutcast output streams.

```yml
stream:
  outputs:
    # Shoutcast output streams.
    # > max items is 1
    shoutcast:
      - # Whether the output is enabled.
        # > default is false
        enabled: false
        # Output public url. If not defined, the value will be generated from
        # the [general.public_url] hostname and the output port.
        public_url:
        # Shoutcast server host.
        # > default is localhost
        host: "localhost"
        # Shoutcast server port.
        # > default is 8000
        port: 8000
        # Shoutcast source user.
        # > default is source
        source_user: "source"
        # Shoutcast source password.
        # > this field is REQUIRED
        source_password: "hackme"
        # Shoutcast admin user.
        # > default is admin
        admin_user: "admin"
        # Shoutcast admin password. If not defined, statistics will not be collected.
        admin_password: "hackme"

        # Shoutcast output audio.
        audio:
          # Shoutcast output audio format.
          # > must be one of (aac, mp3)
          # > this field is REQUIRED
          format: "mp3"
          # Shoutcast output audio bitrate.
          # > must be one of (32, 48, 64, 96, 128, 160, 192, 224, 256, 320)
          # > this field is REQUIRED
          bitrate: 256

        # Shoutcast stream name.
        name: "LibreTime!"
        # Shoutcast stream website.
        website: "https://libretime.org"
        # Shoutcast stream genre.
        genre: "various"
```

#### System

The `stream.outputs.system` section configures the system outputs.

```yml
stream:
  outputs:
    # System outputs.
    # > max items is 1
    system:
      - # Whether the output is enabled.
        # > default is false
        enabled: false
        # System output kind.
        # > must be one of (alsa, ao, oss, portaudio, pulseaudio)
        # > default is alsa
        kind: "alsa"
```

## LDAP

The `ldap` section provide additional configuration for the authentication mechanism defined in `general.auth`, please see the [custom authentication documentation](../custom-authentication.md) for more details.

```yml
ldap:
  # Hostname of LDAP server.
  hostname: "ldap.example.org"
  # Complete DN of user used to bind to LDAP.
  binddn: "uid=libretime,cn=sysaccounts,cn=etc,dc=int,dc=example,dc=org"
  # Password for binddn user.
  password: "hackme"
  # Domain part of username.
  account_domain: "INT.EXAMPLE.ORG"
  # Base search DN.
  basedn: "cn=users,cn=accounts,dc=int,dc=example,dc=org"
  # Name of the uid field for searching. Usually uid, may be cn.
  filter_field: "uid"

  # Map user types to LDAP groups. Assign user types based on the group of a given user
  # Key format is groupmap_*.
  groupmap_superadmin: "cn=superadmin,cn=groups,cn=accounts,dc=int,dc=example,dc=org"
  groupmap_admin: "cn=admin,cn=groups,cn=accounts,dc=int,dc=example,dc=org"
  groupmap_program_manager: "cn=program_manager,cn=groups,cn=accounts,dc=int,dc=example,dc=org"
  groupmap_host: "cn=host,cn=groups,cn=accounts,dc=int,dc=example,dc=org"
  groupmap_guest: "cn=guest,cn=groups,cn=accounts,dc=int,dc=example,dc=org"
```
