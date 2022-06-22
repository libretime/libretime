from django.db import models


class UserToken(models.Model):
    user = models.ForeignKey("User", on_delete=models.DO_NOTHING)
    action = models.CharField(max_length=255)
    token = models.CharField(unique=True, max_length=40)
    created = models.DateTimeField()

    def get_owner(self):
        return self.user

    class Meta:
        managed = False
        db_table = "cc_subjs_token"


class LoginAttempt(models.Model):
    ip = models.CharField(primary_key=True, max_length=32)
    attempts = models.IntegerField(blank=True, null=True)

    class Meta:
        managed = False
        db_table = "cc_login_attempts"
