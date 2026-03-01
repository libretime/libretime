# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
delete from cc_pref
where "keystr" in (
    'default_icecast_password',
    'default_stream_mount_point',
    'live_dj_connection_url_override',
    'live_dj_source_connection_url',
    'master_dj_connection_url_override',
    'master_dj_source_connection_url',
    'max_bitrate',
    'num_of_streams',
    'stream_bitrate',
    'stream_type'
);
"""

DOWN = """
insert into
    cc_pref ("keystr", "valstr")
values
    ('default_stream_mount_point', 'main'),
    ('max_bitrate', '320'),
    ('num_of_streams', '3'),
    ('stream_bitrate', '24, 32, 48, 64, 96, 128, 160, 192, 224, 256, 320'),
    ('stream_type', 'ogg, mp3, opus, aac');
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0041_drop_stream_setting_table"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="42",
                sql=UP,
            )
        )
    ]
