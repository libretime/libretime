import pytest
from pydantic import TypeAdapter

from libretime_shared.config._fields import (
    AnyHttpUrlStr,
    StrNoLeadingSlash,
    StrNoTrailingSlash,
)


@pytest.mark.parametrize(
    "data, expected",
    [
        ("something/", "something"),
        ("something//", "something"),
        ("something/keep", "something/keep"),
        ("/something/", "/something"),
    ],
)
def test_str_no_trailing_slash(data, expected):
    found = TypeAdapter(StrNoTrailingSlash).validate_python(data)
    assert found == expected


@pytest.mark.parametrize(
    "data, expected",
    [
        ("/something", "something"),
        ("//something", "something"),
        ("keep/something", "keep/something"),
        ("/something/", "something/"),
    ],
)
def test_str_no_leading_slash(data, expected):
    found = TypeAdapter(StrNoLeadingSlash).validate_python(data)
    assert found == expected


@pytest.mark.parametrize(
    "data, expected",
    [
        ("http://localhost:8080", "http://localhost:8080"),
        ("http://localhost:8080/path/", "http://localhost:8080/path"),
        ("https://example.com/", "https://example.com"),
        ("https://example.com/keep", "https://example.com/keep"),
        ("https://example.com/keep/", "https://example.com/keep"),
    ],
)
def test_any_http_url_str(data, expected):
    found = TypeAdapter(AnyHttpUrlStr).validate_python(data)
    assert found == expected
