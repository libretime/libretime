import logging

import pytest
from libretime_shared.logging import setup_logger

from libretime_playout.liquidsoap.client import LiquidsoapConnection

logger = logging.getLogger(__name__)

setup_logger("debug")


def test_liq_conn_version(liq_conn: LiquidsoapConnection, liq_version):
    liq_conn.write("version")
    result = liq_conn.read()
    assert result == f"Liquidsoap {'.'.join(map(str, liq_version))}"


def test_liq_conn_allow_reopen(liq_conn: LiquidsoapConnection, liq_version):
    for _ in range(2):
        liq_conn.close()
        liq_conn.connect()

        liq_conn.write("version")
        result = liq_conn.read()
        assert result == f"Liquidsoap {'.'.join(map(str, liq_version))}"


def test_liq_conn_vars(liq_conn: LiquidsoapConnection, snapshot, liq_version_param):
    liq_conn.write("var.get var1")
    result = liq_conn.read()
    assert result == snapshot

    if liq_version_param < (2, 1, 0):
        liq_conn.write('var.set var1 = "changed"')
    else:
        liq_conn.write('var.set var1="changed"')

    result = liq_conn.read()
    assert result == snapshot

    liq_conn.write("var.get var1")
    result = liq_conn.read()
    assert result == snapshot


# pylint: disable=unused-argument
def test_liq_conn_help(liq_conn: LiquidsoapConnection, snapshot, liq_version_param):
    liq_conn.write("help")
    result = liq_conn.read()
    assert result == snapshot


def test_liq_conn_raises():
    liq_conn = LiquidsoapConnection(host="localhost", port=12345)

    with pytest.raises(OSError):
        with liq_conn:
            pass

    liq_conn = LiquidsoapConnection(path="/somewhere/invalid")

    with pytest.raises(OSError):
        with liq_conn:
            pass
