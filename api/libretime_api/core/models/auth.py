from django.db import models


class UserToken(models.Model):
    user = models.ForeignKey("User", models.DO_NOTHING)
    action = models.CharField(max_length=255)
    token = models.CharField(unique=True, max_length=40)
    created = models.DateTimeField()

    def get_owner(self):
        return self.user

    class Meta:
        managed = False
        db_table = "cc_subjs_token"


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


class LoginAttempt(models.Model):
    ip = models.CharField(primary_key=True, max_length=32)
    attempts = models.IntegerField(blank=True, null=True)

    class Meta:
        managed = False
        db_table = "cc_login_attempts"
