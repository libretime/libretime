from django.db import models


class Country(models.Model):
    iso_code = models.CharField(primary_key=True, max_length=3, db_column="isocode")
    name = models.CharField(max_length=255)

    class Meta:
        managed = False
        db_table = "cc_country"
