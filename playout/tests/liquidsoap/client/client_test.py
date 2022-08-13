from libretime_playout.liquidsoap.client import LiquidsoapClient


def test_liq_client():
    assert LiquidsoapClient(
        host="localhost",
        port=1234,
        timeout=15,
    )
