# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = None

DOWN = None


def update_disk_usage(cursor):
    cursor.execute(
        """
        UPDATE cc_pref SET valstr = (
            SELECT SUM(filesize)
            FROM cc_files
        )
        WHERE keystr = 'disk_usage';
        """
    )


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0008_2_5_10"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="2.5.11",
                sql=update_disk_usage,
            )
        )
    ]
