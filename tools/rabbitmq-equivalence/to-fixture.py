#!/usr/bin/env python3
"""
Turn a capture of the legacy service into a golden fixture of the api tests.

    python capture.py --output legacy.jsonl --duration 20 &
    docker compose exec -T legacy php /tmp/publish.php   # or publish-legacy.php
    python to-fixture.py --capture legacy.jsonl --output ../../api/libretime_api/notify/tests/fixtures

The fixture records, per exchange, the topology and the message properties the
legacy producer used and the body it put on the wire. The recorder events are
left out: playout logs them as invalid commands, no consumer handles them.
"""

import argparse
import json
from typing import Any

# Not ported, kept out of the fixtures on purpose.
IGNORED_EVENTS = {"cancel_recording", "update_recorder_schedule"}


def case_of(message: dict[str, Any]) -> str | None:
    """Name a captured message after the case of the producer it came from."""
    payload = json.loads(message["body"])

    if message["exchange"] == "playout":
        event = payload["event_type"]

        if event in IGNORED_EVENTS:
            return None

        if event == "switch_source":
            return f'switch_source_{payload["status"]}'

        return event

    if "event_type" in payload:
        raise ValueError(f"unexpected event on {message['exchange']}: {payload['event_type']}")

    if not payload["options"]:
        return "analyze_file_without_track_type"

    return f"analyze_file_cue_points_{str(payload['options']['analyze_cue_points']).lower()}"


def build(capture: list[dict[str, Any]]) -> dict[str, dict[str, Any]]:
    fixtures: dict[str, dict[str, Any]] = {}

    for message in capture:
        case = case_of(message)
        if case is None:
            continue

        fixture = fixtures.setdefault(
            message["exchange"],
            {
                "exchange": message["exchange"],
                "type": message["exchange_type"],
                "durable": message["exchange_durable"],
                "auto_delete": message["exchange_auto_delete"],
                "routing_key": message["routing_key"],
                "properties": message["properties"],
                "messages": [],
            },
        )
        fixture["messages"].append({"case": case, "body": message["body"]})

    for fixture in fixtures.values():
        fixture["messages"].sort(key=lambda message: message["case"])

    return fixtures


def main() -> None:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--capture", required=True, help="jsonl file written by capture.py")
    parser.add_argument("--output", required=True, help="fixture directory to write")
    args = parser.parse_args()

    with open(args.capture, encoding="utf-8") as handle:
        capture = [json.loads(line) for line in handle if line.strip()]

    for name, fixture in build(capture).items():
        path = f"{args.output}/{name}.json"

        with open(path, "w", encoding="utf-8") as handle:
            json.dump(fixture, handle, indent=2, sort_keys=True)
            handle.write("\n")

        print(f" wrote {path} ({len(fixture['messages'])} message(s))")


if __name__ == "__main__":
    main()
