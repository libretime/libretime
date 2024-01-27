from pathlib import Path
from subprocess import CalledProcessError, check_call, check_output
from unittest import mock

import pytest

from libretime_playout.config import Config
from libretime_playout.liquidsoap.entrypoint import generate_entrypoint
from libretime_playout.liquidsoap.models import Info, StreamPreferences
from libretime_playout.liquidsoap.version import get_liquidsoap_version

from .conftest import LIQ_VERSION
from .fixtures import TEST_STREAM_CONFIGS, make_config_with_stream


@pytest.mark.parametrize(
    "version",
    [pytest.param((1, 4, 4), id="1.4")],
)
@pytest.mark.parametrize(
    "stream_config",
    TEST_STREAM_CONFIGS,
)
def test_generate_entrypoint(stream_config: Config, version, snapshot):
    with mock.patch(
        "libretime_playout.liquidsoap.entrypoint.here",
        Path("/fake"),
    ):
        found = generate_entrypoint(
            log_filepath=Path("/var/log/radio.log"),
            config=stream_config,
            preferences=StreamPreferences(
                input_fade_transition=0.0,
                message_format=0,
                message_offline="LibreTime - offline",
                master_me_lufs=-16,
                master_me_preset=0,
            ),
            info=Info(
                station_name="LibreTime",
            ),
            version=version,
        )

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

    entrypoint_filepath.write_text(
        generate_entrypoint(
            log_filepath=log_filepath,
            config=stream_config,
            preferences=StreamPreferences(
                input_fade_transition=0.0,
                message_format=0,
                message_offline="LibreTime - offline",
                master_me_lufs=-16,
                master_me_preset=0,
            ),
            info=Info(
                station_name="LibreTime",
            ),
            version=get_liquidsoap_version(),
        ),
        encoding="utf-8",
    )

    check_call(["liquidsoap", "--check", str(entrypoint_filepath)])


@pytest.mark.skipif(
    LIQ_VERSION >= (2, 0, 0),
    reason="unsupported liquidsoap >= 2.0.0",
)
def test_liquidsoap_unsupported_output_aac(tmp_path: Path):
    entrypoint_filepath = tmp_path / "radio.liq"
    log_filepath = tmp_path / "radio.log"

    entrypoint_filepath.write_text(
        generate_entrypoint(
            log_filepath=log_filepath,
            config=make_config_with_stream(
                outputs={
                    "icecast": [
                        {
                            "enabled": True,
                            "mount": "main.aac",
                            "source_password": "hackme",
                            "audio": {"format": "aac", "bitrate": 128},
                        }
                    ]
                }
            ),
            preferences=StreamPreferences(
                input_fade_transition=0.0,
                message_format=0,
                message_offline="LibreTime - offline",
                master_me_lufs=-16,
                master_me_preset=0,
            ),
            info=Info(
                station_name="LibreTime",
            ),
            version=get_liquidsoap_version(),
        ),
        encoding="utf-8",
    )

    with pytest.raises(CalledProcessError) as exception:
        check_output(["liquidsoap", "--check", str(entrypoint_filepath)])
    assert b"You must be missing an optional dependency." in exception.value.stdout
