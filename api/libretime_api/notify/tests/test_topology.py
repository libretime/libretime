"""
The exchanges have to be declared the way the legacy producer declares them.

RabbitMQ closes the channel when an existing exchange is declared with other
flags, so a station that already runs the legacy producer would stop receiving
notifications on the very first message. The flags of the fixture were read
from a broker the legacy producer created, with tools/rabbitmq-equivalence.
"""

import pytest

from ..analyzer import UPLOADS_EXCHANGE
from ..playout import PLAYOUT_EXCHANGE
from .conftest import legacy_capture

EXCHANGES = {
    "playout": PLAYOUT_EXCHANGE,
    "airtime-uploads": UPLOADS_EXCHANGE,
}


@pytest.mark.parametrize("name", sorted(EXCHANGES))
def test_declares_the_exchange_the_legacy_service_declared(name):
    captured = legacy_capture(name)
    exchange = EXCHANGES[name].kombu_exchange

    assert exchange.name == captured["exchange"]
    assert exchange.type == captured["type"]
    assert exchange.durable == captured["durable"]
    assert exchange.auto_delete == captured["auto_delete"]


@pytest.mark.parametrize("name", sorted(EXCHANGES))
def test_publishes_without_routing_key(name):
    """Application_Model_RabbitMq publishes with an empty routing key."""
    assert legacy_capture(name)["routing_key"] == ""
