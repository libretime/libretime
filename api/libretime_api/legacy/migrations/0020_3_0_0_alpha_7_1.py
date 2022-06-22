# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

# https://github.com/libretime/libretime/pull/659
# Add description and title to podcast episodes database table

UP = """
ALTER TABLE podcast_episodes ADD COLUMN episode_title VARCHAR(4096);
ALTER TABLE podcast_episodes ADD COLUMN episode_description VARCHAR(4096);
"""

DOWN = """
ALTER TABLE podcast_episodes DROP COLUMN IF EXISTS episode_title;
ALTER TABLE podcast_episodes DROP COLUMN IF EXISTS episode_description;
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0019_3_0_0_alpha_7"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.7.1",
                sql=UP,
            )
        )
    ]
