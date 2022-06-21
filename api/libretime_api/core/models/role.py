from django.db import models


class Role(models.TextChoices):
    GUEST = "G", "Guest"
    EDITOR = "H", "Editor"
    MANAGER = "P", "Manager"
    ADMIN = "A", "Admin"
