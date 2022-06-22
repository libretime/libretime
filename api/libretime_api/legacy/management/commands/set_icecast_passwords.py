from pathlib import Path
from typing import Optional, Tuple
from xml.etree import ElementTree

from django.core.management.base import BaseCommand, CommandParser

from libretime_api.core.models import StreamSetting
from libretime_api.core.models.preference import Preference


def get_passwords_from_config(
    config_filepath: Path,
) -> Tuple[Optional[str], Optional[str]]:
    root = ElementTree.fromstring(config_filepath.read_text(encoding="utf-8"))  # nosec

    def _get_text(path) -> Optional[str]:
        element = root.find(path)
        return element.text if element is not None else None

    return (
        _get_text("./authentication/admin-password"),
        _get_text("./authentication/source-password"),
    )


class Command(BaseCommand):
    help = "Configure icecast passwords in the database."

    def add_arguments(self, parser: CommandParser):
        parser.add_argument(
            "--from-icecast-config",
            help="Get passwords from the Icecast configuration file",
            nargs="?",
            const="/etc/icecast2/icecast.xml",
            type=str,
        )
        parser.add_argument(
            "--admin-password",
            help="Icecast admin password",
            type=str,
        )
        parser.add_argument(
            "--source-password",
            help="Icecast source password",
            type=str,
        )

    def handle(self, *args, **options):
        icecast_config = options.get("from_icecast_config")
        admin_password = options.get("admin_password")
        source_password = options.get("source_password")

        if icecast_config is not None:
            config_passwords = get_passwords_from_config(Path(icecast_config))
            if admin_password is None and config_passwords[0] is not None:
                admin_password = config_passwords[0]
            if source_password is None and config_passwords[1] is not None:
                source_password = config_passwords[1]

        if source_password is not None:
            Preference.objects.update_or_create(
                key="default_icecast_password",
                defaults={"value": source_password},
            )

        for key in ["s1", "s2", "s3", "s4"]:
            if admin_password is not None:
                StreamSetting.objects.update_or_create(
                    key=f"{key}_admin_pass",
                    defaults={"value": admin_password},
                )
            if source_password is not None:
                StreamSetting.objects.update_or_create(
                    key=f"{key}_pass",
                    defaults={"value": source_password},
                )
