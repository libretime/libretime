#!/usr/bin/env python3
"""
Capture the messages LibreTime publishes to RabbitMQ, as a consumer sees them.

The playout consumer only looks at the decoded JSON body, which makes the payload
alone a weak proof that a new producer is equivalent to an old one. This records
everything a consumer receives:

* the body exactly as it is on the wire, which the 'text/plain' content type
  keeps undecoded, so that escapes and key order stay visible,
* the AMQP message properties,
* the exchange and routing key the message was published with,
* the type and flags of the exchange, read back from the management API, since
  declaring an existing exchange with other flags fails.

Run it next to the services so that the capture sees the same broker:

    python capture.py --output legacy.jsonl --duration 25

The broker is taken from the environment of the stack, in the uppercase spelling
documented for overrides and in the lowercase one docker-compose gives to the
services. Without them the broker of docker/config.yml is assumed:

    LIBRETIME_RABBITMQ_HOST / LIBRETIME_rabbitmq_host
    LIBRETIME_RABBITMQ_PORT / LIBRETIME_rabbitmq_port
    LIBRETIME_RABBITMQ_USER / LIBRETIME_rabbitmq_user
    LIBRETIME_RABBITMQ_PASSWORD / LIBRETIME_rabbitmq_password
    LIBRETIME_RABBITMQ_VHOST / LIBRETIME_rabbitmq_vhost
"""

import argparse
import base64
import json
import os
import signal
import sys
import urllib.request
from contextlib import ExitStack
from typing import Any
from urllib.parse import quote

from kombu import Connection, Consumer, Exchange, Queue

# The exchanges LibreTime publishes notifications to, and the flags their
# producers declare them with.
EXCHANGES = {
    # playout: Application_Model_RabbitMq::$exchangeName
    "playout": ("fanout", "", True),
    # analyzer: Application_Model_RabbitMq::SendMessageToAnalyzer
    "airtime-uploads": ("topic", "", False),
}

MANAGEMENT_PORT = 15672


def option(field: str, default: str) -> str:
    """A rabbitmq option of the stack, in either spelling of its variable."""
    return (
        os.environ.get(f"LIBRETIME_RABBITMQ_{field.upper()}")
        or os.environ.get(f"LIBRETIME_rabbitmq_{field}")
        or default
    )


HOST = option("host", "localhost")
PORT = option("port", "5672")
USER = option("user", "libretime")
PASSWORD = option("password", "libretime")
VHOST = option("vhost", "/libretime")


def exchange_info(name: str) -> dict[str, Any]:
    """Type and flags of an exchange, read back from the broker."""
    url = (
        f"http://{HOST}:{MANAGEMENT_PORT}/api/exchanges/"
        f"{quote(VHOST, safe='')}/{quote(name, safe='')}"
    )
    credentials = base64.b64encode(f"{USER}:{PASSWORD}".encode())
    request = urllib.request.Request(
        url, headers={"Authorization": f"Basic {credentials.decode()}"}
    )

    try:
        with urllib.request.urlopen(request, timeout=5) as response:
            data = json.load(response)
    except Exception as exception:  # pylint: disable=broad-exception-caught
        print(f"could not read the topology of {name}: {exception}", file=sys.stderr)
        return {}

    return {
        "exchange_type": data["type"],
        "exchange_durable": data["durable"],
        "exchange_auto_delete": data["auto_delete"],
    }


def message_record(message: Any) -> dict[str, Any]:
    """Describe a delivered message the way a consumer receives it."""
    body = message.payload

    if not isinstance(body, str):
        raise TypeError(f"expected an undecoded body, got {type(body).__name__}")

    return {
        # The body is what has to stay compatible, so it is kept as it is on
        # the wire and never re-encoded.
        "body": body,
        "properties": {
            key: value
            for key, value in message.properties.items()
            # Delivery information belongs to the delivery, not to the message.
            if key
            not in ("delivery_tag", "delivery_info", "redelivered", "consumer_tag")
        },
        "exchange": message.delivery_info.get("exchange"),
        "routing_key": message.delivery_info.get("routing_key"),
    }


def main() -> None:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--output", required=True, help="file to write the capture to")
    parser.add_argument("--duration", type=float, default=30, help="seconds to capture")
    args = parser.parse_args()

    captured: list[dict[str, Any]] = []

    def stop(*_args: Any) -> None:
        raise SystemExit(0)

    signal.signal(signal.SIGTERM, stop)
    signal.signal(signal.SIGINT, stop)

    # A vhost starting with a slash needs the slash of the uri path too,
    # which is how the platform builds its own url.
    url = f"amqp://{USER}:{PASSWORD}@{HOST}:{PORT}/{VHOST}"

    with Connection(url) as connection:

        def on_message(_body: Any, message: Any) -> None:
            captured.append(message_record(message))
            message.ack()

        # Both consumers are registered for the whole capture, otherwise the
        # messages of the exchange that is not being drained right now would be
        # dropped by the broker.
        with ExitStack() as stack:
            for name, (exchange_type, routing_key, auto_delete) in EXCHANGES.items():
                exchange = Exchange(
                    name, exchange_type, durable=True, auto_delete=auto_delete
                )

                # Expiring and unnamed like the queues of the analyzer and
                # playout consumers, so an interrupted capture leaves nothing
                # behind.
                queue = Queue(
                    "",
                    exchange,
                    routing_key=routing_key,
                    durable=False,
                    exclusive=True,
                    auto_delete=True,
                    expires=60_000,
                )

                stack.enter_context(
                    Consumer(
                        connection, queue, callbacks=[on_message], accept=["text/plain"]
                    ),
                )

            remaining = args.duration

            while remaining > 0:
                try:
                    connection.drain_events(timeout=min(remaining, 0.5))
                except TimeoutError:
                    # Nothing was published during the interval.
                    pass

                remaining -= 0.5

    topology = {name: exchange_info(name) for name in EXCHANGES}

    for record in captured:
        record.update(topology.get(record["exchange"], {}))

    with open(args.output, "w", encoding="utf-8") as handle:
        for record in captured:
            handle.write(json.dumps(record, sort_keys=True) + "\n")

    print(f"captured {len(captured)} message(s) to {args.output}")


if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        sys.exit(0)
