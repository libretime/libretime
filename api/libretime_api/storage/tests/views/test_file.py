import os

from django.conf import settings
from model_bakery import baker
from rest_framework.test import APITestCase

from ...._fixtures import AUDIO_FILENAME


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
        file = baker.make(
            "storage.File",
            mime="audio/mp3",
            filepath=AUDIO_FILENAME,
        )
        response = self.client.get(f"/api/v2/files/{file.id}/download")
        self.assertEqual(response.status_code, 200)

    def test_destroy(self):
        path = "/api/v2/files/{id}"
        file = baker.make(
            "storage.File",
            mime="audio/mp3",
            filepath=AUDIO_FILENAME,
        )
        path = path.format(id=str(file.pk))
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        response = self.client.delete(path)
        self.assertEqual(response.status_code, 204)
        self.assertFalse(
            os.path.isfile(os.path.join(settings.CONFIG.storage.path, file.filepath))
        )
