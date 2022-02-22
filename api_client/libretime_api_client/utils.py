import datetime
import logging
from time import sleep

import requests
from requests.auth import AuthBase


class UrlParamDict(dict):
    def __missing__(self, key):
        return "{" + key + "}"


class UrlException(Exception):
    pass


class IncompleteUrl(UrlException):
    def __init__(self, url):
        self.url = url

    def __str__(self):
        return f"Incomplete url: '{self.url}'"


class UrlBadParam(UrlException):
    def __init__(self, url, param):
        self.url = url
        self.param = param

    def __str__(self):
        return f"Bad param '{self.param}' passed into url: '{self.url}'"


class KeyAuth(AuthBase):
    def __init__(self, key):
        self.key = key

    def __call__(self, r):
        r.headers["Authorization"] = f"Api-Key {self.key}"
        return r


class ApcUrl:
    """A safe abstraction and testable for filling in parameters in
    api_client.cfg"""

    def __init__(self, base_url):
        self.base_url = base_url

    def params(self, **params):
        temp_url = self.base_url
        for k, v in params.items():
            wrapped_param = "{" + k + "}"
            if not wrapped_param in temp_url:
                raise UrlBadParam(self.base_url, k)
        temp_url = temp_url.format_map(UrlParamDict(**params))
        return ApcUrl(temp_url)

    def url(self):
        if "{" in self.base_url:
            raise IncompleteUrl(self.base_url)
        else:
            return self.base_url


class ApiRequest:
    API_HTTP_REQUEST_TIMEOUT = 30  # 30 second HTTP request timeout

    def __init__(self, name, url, logger=None, api_key=None):
        self.name = name
        self.url = url
        self.__req = None
        if logger is None:
            self.logger = logging
        else:
            self.logger = logger
        self.auth = KeyAuth(api_key)

    def __call__(self, *, _post_data=None, _put_data=None, params=None, **kwargs):
        final_url = self.url.params(**kwargs).url()
        self.logger.debug(final_url)
        try:
            if _post_data is not None:
                res = requests.post(
                    final_url,
                    data=_post_data,
                    auth=self.auth,
                    timeout=ApiRequest.API_HTTP_REQUEST_TIMEOUT,
                )
            elif _put_data is not None:
                res = requests.put(
                    final_url,
                    data=_put_data,
                    auth=self.auth,
                    timeout=ApiRequest.API_HTTP_REQUEST_TIMEOUT,
                )
            else:
                res = requests.get(
                    final_url,
                    params=params,
                    auth=self.auth,
                    timeout=ApiRequest.API_HTTP_REQUEST_TIMEOUT,
                )

            # Check for bad HTTP status code
            res.raise_for_status()

            if "application/json" in res.headers["content-type"]:
                return res.json()
            return res
        except requests.exceptions.Timeout:
            self.logger.error("HTTP request to %s timed out", final_url)
            raise
        except requests.exceptions.HTTPError:
            self.logger.error(
                f"{res.request.method} {res.request.url} request failed '{res.status_code}':"
                f"\nPayload: {res.request.body}"
                f"\nResponse: {res.text}"
            )
            raise

    def req(self, *args, **kwargs):
        self.__req = lambda: self(*args, **kwargs)
        return self

    def retry(self, n, delay=5):
        """Try to send request n times. If after n times it fails then
        we finally raise exception"""
        for i in range(0, n - 1):
            try:
                return self.__req()
            except Exception:
                sleep(delay)
        return self.__req()


class RequestProvider:
    """
    Creates the available ApiRequest instance
    """

    def __init__(self, base_url: str, api_key: str, endpoints: dict):
        self.requests = {}
        self.url = ApcUrl(base_url + "/{action}")

        # Now we must discover the possible actions
        for action_name, action_value in endpoints.items():
            new_url = self.url.params(action=action_value)
            if "{api_key}" in action_value:
                new_url = new_url.params(api_key=api_key)
            self.requests[action_name] = ApiRequest(
                action_name, new_url, api_key=api_key
            )

    def available_requests(self):
        return list(self.requests.keys())

    def __contains__(self, request):
        return request in self.requests

    def __getattr__(self, attr):
        if attr in self:
            return self.requests[attr]
        else:
            return super().__getattribute__(attr)


def time_in_seconds(value):
    return (
        value.hour * 60 * 60
        + value.minute * 60
        + value.second
        + value.microsecond / 1000000.0
    )


def time_in_milliseconds(value):
    return time_in_seconds(value) * 1000


def fromisoformat(time_string):
    """
    This is required for Python 3.6 support. datetime.time.fromisoformat was
    only added in Python 3.7. Until LibreTime drops Python 3.6 support, this
    wrapper uses the old way of doing it.
    """
    try:
        datetime_obj = datetime.datetime.strptime(time_string, "%H:%M:%S.%f")
    except ValueError:
        datetime_obj = datetime.datetime.strptime(time_string, "%H:%M:%S")
    return datetime_obj.time()
