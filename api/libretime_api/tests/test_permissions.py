from django.conf import settings
from django.contrib.auth import get_user_model
from django.contrib.auth.models import AnonymousUser
from model_bakery import baker
from rest_framework.test import APIRequestFactory, APITestCase

from ..core.models import Role
from ..permissions import IsSystemTokenOrUser


class TestIsSystemTokenOrUser(APITestCase):
    @classmethod
    def setUpTestData(cls):
        cls.path = "/api/v2/files"

    def test_unauthorized(self):
        response = self.client.get(self.path.format("files"))
        self.assertEqual(response.status_code, 403)

    def test_token_incorrect(self):
        token = "doesnotexist"
        request = APIRequestFactory().get(
            self.path,
            headers={"Authorization": f"Api-Key {token}"},
        )
        request.user = AnonymousUser()
        allowed = IsSystemTokenOrUser().has_permission(request, None)
        self.assertFalse(allowed)

    def test_token_correct(self):
        token = settings.CONFIG.general.api_key
        request = APIRequestFactory().get(
            self.path,
            headers={"Authorization": f"Api-Key {token}"},
        )
        request.user = AnonymousUser()
        allowed = IsSystemTokenOrUser().has_permission(request, None)
        self.assertTrue(allowed)


class TestPermissions(APITestCase):
    URLS = [
        "schedule",
        "shows",
        "show-days",
        "show-hosts",
        "show-instances",
        "show-rebroadcasts",
        "files",
        "playlists",
        "playlist-contents",
        "smart-blocks",
        "smart-block-contents",
        "smart-block-criteria",
        "webstreams",
    ]

    def logged_in_test_model(self, model, role, username, function):
        path = self.path.format(model)
        if not get_user_model().objects.filter(username=username):
            get_user_model().objects.create_user(
                role=role,
                username=username,
                password="test",
                email="test@example.com",
                first_name="test",
                last_name="user",
            )
        self.client.login(username=username, password="test")
        return function(path)

    @classmethod
    def setUpTestData(cls):
        cls.path = "/api/v2/{}"

    def test_guest_permissions_success(self):
        for model in self.URLS:
            response = self.logged_in_test_model(
                model,
                Role.GUEST,
                "guest",
                self.client.get,
            )
            self.assertEqual(
                response.status_code,
                200,
                msg=f"Invalid for model {model}",
            )

    def test_guest_permissions_failure(self):
        for model in self.URLS:
            response = self.logged_in_test_model(
                model,
                Role.GUEST,
                "guest",
                self.client.post,
            )
            self.assertEqual(
                response.status_code,
                403,
                msg=f"Invalid for model {model}",
            )

    def test_host_get_permissions(self):
        for model in self.URLS:
            response = self.logged_in_test_model(
                model,
                Role.HOST,
                "host",
                self.client.get,
            )
            self.assertEqual(
                response.status_code,
                200,
                msg=f"Invalid for model {model}",
            )

    def test_host_post_permissions(self):
        user = get_user_model().objects.create_user(
            role=Role.HOST,
            username="host2",
            password="test",
            email="test@example.com",
            first_name="test",
            last_name="user",
        )
        file = baker.make("storage.File", owner=user)
        model = f"files/{file.id}"
        path = self.path.format(model)
        self.client.login(username="host2", password="test")
        response = self.client.patch(path, {"name": "newFilename"})
        self.assertEqual(response.status_code, 200)

    def test_host_post_permissions_failure(self):
        get_user_model().objects.create_user(
            role=Role.HOST,
            username="host2",
            password="test",
            email="test@example.com",
            first_name="test",
            last_name="user",
        )
        file = baker.make("storage.File")
        model = f"files/{file.id}"
        path = self.path.format(model)
        self.client.login(username="host2", password="test")
        response = self.client.patch(path, {"name": "newFilename"})
        self.assertEqual(response.status_code, 403)
