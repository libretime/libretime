# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """"""

DOWN = """"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0039_remove_stream_settings_stats_status"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-beta.0.1",
            )
        )
    ]
