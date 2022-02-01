# pylint: disable=too-many-public-methods,too-many-arguments

import json
from base64 import b64encode

from ..client import AbstractApiClient

API_VERSION = "1.1"


class ApiClient(AbstractApiClient):
    def __init__(self, base_url, api_key):
        super().__init__(base_url=base_url)

        self.session.headers.update({"Api-Key": api_key})
        self.session.params.update({"format": "json"})

    def version(self, **kwargs):
        return self._request(
            "GET",
            "/api/version",
            **kwargs,
        )

    def register_component(self, *, component, **kwargs):
        return self._request(
            "GET",
            "/api/register-component",
            params={
                "component": component,
            },
            **kwargs,
        )

    # Renamed from services.media_setup_url()
    def setup_media_monitor(self, **kwargs):
        return self._request(
            "GET",
            "/api/media-monitor-setup",
            **kwargs,
        )

    def upload_recorded(self, *, fileid, showinstanceid, **kwargs):
        return self._request(
            "GET",
            "/api/upload-recorded",
            params={
                "fileid": fileid,
                "showinstanceid": showinstanceid,
            },
            **kwargs,
        )

    def update_media(self, *, mode, **kwargs):
        return self._request(
            "GET",
            "/api/reload-metadata",
            params={
                "mode": mode,
            },
            **kwargs,
        )

    def list_all_db_files(self, *, dir_id, all_, **kwargs):
        return self._request(
            "GET",
            "/api/list-all-files",
            params={
                "dir_id": dir_id,
                "all": all_,
            },
            **kwargs,
        )

    def list_all_watched_dirs(self, **kwargs):
        return self._request(
            "GET",
            "/api/list-all-watched-dirs",
            **kwargs,
        )

    def add_watched_dir(self, path, **kwargs):
        return self._request(
            "GET",
            "/api/add-watched-dir",
            params={
                "path": b64encode(path.encode("utf-8")),
            },
            **kwargs,
        )

    def remove_watched_dir(self, *, path, **kwargs):
        return self._request(
            "GET",
            "/api/remove-watched-dir",
            params={
                "path": b64encode(path.encode("utf-8")),
            },
            **kwargs,
        )

    def set_storage_dir(self, *, path, **kwargs):
        return self._request(
            "GET",
            "/api/set-storage-dir",
            params={
                "path": b64encode(path.encode("utf-8")),
            },
            **kwargs,
        )

    def update_file_system_mount(self, **kwargs):
        return self._request(
            "GET",
            "/api/update-file-system-mount",
            **kwargs,
        )

    def reload_metadata_group(self, *, data, **kwargs):
        return self._request(
            "POST",
            "/api/reload-metadata-group",
            data=data,
            **kwargs,
        )

    def handle_watched_dir_missing(self, *, dir_, **kwargs):
        return self._request(
            "GET",
            "/api/handle-watched-dir-missing",
            params={
                "dir": dir_,
            },
            **kwargs,
        )

    def show_schedule(self, **kwargs):
        return self._request(
            "GET",
            "/api/recorded-shows",
            **kwargs,
        )

    def upload_files(self, *, files, **kwargs):
        return self._request(
            "POST",
            "/api/rest/media",
            files=files,
            timeout=30,
            **kwargs,
        )

    def export(self, **kwargs):
        return self._request(
            "GET",
            "/api/schedule",
            **kwargs,
        )

    def get_media(self, *, file, **kwargs):
        return self._request(
            "GET",
            "/api/get-media",
            params={
                "file": file,
            },
            **kwargs,
        )

    def update_item(self, *, schedule_id, **kwargs):
        return self._request(
            "GET",
            "/api/notify-schedule-group-play",
            params={
                "schedule_id": schedule_id,
            },
            **kwargs,
        )

    def update_start_playing(self, *, media_id, **kwargs):
        return self._request(
            "GET",
            "/api/notify-media-item-start-play",
            params={
                "media_id": media_id,
            },
            **kwargs,
        )

    def get_stream_setting(self, **kwargs):
        return self._request(
            "GET",
            "/api/get-stream-setting",
            **kwargs,
        )

    def update_liquidsoap_status(self, *, data, msg, stream_id, boot_time, **kwargs):
        return self._request(
            "POST",
            "/api/update-liquidsoap-status",
            data=data,
            params={
                "msg": msg,
                "stream_id": stream_id,
                "boot_time": boot_time,
            },
            **kwargs,
        )

    def update_source_status(self, *, sourcename, status, **kwargs):
        return self._request(
            "GET",
            "/api/update-source-status",
            params={
                "sourcename": sourcename,
                "status": status,
            },
            **kwargs,
        )

    def check_live_stream_auth(self, *, username, password, djtype, **kwargs):
        return self._request(
            "GET",
            "/api/check-live-stream-auth",
            params={
                "username": username,
                "password": password,
                "djtype": djtype,
            },
            **kwargs,
        )

    def get_bootstrap_info(self, **kwargs):
        return self._request(
            "GET",
            "/api/get-bootstrap-info",
            **kwargs,
        )

    def get_files_without_replay_gain(self, *, dir_id, **kwargs):
        return self._request(
            "GET",
            "/api/get-files-without-replay-gain",
            params={
                "dir_id": dir_id,
            },
            **kwargs,
        )

    def update_replay_gain_value(self, *, data, **kwargs):
        return self._request(
            "POST",
            "/api/update-replay-gain-value",
            data={
                "data": json.dumps(data),
            },
            **kwargs,
        )

    def notify_webstream_data(self, *, media_id, data, **kwargs):
        return self._request(
            "POST",
            "/api/notify-webstream-data",
            params={
                "media_id": media_id,
            },
            data={"data": data},
            **kwargs,
        )

    def notify_liquidsoap_started(self, **kwargs):
        return self._request(
            "GET",
            "/api/rabbitmq-do-push",
            **kwargs,
        )

    def get_stream_parameters(self, **kwargs):
        return self._request(
            "GET",
            "/api/get-stream-parameters",
            **kwargs,
        )

    def push_stream_stats(self, *, data, **kwargs):
        return self._request(
            "POST",
            "/api/push-stream-stats",
            data={
                "data": json.dumps(data),
            },
            **kwargs,
        )

    def update_stream_setting_table(self, *, data, **kwargs):
        return self._request(
            "POST",
            "/api/update-stream-setting-table",
            data={
                "data": json.dumps(data),
            },
            **kwargs,
        )

    def get_files_without_silan_value(self, **kwargs):
        return self._request(
            "GET",
            "/api/get-files-without-silan-value",
            **kwargs,
        )

    def update_cue_values_by_silan(self, *, data, **kwargs):
        return self._request(
            "POST",
            "/api/update-cue-values-by-silan",
            data={
                "data": json.dumps(data),
            },
            **kwargs,
        )

    def update_metadata_on_tunein(self, **kwargs):
        return self._request(
            "GET",
            "/api/update-metadata-on-tunein",
            **kwargs,
        )
