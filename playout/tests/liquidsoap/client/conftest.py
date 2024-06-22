import logging
from contextlib import contextmanager
from pathlib import Path
from random import randint
from subprocess import PIPE, STDOUT, Popen
from time import sleep
from typing import Generator, Protocol

import pytest
from libretime_shared.logging import setup_logger

from libretime_playout.liquidsoap.client import LiquidsoapClient, LiquidsoapConnection

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


class LiquidsoapManager(Protocol):
    def generate_entrypoint(self) -> str:
        pass

    def wait_start(self, process: Popen) -> None:
        pass

    def make_connection(self) -> LiquidsoapConnection:
        pass

    def make_client(self) -> LiquidsoapClient:
        pass


class LiquidsoapManagerTelnet:
    def __init__(self) -> None:
        self.telnet_port = randint(32768, 65535)

    def generate_entrypoint(self) -> str:
        liq_settings = LIQ_TELNET_SETTINGS.format(telnet_port=self.telnet_port)
        liq_script = LIQ_SCRIPT.format(settings=liq_settings.strip())
        return liq_script

    # pylint: disable=unused-argument
    def wait_start(self, process: Popen) -> None:
        sleep(2)

    def make_connection(self) -> LiquidsoapConnection:
        return LiquidsoapConnection(host="localhost", port=self.telnet_port)

    def make_client(self) -> LiquidsoapClient:
        return LiquidsoapClient(host="localhost", port=self.telnet_port)


class LiquidsoapManagerSocket:
    def __init__(self, tmp_path: Path) -> None:
        self.socket_path = tmp_path / "main.sock"

    def generate_entrypoint(self) -> str:
        liq_settings = LIQ_SOCKET_SETTINGS.format(socket_path=self.socket_path)
        liq_script = LIQ_SCRIPT.format(settings=liq_settings.strip())
        return liq_script

    def wait_start(self, process: Popen):
        while process.poll() is None and not self.socket_path.is_socket():
            sleep(0.1)

    def make_connection(self) -> LiquidsoapConnection:
        return LiquidsoapConnection(path=self.socket_path)

    def make_client(self) -> LiquidsoapClient:
        return LiquidsoapClient(path=self.socket_path)


@contextmanager
def run_liq_server(
    kind: str,
    tmp_path: Path,
) -> Generator[LiquidsoapManager, None, None]:
    entrypoint = tmp_path / "main.liq"

    manager: LiquidsoapManager
    if kind == "telnet":
        manager = LiquidsoapManagerTelnet()
    elif kind == "socket":
        manager = LiquidsoapManagerSocket(tmp_path)

    liq_script = manager.generate_entrypoint()
    logger.debug(liq_script)
    entrypoint.write_text(liq_script)

    # The --verbose flag seem to hang when testing in CI
    with Popen(
        ("liquidsoap", "--debug", str(entrypoint)),
        stdout=PIPE,
        stderr=STDOUT,
        text=True,
    ) as process:
        manager.wait_start(process)

        if process.poll() is not None:
            pytest.fail(process.stdout.read())

        try:
            yield manager
        finally:
            process.terminate()


@pytest.fixture(
    name="liq_conn",
    scope="session",
    params=["telnet", "socket"],
)
def liq_conn_fixture(request, tmp_path_factory):
    tmp_path: Path = tmp_path_factory.mktemp(__name__)

    with run_liq_server(request.param, tmp_path) as manager:
        conn = manager.make_connection()
        with conn:
            yield conn


@pytest.fixture(
    name="liq_client",
    scope="session",
    params=["telnet", "socket"],
)
def liq_client_fixture(request, tmp_path_factory):
    tmp_path: Path = tmp_path_factory.mktemp(__name__)

    with run_liq_server(request.param, tmp_path) as manager:
        yield manager.make_client()
