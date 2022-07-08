# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
UPDATE "cc_pref" SET "valstr" = '4'
WHERE "keystr" = 'num_of_streams';
"""

DOWN = """
UPDATE "cc_pref" SET "valstr" = '3'
WHERE "keystr" = 'num_of_streams';
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0034_3_0_0_alpha_13_10"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.13.11",
                sql=UP,
            )
        )
    ]
