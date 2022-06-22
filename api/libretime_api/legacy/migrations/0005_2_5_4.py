# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = None

DOWN = None


def promote_admin_to_superadmin(cursor):
    # Ensure there are no superadmins already
    super_admin_count = cursor.execute(
        """
        SELECT COUNT(id)
        FROM cc_subjs
        WHERE type = 'S'
        AND login != 'sourcefabric_admin';
        """
    ).fetchone()
    if super_admin_count != 0:
        return

    # Promote the "admin" user to superadmin
    cursor.execute(
        """
        UPDATE cc_subjs SET type = 'S'
        WHERE login = 'admin';
        """
    )
    if cursor.rowcount == 0:
        # Otherwise promote the administrator with the lowest ID
        cursor.execute(
            """
            UPDATE cc_subjs SET type = 'S'
            WHERE id = (
                SELECT id
                FROM cc_subjs
                WHERE type = 'A'
                ORDER BY id
                LIMIT 1
            );
            """
        )
        if cursor.rowcount == 0:
            raise RuntimeError("Failed to find any users of type 'admin' ('A')")

    # Ignoring the sourcefabric_admin user


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0004_2_5_3"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="2.5.4",
                before=promote_admin_to_superadmin,
            )
        )
    ]
