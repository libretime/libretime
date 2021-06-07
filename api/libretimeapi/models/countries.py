from django.db import models


class Country(models.Model):
    isocode = models.CharField(primary_key=True, max_length=3)
    name = models.CharField(max_length=255)

    class Meta:
        managed = False
        db_table = "cc_country"
