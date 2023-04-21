---
title: How to setup a PulseAudio output inside containers
---

This tutorials walks you though the steps required to setup a PulseAudio output when running LibreTime inside containers.

:::info

We assume you already [installed LibreTime using docker-compose](../install/README.md#using-docker-compose).

:::

:::note links

- https://github.com/mviereck/x11docker/wiki/Container-sound:-ALSA-or-Pulseaudio#pulseaudio-with-shared-socket

:::

## 1. Create a PulseAudio server socket

First you need to create a PulseAudio connection socket on the host:

```bash
pactl load-module module-native-protocol-unix socket=$(pwd)/pulse.socket
```

To persist the socket after a reboot, you can save the socket configuration to a file:

```bash
mkdir -p ~/.config/pulse
cp /etc/pulse/default.pa ~/.config/pulse/default.pa
echo "load-module module-native-protocol-unix socket=$(pwd)/pulse.socket" | tee -a ~/.config/pulse/default.pa
```

:::info

See `man default.pa` for more details on how to persist a PulseAudio configuration.

:::

:::warning

Make sure that the PulseAudio connection socket is owned by the same user running inside the container. By default the user inside the container will be `1000:1000`, so you should be fine if your host user also has the uid `1000`.

:::

## 2. Configure the PulseAudio client

Next, you need to configure the PulseAudio client inside the `liquidsoap` container. Save the following configuration file to `pulse.client.conf`:

```ini title="pulse.client.conf"
default-server = unix:/tmp/pulse.socket

# Prevent a server running in the container
autospawn = no
daemon-binary = /bin/true

# Prevent the use of shared memory
enable-shm = false
```

Configure the `liquidsoap` service in your docker compose file using the following settings:

```yaml title="docker-compose.yml"
services:
  liquidsoap:
    volumes:
      - ./pulse.socket:/tmp/pulse.socket # Mount the PulseAudio server socket
      - ./pulse.client.conf:/etc/pulse/client.conf # Mount the PulseAudio client configuration
```

## 3. Configure LibreTime with the new PulseAudio output

Finally, you need to configure LibreTime to output to the PulseAudio client, add the following to your configuration file:

```yaml title="config.yml"
stream:
  outputs:
    system:
      - enabled: true
        kind: pulseaudio
```

You can now start/restart LibreTime, and check the logs for any errors.

```bash
docker-compose down
docker-compose up -d

docker-compose logs -f liquidsoap
```
