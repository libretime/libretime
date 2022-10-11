import pytest

from ...migrations._version import parse_version


@pytest.mark.parametrize(
    "version,expected",
    [
        # fmt: off
        ("3.0.0-alpha",     (3, 0, 0, -2, 0, 0)),
        ("3.1.0-alpha.1",   (3, 1, 0, -2, 1, 0)),
        ("3.0.1-alpha.1.2", (3, 0, 1, -2, 1, 2)),
        ("3.0.0-beta",      (3, 0, 0, -1, 0, 0)),
        ("3.0.0-beta.10",   (3, 0, 0, -1, 10, 0)),
        ("2.5.2",           (2, 5, 2, 0, 0, 0)),
        ("3.0.0",           (3, 0, 0, 0, 0, 0)),
        ("3.0",             (3, 0, 0, 0, 0, 0)),
        ("3",               (3, 0, 0, 0, 0, 0)),
        # fmt: on
    ],
)
def test_parse_version(version: str, expected):
    assert parse_version(version) == expected


@pytest.mark.parametrize(
    "before,after",
    [
        # fmt: off
        ("3.0.0-alpha",     "3.0.0-alpha.1"),
        ("3.0.0-alpha.1",   "3.0.0-alpha.2"),
        ("3.0.0-alpha.1",   "3.0.0-alpha.1.1"),
        ("3.0.0-alpha",     "3.0.0-beta"),
        ("3.0.0-beta",      "3.0.0"),
        ("3.0.0",           "3.0.1"),
        ("3.0.0",           "3.1.0"),
        ("3.0.0",           "4.0.0"),
        ("2.5.3",           "3.0.0"),
        ("3.0.0",           "3.0.0"),
        ("3.0",             "3.1"),
        ("3",               "4"),
        # fmt: on
    ],
)
def test_version_compare(before: str, after: str):
    version_before = parse_version(before)
    version_after = parse_version(after)
    assert version_before <= version_after
