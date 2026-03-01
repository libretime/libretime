# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
INSERT INTO "cc_pref" ("keystr", "valstr") VALUES ('default_stream_mount_point', 'airtime_128');
"""

DOWN = """
DELETE FROM "cc_pref" WHERE "keystr" = 'default_stream_mount_point';
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0030_3_0_0_alpha_13_4"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.13.5",
                sql=UP,
            )
        )
    ]
