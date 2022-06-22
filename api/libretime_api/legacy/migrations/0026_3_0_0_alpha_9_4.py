# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
ALTER TABLE cc_files DROP COLUMN soundcloud_id;
ALTER TABLE cc_files DROP COLUMN soundcloud_error_code;
ALTER TABLE cc_files DROP COLUMN soundcloud_error_msg;
ALTER TABLE cc_files DROP COLUMN soundcloud_link_to_file;
ALTER TABLE cc_files DROP COLUMN soundcloud_upload_time;
"""

DOWN = """
ALTER TABLE cc_files ADD COLUMN soundcloud_id INTEGER;
ALTER TABLE cc_files ADD COLUMN soundcloud_error_code INTEGER;
ALTER TABLE cc_files ADD COLUMN soundcloud_error_msg VARCHAR(512);
ALTER TABLE cc_files ADD COLUMN soundcloud_link_to_file VARCHAR(4096);
ALTER TABLE cc_files ADD COLUMN soundcloud_upload_time TIMESTAMP(6);
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0025_3_0_0_alpha_9_3"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.9.4",
                sql=UP,
            )
        )
    ]
