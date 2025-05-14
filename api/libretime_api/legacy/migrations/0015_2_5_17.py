# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
ALTER TABLE cc_files ADD COLUMN artwork VARCHAR(255);
"""

DOWN = None


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0014_2_5_16"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="2.5.17",
                sql=UP,
            )
        )
    ]
