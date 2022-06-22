# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
CREATE TABLE cloud_file
(
    id serial NOT NULL,
    resource_id text NOT NULL,
    storage_backend text NOT NULL,
    cc_file_id integer NOT NULL,
    CONSTRAINT cloud_file_pkey PRIMARY KEY (id),
    CONSTRAINT "cloud_file_FK_1" FOREIGN KEY (cc_file_id)
        REFERENCES cc_files (id) MATCH SIMPLE
        ON UPDATE NO ACTION ON DELETE CASCADE
)
"""

DOWN = None


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0006_2_5_5"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="2.5.9",
                sql=UP,
            )
        )
    ]
