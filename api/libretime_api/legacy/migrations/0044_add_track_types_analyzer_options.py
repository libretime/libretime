# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
alter table "cc_track_types" add column "analyze_cue_points" boolean default 'f' not null;

update "cc_track_types" set "analyze_cue_points" = 't';
"""

DOWN = """
alter table "cc_track_types" drop column if exists "analyze_cue_points";
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0043_remove_cors_preference"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="44",
                sql=UP,
            )
        )
    ]
