import re
from subprocess import PIPE, run
from typing import Tuple

LIQUIDSOAP_VERSION_RE = re.compile(r"(?:Liquidsoap )?(\d+).(\d+).(\d+)")
LIQUIDSOAP_MIN_VERSION = (1, 3, 3)


def parse_liquidsoap_version(version: str) -> Tuple[int, int, int]:
    match = LIQUIDSOAP_VERSION_RE.search(version)

    if match is None:
        return (0, 0, 0)
    return (int(match.group(1)), int(match.group(2)), int(match.group(3)))


def get_liquidsoap_version() -> Tuple[int, int, int]:
    cmd = run(
        ("liquidsoap", "--check", "print(liquidsoap.version) shutdown()"),
        check=True,
        stdout=PIPE,
        stderr=PIPE,
        universal_newlines=True,
    )

    return parse_liquidsoap_version(cmd.stdout)
