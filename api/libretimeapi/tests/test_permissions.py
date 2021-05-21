import os
from django.contrib.auth import get_user_model
from django.contrib.auth.models import AnonymousUser
from django.conf import settings
from rest_framework.test import APITestCase, APIRequestFactory
from model_bakery import baker
from libretimeapi.permissions import IsSystemTokenOrUser
from libretimeapi.permission_constants import GUEST_PERMISSIONS, DJ_PERMISSIONS, PROGRAM_MANAGER_PERMISSIONS
from libretimeapi.models.user_constants import GUEST, DJ, PROGRAM_MANAGER, ADMIN


class TestIsSystemTokenOrUser(APITestCase):
    @classmethod
    def setUpTestData(cls):
        cls.path = "/api/v2/files/"

    def test_unauthorized(self):
        response = self.client.get(self.path.format('files'))
        self.assertEqual(response.status_code, 403)

    def test_token_incorrect(self):
        token = 'doesnotexist'
        request = APIRequestFactory().get(self.path)
        request.user = AnonymousUser()
        request.META['Authorization'] = 'Api-Key {token}'.format(token=token)
        allowed = IsSystemTokenOrUser().has_permission(request, None)
        self.assertFalse(allowed)

    def test_token_correct(self):
        token = settings.CONFIG.get('general', 'api_key')
        request = APIRequestFactory().get(self.path)
        request.user = AnonymousUser()
        request.META['Authorization'] = 'Api-Key {token}'.format(token=token)
        allowed = IsSystemTokenOrUser().has_permission(request, None)
        self.assertTrue(allowed)


class TestPermissions(APITestCase):
    URLS = [
        'schedule',
        'shows',
        'show-days',
        'show-hosts',
        'show-instances',
        'show-rebroadcasts',
        'files',
        'playlists',
        'playlist-contents',
        'smart-blocks',
        'smart-block-contents',
        'smart-block-criteria',
        'webstreams',
    ]

    def logged_in_test_model(self, model, name, user_type, fn):
        path = self.path.format(model)
        user_created = get_user_model().objects.filter(username=name)
        if not user_created:
            user = get_user_model().objects.create_user(name,
                                                        email='test@example.com',
                                                        password='test',
                                                        type=user_type,
                                                        first_name='test',
                                                        last_name='user')
        self.client.login(username=name, password='test')
        return fn(path)

    @classmethod
    def setUpTestData(cls):
        cls.path = "/api/v2/{}/"

    def test_guest_permissions_success(self):
        for model in self.URLS:
            response = self.logged_in_test_model(model, 'guest', GUEST, self.client.get)
            self.assertEqual(response.status_code, 200,
                             msg='Invalid for model {}'.format(model))

    def test_guest_permissions_failure(self):
        for model in self.URLS:
            response = self.logged_in_test_model(model, 'guest', GUEST, self.client.post)
            self.assertEqual(response.status_code, 403,
                             msg='Invalid for model {}'.format(model))
        response = self.logged_in_test_model('users', 'guest', GUEST, self.client.get)
        self.assertEqual(response.status_code, 403, msg='Invalid for model users')

    def test_dj_get_permissions(self):
        for model in self.URLS:
            response = self.logged_in_test_model(model, 'dj', DJ, self.client.get)
            self.assertEqual(response.status_code, 200,
                             msg='Invalid for model {}'.format(model))

    def test_dj_post_permissions(self):
        user = get_user_model().objects.create_user('test-dj',
                                                    email='test@example.com',
                                                    password='test',
                                                    type=DJ,
                                                    first_name='test',
                                                    last_name='user')
        f = baker.make('libretimeapi.File',
                       owner=user)
        model = 'files/{}'.format(f.id)
        path = self.path.format(model)
        self.client.login(username='test-dj', password='test')
        response = self.client.patch(path, {'name': 'newFilename'})
        self.assertEqual(response.status_code, 200)

    def test_dj_post_permissions_failure(self):
        user = get_user_model().objects.create_user('test-dj',
                                                    email='test@example.com',
                                                    password='test',
                                                    type=DJ,
                                                    first_name='test',
                                                    last_name='user')
        f = baker.make('libretimeapi.File')
        model = 'files/{}'.format(f.id)
        path = self.path.format(model)
        self.client.login(username='test-dj', password='test')
        response = self.client.patch(path, {'name': 'newFilename'})
        self.assertEqual(response.status_code, 403)
