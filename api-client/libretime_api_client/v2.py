from ._client import AbstractApiClient, Response, default_retry


class ApiClient(AbstractApiClient):
    VERSION = "2.0"

    def __init__(self, base_url: str, api_key: str):
        super().__init__(
            base_url=base_url,
            retry=default_retry(),
        )
        self.session.headers.update({"Authorization": f"Api-Key {api_key}"})

    def get_info(self, **kwargs) -> Response:
        return self._request("GET", "/api/v2/info", **kwargs)

    def get_version(self, **kwargs) -> Response:
        return self._request("GET", "/api/v2/version", **kwargs)

    def get_show(self, item_id: int, **kwargs) -> Response:
        return self._request("GET", f"/api/v2/shows/{item_id}", **kwargs)

    def get_show_instance(self, item_id: int, **kwargs) -> Response:
        return self._request("GET", f"/api/v2/show-instances/{item_id}", **kwargs)

    def list_schedule(self, **kwargs) -> Response:
        return self._request("GET", "/api/v2/schedule", **kwargs)

    def get_webstream(self, item_id: int, **kwargs) -> Response:
        return self._request("GET", f"/api/v2/webstreams/{item_id}", **kwargs)

    def get_file(self, item_id: int, **kwargs) -> Response:
        return self._request("GET", f"/api/v2/files/{item_id}", **kwargs)

    def update_file(self, item_id: int, **kwargs) -> Response:
        return self._request("PATCH", f"/api/v2/files/{item_id}", **kwargs)

    def download_file(self, item_id: int, **kwargs) -> Response:
        return self._request("GET", f"/api/v2/files/{item_id}/download", **kwargs)

    def get_stream_preferences(self, **kwargs) -> Response:
        return self._request("GET", "/api/v2/stream/preferences", **kwargs)

    def get_stream_state(self, **kwargs) -> Response:
        return self._request("GET", "/api/v2/stream/state", **kwargs)
