# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
insert into cc_pref ("keystr", "valstr")
select
    "keyname" as "keystr",
    coalesce("value", 'LibreTime - offline') as "valstr"
from cc_stream_setting
where "keyname" = 'off_air_meta';

delete from cc_stream_setting
where "keyname" = 'off_air_meta';
"""

DOWN = """
insert into cc_stream_setting ("keyname", "value", "type")
select
    "keystr" as "keyname",
    coalesce("valstr", 'LibreTime - offline') as "value",
    'string' as "type"
from cc_pref
where "keystr" = 'off_air_meta';

delete from cc_pref
where "keystr" = 'off_air_meta';
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0036_3_0_0_alpha_13_10"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.14.1",
                sql=UP,
            )
        )
    ]
