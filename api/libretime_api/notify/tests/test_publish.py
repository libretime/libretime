"""
The message properties the producers hand to the AMQP client.

The capture of tools/rabbitmq-equivalence is the reference for the wire format,
this pins the properties the producers are responsible for, without a broker.
"""

from libretime_api.notify import playout


def test_properties_of_a_notification(published):
    playout.update_station_name("Radio")

    assert len(published) == 1
    message = published[0]

    # Playout only accepts text/plain, the serializer has to stay out of it.
    assert message["content_type"] == "text/plain"

    # The legacy producer leaves the priority unset, kombu defaults to zero.
    assert message["priority"] is None

    # The exchange is declared with every publish, like the legacy model that
    # opens a connection per message.
    assert [exchange.name for exchange in message["declare"]] == ["playout"]
    assert message["routing_key"] == ""

    # The producers do not ask for a delivery mode, unlike kombu which sends
    # persistent messages by default. See _amqp.publish for the consequences.
    assert "delivery_mode" not in message
