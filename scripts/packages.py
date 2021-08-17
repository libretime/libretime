#!/usr/bin/env python3

import json
import sys
from argparse import ArgumentParser
from configparser import ConfigParser
from os import PathLike
from pathlib import Path
from typing import Iterator, Set

DEFAULT_PACKAGES_FILENAME = "packages.ini"
FORMATS = ("list", "line")
SYSTEMS = ("buster", "bionic")


def load_packages(raw: str, distribution: str) -> Set[str]:
    manager = ConfigParser(default_section="common")
    manager.read_string(raw)

    packages = set()
    for section, entries in manager.items():
        for package, distributions in entries.items():
            if distribution in distributions.split(", "):
                packages.add(package)

    return packages


def list_packages_files(paths: str) -> Iterator[Path]:
    for path in paths:
        path = Path(path)

        if path.is_dir():
            path = path / DEFAULT_PACKAGES_FILENAME

        if not path.is_file():
            raise Exception(f"{path} is not a file!")

        yield path


def list_packages(paths: str, distribution: str) -> Set[str]:
    packages = set()
    for package_file in list_packages_files(paths):
        packages.update(load_packages(package_file.read_text(), distribution))

    return set(sorted(packages))


def run():
    parser = ArgumentParser()
    parser.add_argument(
        "-f",
        "--format",
        choices=FORMATS,
        help="print packages list in a specific format.",
        default="list",
    )
    parser.add_argument(
        "distribution",
        choices=SYSTEMS,
        help="list packages for the given distribution.",
    )
    parser.add_argument(
        "path",
        nargs="+",
        help="list packages from given files or directories.",
    )
    args = parser.parse_args()

    packages = list_packages(args.path, args.distribution)

    if args.format == "list":
        print("\n".join(packages))
    else:
        print(" ".join(packages))


if __name__ == "__main__":
    run()
