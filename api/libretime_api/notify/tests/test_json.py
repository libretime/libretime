"""The json_encode compatible encoding of the notification payloads."""

import json

import pytest

from .._json import php_json_dumps
from .conftest import FIXTURES_PATH

# The expected values are the output of json_encode, run against the same
# input, as captured by tools/rabbitmq-equivalence.
CASES = {
    "empty object": ({}, "{}"),
    "string": ("Radio", '"Radio"'),
    "integer": (42, "42"),
    "booleans": ({"on": True, "off": False}, '{"on":true,"off":false}'),
    "float": (1.5, "1.5"),
    "none": (None, "null"),
    "key order is kept": ({"b": 1, "a": 2}, '{"b":1,"a":2}'),
    "no whitespace": ({"a": [1, 2]}, '{"a":[1,2]}'),
    "slashes": ("/tmp/song.mp3", '"\\/tmp\\/song.mp3"'),
    "accents": ("über", '"\\u00fcber"'),
    "astral": ("😀", '"\\ud83d\\ude00"'),
    "quotes and backslash": ('a"b\\c', '"a\\"b\\\\c"'),
    "control character": ("a\nb", '"a\\nb"'),
    "nested": (
        {"options": {"analyze_cue_points": True}},
        '{"options":{"analyze_cue_points":true}}',
    ),
    "empty nested object": ({"options": {}}, '{"options":{}}'),
}


@pytest.mark.parametrize("case", sorted(CASES))
def test_encoding(case):
    value, expected = CASES[case]

    assert php_json_dumps(value) == expected


@pytest.mark.parametrize("name", ["playout", "airtime-uploads"])
def test_captured_bodies_are_reproduced(name):
    """Re-encoding a captured payload gives the captured bytes again.

    The payloads were put on the wire by PHP, so this fails as soon as the
    encoding of one of them drifts away from json_encode.
    """
    with open(FIXTURES_PATH / f"{name}.json", encoding="utf-8") as handle:
        captured = json.load(handle)

    for message in captured["messages"]:
        assert php_json_dumps(json.loads(message["body"])) == message["body"]
