from os import PathLike
from secrets import token_hex
from typing import Optional

import click

from .utils import run_, which


def xml_update(config_file: PathLike, xpath: str, value):
    return run_(
        which("sudo"),
        which("xmlstarlet"),
        *("edit", "--inplace"),
        *("--update", xpath),
        *("--value", value),
        config_file,
    )


def icecast_update_passwords(
    config_filepath: PathLike,
    *,
    admin_user: Optional[str] = None,
    admin_password: Optional[str] = None,
    source_password: Optional[str] = None,
    relay_password: Optional[str] = None,
):
    for xpath, value in {
        "/icecast/authentication/admin-user": admin_user,
        "/icecast/authentication/admin-password": admin_password,
        "/icecast/authentication/source-password": source_password,
        "/icecast/authentication/relay-password": relay_password,
    }.items():
        if value is not None:
            xml_update(config_filepath, xpath, value)


@click.group()
def icecast():
    """
    Manage Icecast.

    Management on remote hosts is not supported.
    """


@icecast.command()
@click.argument(
    "config_filepath",
    envvar="ICECAST_CONFIG_FILEPATH",
)
@click.option(
    "--admin-password",
    envvar="ICECAST_ADMIN_PASSWORD",
    help="Icecast admin password.",
)
@click.option(
    "--source-password",
    envvar="ICECAST_SOURCE_PASSWORD",
    help="Icecast source password.",
)
@click.option(
    "--relay-password",
    envvar="ICECAST_RELAY_PASSWORD",
    help="Icecast relay password.",
)
def update_passwords(
    config_filepath: PathLike,
    admin_password: Optional[str],
    source_password: Optional[str],
    relay_password: Optional[str],
):
    """
    Change Icecast passwords.

    If passwords are not given, random passwords will be generated.
    """

    icecast_update_passwords(
        config_filepath,
        admin_password=admin_password or token_hex(16),
        source_password=source_password or token_hex(16),
        relay_password=relay_password or token_hex(16),
    )
    click.echo("updated icecast passwords")
    click.echo(f"admin_password '{admin_password}'")
    click.echo(f"source_password '{source_password}'")
    click.echo(f"relay_password '{relay_password}'")
