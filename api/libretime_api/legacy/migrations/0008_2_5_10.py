# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
ALTER TABLE cc_files ADD COLUMN filesize integer NOT NULL
CONSTRAINT filesize_default DEFAULT 0
"""

DOWN = None


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0007_2_5_9"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="2.5.10",
                sql=UP,
            )
        )
    ]
