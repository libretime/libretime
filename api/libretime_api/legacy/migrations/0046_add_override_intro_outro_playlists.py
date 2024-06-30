# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
ALTER TABLE cc_show ADD COLUMN intro_playlist_id integer DEFAULT NULL;
ALTER TABLE cc_show ADD CONSTRAINT cc_playlist_intro_playlist_fkey FOREIGN KEY (intro_playlist_id) REFERENCES cc_playlist (id) ON DELETE SET NULL;
ALTER TABLE cc_show ADD COLUMN outro_playlist_id integer DEFAULT NULL;
ALTER TABLE cc_show ADD CONSTRAINT cc_playlist_outro_playlist_fkey FOREIGN KEY (outro_playlist_id) REFERENCES cc_playlist (id) ON DELETE SET NULL;
"""

DOWN = """
ALTER TABLE cc_show DROP COLUMN IF EXISTS intro_playlist_id;
ALTER TABLE cc_show DROP CONSTRAINT IF EXISTS cc_playlist_intro_playlist_fkey;
ALTER TABLE cc_show DROP COLUMN IF EXISTS outro_playlist_id;
ALTER TABLE cc_show DROP CONSTRAINT IF EXISTS cc_playlist_outro_playlist_fkey;
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
