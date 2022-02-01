from libretime_api_client.v1.client import ApiClient
from libretime_api_client.v1.compat import ApiClientCompat
from pytest import fixture
from requests_mock import Mocker as RequestsMocker

BASE_URL = "https://localhost/api"
API_KEY = "api_key"


@fixture
def api_client():
    return ApiClient(base_url=BASE_URL, api_key=API_KEY)


@fixture
def api_client_compat():
    return ApiClientCompat(base_url=BASE_URL, api_key=API_KEY)


@fixture
def m():
    with RequestsMocker() as mocker:
        yield mocker
