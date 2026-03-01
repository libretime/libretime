# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
-- DELETE FROM cc_pref WHERE keystr = 'system_version';
-- INSERT INTO cc_pref (keystr, valstr) VALUES ('system_version', '2.5.5');

"""

DOWN = None


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0005_2_5_4"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="2.5.5",
                sql=UP,
            )
        )
    ]
