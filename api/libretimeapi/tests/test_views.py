import os
from django.contrib.auth.models import AnonymousUser
from django.conf import settings
from rest_framework.test import APITestCase, APIRequestFactory
from model_bakery import baker
from libretimeapi.views import FileViewSet


class TestFileViewSet(APITestCase):
    @classmethod
    def setUpTestData(cls):
        cls.path = "/api/v2/files/{id}/download/"
        cls.token = settings.CONFIG.get('general', 'api_key')

    def test_invalid(self):
        path = self.path.format(id='a')
        self.client.credentials(HTTP_AUTHORIZATION='Api-Key {}'.format(self.token))
        response = self.client.get(path)
        self.assertEqual(response.status_code, 400)

    def test_does_not_exist(self):
        path = self.path.format(id='1')
        self.client.credentials(HTTP_AUTHORIZATION='Api-Key {}'.format(self.token))
        response = self.client.get(path)
        self.assertEqual(response.status_code, 404)

    def test_exists(self):
        music_dir = baker.make('libretimeapi.MusicDir',
                               directory=os.path.join(os.path.dirname(__file__),
                                                      'resources'))
        f = baker.make('libretimeapi.File',
                       directory=music_dir,
                       mime='audio/mp3',
                       filepath='song.mp3')
        path = self.path.format(id=str(f.pk))
        self.client.credentials(HTTP_AUTHORIZATION='Api-Key {}'.format(self.token))
        response = self.client.get(path)
        self.assertEqual(response.status_code, 200)
