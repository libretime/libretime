# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
DROP TABLE IF EXISTS "cc_sess" CASCADE;
"""

DOWN = """
CREATE TABLE "cc_sess"
(
    "sessid" CHAR(32) NOT NULL,
    "userid" INTEGER,
    "login" VARCHAR(255),
    "ts" TIMESTAMP,
    PRIMARY KEY ("sessid")
);

CREATE INDEX "cc_sess_login_idx" ON "cc_sess" ("login");

CREATE INDEX "cc_sess_userid_idx" ON "cc_sess" ("userid");

ALTER TABLE "cc_sess" ADD CONSTRAINT "cc_sess_userid_fkey"
    FOREIGN KEY ("userid")
    REFERENCES "cc_subjs" ("id")
    ON DELETE CASCADE;
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0031_3_0_0_alpha_13_5"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.13.6",
                sql=UP,
            )
        )
    ]
