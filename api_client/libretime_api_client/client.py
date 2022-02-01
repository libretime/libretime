from typing import Any, Dict, Optional, Union
from urllib.parse import urljoin

from loguru import logger
from requests import Response
from requests import Session as BaseSession
from requests.adapters import HTTPAdapter
from requests.exceptions import RequestException
from urllib3.util import Retry

DEFAULT_TIMEOUT = 5


class TimeoutHTTPAdapter(HTTPAdapter):
    timeout: int = DEFAULT_TIMEOUT

    def __init__(self, *args, **kwargs):
        if "timeout" in kwargs:
            self.timeout = kwargs["timeout"]
            del kwargs["timeout"]
        super().__init__(*args, **kwargs)

    def send(self, request, *args, **kwargs):
        if "timeout" not in kwargs:
            kwargs["timeout"] = self.timeout
        return super().send(request, *args, **kwargs)


class Session(BaseSession):
    base_url: Optional[str]

    def __init__(self, base_url: Optional[str] = None):
        super().__init__()
        self.base_url = base_url

        retry_strategy = Retry(
            total=3,
            status_forcelist=[429, 500, 502, 503, 504],
            method_whitelist=["HEAD", "GET", "OPTIONS"],
        )

        adapter = TimeoutHTTPAdapter(max_retries=retry_strategy)

        self.mount("http://", adapter)
        self.mount("https://", adapter)

    def request(self, method, url, *args, **kwargs):
        """Send the request after generating the complete URL."""
        url = self.create_url(url)
        return super().request(method, url, *args, **kwargs)

    def create_url(self, url):
        """Create the URL based off this partial path."""
        return urljoin(self.base_url, url)


class AbstractApiClient:
    session: Session
    base_url: str

    def __init__(self, base_url: str):
        self.base_url = base_url
        self.session = Session(base_url=base_url)

    def _request(
        self,
        method,
        url,
        *,
        silent: bool = False,
        **kwargs,
    ) -> Union[Optional[Dict[str, Any]], Optional[Response]]:
        try:
            response = self.session.request(method, url, **kwargs)
            response.raise_for_status()

            if "application/json" in response.headers.get("content-type", None):
                return response.json()
            return response

        except RequestException as error:
            logger.error(error)
            if not silent:
                raise error
