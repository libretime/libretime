from pathlib import Path
from random import randint
from subprocess import PIPE, STDOUT, Popen
from textwrap import dedent
from time import sleep

import pytest
from libretime_shared.logging import TRACE, setup_logger
from loguru import logger

from libretime_playout.liquidsoap.client import LiquidsoapConnection
from libretime_playout.liquidsoap.version import get_liquidsoap_version

setup_logger(TRACE)

LIQ_VERSION = get_liquidsoap_version()
LIQ_VERSION_STR = ".".join(map(str, LIQ_VERSION))

pytestmark = pytest.mark.skipif(
    LIQ_VERSION >= (2, 0, 0),
    reason="unsupported liquidsoap >= 2.0.0",
)

LIQ_SCRIPT = """
set("log.file", false)
{settings}

var1 = interactive.string("var1", "default")

output.dummy(blank(id="safe_blank"))
"""

LIQ_TELNET_SETTINGS = """
set("server.telnet", true)
set("server.telnet.port", {telnet_port})
"""

LIQ_SOCKET_SETTINGS = """
set("server.socket", true)
set("server.socket.path", "{socket_path}")
"""


@pytest.fixture(
    name="liq_conn",
    scope="session",
    params=["telnet", "socket"],
)
def liq_conn_fixture(request, tmp_path_factory):
    tmp_path: Path = tmp_path_factory.mktemp(__name__)

    entrypoint = tmp_path / "main.liq"

    if request.param == "telnet":
        telnet_port = randint(32768, 65535)
        liq_settings = LIQ_TELNET_SETTINGS.format(telnet_port=telnet_port)
    elif request.param == "socket":
        socket_path = entrypoint.with_name("main.sock")
        liq_settings = LIQ_SOCKET_SETTINGS.format(socket_path=socket_path)

    liq_script = LIQ_SCRIPT.format(settings=liq_settings.strip())
    logger.debug(liq_script)
    entrypoint.write_text(liq_script)

    # The --verbose flag seem to hang when testing in CI
    with Popen(
        ("liquidsoap", "--debug", str(entrypoint)),
        stdout=PIPE,
        stderr=STDOUT,
        text=True,
    ) as process:
        if request.param == "telnet":
            sleep(2)
        elif request.param == "socket":
            while process.poll() is None and not socket_path.is_socket():
                sleep(0.1)

        if process.poll() is not None:
            pytest.fail(process.stdout.read())

        if request.param == "telnet":
            conn = LiquidsoapConnection(host="localhost", port=telnet_port)
        elif request.param == "socket":
            conn = LiquidsoapConnection(path=socket_path)

        with conn:
            yield conn
        process.terminate()


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
