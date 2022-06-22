# pylint: disable=invalid-name

from pathlib import Path

from django.db import connection, migrations

from . import LEGACY_SCHEMA_VERSION
from ._migrations import get_schema_version, set_schema_version

here = Path(__file__).resolve().parent


def create_schema(_apps, _schema_editor):
    schema_version = get_schema_version()

    # A schema already exists, don't overwrite !
    if schema_version is not None:
        return

    with connection.cursor() as cursor:
        for migration_filename in ("schema.sql", "data.sql"):
            raw = (here / "sql" / migration_filename).read_text(encoding="utf-8")
            cursor.execute(raw)

            set_schema_version(cursor, LEGACY_SCHEMA_VERSION)


class Migration(migrations.Migration):
    initial = True
    dependencies = []
    operations = [migrations.RunPython(create_schema)]
