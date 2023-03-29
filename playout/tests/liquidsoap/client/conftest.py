import logging
from pathlib import Path
from random import randint
from subprocess import PIPE, STDOUT, Popen
from time import sleep

import pytest
from libretime_shared.logging import setup_logger

from libretime_playout.liquidsoap.client import LiquidsoapConnection

logger = logging.getLogger(__name__)

setup_logger("debug")


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
