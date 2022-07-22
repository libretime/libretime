from pathlib import Path

import pytest

from libretime_api_client.v2 import ApiClient


@pytest.fixture()
def config_filepath(tmp_path: Path):
    filepath = tmp_path / "config.yml"
    filepath.write_text(
        """
general:
  public_url: http://localhost/test
  api_key: TEST_KEY
"""
    )
    return filepath


def test_api_client(config_filepath):
    client = ApiClient(config_path=config_filepath)
    assert callable(client.services.version_url)
    assert callable(client.services.schedule_url)
    assert callable(client.services.webstream_url)
    assert callable(client.services.show_instance_url)
    assert callable(client.services.show_url)
    assert callable(client.services.file_url)
    assert callable(client.services.file_download_url)
