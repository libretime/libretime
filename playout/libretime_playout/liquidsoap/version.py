import re
from subprocess import run
from typing import Tuple

LIQUIDSOAP_VERSION_RE = re.compile(r"(?:Liquidsoap )?(\d+).(\d+).(\d+)")
LIQUIDSOAP_MIN_VERSION = (1, 4, 0)


def parse_liquidsoap_version(version: str) -> Tuple[int, int, int]:
    match = LIQUIDSOAP_VERSION_RE.search(version)

    if match is None:
        return (0, 0, 0)
    return (int(match.group(1)), int(match.group(2)), int(match.group(3)))


def get_liquidsoap_version() -> Tuple[int, int, int]:
    cmd = run(
        ("liquidsoap", "--check", "print(liquidsoap.version) shutdown()"),
        check=True,
        capture_output=True,
        text=True,
    )

    return parse_liquidsoap_version(cmd.stdout)
