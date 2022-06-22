# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
DROP TABLE IF EXISTS "cc_perms" CASCADE;
"""

DOWN = """
CREATE TABLE "cc_perms"
(
    "permid" INTEGER NOT NULL,
    "subj" INTEGER,
    "action" VARCHAR(20),
    "obj" INTEGER,
    "type" CHAR(1),
    PRIMARY KEY ("permid"),
    CONSTRAINT "cc_perms_all_idx" UNIQUE ("subj","action","obj"),
    CONSTRAINT "cc_perms_permid_idx" UNIQUE ("permid")
);

CREATE INDEX "cc_perms_subj_obj_idx" ON "cc_perms" ("subj","obj");

ALTER TABLE "cc_perms" ADD CONSTRAINT "cc_perms_subj_fkey"
    FOREIGN KEY ("subj")
    REFERENCES "cc_subjs" ("id")
    ON DELETE CASCADE;
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0032_3_0_0_alpha_13_6"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.13.7",
                sql=UP,
            )
        )
    ]
