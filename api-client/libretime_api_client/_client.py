import logging
from contextlib import contextmanager
from typing import Optional

from requests import PreparedRequest, Response, Session as BaseSession
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


def default_retry(max_retries: int = 5):
    return Retry(
        total=max_retries,
        backoff_factor=2,
        status_forcelist=[413, 429, 500, 502, 503, 504],
    )


class Session(BaseSession):
    base_url: Optional[str]

    def __init__(
        self,
        base_url: Optional[str] = None,
        retry: Optional[Retry] = None,
    ):
        super().__init__()
        self.base_url = base_url

        adapter = TimeoutHTTPAdapter(max_retries=retry)

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


class CachedSession(Session):
    cache: dict[str, Response]

    def __init__(self, *args, **kwargs) -> None:
        super().__init__(*args, **kwargs)
        self.cache = {}

    def send(self, request: PreparedRequest, **kwargs) -> Response:  # type: ignore[no-untyped-def]
        """
        Send a given PreparedRequest.
        """
        if request.method != "GET" or request.url is None:
            return super().send(request, **kwargs)

        if request.url in self.cache:
            return self.cache[request.url]

        response = super().send(request, **kwargs)
        if response.ok:
            self.cache[request.url] = response

        return response


# pylint: disable=too-few-public-methods
class AbstractApiClient:
    session: Session
    base_url: str
    retry: Optional[Retry]

    def __init__(
        self,
        base_url: str,
        retry: Optional[Retry] = None,
    ):
        self.base_url = base_url
        self.retry = retry
        self.session = Session(
            base_url=base_url,
            retry=retry,
        )

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

    @contextmanager
    def cached_session(self):
        """
        Swap the client session during the scope of the context. The session will cache
        all GET requests.

        Cached response will not expire, therefore the cached client must not be used
        for long living scopes.
        """
        original_session = self.session
        self.session = CachedSession(base_url=self.base_url, retry=self.retry)
        try:
            yield
        finally:
            self.session = original_session
