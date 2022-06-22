# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
-- DELETE FROM cc_pref WHERE keystr = 'system_version';
-- INSERT INTO cc_pref (keystr, valstr) VALUES ('system_version', '2.5.3');

ALTER TABLE cc_files DROP COLUMN state;
ALTER TABLE cc_files ADD import_status integer default 1; -- Default is "pending"
UPDATE cc_files SET import_status=0; -- Existing files are already "imported"
"""

DOWN = None


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0003_2_5_2"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="2.5.3",
                sql=UP,
            )
        )
    ]
