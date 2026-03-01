import pytest

from libretime_playout.liquidsoap.utils import quote


@pytest.mark.parametrize(
    "value, double, expected",
    [
        ("something", False, '"something"'),
        ('something"', False, '"something\\""'),
        ('something"', True, '"something\\\\""'),
    ],
)
def test_quote(value, double, expected):
    assert quote(value, double) == expected
