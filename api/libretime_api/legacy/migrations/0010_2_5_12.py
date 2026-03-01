# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
ALTER TABLE cc_show ALTER COLUMN description TYPE varchar(8192);
ALTER TABLE cc_show_instances ALTER COLUMN description TYPE varchar(8192);
"""

DOWN = None


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0009_2_5_11"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="2.5.12",
                sql=UP,
            )
        )
    ]
