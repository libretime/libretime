"""
Notifications the platform sends to its services over RabbitMQ.

The message bodies, the exchange topology and the message properties are the
ones the legacy service always sent from ``Application_Model_RabbitMq``, so
that a producer can move from one service to the other without the consumers
noticing. See ``tools/rabbitmq-equivalence`` to reproduce the captures this
package is tested against.
"""

from ._amqp import ExchangeSpec, publish
from ._json import php_json_dumps
from .analyzer import send_message_to_analyzer
from .playout import (
    PlayoutEvent,
    disconnect_source,
    reset_liquidsoap_bootstrap,
    send_message_to_pypo,
    switch_source,
    update_message_offline,
    update_schedule,
    update_station_name,
    update_stream_format,
    update_transition_fade,
)

__all__ = [
    "ExchangeSpec",
    "PlayoutEvent",
    "disconnect_source",
    "php_json_dumps",
    "publish",
    "reset_liquidsoap_bootstrap",
    "send_message_to_analyzer",
    "send_message_to_pypo",
    "switch_source",
    "update_message_offline",
    "update_schedule",
    "update_station_name",
    "update_stream_format",
    "update_transition_fade",
]
