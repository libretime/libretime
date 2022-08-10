from pathlib import Path
from unittest import mock

import pytest

from libretime_playout.config import Config
from libretime_playout.liquidsoap.entrypoint import generate_entrypoint
from libretime_playout.liquidsoap.models import Info, StreamPreferences

from ..fixtures import entrypoint_1_1_snapshot, entrypoint_1_4_snapshot


@pytest.mark.parametrize(
    "version, expected",
    [
        pytest.param((1, 1, 1), entrypoint_1_1_snapshot, id="snapshot_1.1"),
        pytest.param((1, 4, 4), entrypoint_1_4_snapshot, id="snapshot_1.4"),
    ],
)
def test_generate_entrypoint(tmp_path: Path, config: Config, version, expected):
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
    assert found == expected
