from django.apps import apps
from django.contrib.auth.models import Group
from libretimeapi.models import User
from libretimeapi.models.user_constants import DJ, GUEST
from libretimeapi.permission_constants import GROUPS
from rest_framework.test import APITestCase


class TestUserManager(APITestCase):
    def test_create_user(self):
        user = User.objects.create_user(
            "test",
            email="test@example.com",
            password="test",
            type=DJ,
            first_name="test",
            last_name="user",
        )
        db_user = User.objects.get(pk=user.pk)
        self.assertEqual(db_user.username, user.username)

    def test_create_superuser(self):
        user = User.objects.create_superuser(
            "test",
            email="test@example.com",
            password="test",
            first_name="test",
            last_name="user",
        )
        db_user = User.objects.get(pk=user.pk)
        self.assertEqual(db_user.username, user.username)


class TestUser(APITestCase):
    def test_guest_get_group_perms(self):
        user = User.objects.create_user(
            "test",
            email="test@example.com",
            password="test",
            type=GUEST,
            first_name="test",
            last_name="user",
        )
        permissions = user.get_group_permissions()
        # APIRoot permission hardcoded in the check as it isn't a Permission object
        str_perms = [p.codename for p in permissions] + ["view_apiroot"]
        self.assertCountEqual(str_perms, GROUPS[GUEST])
