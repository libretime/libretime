# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
delete from cc_stream_setting
where "keyname" like '%_listener_stat_error';
"""

DOWN = """
delete from cc_pref
where "keystr" like 'stream_stats_status:%';
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0038_3_0_0_alpha_14_2"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.14.3",
                sql=UP,
            )
        )
    ]
