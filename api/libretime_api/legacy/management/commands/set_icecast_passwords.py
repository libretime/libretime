import os

from django.core.management.base import BaseCommand, CommandParser

from libretime_api.core.models import StreamSetting


class Command(BaseCommand):
    help = "Configure icecast passwords in the database."

    def add_arguments(self, parser: CommandParser):
        parser.add_argument(
            "--admin-password",
            help="Icecast admin password",
            default=os.environ.get("LIBRETIME_ICECAST_ADMIN_PASSWORD"),
            type=str,
        )
        parser.add_argument(
            "--source-password",
            help="Icecast source password",
            default=os.environ.get("LIBRETIME_ICECAST_SOURCE_PASSWORD"),
            type=str,
        )

    def handle(self, *args, **options):
        admin_password = options.get("admin_password")
        source_password = options.get("source_password")

        for key in ["s1", "s2", "s3", "s4"]:
            if admin_password is not None:
                StreamSetting.objects.update_or_create(
                    keyname=f"{key}_admin_pass",
                    defaults={"value": admin_password},
                )
            if source_password is not None:
                StreamSetting.objects.update_or_create(
                    keyname=f"{key}_pass",
                    defaults={"value": source_password},
                )
