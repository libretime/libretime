# pylint: disable=protected-access

from pathlib import Path
from shutil import copy
from typing import Tuple
from unittest.mock import MagicMock

import pytest
from model_bakery import baker
from requests_mock import Mocker

from ....._fixtures import AUDIO_FILENAME, fixture_path
from ....management.commands.bulk_import import Importer

FAKE_URL = "https://somehost.com"


@pytest.fixture(name="import_paths")
def _import_paths(tmp_path: Path):
    sub_dir = tmp_path / "dir1/dir2"
    sub_dir.mkdir(parents=True)

    test_file = sub_dir / AUDIO_FILENAME
    copy(fixture_path / AUDIO_FILENAME, test_file)

    return (tmp_path, test_file)


@pytest.fixture(name="library")
def _library():
    return baker.make(
        "storage.Library",
        id=1,
        code="MUS",
        name="Music",
        description="Some music",
    )


class MockImporter(Importer):
    _handle_file: MagicMock
    _upload_file: MagicMock
    _delete_file: MagicMock


@pytest.fixture(name="importer")
def _importer(requests_mock: Mocker):
    requests_mock.post(f"{FAKE_URL}/rest/media", status_code=200)

    obj: MockImporter = Importer(FAKE_URL, "auth")  # type: ignore
    obj._handle_file = MagicMock(wraps=obj._handle_file)
    obj._upload_file = MagicMock(wraps=obj._upload_file)
    obj._delete_file = MagicMock(wraps=obj._delete_file)

    yield obj


@pytest.mark.django_db
def test_importer(
    import_paths: Tuple[Path, Path],
    importer: MockImporter,
    library,
):
    importer.import_dir(import_paths[0], library.code, [".mp3"])

    importer._handle_file.assert_called_with(import_paths[1], library.id)
    importer._upload_file.assert_called_with(import_paths[1], library.id)
    importer._delete_file.assert_not_called()


@pytest.mark.django_db
def test_importer_and_delete(
    import_paths: Tuple[Path, Path],
    importer: MockImporter,
    library,
):
    importer.delete_after_upload = True
    importer.import_dir(import_paths[0], library.code, [".mp3"])

    importer._handle_file.assert_called_with(import_paths[1], library.id)
    importer._upload_file.assert_called_with(import_paths[1], library.id)
    importer._delete_file.assert_called_with(import_paths[1])


@pytest.mark.django_db
def test_importer_existing_file(
    import_paths: Tuple[Path, Path],
    importer: MockImporter,
    library,
):
    baker.make("storage.File", id=1, md5="46305a7cf42ee53976c88d337e47e940")

    importer.import_dir(import_paths[0], library.code, [".mp3"])

    importer._handle_file.assert_called_with(import_paths[1], library.id)
    importer._upload_file.assert_not_called()
    importer._delete_file.assert_not_called()


@pytest.mark.django_db
def test_importer_existing_file_and_delete(
    import_paths: Tuple[Path, Path],
    importer: MockImporter,
    library,
):
    baker.make("storage.File", id=1, md5="46305a7cf42ee53976c88d337e47e940")

    importer.delete_if_exists = True
    importer.import_dir(import_paths[0], library.code, [".mp3"])

    importer._handle_file.assert_called_with(import_paths[1], library.id)
    importer._upload_file.assert_not_called()
    importer._delete_file.assert_called_with(import_paths[1])


@pytest.mark.django_db
def test_importer_missing_library(
    import_paths: Tuple[Path, Path],
    importer: MockImporter,
):
    with pytest.raises(
        ValueError,
        match="provided library MISSING does not exist",
    ):
        importer.import_dir(import_paths[0], "MISSING", [".mp3"])
