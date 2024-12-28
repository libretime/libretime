import os
from unittest.mock import patch

from django.conf import settings
from model_bakery import baker
from rest_framework.test import APITestCase

from ...._fixtures import AUDIO_FILENAME
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

    def test_destroy(self):
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        file: File = baker.make(
            "storage.File",
            mime="audio/mp3",
            filepath=AUDIO_FILENAME,
        )

        with patch("libretime_api.storage.views.file.remove") as remove_mock:
            response = self.client.delete(f"/api/v2/files/{file.id}")

        self.assertEqual(response.status_code, 204)
        remove_mock.assert_called_with(
            os.path.join(settings.CONFIG.storage.path, file.filepath)
        )

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

        path = "/api/v2/files?limit=1"
        results = self.client.get(path).json()
        self.assertEqual(len(results["results"]), 1)

        path = f"/api/v2/files?md5={file.md5}"
        results = self.client.get(path).json()
        self.assertEqual(len(results), 1)

        path = "/api/v2/files?genre=Soul"
        results = self.client.get(path).json()
        self.assertEqual(len(results), 1)
