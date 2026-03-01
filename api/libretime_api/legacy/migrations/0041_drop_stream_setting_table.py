# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
drop table if exists "cc_stream_setting" cascade;
"""

DOWN = """
create table "cc_stream_setting"
(
    "keyname" varchar(64) not null,
    "value" varchar(255),
    "type" varchar(16) not null,
    primary key ("keyname")
);
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0040_bump_legacy_schema_version"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="41",
                sql=UP,
            )
        )
    ]
