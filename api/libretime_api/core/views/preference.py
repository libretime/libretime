from typing import Callable

from rest_framework import viewsets

from ...notify import playout
from ..models import Preference
from ..serializers import PreferenceSerializer

# Playout keeps a copy of these preferences in liquidsoap, and the legacy
# service notifies it whenever one of them is written. The payload fields of
# the notifications are the ones of Application_Model_Preference, which is
# what playout reads.
PLAYOUT_NOTIFICATIONS: dict[str, Callable[[str], None]] = {
    "station_name": playout.update_station_name,
    "default_transition_fade": playout.update_transition_fade,
    "stream_label_format": playout.update_stream_format,
    "off_air_meta": playout.update_message_offline,
}


def notify_playout(preference: Preference) -> None:
    """Tell playout about the write of a preference it caches."""
    notify = PLAYOUT_NOTIFICATIONS.get(preference.key or "")

    if notify is not None:
        notify(preference.value or "")


class PreferenceViewSet(viewsets.ModelViewSet):
    queryset = Preference.objects.all()
    serializer_class = PreferenceSerializer
    model_permission_name = "preference"

    def perform_create(self, serializer):
        preference = serializer.save()

        notify_playout(preference)

    def perform_update(self, serializer):
        preference = serializer.save()

        # A patch of any other field of the row does not change the
        # preference playout caches.
        if {"key", "value"} & set(serializer.validated_data):
            notify_playout(preference)
