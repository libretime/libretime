# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
delete from cc_pref
where "keystr" = 'allowed_cors_urls';
"""

DOWN = """"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0042_remove_stream_preferences"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="43",
                sql=UP,
            )
        )
    ]
