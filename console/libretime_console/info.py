import click

from .utils import run_, which


@click.command()
def status():
    """
    Show the status of systemd services.
    """
    for service in (
        "libretime-api",
        "libretime-playout",
        "libretime-liquidsoap",
        "libretime-analyzer",
        "libretime-celery",
    ):
        cmd = run_(which("systemctl"), "status", service)
        print(cmd.stdout)
