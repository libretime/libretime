from pathlib import Path
from shutil import copy

from django.conf import settings
from model_bakery import baker
from rest_framework.test import APITestCase

from ...._fixtures import AUDIO_FILENAME, fixture_path
from ....schedule.models import Playlist, PlaylistContent
from ...models import File


class TestFileViewSet(APITestCase):
    @classmethod
    def setUpTestData(cls):
        cls.token = settings.CONFIG.general.api_key

    def test_download_invalid(self):
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        file_id = "1"
        response = self.client.get(f"/api/v2/files/{file_id}/download")
        self.assertEqual(response.status_code, 404)

    def test_download(self):
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        file: File = baker.make(
            "storage.File",
            mime="audio/mp3",
            filepath=AUDIO_FILENAME,
        )
        response = self.client.get(f"/api/v2/files/{file.id}/download")
        self.assertEqual(response.status_code, 200)

    def test_patch_size_and_md5(self):
        """
        What playout patches onto a file after recording a show.

        The field is "size", not the column's "filesize": an unrecognised key is
        dropped in silence, so the name is asserted here as well as in
        playout/tests/player/file_test.py.
        """
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        file: File = baker.make(
            "storage.File",
            mime="audio/mpeg",
            filepath=AUDIO_FILENAME,
        )

        response = self.client.patch(
            f"/api/v2/files/{file.id}",
            {"size": 4711, "md5": "5a11ffe0e6c6d70fcdbad1b734be6482"},
            format="json",
        )

        self.assertEqual(response.status_code, 200, response.data)

        file.refresh_from_db()
        self.assertEqual(file.size, 4711)
        self.assertEqual(file.md5, "5a11ffe0e6c6d70fcdbad1b734be6482")

    def _file_with_bytes(self, name: str) -> File:
        """Fixture-safe: the test storage path IS the _fixtures directory,
        so never point a delete at the shared AUDIO_FILENAME itself."""
        path = Path(settings.CONFIG.storage.path) / name
        copy(fixture_path / AUDIO_FILENAME, path)
        self.addCleanup(path.unlink, missing_ok=True)
        return baker.make("storage.File", mime="audio/mp3", filepath=name)

    def test_destroy(self):
        """Soft delete (#1163): bytes are disposed of, the row survives,
        marked with intent — and stays resolvable by id."""
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        file = self._file_with_bytes("soft-delete-me.mp3")
        path = Path(settings.CONFIG.storage.path) / file.filepath

        response = self.client.delete(f"/api/v2/files/{file.id}")
        self.assertEqual(response.status_code, 204)

        file.refresh_from_db()
        self.assertIsNotNone(file.deleted_at)
        self.assertIsNone(file.deleted_by)  # system token has no user
        self.assertFalse(file.exists)
        self.assertFalse(path.exists())

    def test_destroy_still_returns_bytes_gone_as_success(self):
        """A delete on storage that is already missing must still record
        the human intent (unmounted volume must not resurrect the row)."""
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        file = baker.make("storage.File", mime="audio/mp3", filepath="gone.mp3")

        response = self.client.delete(f"/api/v2/files/{file.id}")
        self.assertEqual(response.status_code, 204)

        file.refresh_from_db()
        self.assertIsNotNone(file.deleted_at)

    def test_destroy_is_visible_in_list(self):
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        keep = baker.make("storage.File", mime="audio/mp3", filepath=AUDIO_FILENAME)
        gone = baker.make("storage.File", mime="audio/mp3", filepath="gone.mp3")

        self.client.delete(f"/api/v2/files/{gone.id}")

        ids = [f["id"] for f in self.client.get("/api/v2/files").json()]
        self.assertIn(keep.id, ids)
        self.assertNotIn(gone.id, ids)

    def test_destroy_keeps_retrieve_for_the_calendar(self):
        """#1163 expected behavior: 'the metadata for tracks including
        artist etc and when they were played should be retained for
        reporting purposes and viewable via the schedule'."""
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        file = baker.make("storage.File", mime="audio/mp3", filepath="gone.mp3")
        self.client.delete(f"/api/v2/files/{file.id}")

        response = self.client.get(f"/api/v2/files/{file.id}")
        self.assertEqual(response.status_code, 200)
        self.assertEqual(response.json()["id"], file.id)

    def test_download_deleted_returns_gone(self):
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        file = self._file_with_bytes("soft-download-gone.mp3")
        self.client.delete(f"/api/v2/files/{file.id}")

        response = self.client.get(f"/api/v2/files/{file.id}/download")
        self.assertEqual(response.status_code, 410)

    def test_md5_dedupe_ignores_deleted_files(self):
        """Re-importing a previously deleted track must not be blocked by
        its ghost row (md5 dedupe contract)."""
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        md5 = "5a11ffe0e6c6d70fcdbad1b734be6499"
        file = baker.make(
            "storage.File", mime="audio/mp3", filepath="gone.mp3", md5=md5
        )
        self.client.delete(f"/api/v2/files/{file.id}")

        results = self.client.get(f"/api/v2/files?md5={md5}").json()
        self.assertEqual(len(results), 0)

    def test_destroy_prunes_playlist_templates(self):
        """Playlists are forward-looking templates and are pruned on file
        delete (the behavior the cascade used to provide silently) — an
        autoloaded playlist must never keep an item whose bytes are gone,
        or playout punches a hole in the show. The playlist itself and the
        file row both survive."""
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        file = baker.make("storage.File", mime="audio/mp3", filepath="gone.mp3")
        playlist = baker.make("schedule.Playlist", name="reused-weekly")
        baker.make("schedule.PlaylistContent", playlist=playlist, file=file)
        other = baker.make("storage.File", mime="audio/mp3", filepath="keep.mp3")
        baker.make("schedule.PlaylistContent", playlist=playlist, file=other)

        response = self.client.delete(f"/api/v2/files/{file.id}")
        self.assertEqual(response.status_code, 204)

        self.assertEqual(PlaylistContent.objects.filter(file=file).count(), 0)
        self.assertEqual(PlaylistContent.objects.filter(file=other).count(), 1)
        self.assertTrue(Playlist.objects.filter(pk=playlist.pk).exists())
        file.refresh_from_db()
        self.assertIsNotNone(file.deleted_at)

    def test_destroy_no_file(self):
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        file = baker.make(
            "storage.File",
            mime="audio/mp3",
            filepath="invalid.mp3",
        )
        response = self.client.delete(f"/api/v2/files/{file.id}")
        self.assertEqual(response.status_code, 204)

    def test_destroy_invalid(self):
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        file_id = "1"
        response = self.client.delete(f"/api/v2/files/{file_id}")
        self.assertEqual(response.status_code, 404)

    def test_filters(self):
        file = baker.make(
            "storage.File",
            mime="audio/mp3",
            filepath=AUDIO_FILENAME,
            genre="Soul",
            md5="5a11ffe0e6c6d70fcdbad1b734be6482",
        )
        baker.make(
            "storage.File",
            mime="audio/mp3",
            filepath=AUDIO_FILENAME,
            genre="R&B",
            md5="5a11ffe0e6c6d70fcdbad1b734be6483",
        )
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")

        path = "/api/v2/files"
        results = self.client.get(path).json()
        self.assertEqual(len(results), 2)

        path = f"/api/v2/files?md5={file.md5}"
        results = self.client.get(path).json()
        self.assertEqual(len(results), 1)

        path = "/api/v2/files?genre=Soul"
        results = self.client.get(path).json()
        self.assertEqual(len(results), 1)

        path = "/api/v2/files?genre=R%26B"
        results = self.client.get(path).json()
        self.assertEqual(len(results), 1)
