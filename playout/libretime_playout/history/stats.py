from dataclasses import dataclass
from datetime import datetime
from threading import Thread
from time import sleep
from typing import Any, Dict, List, Optional, Tuple

from libretime_api_client.v1 import ApiClient as LegacyClient
from loguru import logger
from lxml import etree
from requests import Session
from requests.exceptions import (  # pylint: disable=redefined-builtin
    ConnectionError,
    HTTPError,
    Timeout,
)


@dataclass
class Source:
    stream_id: str
    mount: str


@dataclass
class Server:
    host: str
    port: int
    auth: Tuple[str, str]
    sources: List[Source]
    is_shoutcast: bool = False


@dataclass
class Stats:
    listeners: int


# pylint: disable=too-few-public-methods
class StatsCollector:
    """
    Collect stats from Icecast and Shoutcast.
    """

    _session: Session

    def __init__(self, legacy_client: LegacyClient):
        self._session = Session()
        self._timeout = 30
        self._legacy_client = legacy_client

    def get_streams_grouped_by_server(self) -> List[Server]:
        """
        Get streams grouped by server to prevent duplicate requests.
        """
        dirty_streams: Dict[str, Dict[str, Any]]
        dirty_streams = self._legacy_client.get_stream_parameters()["stream_params"]

        servers: Dict[str, Server] = {}
        for stream_id, dirty_stream in dirty_streams.items():
            if dirty_stream["enable"].lower() != "true":
                continue

            source = Source(stream_id=stream_id, mount=dirty_stream["mount"])

            server_id = f"{dirty_stream['host']}:{dirty_stream['port']}"
            if server_id not in servers:
                servers[server_id] = Server(
                    host=dirty_stream["host"],
                    port=dirty_stream["port"],
                    auth=(dirty_stream["admin_user"], dirty_stream["admin_pass"]),
                    sources=[source],
                    is_shoutcast=dirty_stream["output"] == "shoutcast",
                )
            else:
                servers[server_id].sources.append(source)

        return list(servers.values())

    def report_server_error(self, server: Server, error: Exception):
        self._legacy_client.update_stream_setting_table(
            {source.stream_id: str(error) for source in server.sources}
        )

    def collect_server_stats(self, server: Server) -> Dict[str, Stats]:
        url = f"http://{server.host}:{server.port}/admin/stats.xml"

        # Shoutcast specific url
        if server.is_shoutcast:
            url = f"http://{server.host}:{server.port}/admin.cgi?sid=1&mode=viewxml"

        try:
            response = self._session.get(url, auth=server.auth, timeout=self._timeout)
            response.raise_for_status()

        except (
            ConnectionError,
            HTTPError,
            Timeout,
        ) as exception:
            logger.exception(exception)
            self.report_server_error(server, exception)
            return {}

        try:
            root = etree.fromstring(  # nosec
                response.content,
                parser=etree.XMLParser(resolve_entities=False),
            )
        except etree.XMLSyntaxError as exception:
            logger.exception(exception)
            self.report_server_error(server, exception)
            return {}

        stats = {}

        # Shoutcast specific parsing
        if server.is_shoutcast:
            listeners_el = root.find("CURRENTLISTENERS")
            listeners = 0 if listeners_el is None else int(listeners_el.text)

            stats["shoutcast"] = Stats(
                listeners=listeners,
            )
            return stats

        mounts = [source.mount for source in server.sources]
        for source in root.iterchildren("source"):
            mount = source.attrib.get("mount")
            if mount is None:
                continue
            mount = mount.lstrip("/")
            if mount not in mounts:
                continue

            listeners_el = source.find("listeners")
            listeners = 0 if listeners_el is None else int(listeners_el.text)

            stats[mount] = Stats(
                listeners=listeners,
            )

        return stats

    def collect(self, *, _timestamp: Optional[datetime] = None):
        if _timestamp is None:
            _timestamp = datetime.utcnow()

        servers = self.get_streams_grouped_by_server()

        stats: List[Dict[str, Any]] = []
        stats_timestamp = _timestamp.strftime("%Y-%m-%d %H:%M:%S")

        for server in servers:
            server_stats = self.collect_server_stats(server)
            if not server_stats:
                continue

            stats.extend(
                {
                    "timestamp": stats_timestamp,
                    "num_listeners": mount_stats.listeners,
                    "mount_name": mount,
                }
                for mount, mount_stats in server_stats.items()
            )

        if stats:
            self._legacy_client.push_stream_stats(stats)


class StatsCollectorThread(Thread):
    name = "stats collector"
    daemon = True

    def __init__(self, legacy_client: LegacyClient) -> None:
        super().__init__()
        self._collector = StatsCollector(legacy_client)

    def run(self):
        logger.info(f"starting {self.name}")
        while True:
            try:
                self._collector.collect()
            except Exception as exception:
                logger.exception(exception)
            sleep(120)
