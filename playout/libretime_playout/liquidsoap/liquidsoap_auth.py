from argparse import ArgumentParser
from typing import Literal

from libretime_api_client.v1 import ApiClient as LegacyClient


def main(input_name: Literal["main", "show"], username: str, password: str) -> int:
    legacy_client = LegacyClient()

    input_name_map = {"main": "master", "show": "dj"}
    response: dict = legacy_client.check_live_stream_auth(
        username,
        password,
        input_name_map[input_name],
    )

    if response.get("msg", False) is True:
        return 0
    return 1


if __name__ == "__main__":
    parser = ArgumentParser()

    parser.add_argument("input_name", choices=["main", "show"])
    parser.add_argument("username")
    parser.add_argument("password")
    args = parser.parse_args()

    raise SystemExit(
        main(
            input_name=args.input_name,
            username=args.username,
            password=args.password,
        )
    )
