from django.db import models


class Role(models.TextChoices):
    GUEST = "G", "Guest"
    HOST = "H", "Host"
    MANAGER = "P", "Manager"
    ADMIN = "A", "Admin"
