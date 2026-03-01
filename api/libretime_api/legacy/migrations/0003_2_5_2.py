# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
-- Replacing system_version with schema_version
DELETE FROM cc_pref WHERE keystr = 'system_version';
INSERT INTO cc_pref (keystr, valstr) VALUES ('schema_version', '2.5.2');

ALTER TABLE cc_show ADD COLUMN image_path varchar(255) DEFAULT '';
ALTER TABLE cc_show_instances ADD COLUMN description varchar(255) DEFAULT '';
"""

DOWN = None


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0001_initial"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="2.5.2",
                sql=UP,
            )
        )
    ]
