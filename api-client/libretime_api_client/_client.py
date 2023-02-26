import logging
from typing import Optional

from requests import Response, Session as BaseSession
from requests.adapters import HTTPAdapter
from requests.exceptions import RequestException
from urllib3.util import Retry

logger = logging.getLogger(__name__)

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
            total=5,
            backoff_factor=2,
            status_forcelist=[413, 429, 500, 502, 503, 504],
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
        if self.base_url is None:
            return url
        return f"{self.base_url.rstrip('/')}/{url.lstrip('/')}"


# pylint: disable=too-few-public-methods
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
        **kwargs,
    ) -> Response:
        try:
            response = self.session.request(method, url, **kwargs)
            response.raise_for_status()
            return response

        except RequestException as exception:
            logger.error(exception)
            raise exception
