from secrets import token_hex
from typing import Optional

import click

from .utils import run_, which


def psql(sql: str):
    return run_(
        which("sudo"),
        "-u",
        "postgres",
        which("psql"),
        "--tuples-only",
        "--no-align",
        input=sql,
    )


def psql_create_user(name: str, password: str):
    exist = psql(f"SELECT 1 FROM pg_user WHERE usename = '{name}';")

    if "1" in exist.stdout:
        raise ValueError(f"postgresql user {name} already exists!")

    return psql(f"CREATE USER {name} WITH ENCRYPTED PASSWORD '{password}';")


def psql_update_user_password(name: str, password: str):
    return psql(f"ALTER USER {name} WITH PASSWORD '{password}';")


def psql_create_db(name: str, owner: str):
    return psql(
        f"CREATE DATABASE IF NOT EXISTS {name} "
        "WITH ENCODING 'UTF8' "
        f"TEMPLATE template0 OWNER {owner};"
    )


@click.group()
def db():
    """
    Manage PostgreSQL.

    Management on remote hosts is not supported.
    """


cli_psql_user_option = click.option(
    "--user",
    envvar="POSTGRES_USER",
    default="libretime",
    help="PostgreSQL user name.",
)
cli_psql_password_option = click.option(
    "--password",
    envvar="POSTGRES_PASSWORD",
    help="PostgreSQL user password.",
)

cli_psql_database_option = click.option(
    "--database",
    envvar="POSTGRES_DB",
    default="libretime",
    help="PostgreSQL database.",
)


@db.command()
@cli_psql_user_option
@cli_psql_password_option
def user_create(user: str, password: Optional[str]):
    """
    Create a PostgreSQL user.

    If password is not given, a random password will be generated.
    """
    if not (isinstance(user, str) and user.isalnum()):
        raise click.BadParameter("user must be alphanumeric")

    if password is None:
        password = token_hex(16)

    psql_create_user(user, password)
    click.echo(f"created user '{user}' with password '{password}'")


@db.command()
@cli_psql_user_option
@cli_psql_password_option
def user_password(user: str, password: Optional[str]):
    """
    Change a PostgreSQL user password.

    If password is not given, a random password will be generated.
    """
    if password is None:
        password = token_hex(16)
    psql_update_user_password(user, password)
    click.echo(f"updated user password '{user}' with '{password}'")


@db.command()
@cli_psql_database_option
@cli_psql_user_option
def create(database: str, user: str):
    """
    Create a PostgreSQL database.
    """
    psql_create_db(database, user)
    click.echo(f"created database '{database}' with owner '{user}'")
