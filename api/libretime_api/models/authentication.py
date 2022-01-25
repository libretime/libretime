import hashlib

from django.contrib import auth
from django.contrib.auth.models import AbstractBaseUser, Permission
from django.core.exceptions import PermissionDenied
from django.db import models

from libretime_api.managers import UserManager
from libretime_api.permission_constants import GROUPS

from .user_constants import ADMIN, USER_TYPES


class LoginAttempt(models.Model):
    ip = models.CharField(primary_key=True, max_length=32)
    attempts = models.IntegerField(blank=True, null=True)

    class Meta:
        managed = False
        db_table = "cc_login_attempts"


class Session(models.Model):
    sessid = models.CharField(primary_key=True, max_length=32)
    userid = models.ForeignKey(
        "User", models.DO_NOTHING, db_column="userid", blank=True, null=True
    )
    login = models.CharField(max_length=255, blank=True, null=True)
    ts = models.DateTimeField(blank=True, null=True)

    class Meta:
        managed = False
        db_table = "cc_sess"


USER_TYPE_CHOICES = ()
for item in USER_TYPES.items():
    USER_TYPE_CHOICES = USER_TYPE_CHOICES + (item,)


class User(AbstractBaseUser):
    username = models.CharField(db_column="login", unique=True, max_length=255)
    password = models.CharField(
        db_column="pass", max_length=255
    )  # Field renamed because it was a Python reserved word.
    type = models.CharField(max_length=1, choices=USER_TYPE_CHOICES)
    first_name = models.CharField(max_length=255)
    last_name = models.CharField(max_length=255)
    last_login = models.DateTimeField(db_column="lastlogin", blank=True, null=True)
    lastfail = models.DateTimeField(blank=True, null=True)
    skype_contact = models.CharField(max_length=1024, blank=True, null=True)
    jabber_contact = models.CharField(max_length=1024, blank=True, null=True)
    email = models.CharField(max_length=1024, blank=True, null=True)
    cell_phone = models.CharField(max_length=1024, blank=True, null=True)
    login_attempts = models.IntegerField(blank=True, null=True)

    USERNAME_FIELD = "username"
    EMAIL_FIELD = "email"
    REQUIRED_FIELDS = ["type", "email", "first_name", "last_name"]
    objects = UserManager()

    def get_full_name(self):
        return f"{self.first_name} {self.last_name}"

    def get_short_name(self):
        return self.first_name

    def set_password(self, password):
        if not password:
            self.set_unusable_password()
        else:
            self.password = hashlib.md5(password.encode()).hexdigest()

    def is_staff(self):
        return self.type == ADMIN

    def check_password(self, password):
        if self.has_usable_password():
            test_password = hashlib.md5(password.encode()).hexdigest()
            return test_password == self.password
        return False

    """
    The following methods have to be re-implemented here, as PermissionsMixin
    assumes that the User class has a 'group' attribute, which LibreTime does
    not currently provide. Once Django starts managing the Database
    (managed = True), then this can be replaced with
    django.contrib.auth.models.PermissionMixin.
    """

    def is_superuser(self):
        return self.type == ADMIN

    def get_user_permissions(self, obj=None):
        """
        Users do not have permissions directly, only through groups
        """
        return []

    def get_group_permissions(self, obj=None):
        permissions = GROUPS[self.type]
        if obj:
            obj_name = obj.__class__.__name__.lower()
            permissions = [perm for perm in permissions if obj_name in perm]
        # get permissions objects
        q = models.Q()
        for perm in permissions:
            q = q | models.Q(codename=perm)
        return list(Permission.objects.filter(q))

    def get_all_permissions(self, obj=None):
        return self.get_user_permissions(obj) + self.get_group_permissions(obj)

    def has_perm(self, perm, obj=None):
        if self.is_superuser():
            return True
        if not perm:
            return False
        permissions = self.get_all_permissions(obj)
        try:
            permission = Permission.objects.get(codename=perm)
            return permission in permissions
        except Permission.DoesNotExist:
            return False

    def has_perms(self, perm_list, obj=None):
        result = True
        for permission in perm_list:
            result = result and self.has_perm(permission, obj)
        return result

    class Meta:
        managed = False
        db_table = "cc_subjs"


class UserToken(models.Model):
    user = models.ForeignKey(User, models.DO_NOTHING)
    action = models.CharField(max_length=255)
    token = models.CharField(unique=True, max_length=40)
    created = models.DateTimeField()

    def get_owner(self):
        return self.user

    class Meta:
        managed = False
        db_table = "cc_subjs_token"
