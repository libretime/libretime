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
