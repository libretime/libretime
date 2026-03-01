# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

# https://github.com/libretime/libretime/pull/704
# Add criteria group to smartblock table to enable database to store separately

UP = """
ALTER TABLE cc_blockcriteria ADD COLUMN criteriagroup integer;
"""

DOWN = """
ALTER TABLE cc_blockcriteria DROP COLUMN IF EXISTS criteriagroup;
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0020_3_0_0_alpha_7_1"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.7.2",
                sql=UP,
            )
        )
    ]
