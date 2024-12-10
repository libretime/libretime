from typing import Callable, Optional

from django.db import DataError, connection

from ._version import parse_version


def get_schema_version():
    """
    Get the schema version from a legacy database.

    Don't use django models as they might break in the future. Our concern is to upgrade
    the legacy database schema to the point where django is in charge of the migrations.
    """

    if "cc_pref" not in connection.introspection.table_names():
        return None

    with connection.cursor() as cursor:
        cursor.execute("SELECT valstr FROM cc_pref WHERE keystr = 'schema_version'")
        row = cursor.fetchone()
        if row: return row[0]

        # Check to see if this is an airtime 2.5.1 migration which will not return a schema_version
        # We look for system_version to have a value of 2.5.1
        with connection.cursor() as cursor:
            cursor.execute("SELECT valstr FROM cc_pref WHERE keystr = 'system_version'")
            row = cursor.fetchone()
            if row and row[0] == '2.5.1':
                return '0'  # A low schema version that is not None

        return None

def set_schema_version(cursor, version: str):
    cursor.execute(
        """
        UPDATE cc_pref
        SET valstr = %s
        WHERE keystr = 'schema_version';
        """,
        [version],
    )
    if not cursor.rowcount:
        cursor.execute(
            """
            INSERT INTO cc_pref (keystr, valstr)
            VALUES ('schema_version', %s);
            """,
            [version],
        )


def legacy_migration_factory(
    target: str,
    before: Optional[Callable] = None,
    sql: Optional[str] = None,
    after: Optional[Callable] = None,
    reverse: bool = False,
):
    target_version = parse_version(target)

    def inner(_apps, _schema_editor):
        current = get_schema_version()
        if current is None:
            raise DataError("current schema version was not found!")

        current_version = parse_version(current)
        if current_version >= target_version and not reverse:
            return

        if current_version < target_version and reverse:
            return

        with connection.cursor() as cursor:
            if before is not None:
                before(cursor)

            if sql is not None:
                cursor.execute(sql)

            if after is not None:
                after(cursor)

            set_schema_version(cursor, version=target)

    return inner
