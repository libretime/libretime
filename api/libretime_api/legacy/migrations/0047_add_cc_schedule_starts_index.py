# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
CREATE INDEX IF NOT EXISTS cc_schedule_starts_idx ON cc_schedule (starts);
"""

DOWN = """
DROP INDEX IF EXISTS cc_schedule_starts_idx;
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0046_add_override_intro_outro_playlists"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="47",
                sql=UP,
            )
        )
    ]
