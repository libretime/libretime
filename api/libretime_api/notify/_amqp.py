"""RabbitMQ primitives shared by the notification producers."""

from dataclasses import dataclass
from typing import Any

from django.conf import settings
from kombu import Connection, Exchange
from kombu.messaging import Producer

from ._json import php_json_dumps

# Name shown for the connection in the RabbitMQ management UI, like the
# other services do (playout, liquidsoap, ...).
CONNECTION_NAME = "api"


@dataclass(frozen=True)
class ExchangeSpec:
    """An exchange LibreTime publishes to, and how it has to be declared."""

    name: str
    type: str
    auto_delete: bool = False

    @property
    def kombu_exchange(self) -> Exchange:
        return Exchange(
            self.name,
            self.type,
            durable=True,
            auto_delete=self.auto_delete,
        )


def get_connection() -> Connection:
    """Open a connection to the RabbitMQ server of the platform configuration."""
    return Connection(
        settings.CONFIG.rabbitmq.url,
        transport_options={"client_properties": {"connection_name": CONNECTION_NAME}},
    )


def publish(exchange: ExchangeSpec, payload: Any, routing_key: str = "") -> None:
    """Publish a JSON payload to an exchange, like the legacy service does.

    The payload is encoded with :func:`libretime_api.notify.php_json_dumps`,
    the exchange is declared with the same type, durability and auto delete
    flags the legacy ``Application_Model_RabbitMq`` model used, and the
    message is sent as ``text/plain``, the only content type playout accepts.

    The AMQP client marks the message persistent, where the legacy producer
    left the delivery mode unset, which the broker defaults to transient.
    Nothing is persisted either way, since both consumers read from queues that
    are not durable and expire, and persistent is the safer choice for a queue
    that is. See ``tools/rabbitmq-equivalence`` for the full comparison.
    """
    body = php_json_dumps(payload)
    kombu_exchange = exchange.kombu_exchange

    with get_connection() as connection:
        with connection.channel() as channel:
            Producer(channel).publish(
                body,
                exchange=kombu_exchange,
                routing_key=routing_key,
                declare=[kombu_exchange],
                content_type="text/plain",
                # Left unset, as in the message properties of the legacy
                # producer, which never passes a priority.
                priority=None,
            )
