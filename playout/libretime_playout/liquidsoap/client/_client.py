from pathlib import Path
from subprocess import CalledProcessError, check_output, run
from time import sleep
from typing import Any, Literal, Optional, Tuple

from loguru import logger

from ..models import MessageFormatKind
from ..utils import quote
from ..version import parse_liquidsoap_version
from ._connection import LiquidsoapConnection


class LiquidsoapClientError(Exception):
    """
    A Liquidsoap client error
    """


class LiquidsoapClient:
    """
    A client to communicate with a running Liquidsoap server.
    """

    conn: LiquidsoapConnection

    def __init__(
        self,
        host: str = "localhost",
        port: int = 0,
        path: Optional[Path] = None,
        timeout: int = 15,
    ):
        self.conn = LiquidsoapConnection(
            host=host,
            port=port,
            path=path,
            timeout=timeout,
        )

    def _quote(self, value: Any):
        return quote(value, double=True)

    def _set_var(self, name: str, value: Any):
        self.conn.write(f"var.set {name} = {value}")
        result = self.conn.read()
        if f"Variable {name} set" not in result:
            logger.error(result)

    def version(self) -> Tuple[int, int, int]:
        with self.conn:
            self.conn.write("version")
            return parse_liquidsoap_version(self.conn.read())

    def wait_for_version(self, timeout: int = 30) -> Tuple[int, int, int]:
        while timeout > 0:
            try:
                version = self.version()
                logger.info("found version %s", version)
                return version
            except (ConnectionError, TimeoutError) as exception:
                logger.warning("could not get version: %s", exception)
                timeout -= 1
                sleep(1)

        raise LiquidsoapClientError("could not get liquidsoap version")

    def queues_remove(self, *queues: int) -> None:
        with self.conn:
            for queue_id in queues:
                self.conn.write(f"queues.{queue_id}_skip")

    def queue_push(self, queue_id: int, entry: str, show_name: str) -> None:
        with self.conn:
            self.conn.write(f"{queue_id}.push {entry}")
            self._set_var("show_name", self._quote(show_name))

    def web_stream_get_id(self) -> str:
        with self.conn:
            self.conn.write("web_stream.get_id")
            return self.conn.read().splitlines()[0]

    def web_stream_start(self) -> None:
        with self.conn:
            self.conn.write("sources.start_schedule")
            self.conn.write("sources.start_web_stream")

    def web_stream_start_buffer(self, schedule_id: int, uri: str) -> None:
        with self.conn:
            self.conn.write(f"web_stream.set_id {schedule_id}")
            self.conn.write(f"http.restart {uri}")

    def web_stream_stop(self) -> None:
        with self.conn:
            self.conn.write("sources.stop_web_stream")

    def web_stream_stop_buffer(self) -> None:
        with self.conn:
            self.conn.write("http.stop")
            self.conn.write("web_stream.set_id -1")

    def source_disconnect(self, name: Literal["master_dj", "live_dj"]) -> None:
        command_map = {
            "master_dj": "master_harbor.stop",
            "live_dj": "live_dj_harbor.stop",
        }
        with self.conn:
            self.conn.write(command_map[name])

    def source_switch_status(
        self,
        name: Literal["master_dj", "live_dj", "scheduled_play"],
        streaming: bool,
    ) -> None:
        name_map = {
            "master_dj": "input_main",
            "live_dj": "input_show",
            "scheduled_play": "schedule",
        }
        action = "start" if streaming else "stop"
        with self.conn:
            self.conn.write(f"sources.{action}_{name_map[name]}")

    def settings_update(
        self,
        *,
        station_name: Optional[str] = None,
        message_format: Optional[MessageFormatKind] = None,
        message_offline: Optional[str] = None,
        input_fade_transition: Optional[float] = None,
    ):
        with self.conn:
            if station_name is not None:
                self._set_var("station_name", self._quote(station_name))
            if message_format is not None:
                self._set_var("message_format", self._quote(message_format.value))
            if message_offline is not None:
                self._set_var("message_offline", self._quote(message_offline))
            if input_fade_transition is not None:
                self._set_var("input_fade_transition", input_fade_transition)
