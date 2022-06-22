# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
ALTER TABLE cc_show ADD COLUMN has_autoplaylist boolean default 'f' NOT NULL;
ALTER TABLE cc_show ADD COLUMN autoplaylist_id integer DEFAULT NULL;
ALTER TABLE cc_show_instances ADD COLUMN autoplaylist_built boolean default 'f' NOT NULL;
"""

DOWN = """
ALTER TABLE cc_show_instances DROP COLUMN IF EXISTS autoplaylist_built;
ALTER TABLE cc_show DROP COLUMN IF EXISTS has_autoplaylist;
ALTER TABLE cc_show DROP COLUMN IF EXISTS autoplaylist_id;
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0015_2_5_17"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha",
                sql=UP,
            )
        )
    ]
