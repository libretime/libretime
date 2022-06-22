# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

# SAAS-1071
# Remove not null constraint from file_id fk in third_party_track_references
# so that we can create track references for downloads (which won't have a
# file ID until the task is run and the file is POSTed back to Airtime)

UP = """
ALTER TABLE third_party_track_references ALTER COLUMN file_id DROP NOT NULL;
"""

DOWN = """
ALTER TABLE third_party_track_references ALTER COLUMN file_id SET NOT NULL;
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0012_2_5_14"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="2.5.15",
                sql=UP,
            )
        )
    ]
