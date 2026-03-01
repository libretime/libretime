from os import getenv

import distro
import pytest

from libretime_playout.liquidsoap.version import (
    get_liquidsoap_version,
    parse_liquidsoap_version,
)


@pytest.mark.parametrize(
    "version, expected",
    [
        ("invalid data", (0, 0, 0)),
        ("1.1.0", (1, 1, 0)),
        ("1.4.4", (1, 4, 4)),
        ("2.0.0", (2, 0, 0)),
        ("Liquidsoap 1.1.0", (1, 1, 0)),
        ("Liquidsoap 1.4.4", (1, 4, 4)),
        ("Liquidsoap 2.0.0", (2, 0, 0)),
    ],
)
def test_parse_liquidsoap_version(version, expected):
    assert parse_liquidsoap_version(version) == expected


@pytest.mark.skipif(getenv("CI") != "true", reason="requires liquidsoap")
def test_get_liquidsoap_version():
    liquidsoap_version_map = {
        "focal": (1, 4, 2),
        "bullseye": (1, 4, 3),
        "jammy": (2, 0, 2),
    }
    assert get_liquidsoap_version() == liquidsoap_version_map[distro.codename()]
