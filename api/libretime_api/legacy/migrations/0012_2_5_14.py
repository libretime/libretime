# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

# SAAS-923
# Add a partial constraint to cc_pref so that keystrings must be unique

UP = """
ALTER TABLE cc_pref ALTER COLUMN subjid SET DEFAULT NULL;
CREATE UNIQUE INDEX cc_pref_key_idx ON cc_pref (keystr) WHERE subjid IS NULL;
ANALYZE cc_pref;
"""

DOWN = None


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0011_2_5_13"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="2.5.14",
                sql=UP,
            )
        )
    ]
