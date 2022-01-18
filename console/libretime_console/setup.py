from pathlib import Path
from secrets import token_hex

import click

from .db import psql_create_db, psql_create_user
from .mq import rabbitmq_create_user, rabbitmq_create_vhost
from .utils import warning


def echo_section(text):
    fill = (80 - len(text)) * " "
    click.echo()
    click.secho(text + fill, fg="cyan")
    click.echo()


@click.command()
def setup():
    """
    Setup LibreTime.
    """

    # Defaults
    public_url = "http://localhost:80/"
    database_host = "localhost"
    database_port = 5432
    database_name = "libretime"
    database_user = "libretime"
    database_password = token_hex(16)
    message_queue_host = "localhost"
    message_queue_port = 5672
    message_queue_vhost = "/libretime"
    message_queue_user = "libretime"
    message_queue_password = token_hex(16)
    storage_path = "/srv/libretime/storage"

    confirmation = False
    while not confirmation:
        click.clear()

        echo_section("# Global settings")

        public_url = click.prompt(
            text="Enter the full public url",
            default=public_url,
            type=str,
        )

        echo_section("# Database settings (Postgresql)")

        database_host = click.prompt(
            text="Your database host",
            default=database_host,
            type=str,
        )
        database_port = click.prompt(
            text="Your database port",
            default=database_port,
            type=int,
        )
        database_name = click.prompt(
            text="Your database name",
            default=database_name,
            type=str,
        )
        database_user = click.prompt(
            text="Your database user",
            default=database_user,
            type=str,
        )
        database_password = click.prompt(
            text="Your database user password",
            default=database_password,
            type=str,
        )

        echo_section("# Message Queue settings (RabbitMQ)")

        message_queue_host = click.prompt(
            text="Your message queue host",
            default=message_queue_host,
            type=str,
        )
        message_queue_port = click.prompt(
            text="Your message queue port",
            default=message_queue_port,
            type=int,
        )
        message_queue_vhost = click.prompt(
            text="Your message queue vhost",
            default=message_queue_vhost,
            type=str,
        )
        message_queue_user = click.prompt(
            text="Your message queue user",
            default=message_queue_user,
            type=str,
        )
        message_queue_password = click.prompt(
            text="Your message queue user password",
            default=message_queue_password,
            type=str,
        )

        echo_section("# Storage settings")

        storage_path = click.prompt(
            text="Your storage directory path",
            default=storage_path,
            type=Path,
        )

        click.secho(
            f"""
public_url: {public_url}

database:
    host: {database_host}
    port: {database_port}
    name: {database_name}
    user: {database_user}
    password: {database_password}

rabbitmq:
    host: {message_queue_host}
    port: {message_queue_port}
    vhost: {message_queue_vhost}
    user: {message_queue_user}
    password: {message_queue_password}

storage:
    path: {storage_path}
""",
            fg="bright_magenta",
            bold=True,
        )

        confirmation = click.confirm(
            click.style("Do you confirm your choices", fg="red"),
            default=False,
        )

    if database_host != "localhost":
        warning("database management on remote hosts is not supported!")
    else:
        psql_create_user(database_user, database_password)
        psql_create_db(database_name, database_user)

    if message_queue_host != "localhost":
        warning("message queue management on remote hosts is not supported!")
    else:
        rabbitmq_create_user(message_queue_user, message_queue_password)
        rabbitmq_create_vhost(message_queue_vhost, message_queue_user)
