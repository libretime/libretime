"""
Writes to cc_pref through the api have to notify playout, like the legacy
service does when the same preference is written from its forms.
"""

import pytest

from libretime_api.core.models import Preference
from libretime_api.core.views.preference import PLAYOUT_NOTIFICATIONS

# The preferences playout caches, the event it expects and the field of the
# payload it reads, as in Application_Model_Preference.
NOTIFIED = [
    ("station_name", "update_station_name", "station_name"),
    ("default_transition_fade", "update_transition_fade", "transition_fade"),
    ("stream_label_format", "update_stream_format", "stream_format"),
    ("off_air_meta", "update_message_offline", "message_offline"),
]


@pytest.fixture()
def notifications(monkeypatch) -> list:
    """The notifications the writes published, as the services would get them."""
    published: list = []

    def publish(exchange, payload, routing_key=""):
        published.append((exchange.name, payload))

    monkeypatch.setattr("libretime_api.notify.playout.publish", publish)

    return published


def given_preference(key: str, value: str) -> Preference:
    """A preference row, whatever the migrations seeded for that key."""
    preference, _ = Preference.objects.update_or_create(
        key=key, defaults={"value": value}
    )

    return preference


# pylint: disable=invalid-name,too-many-arguments,unused-argument
@pytest.mark.parametrize(("key", "event", "field"), NOTIFIED)
def test_write_notifies_playout(db, api_client, notifications, key, event, field):
    preference = given_preference(key, "offline")

    response = api_client.patch(
        f"/api/v2/preferences/{preference.id}",
        {"value": "on air"},
        format="json",
    )

    assert response.status_code == 200
    assert notifications == [("playout", {field: "on air", "event_type": event})]

    # The legacy producer appends the event to the payload it is given.
    assert list(notifications[0][1]) == [field, "event_type"]


# pylint: disable=invalid-name,unused-argument
def test_create_notifies_playout(db, api_client, notifications):
    response = api_client.post(
        "/api/v2/preferences",
        {"key": "station_name", "value": "New station"},
        format="json",
    )

    assert response.status_code == 201
    assert notifications == [
        (
            "playout",
            {"station_name": "New station", "event_type": "update_station_name"},
        )
    ]


# pylint: disable=invalid-name,unused-argument
def test_other_preference_does_not_notify(db, api_client, notifications):
    preference = given_preference("email", "old@example.com")

    response = api_client.patch(
        f"/api/v2/preferences/{preference.id}",
        {"value": "new@example.com"},
        format="json",
    )

    assert response.status_code == 200
    assert notifications == []


# pylint: disable=invalid-name,unused-argument
def test_patch_of_another_field_does_not_notify(db, api_client, notifications):
    preference = given_preference("station_name", "on air")

    response = api_client.patch(
        f"/api/v2/preferences/{preference.id}", {}, format="json"
    )

    assert response.status_code == 200
    assert notifications == []


# pylint: disable=invalid-name,unused-argument
def test_destroy_does_not_notify(db, api_client, notifications):
    """Playout has no notification for a preference that is gone."""
    preference = given_preference("station_name", "on air")

    response = api_client.delete(f"/api/v2/preferences/{preference.id}")

    assert response.status_code == 204
    assert notifications == []


def test_every_notified_preference_is_documented():
    assert {key for key, _, _ in NOTIFIED} == set(PLAYOUT_NOTIFICATIONS)
