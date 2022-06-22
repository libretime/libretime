# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
ALTER TABLE imported_podcast ADD COLUMN album_override boolean default 'f' NOT NULL;
ALTER TABLE third_party_track_references ALTER COLUMN file_id SET DEFAULT 0;
ALTER TABLE third_party_track_references ALTER COLUMN file_id DROP NOT NULL;
ALTER TABLE cc_show ADD COLUMN autoplaylist_repeat boolean default 'f' NOT NULL;
"""

DOWN = """
ALTER TABLE imported_podcast DROP COLUMN IF EXISTS album_override;
ALTER TABLE third_party_track_references ALTER COLUMN file_id DROP DEFAULT;
ALTER TABLE third_party_track_references ALTER COLUMN file_id SET NOT NULL;
ALTER TABLE cc_show DROP COLUMN IF EXISTS autoplaylist_repeat;
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0016_3_0_0_alpha"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.1",
                sql=UP,
            )
        )
    ]
