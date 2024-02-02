# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
alter table "cc_subjs" add column "is_active" boolean default 'f' not null;

update "cc_subjs" set "is_active" = 't';
"""

DOWN = """
alter table "cc_subjs" drop column if exists "is_active";
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0045_add_sessions_table"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="46",
                sql=UP,
            )
        )
    ]
