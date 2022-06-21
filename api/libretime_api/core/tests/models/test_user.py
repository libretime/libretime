from rest_framework.test import APITestCase

from ....permission_constants import GROUPS
from ...models import Role, User


class TestUserManager(APITestCase):
    def test_create_user(self):
        user = User.objects.create_user(
            role=Role.EDITOR,
            username="test",
            password="test",
            email="test@example.com",
            first_name="test",
            last_name="user",
        )
        db_user = User.objects.get(pk=user.pk)
        self.assertEqual(db_user.username, user.username)

    def test_create_superuser(self):
        user = User.objects.create_superuser(
            username="test",
            password="test",
            email="test@example.com",
            first_name="test",
            last_name="user",
        )
        db_user = User.objects.get(pk=user.pk)
        self.assertEqual(db_user.username, user.username)
        self.assertEqual(db_user.role, Role.ADMIN)


class TestUser(APITestCase):
    def test_guest_get_group_perms(self):
        user = User.objects.create_user(
            role=Role.GUEST,
            username="test",
            password="test",
            email="test@example.com",
            first_name="test",
            last_name="user",
        )

        permissions = user.get_group_permissions()
        # APIRoot permission hardcoded in the check as it isn't a Permission object
        str_perms = [p.codename for p in permissions] + ["view_apiroot"]
        self.assertCountEqual(str_perms, GROUPS[Role.GUEST.value])
