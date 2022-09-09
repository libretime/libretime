from pathlib import Path
from unittest import mock

import pytest

from libretime_playout.config import Config
from libretime_playout.liquidsoap.entrypoint import generate_entrypoint
from libretime_playout.liquidsoap.models import Info, StreamPreferences


@pytest.mark.parametrize(
    "version",
    [pytest.param((1, 4, 4), id="1.4")],
)
def test_generate_entrypoint(tmp_path: Path, config: Config, version, snapshot):
    entrypoint_filepath = tmp_path / "radio.liq"

    with mock.patch(
        "libretime_playout.liquidsoap.entrypoint.here",
        Path("/fake"),
    ):
        generate_entrypoint(
            entrypoint_filepath,
            log_filepath=Path("/var/log/radio.log"),
            config=config,
            preferences=StreamPreferences(
                input_fade_transition=0.0,
                message_format=0,
                message_offline="LibreTime - offline",
            ),
            info=Info(
                station_name="LibreTime",
            ),
            version=version,
        )

    found = entrypoint_filepath.read_text(encoding="utf-8")
    assert found == snapshot
