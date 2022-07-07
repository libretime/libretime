###############################################################################
# This file holds the implementations for all the API clients.
#
# If you want to develop a new client, here are some suggestions: Get the fetch
# methods working first, then the push, then the liquidsoap notifier.  You will
# probably want to create a script on your server side to automatically
# schedule a playlist one minute from the current time.
###############################################################################
import logging

from ._config import Config
from .utils import RequestProvider

LIBRETIME_API_VERSION = "2.0"


api_endpoints = {}

api_endpoints["version_url"] = "version/"
api_endpoints["schedule_url"] = "schedule/"
api_endpoints["webstream_url"] = "webstreams/{id}/"
api_endpoints["show_instance_url"] = "show-instances/{id}/"
api_endpoints["show_url"] = "shows/{id}/"
api_endpoints["file_url"] = "files/{id}/"
api_endpoints["file_download_url"] = "files/{id}/download/"


class AirtimeApiClient:
    API_BASE = "/api/v2"

    def __init__(self, logger=None, config_path="/etc/libretime/config.yml"):
        self.logger = logger or logging

        config = Config(filepath=config_path)
        self.base_url = config.general.get_internal_url()
        self.api_key = config.general.api_key

        self.services = RequestProvider(
            base_url=self.base_url + self.API_BASE,
            api_key=self.api_key,
            endpoints=api_endpoints,
        )

    def update_file(self, file_id, payload):
        data = self.services.file_url(id=file_id)
        data.update(payload)
        return self.services.file_url(id=file_id, _put_data=data)
