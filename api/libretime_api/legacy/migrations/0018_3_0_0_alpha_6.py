# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
ALTER TABLE cc_service_register ALTER COLUMN ip TYPE character varying(45);
"""

DOWN = """
ALTER TABLE cc_service_register ALTER COLUMN ip TYPE character varying(18);
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0017_3_0_0_alpha_1"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.6",
                sql=UP,
            )
        )
    ]
