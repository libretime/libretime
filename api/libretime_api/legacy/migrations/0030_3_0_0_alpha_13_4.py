# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
DROP SEQUENCE IF EXISTS schedule_group_id_seq CASCADE;
DROP SEQUENCE IF EXISTS show_group_id_seq CASCADE;
"""

DOWN = None


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0029_3_0_0_alpha_13_3"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.13.4",
                sql=UP,
            )
        )
    ]
