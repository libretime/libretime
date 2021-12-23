from django.db import models


class ServiceRegister(models.Model):
    name = models.CharField(primary_key=True, max_length=32)
    ip = models.CharField(max_length=45)

    class Meta:
        managed = False
        db_table = "cc_service_register"
