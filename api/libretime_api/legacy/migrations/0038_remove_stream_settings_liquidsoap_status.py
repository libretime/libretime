# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
delete from cc_stream_setting
where "keyname" like '%_liquidsoap_error';
"""

DOWN = """
delete from cc_pref
where "keystr" like 'stream_liquidsoap_status:%';
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0037_move_stream_settings_to_preferences"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.14.2",
                sql=UP,
            )
        )
    ]
