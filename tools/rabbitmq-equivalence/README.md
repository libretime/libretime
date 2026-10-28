# RabbitMQ wire equivalence

`libretime_api.notify` replaces the producers of
`legacy/application/models/RabbitMq.php`, which the services on the other end of
the bus have been reading since they were written. A notification that differs
from theirs is not a lint error, it is a station that stops showing its name, a
fade that is silently dropped, or an analyzer that returns a track without cue
points.

The scripts here capture what both implementations put on the wire of a running
stack and compare them byte by byte. The payloads of the legacy capture are kept
as fixtures in `api/libretime_api/notify/tests/fixtures/`, so that the
equivalence is enforced by `make -C api test` without PHP on the bus.

## What is compared

- the payload byte for byte, including the key order and the `/` escaping that
  `json_encode` produces,
- the exchange each producer declares (name, type, durable, auto delete),
- every message property (content type, priority, delivery mode, headers).

## Setup

Start a development stack, which builds the images of the branch and mounts the
source of `api/` and `legacy/` into their containers:

```console
$ make dev
```

The capture reads the exchange topology from the management api of the broker, so
enable the plugin once:

```console
$ docker compose exec rabbitmq rabbitmq-plugins enable rabbitmq_management
```

Copy the tooling into the containers that produce and record the notifications:

```console
$ docker compose cp tools/rabbitmq-equivalence api:/tmp/eq
$ docker compose cp tools/rabbitmq-equivalence legacy:/tmp/eq
```

## Capture the legacy producer

Start the capture, which binds a queue to each exchange and stops a few seconds
after the last message:

```console
$ docker compose exec api python3 /tmp/eq/capture.py \
    --output /tmp/eq/legacy-capture.jsonl --duration 25
```

While it runs, publish one message per notification from the legacy service, in
another terminal:

```console
$ docker compose exec legacy php /tmp/eq/publish-legacy.php
```

The analyzer cases are analyzed over the `airtime-uploads` exchange, and the
analyzer service moves the file it is given, so the script asks for a copy of a
track in `/tmp` of the legacy container.

When the capture stopped, bring it back:

```console
$ docker compose cp api:/tmp/eq/legacy-capture.jsonl tools/rabbitmq-equivalence/
```

## Capture the api producer

The api container runs the mounted working tree, so nothing has to be rebuilt
between runs:

```console
$ docker compose exec api python3 /tmp/eq/capture.py \
    --output /tmp/eq/api-capture.jsonl --duration 20
```

```console
$ docker compose exec api python3 /tmp/eq/publish-api.py
```

```console
$ docker compose cp api:/tmp/eq/api-capture.jsonl tools/rabbitmq-equivalence/
```

## Compare

```console
$ python3 compare_rabbitmq.py --legacy legacy-capture.jsonl --api api-capture.jsonl
```

The report lists the property differences per message and the payload diff of the
cases that differ. Two messages of the legacy capture have no counterpart on
purpose:

- `cancel_recording`, `update_recorder_schedule`: recording is still driven by
  `legacy/application/models/Scheduler.php`, so its producer stays with it until
  the recorder moves to the api.

## Refresh the fixtures

The fixtures are what pins the payloads in the test suite. They are committed,
because PHP is not available in the test job of the api:

```console
$ python3 to-fixture.py --capture legacy-capture.jsonl \
    --output ../api/libretime_api/notify/tests/fixtures
```

Regenerate them whenever a producer of the legacy service changes, and expect
`make -C api test` to fail until `libretime_api.notify` follows.

## Known differences

The api producer sends three message properties the PHP one does not:
`content_encoding`, `application_headers` and `delivery_mode`. They come from the
AMQP client, and both consumers ignore them: playout reads the payload of a text
message, and `analyzer/src/job.py` only rejects a message whose content type is
not `text/plain`. `delivery_mode` is persistent where PHP left it unset, which
the broker then defaults to transient; nothing is persisted either way, since the
queues of both consumers are non-durable and expire.

## Running outside docker compose

Both scripts read the broker from the environment, in the uppercase spelling
documented for configuration overrides and in the lowercase one docker-compose
gives the services: `LIBRETIME_RABBITMQ_HOST`, `_PORT`, `_USER`, `_PASSWORD`,
`_VHOST`. Without them the broker of `docker/config.yml` is assumed.

`publish-api.py` resolves the broker url through the api settings, like the
producers do, so it needs `LIBRETIME_CONFIG_FILEPATH` when it does not run inside
the api container.
