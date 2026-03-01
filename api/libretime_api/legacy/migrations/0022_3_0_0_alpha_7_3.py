# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
ALTER TABLE podcast_episodes ALTER COLUMN episode_description TYPE text;
"""

DOWN = """
ALTER TABLE podcast_episodes ALTER COLUMN episode_description TYPE VARCHAR(4096);
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0021_3_0_0_alpha_7_2"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.7.3",
                sql=UP,
            )
        )
    ]
