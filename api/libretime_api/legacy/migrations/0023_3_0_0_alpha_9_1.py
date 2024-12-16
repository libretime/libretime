# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

# This migration is currently a placeholder for 3.0.0-alpha.9.1.
# Please do not remove it.  There are currently no actions, but it
# needs to remain intact so it does not fail when called from the
# migrations script. Any future migrations that may apply to
# 3.0.0-alpha.9.1 will be added to this file.

UP = """

"""

DOWN = """

"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0022_3_0_0_alpha_7_3"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.9.1",
                sql=UP,
            )
        )
    ]
