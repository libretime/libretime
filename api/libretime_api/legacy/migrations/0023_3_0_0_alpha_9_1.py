# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
ALTER TABLE cc_files ADD COLUMN artwork VARCHAR(4096);
"""

DOWN = """
ALTER TABLE cc_files DROP COLUMN IF EXISTS artwork;
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0022_3_0_0_alpha_7_3"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.9.1",
                sql=UP,
            )
        )
    ]
