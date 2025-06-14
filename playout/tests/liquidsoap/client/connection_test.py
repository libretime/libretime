import logging
from textwrap import dedent

import pytest
from libretime_shared.logging import setup_logger

from libretime_playout.liquidsoap.client import LiquidsoapConnection

from ..conftest import LIQ_VERSION_STR

logger = logging.getLogger(__name__)

setup_logger("debug")


def test_liq_conn_version(liq_conn: LiquidsoapConnection):
    liq_conn.write("version")
    result = liq_conn.read()
    assert result == f"Liquidsoap {LIQ_VERSION_STR}"


def test_liq_conn_allow_reopen(liq_conn: LiquidsoapConnection):
    for _ in range(2):
        liq_conn.close()
        liq_conn.connect()

        liq_conn.write("version")
        result = liq_conn.read()
        assert result == f"Liquidsoap {LIQ_VERSION_STR}"


def test_liq_conn_vars(liq_conn: LiquidsoapConnection):
    liq_conn.write("var.get var1")
    result = liq_conn.read()
    assert result == '"default"'

    liq_conn.write('var.set var1 = "changed"')
    result = liq_conn.read()
    assert result == 'Variable var1 set (was "default").'

    liq_conn.write("var.get var1")
    result = liq_conn.read()
    assert result == '"changed"'


def test_liq_conn_help(liq_conn: LiquidsoapConnection):
    expected = dedent(
        """
        Available commands:
        | dummy.autostart
        | dummy.metadata
        | dummy.remaining
        | dummy.skip
        | dummy.start
        | dummy.status
        | dummy.stop
        | exit
        | help [<command>]
        | list
        | quit
        | request.alive
        | request.all
        | request.metadata <rid>
        | request.on_air
        | request.resolving
        | request.trace <rid>
        | uptime
        | var.get <variable>
        | var.list
        | var.set <variable> = <value>
        | version

        Type "help <command>" for more information.
        """
    ).strip()
    liq_conn.write("help")
    result = liq_conn.read()
    assert result == expected


def test_liq_conn_raises():
    liq_conn = LiquidsoapConnection(host="localhost", port=12345)

    with pytest.raises(OSError):
        with liq_conn:
            pass

    liq_conn = LiquidsoapConnection(path="/somewhere/invalid")

    with pytest.raises(OSError):
        with liq_conn:
            pass
