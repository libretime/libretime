# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

# https://github.com/libretime/libretime/pull/636
# Change dynamic smartblock to be default smartblock type


UP = """
ALTER TABLE cc_block ALTER COLUMN type SET DEFAULT 'dynamic';
"""

DOWN = """
ALTER TABLE cc_block ALTER COLUMN type SET DEFAULT 'static';
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0018_3_0_0_alpha_6"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.7",
                sql=UP,
            )
        )
    ]
