import click

from .db import db
from .icecast import icecast
from .info import status
from .mq import mq
from .setup import setup


@click.group()
def cli():
    pass


cli.add_command(db)
cli.add_command(icecast)
cli.add_command(mq)
cli.add_command(setup)
cli.add_command(status)
