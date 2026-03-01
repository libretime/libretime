#!/usr/bin/env python3
# pylint: disable=invalid-name

import logging
from argparse import ArgumentParser
from os import environ
from subprocess import check_output
from typing import Any, Generator, List, Tuple

from requests import Session

logger = logging.getLogger("contributors")

REPOSITORY = "libretime/libretime"
EXCLUDED_CONTRIBUTORS = {
    "dependabot[bot]",
    "invalid-email-address",
    "libretime-bot",
    "renovate-bot",
    "renovate[bot]",
    "web-flow",
    "weblate",
}


def extract_date_range(commit_range: str) -> Tuple[str, str]:
    output = check_output(
        ["git", "log", "--reverse", "--format=%cI", commit_range], text=True
    )
    lines = output.splitlines()
    return lines[0], lines[-1]


def gh_get_commits(
    client: Session,
    since: str,
    until: str,
) -> Generator[dict[str, Any], None, None]:
    per_page = 100
    page = 1

    while True:
        logger.info("querying page %s", page)
        with client.get(
            f"https://api.github.com/repos/{REPOSITORY}/commits",
            params={  # type: ignore[arg-type]
                "per_page": per_page,
                "page": page,
                "since": since,
                "until": until,
            },
            timeout=5,
        ) as resp:
            resp.raise_for_status()
            commits: List[dict] = resp.json()
            yield from commits

            if len(commits) < per_page:
                break

            page += 1


def main(commit_range: str) -> int:
    client = Session()
    if "GITHUB_TOKEN" in environ:
        logger.info("loading GITHUB_TOKEN")
        github_token = environ["GITHUB_TOKEN"]
        client.headers.update({"Authorization": f"token {github_token}"})

    contributors = set()

    since, until = extract_date_range(commit_range)
    logger.info("%s: %s => %s", commit_range, since, until)

    for commit in gh_get_commits(client, since, until):
        if commit["author"] is None or commit["committer"] is None:
            continue

        try:
            author: str = commit["author"]["login"]
            committer: str = commit["committer"]["login"]
            contributors.add(author.casefold())
            contributors.add(committer.casefold())
        except (KeyError, TypeError) as exception:
            logger.error("%s: %s", exception, commit)

    contributors -= EXCLUDED_CONTRIBUTORS

    print()
    for contributor in sorted(contributors):
        print(f"- @{contributor}")
    print()

    return 0


if __name__ == "__main__":
    logging.basicConfig(
        level=logging.INFO,
        format="%(levelname)s:\t%(message)s",
    )

    parser = ArgumentParser()
    parser.add_argument("commit_range")

    args = parser.parse_args()
    raise SystemExit(main(commit_range=args.commit_range))
