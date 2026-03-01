# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
DROP TABLE IF EXISTS cc_locale CASCADE;
"""

DOWN = None


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0026_3_0_0_alpha_9_4"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.13.1",
                sql=UP,
            )
        )
    ]
