import pytest

from libretime_api_client.utils import ApcUrl, IncompleteUrl, UrlBadParam


@pytest.mark.parametrize(
    "url, params, expected",
    [
        ("one/two/three", {}, "one/two/three"),
        ("/testing/{key}", {"key": "aaa"}, "/testing/aaa"),
        (
            "/more/{key_a}/{key_b}/testing",
            {"key_a": "aaa", "key_b": "bbb"},
            "/more/aaa/bbb/testing",
        ),
    ],
)
def test_apc_url(url: str, params: dict, expected: str):
    found = ApcUrl(url)
    assert found.base_url == url
    assert found.params(**params).url() == expected


def test_apc_url_bad_param():
    url = ApcUrl("/testing/{key}")
    with pytest.raises(UrlBadParam):
        url.params(bad_key="testing")


def test_apc_url_incomplete():
    url = ApcUrl("/{one}/{two}/three").params(two="testing")
    with pytest.raises(IncompleteUrl):
        url.url()
