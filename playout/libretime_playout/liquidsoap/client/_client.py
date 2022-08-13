from pathlib import Path
from subprocess import CalledProcessError, check_output, run
from time import sleep
from typing import Optional, Tuple

from loguru import logger
from typing_extensions import Literal

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

    def version(self) -> Tuple[int, int, int]:
        with self.conn:
            self.conn.write("version")
            return parse_liquidsoap_version(self.conn.read())

    def wait_for_version(self, timeout: int = 30) -> Tuple[int, int, int]:
        while timeout > 0:
            try:
                version = self.version()
                logger.info(f"found version {version}")
                return version
            except (ConnectionError, TimeoutError) as exception:
                logger.warning(f"could not get version: {exception}")
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
            self.conn.write(f"vars.show_name {show_name}")

    def web_stream_get_id(self) -> str:
        with self.conn:
            self.conn.write("dynamic_source.get_id")
            return self.conn.read().splitlines()[0]

    def web_stream_start(self) -> None:
        with self.conn:
            self.conn.write("streams.scheduled_play_start")
            self.conn.write("dynamic_source.output_start")

    def web_stream_start_buffer(self, schedule_id: int, uri: str) -> None:
        with self.conn:
            self.conn.write(f"dynamic_source.id {schedule_id}")
            self.conn.write(f"http.restart {uri}")

    def web_stream_stop(self) -> None:
        with self.conn:
            self.conn.write("dynamic_source.output_stop")

    def web_stream_stop_buffer(self) -> None:
        with self.conn:
            self.conn.write("http.stop")
            self.conn.write("dynamic_source.id -1")

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
        action = "start" if streaming else "stop"
        with self.conn:
            self.conn.write(f"streams.{name}_{action}")

    def settings_update(
        self,
        *,
        station_name: Optional[str] = None,
        message_format: Optional[int] = None,
        input_fade_transition: Optional[float] = None,
    ):
        with self.conn:
            if station_name is not None:
                self.conn.write(f"vars.station_name {station_name}")
                self.conn.read()
            if message_format is not None:
                self.conn.write(f"vars.stream_metadata_type {message_format}")
                self.conn.read()
            if input_fade_transition is not None:
                self.conn.write(f"vars.default_dj_fade {input_fade_transition}")
                self.conn.read()

    def restart(self):
        logger.warning("restarting Liquidsoap")

        try:
            output = check_output(("pidof", "libretime-liquidsoap"))
            liq_pid = output.strip().decode("utf-8")
            logger.debug(f"found liquidsoap pid {liq_pid}")

            run(("kill", "-9", liq_pid), check=True)
        except CalledProcessError as exception:
            raise LiquidsoapClientError("could not restart liquidsoap") from exception

        # Wait for the previous process to shutdown.
        sleep(1)

        self.wait_for_version()
