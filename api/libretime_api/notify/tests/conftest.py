"""Test helpers for the notification producers."""

import json
import uuid
from collections.abc import Generator
from pathlib import Path
from typing import Any

import pytest
from kombu import Connection, Queue
from kombu.messaging import Producer

from .. import _amqp
from .._amqp import ExchangeSpec
from ..analyzer import UPLOADS_EXCHANGE
from ..playout import PLAYOUT_EXCHANGE

FIXTURES_PATH = Path(__file__).parent / "fixtures"


def legacy_capture(exchange: str) -> dict[str, Any]:
    """Read the golden capture of the legacy producer for an exchange.

    The files are written by tools/rabbitmq-equivalence from the messages the
    legacy service published to a real broker.
    """
    path = FIXTURES_PATH / f"{exchange}.json"

    with open(path, encoding="utf-8") as handle:
        captured = json.load(handle)

    return {
        "exchange": captured["exchange"],
        "type": captured["type"],
        "durable": captured["durable"],
        "auto_delete": captured["auto_delete"],
        "routing_key": captured["routing_key"],
        "properties": captured["properties"],
        "messages": {
            message["case"]: message["body"] for message in captured["messages"]
        },
    }


class Capture:
    """Messages a producer published, read back as a consumer reads them."""

    def __init__(self) -> None:
        # kombu keeps the state of its in-memory broker in the process, so a
        # connection per producer, like the producers open one per message.
        self.connection = Connection("memory://")
        self.queues: dict[str, str] = {}

    def bind(self, spec: ExchangeSpec) -> None:
        """Consume from an exchange, like the service consuming it does."""
        queue = Queue(
            f"capture-{spec.name}-{uuid.uuid4().hex[:8]}", exchange=spec.kombu_exchange
        )
        queue(self.connection.default_channel).declare()
        self.queues[spec.name] = queue.name

    def messages(self, exchange: str) -> list[Any]:
        """Every message waiting on the exchange, oldest first."""
        channel = self.connection.default_channel
        messages = []

        while message := channel.basic_get(self.queues[exchange], no_ack=True):
            messages.append(message)

        return messages

    def single(self, exchange: str) -> Any:
        """The one message a producer is expected to have published."""
        messages = list(self.messages(exchange))
        assert len(messages) == 1, f"expected one message on {exchange}, got {messages}"

        return messages[0]


@pytest.fixture()
def capture(monkeypatch) -> Generator[Capture, None, None]:
    """Publish to the in-memory broker of kombu, not to the platform one."""
    instance = Capture()
    instance.bind(PLAYOUT_EXCHANGE)
    instance.bind(UPLOADS_EXCHANGE)

    def memory_connection() -> Connection:
        return Connection("memory://")

    monkeypatch.setattr("libretime_api.notify._amqp.get_connection", memory_connection)

    yield instance
    instance.connection.close()


PublishedMessage = dict[str, Any]


@pytest.fixture()
def published(monkeypatch) -> list[PublishedMessage]:
    """Everything a producer asks the AMQP client to publish."""
    calls: list[PublishedMessage] = []

    def record(
        _producer, body, exchange=None, routing_key="", declare=None, **properties
    ):
        calls.append(
            {
                "body": body,
                "exchange": exchange,
                "routing_key": routing_key,
                "declare": declare,
                **properties,
            },
        )

    def memory_connection() -> Connection:
        return Connection("memory://")

    monkeypatch.setattr(Producer, "publish", record)
    monkeypatch.setattr(_amqp, "get_connection", memory_connection)

    return calls
