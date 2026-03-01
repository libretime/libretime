#!/usr/bin/env python3
# pylint: disable=invalid-name

import subprocess
from argparse import (
    ArgumentDefaultsHelpFormatter,
    ArgumentParser,
    RawDescriptionHelpFormatter,
)
from contextlib import suppress


class ArgumentParserFormatter(
    RawDescriptionHelpFormatter,
    ArgumentDefaultsHelpFormatter,
):
    pass


def run():
    parser = ArgumentParser(
        description="Send a sine wave sound to an icecast mount or liquidsoap input harbor.",
        formatter_class=lambda prog: ArgumentParserFormatter(
            prog, max_help_position=60
        ),
    )
    parser.add_argument(
        "--url",
        metavar="<url>",
        help="""Stream <url> (<user>:<password>@<host>:<port>/<mount>) to test. If
                defined any other option will be ignored.""",
    )
    parser.add_argument(
        "--host",
        metavar="<host>",
        help="Stream <host> used to build the stream url.",
        default="localhost",
    )
    parser.add_argument(
        "--port",
        metavar="<port>",
        help="Stream <port> used to build the stream url.",
        default=8001,
    )
    parser.add_argument(
        "--mount",
        metavar="<mount>",
        help="Stream <mount> used to build the stream url.",
        default="main",
    )
    parser.add_argument(
        "--user",
        metavar="<user>",
        help="Stream <user> used to build the stream url.",
        default="source",
    )
    parser.add_argument(
        "--password",
        metavar="<password>",
        help="Stream <password> used to build the stream url.",
        default="hackme",
    )

    args = parser.parse_args()

    stream_url = args.url
    if stream_url is None:
        stream_url = f"icecast://{args.user}:{args.password}@{args.host}:{args.port}/{args.mount}"

    cmd = ["ffmpeg", "-hide_banner"]
    cmd.extend(["-re"])
    cmd.extend(["-f", "lavfi", "-i", "sine=frequency=1000"])
    cmd.extend(["-ar", "48000", "-ac", "2"])
    cmd.extend(["-f", "ogg"])
    cmd.extend(["-content_type", "application/ogg"])
    cmd.extend([stream_url])

    print(" ".join(cmd))
    with suppress(subprocess.CalledProcessError, KeyboardInterrupt):
        subprocess.run(cmd, check=True, text=True)


if __name__ == "__main__":
    run()
