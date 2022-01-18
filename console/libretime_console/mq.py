from secrets import token_hex
from typing import Optional

import click

from .utils import run_, which


def rabbitmq(*args):
    return run_(which("sudo"), which("rabbitmqctl"), *args)


def rabbitmq_create_user(name: str, password: str):
    return rabbitmq("add_user", name, password)


def rabbitmq_update_user_password(name: str, password: str):
    return rabbitmq("change_password", name, password)


def rabbitmq_create_vhost(name: str, owner: str):
    vhosts = rabbitmq("list_vhosts")
    print(vhosts)
    # if name not in vhosts:
    rabbitmq("add_vhost", name)
    rabbitmq("set_permissions", "-p", name, owner, ".*", ".*", ".*")


@click.group()
def mq():
    """
    Manage RabbitMQ.

    Management on remote hosts is not supported.
    """


cli_rabbitmq_user_option = click.option(
    "--user",
    envvar="RABBITMQ_USER",
    default="libretime",
    help="RabbitMQ user name.",
)
cli_rabbitmq_password_option = click.option(
    "--password",
    envvar="RABBITMQ_PASSWORD",
    help="RabbitMQ user password.",
)

cli_rabbitmq_vhost_option = click.option(
    "--vhost",
    envvar="RABBITMQ_VHOST",
    default="/libretime",
    help="RabbitMQ vhost.",
)


@mq.command()
@cli_rabbitmq_user_option
@cli_rabbitmq_password_option
def user_create(user: str, password: Optional[str]):
    """
    Create a RabbitMQ user.

    If password is not given, a random password will be generated.
    """
    if not (isinstance(user, str) and user.isalnum()):
        raise click.BadParameter("user must be alphanumeric")

    if password is None:
        password = token_hex(16)

    rabbitmq_create_user(user, password)
    click.echo(f"created user '{user}' with password '{password}'")


@mq.command()
@cli_rabbitmq_user_option
@cli_rabbitmq_password_option
def user_password(user: str, password: Optional[str]):
    """
    Change a RabbitMQ user password.

    If password is not given, a random password will be generated.
    """
    if password is None:
        password = token_hex(16)
    rabbitmq_update_user_password(user, password)
    click.echo(f"updated user password '{user}' with '{password}'")


@mq.command()
@cli_rabbitmq_vhost_option
@cli_rabbitmq_user_option
def create(vhost: str, user: str):
    """
    Create a RabbitMQ vhost.
    """
    rabbitmq_create_vhost(vhost, user)
    click.echo(f"created vhost '{vhost}' with owner '{user}'")
