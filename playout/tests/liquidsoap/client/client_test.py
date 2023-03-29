import pytest

from libretime_playout.liquidsoap.client import LiquidsoapClient, LiquidsoapClientError


def test_liq_client():
    assert LiquidsoapClient(
        host="localhost",
        port=1234,
        timeout=15,
    )


def test_liq_client_wait_for_version(liq_client: LiquidsoapClient):
    assert liq_client.wait_for_version()


def test_liq_client_wait_for_version_invalid_host():
    liq_client = LiquidsoapClient(
        host="invalid",
        port=1234,
    )
    with pytest.raises(LiquidsoapClientError):
        liq_client.wait_for_version(timeout=1)
