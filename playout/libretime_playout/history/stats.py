from dataclasses import dataclass
from datetime import datetime
from threading import Thread
from time import sleep
from typing import Any, Dict, List, Optional, Union

from libretime_api_client.v1 import ApiClient as LegacyClient
from libretime_shared.config import IcecastOutput, ShoutcastOutput
from loguru import logger
from lxml import etree
from requests import Session
from requests.exceptions import (  # pylint: disable=redefined-builtin
    ConnectionError,
    HTTPError,
    Timeout,
)

from ..config import Config

AnyOutput = Union[IcecastOutput, ShoutcastOutput]


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

    def get_output_url(self, output: AnyOutput) -> str:
        if output.kind == "icecast":
            return f"http://{output.host}:{output.port}/admin/stats.xml"
        return f"http://{output.host}:{output.port}/admin.cgi?sid=1&mode=viewxml"

    def collect_output_stats(
        self,
        output: AnyOutput,
    ) -> Dict[str, Stats]:

        response = self._session.get(
            url=self.get_output_url(output),
            auth=(output.admin_user, output.admin_password),
            timeout=self._timeout,
        )
        response.raise_for_status()

        root = etree.fromstring(  # nosec
            response.content,
            parser=etree.XMLParser(resolve_entities=False),
        )

        stats = {}

        # Shoutcast specific parsing
        if output.kind == "shoutcast":
            listeners_el = root.find("CURRENTLISTENERS")
            listeners = 0 if listeners_el is None else int(listeners_el.text)

            stats["shoutcast"] = Stats(
                listeners=listeners,
            )
            return stats

        # Icecast specific parsing
        for source in root.iterchildren("source"):
            mount = source.attrib.get("mount")
            if mount is None:
                continue

            listeners_el = source.find("listeners")
            listeners = 0 if listeners_el is None else int(listeners_el.text)

            mount = mount.lstrip("/")
            stats[mount] = Stats(
                listeners=listeners,
            )

        return stats

    def collect(
        self,
        outputs: List[AnyOutput],
        *,
        _timestamp: Optional[datetime] = None,
    ):
        if _timestamp is None:
            _timestamp = datetime.utcnow()

        stats: List[Dict[str, Any]] = []
        stats_timestamp = _timestamp.strftime("%Y-%m-%d %H:%M:%S")
        cache: Dict[str, Dict[str, Stats]] = {}

        for output_id, output in enumerate(outputs, start=1):
            if (
                output.kind not in ("icecast", "shoutcast")
                or not output.enabled
                or output.admin_password is None
            ):
                continue

            output_url = self.get_output_url(output)
            if output_url not in cache:
                try:
                    cache[output_url] = self.collect_output_stats(output)
                except (
                    etree.XMLSyntaxError,
                    ConnectionError,
                    HTTPError,
                    Timeout,
                ) as exception:
                    logger.exception(exception)
                    self._legacy_client.update_stream_setting_table(
                        {output_id: str(exception)}
                    )
                    continue

            output_stats = cache[output_url]

            mount = "shoutcast" if output.kind == "shoutcast" else output.mount

            if mount in output_stats:
                stats.append(
                    {
                        "timestamp": stats_timestamp,
                        "num_listeners": output_stats[mount].listeners,
                        "mount_name": mount,
                    }
                )

        if stats:
            self._legacy_client.push_stream_stats(stats)


class StatsCollectorThread(Thread):
    name = "stats collector"
    daemon = True

    def __init__(self, config: Config, legacy_client: LegacyClient) -> None:
        super().__init__()
        self._config = config
        self._collector = StatsCollector(legacy_client)

    def run(self):
        logger.info(f"starting {self.name}")
        while True:
            try:
                self._collector.collect(self._config.stream.outputs.merged)
            except Exception as exception:
                logger.exception(exception)
            sleep(120)
