from pathlib import Path
from subprocess import check_call
from unittest import mock

import pytest

from libretime_playout.config import Config
from libretime_playout.liquidsoap.entrypoint import generate_entrypoint
from libretime_playout.liquidsoap.models import Info, StreamPreferences
from libretime_playout.liquidsoap.version import get_liquidsoap_version

from .conftest import LIQ_VERSION
from .fixtures import TEST_STREAM_CONFIGS


@pytest.mark.parametrize(
    "version",
    [pytest.param((1, 4, 4), id="1.4")],
)
@pytest.mark.parametrize(
    "stream_config",
    TEST_STREAM_CONFIGS,
)
def test_generate_entrypoint(tmp_path: Path, stream_config: Config, version, snapshot):
    entrypoint_filepath = tmp_path / "radio.liq"

    with mock.patch(
        "libretime_playout.liquidsoap.entrypoint.here",
        Path("/fake"),
    ):
        generate_entrypoint(
            entrypoint_filepath,
            log_filepath=Path("/var/log/radio.log"),
            config=stream_config,
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


@pytest.mark.skipif(
    LIQ_VERSION >= (2, 0, 0),
    reason="unsupported liquidsoap >= 2.0.0",
)
@pytest.mark.parametrize(
    "stream_config",
    TEST_STREAM_CONFIGS,
)
def test_liquidsoap_syntax(tmp_path: Path, stream_config):
    entrypoint_filepath = tmp_path / "radio.liq"
    log_filepath = tmp_path / "radio.log"

    generate_entrypoint(
        entrypoint_filepath,
        log_filepath=log_filepath,
        config=stream_config,
        preferences=StreamPreferences(
            input_fade_transition=0.0,
            message_format=0,
            message_offline="LibreTime - offline",
        ),
        info=Info(
            station_name="LibreTime",
        ),
        version=get_liquidsoap_version(),
    )

    check_call(["liquidsoap", "--check", str(entrypoint_filepath)])
