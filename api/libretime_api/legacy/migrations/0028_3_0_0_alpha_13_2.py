# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
ALTER TABLE cc_files ALTER COLUMN artwork TYPE VARCHAR(4096);
"""

DOWN = None


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0027_3_0_0_alpha_13_1"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.13.2",
                sql=UP,
            )
        )
    ]
