#!/usr/bin/env python3
"""
Compare two captures made with capture.py.

The reference capture is the one published by the legacy service, so that the
messages the API publishes can be proven to be what the services already
consume:

    python compare_rabbitmq.py --legacy legacy.jsonl --api api.jsonl

Bodies have to be byte identical. Message properties are compared too, and
are reported separately since a producer may add properties the consumers
ignore, like the content encoding kombu sets.
"""

import argparse
import json
from typing import Any


def load(path: str) -> dict[str, dict[str, Any]]:
    """Index a capture by the message body, the identity of a notification."""
    messages = {}

    with open(path, encoding="utf-8") as handle:
        for line in handle:
            record = json.loads(line)
            messages[f'{record["exchange"]}\n{record["body"]}'] = record

    return messages


def compare(legacy_path: str, api_path: str) -> int:
    legacy = load(legacy_path)
    api = load(api_path)

    failures = 0

    for key, message in sorted(legacy.items()):
        body, exchange = message["body"], message["exchange"]
        other = api.get(key)

        if other is None:
            print(f"MISSING  {exchange} {body}")
            failures += 1

            continue

        missing = set(message["properties"]) ^ set(other["properties"])
        if not missing:
            print(f"IDENTICAL {exchange} {body}")

            continue

        legacy_only = set(message["properties"]) - set(other["properties"])
        api_only = set(other["properties"]) - set(message["properties"])
        differing = {
            name
            for name in set(message["properties"]) & set(other["properties"])
            if message["properties"][name] != other["properties"][name]
        }

        print(f"PROPERTIES {exchange} {body}")

        for name in sorted(legacy_only):
            print(f"           only legacy: {name}={message['properties'][name]!r}")

        for name in sorted(api_only):
            print(f"           only api:    {name}={other['properties'][name]!r}")

        for name in sorted(differing):
            print(
                f"           differs:     {name}"
                f" legacy={message['properties'][name]!r}"
                f" api={other['properties'][name]!r}",
            )

    for key, message in sorted(api.items()):
        if key not in legacy:
            print(f"EXTRA     {message['exchange']} {message['body']}")
            failures += 1

    print(
        f"\n{len(legacy)} legacy message(s), {len(api)} api message(s), {failures} missing"
    )

    return failures


def main() -> None:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--legacy", required=True, help="capture of the legacy service")
    parser.add_argument("--api", required=True, help="capture of the api")
    args = parser.parse_args()

    raise SystemExit(1 if compare(args.legacy, args.api) else 0)


if __name__ == "__main__":
    main()
