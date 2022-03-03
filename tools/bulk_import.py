#!/usr/bin/env python3
# pylint: disable=missing-class-docstring,missing-function-docstring

import json
import sys
from argparse import ArgumentParser
from pathlib import Path
from typing import Any, Optional

import requests

DEFAULT_LOG_FILEPATH = Path("bulk_import.log")
ALLOWED_FILES_EXTENSIONS = (
    ".flac",
    ".m4a",
    ".mp3",
    ".ogg",
    ".opus",
    ".wav",
)


class Uploader:
    url: str
    auth_key: str
    track_type: Optional[str] = None
    log_filepath: Path = DEFAULT_LOG_FILEPATH

    def __init__(
        self,
        url: str,
        auth_key: str,
        track_type: Optional[str] = None,
        log_filepath: Optional[str] = None,
    ) -> None:
        self.url = url.rstrip("/")
        self.auth_key = auth_key
        self.track_type = track_type

        if log_filepath is not None:
            self.log_filepath = Path(log_filepath)

    def to_json_log(self, msg: Any) -> None:
        with self.log_filepath.open("a", encoding="utf-8") as file:
            print(json.dumps(msg), file=file)

    # pylint: disable=no-self-use
    def to_stderr(self, msg: Any) -> None:
        print(msg, file=sys.stderr)

    def upload_dir(self, path: Path) -> None:
        if not path.is_dir():
            raise ValueError(f"provided path '{path}' is not a directory!")

        for sub_path in path.iterdir():
            if sub_path.is_dir():
                self.upload_dir(sub_path)
                continue

            self.upload_file(sub_path.resolve())

    def upload_file(self, filepath: Path) -> None:
        if not filepath.is_file():
            raise ValueError(f"provided path '{filepath}' is not a file!")

        if filepath.suffix not in ALLOWED_FILES_EXTENSIONS:
            self.to_json_log({"status": "ignored", "filepath": str(filepath)})
            return

        try:
            resp = requests.post(
                f"{self.url}/rest/media",
                auth=(self.auth_key, ""),
                files=[
                    ("file", (filepath.name, filepath.open("rb"))),
                ],
                timeout=30,
                cookies={"tt_upload": self.track_type}
                if self.track_type is not None
                else {},
            )
            resp.raise_for_status()

        except requests.exceptions.HTTPError as exception:
            self.to_stderr(exception)
            self.to_json_log({"status": "failed", "filepath": str(filepath)})
            return

        self.to_stderr(f"uploaded: '{filepath}'")
        self.to_json_log({"status": "uploaded", "filepath": str(filepath)})


def main():
    parser = ArgumentParser(
        description="Scan a directory and import each files to the server."
    )
    parser.add_argument(
        "url",
        help="url of the server.",
    )
    parser.add_argument(
        "-a",
        "--auth-key",
        help="authentication key of the server.",
        required=True,
    )
    parser.add_argument(
        "path",
        help="directory to scan.",
    )

    parser.add_argument(
        "-t",
        "--track-type",
        help="track type for the files.",
    )
    parser.add_argument(
        "--log-filepath",
        help="log filepath for the files upload status.",
        default=DEFAULT_LOG_FILEPATH,
        type=Path,
    )
    args = parser.parse_args()

    uploader = Uploader(args.url, args.auth_key, args.track_type, args.log_filepath)
    uploader.upload_dir(Path(args.path).resolve())


if __name__ == "__main__":
    main()
