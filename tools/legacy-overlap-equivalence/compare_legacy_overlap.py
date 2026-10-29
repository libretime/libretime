#!/usr/bin/env python3
"""Compare the two sides' answers.

Exit 0 only when every fixture file got the same answer from php and from python.
A missing line is a failure, not an empty answer: a side that crashed halfway must
not be able to agree with nothing.

With EQ_SELFTEST=1 the expectation is deliberately perturbed first, and the harness
has to NOTICE. That is the negative control - it is the only way to show that a green
run here means something.
"""

import os
import sys

HERE_FIXTURES = "fixture"


def load(path: str) -> dict[str, str]:
    answers: dict[str, str] = {}
    with open(path) as handle:
        for line in handle:
            line = line.strip()
            if not line:
                continue
            ident, _, answer = line.partition("|")
            answers[ident] = answer
    return answers


def describe(expected: dict[str, str], actual: dict[str, str]) -> list[str]:
    problems: list[str] = []
    for ident in sorted(set(expected) | set(actual), key=int):
        left = expected.get(ident)
        right = actual.get(ident)
        if left is None:
            problems.append(f"{ident}: php did not answer")
        elif right is None:
            problems.append(f"{ident}: python did not answer")
        elif left != right:
            problems.append(f"{ident}: php says {left}, python says {right}")
    return problems


def main() -> int:
    php_path, python_path = sys.argv[1], sys.argv[2]
    php = load(php_path)
    python = load(python_path)

    if not php or not python:
        print(f"nothing to compare: php {len(php)} rows, python {len(python)} rows")
        return 1

    selftest = os.environ.get("EQ_SELFTEST") == "1"

    if not selftest:
        problems = describe(php, python)
        if problems:
            print("the two implementations disagree:")
            for problem in problems:
                print(f"  {problem}")
            return 1
        print(f"OK: both implementations agree on {len(php)} fixture files")
        return 0

    # Negative control: lie about one answer and require the comparison to catch it.
    victim = sorted(php, key=int)[0]
    perturbed = dict(php)
    perturbed[victim] = "false" if php[victim] == "true" else "true"

    caught = describe(perturbed, python)
    if caught:
        print(
            f"negative control: perturbed file {victim}, comparison reported -> {caught[0]}"
        )
        return 0

    print(f"negative control FAILED: a wrong answer for {victim} went unnoticed")
    return 1


if __name__ == "__main__":
    sys.exit(main())
