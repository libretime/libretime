# pylint: disable=too-many-public-methods,too-many-arguments


from ..client import AbstractApiClient

API_VERSION = "2.0"


class ApiClient(AbstractApiClient):
    def __init__(self, base_url, api_key):
        super().__init__(base_url=base_url)
        self.session.headers.update({"Api-Key": api_key})

    def get_version(self, **kwargs):
        return self._request("GET", "/api/v2/version", **kwargs)

    def list_schedule(self, **kwargs):
        return self._request("GET", "/api/v2/schedule", **kwargs)

    def get_webstream(self, *, id_, **kwargs):
        return self._request("GET", f"/api/v2/webstreams/{id_}", **kwargs)

    def get_show_instance(self, *, id_, **kwargs):
        return self._request("GET", f"/api/v2/show-instances/{id_}", **kwargs)

    def get_show(self, *, id_, **kwargs):
        return self._request("GET", f"/api/v2/shows/{id_}", **kwargs)

    def get_file(self, *, id_, **kwargs):
        return self._request("GET", f"/api/v2/files/{id_}", **kwargs)

    def update_file(self, *, id_, payload, **kwargs):
        # TODO: Consider using PATCH
        data = self.get_file(id_=id_)
        data.update(payload)
        return self._request("PUT", f"/api/v2/files/{id_}", json=data, **kwargs)

    def download_file(self, *, id_, **kwargs):
        return self._request("GET", f"/api/v2/files/{id_}/download", **kwargs)
