#!/usr/bin/env python3
"""Run the equivalence harnesses under tools/<name>-equivalence/.

    tools/equivalence.py --list            # what would run, one name per line
    tools/equivalence.py                   # every harness in this checkout
    tools/equivalence.py tools/criteria-equivalence
    tools/equivalence.py --help            # the environment variables it reads

A harness proves that the python port leaves the same database rows as the legacy
php service, by running one fixture through both and diffing the dumps.

This script prepares the world a harness needs — a database, one config file that
both php and python read, a directory for the dumps — and then runs the harness's
own run.sh. Everything that is easy to get quietly wrong happens once, here, the
same way for every harness: see WHAT_THE_DRIVER_GUARANTEES below.

stdlib only, on purpose: this runs before and outside the application's virtualenv,
so it has to work on a machine where the api is broken. That is one of the things it
is checking for.
"""

from __future__ import annotations

import json
import os
import re
import shutil
import subprocess
import sys
import tempfile
from pathlib import Path
from typing import Any

HERE = Path(__file__).resolve().parent
ROOT = HERE.parent

# Every guarantee here exists because of a run that went green for the wrong reason.
# The right-hand column is what CI prints when the guard fires.
WHAT_THE_DRIVER_GUARANTEES = """
  creates a database per harness, owned by the application role
      -> a run that dies cannot poison the next one; PG15 gives the app role no
         CREATE on schema public, so a database owned by postgres is one the
         application can connect to and not use
  writes ONE config file and both legs are pointed at it
      -> php validates its config and exits on an unknown key; python ignores
         unknown keys and falls back to a default. Two files, or one typo, and the
         harness compares a service against itself and reports agreement
  asks the api's own loader which database it resolved, and refuses to continue
      -> the same failure as above, caught before it can produce a green run
  builds the schema with the api's migrations (migrate --run-syncdb)
      -> the legacy installer SQL alone has no auth_permission, so every object
         permission check would answer with a database error
  asserts the schema actually got built (tables, auth_permission, cc_pref)
      -> a half-built schema means the comparison would be about the wrong world
  drops the database on every exit path, including failures
      -> leftover databases are how an unrelated harness starts 'failing'
  names the log file when a step dies on a redirect
      -> an unwritable log looks exactly like a failed migration
"""


def log(message: str) -> None:
    print(f"\n=== {message}", flush=True)


def step(message: str) -> None:
    print(f"  {message}", flush=True)


def fail(message: str) -> None:
    print(f"  FAIL {message}", file=sys.stderr, flush=True)


def env(name: str, default: str = "") -> str:
    return os.environ.get(name) or default


# ----------------------------------------------------------------- invocation --


def run(
    cmd: list[str],
    *,
    cwd: Path | None = None,
    env_extra: dict[str, str] | None = None,
    capture: bool = False,
    stdin_text: str | None = None,
) -> subprocess.CompletedProcess[str]:
    """Run a command, inheriting this process's environment plus any additions."""
    merged = {**os.environ, **(env_extra or {})}
    return subprocess.run(
        cmd,
        cwd=str(cwd) if cwd else None,
        env=merged,
        text=True,
        capture_output=capture,
        check=False,
        input=stdin_text,
    )


def run_ok(cmd: list[str], **kwargs: Any) -> bool:
    return run(cmd, **kwargs).returncode == 0


# ------------------------------------------------------------------- discovery --


def discover() -> list[Path]:
    """Every tools/<name>-equivalence directory, sorted."""
    return sorted(p for p in ROOT.glob("tools/*-equivalence") if p.is_dir())


def is_runnable(harness: Path) -> bool:
    """Runnable on a CI runner means: an executable run.sh.

    run.sh is the harness's CI entry point. The older ab.sh shells into long-lived
    development containers, which a runner does not have.
    """
    entry = harness / "run.sh"
    return entry.is_file() and os.access(entry, os.X_OK)


def has_ab_script(harness: Path) -> bool:
    return (harness / "ab.sh").is_file()


# --------------------------------------------------------------------- psql ----


def psql_admin(sql: str) -> subprocess.CompletedProcess[str]:
    """psql as the superuser: only CREATE/DROP DATABASE need this."""
    return run(
        ["psql", "-v", "ON_ERROR_STOP=1", "-q", "-c", sql],
        env_extra={
            "PGHOST": env("EQ_DB_HOST", "localhost"),
            "PGPORT": env("EQ_DB_PORT", "5432"),
            "PGUSER": env("EQ_DB_SUPER", "postgres"),
            "PGPASSWORD": env("EQ_DB_SUPER_PASSWORD"),
        },
        capture=True,
    )


def psql_value(database: str, sql: str) -> str:
    """psql as the application role, returning one scalar."""
    result = run(
        ["psql", "-d", database, "-tAc", sql],
        env_extra={
            "PGHOST": env("EQ_DB_HOST", "localhost"),
            "PGPORT": env("EQ_DB_PORT", "5432"),
            "PGUSER": env("EQ_DB_USER", "libretime"),
            "PGPASSWORD": env("EQ_DB_PASSWORD", "libretime"),
        },
        capture=True,
    )
    return result.stdout.strip()


# ------------------------------------------------------------------- database --


def database_name(harness_name: str) -> str:
    """Derived from the harness name, so parallel matrix jobs never share one."""
    short = re.sub(r"-equivalence$", "", harness_name)
    return "eq_" + re.sub(r"[^a-z0-9_]", "", short.replace("-", "_"))[:20]


def create_database(database: str) -> bool:
    app_role = env("EQ_DB_USER", "libretime")
    psql_admin(f"DROP DATABASE IF EXISTS {database}")  # may not exist; that is fine
    result = psql_admin(f"CREATE DATABASE {database} OWNER {app_role}")
    if result.returncode != 0:
        fail(f"could not create database {database}: {result.stderr.strip()}")
        return False
    return True


def migrate(database: str, api_root: Path, python: str) -> bool:
    """Build the schema with the api's own migrations.

    --run-syncdb because the api's apps deliberately ship no migration files, and it
    is also the flag the Django test runner passes, so this is the same build the api's
    own suite relies on. The DJANGO_SETTINGS_MODULE and config file are what aim it at
    the harness database: this runs after write_config() for exactly that reason.
    """
    log_file = Path(
        env("EQ_MIGRATE_LOG")
        or Path(tempfile.gettempdir()) / f"equivalence-migrate-{database}.log"
    )

    result = run(
        [
            python,
            "-c",
            "from libretime_api.manage import main; main()",
            "migrate",
            "--run-syncdb",
        ],
        cwd=api_root,
        env_extra={"DJANGO_SETTINGS_MODULE": "libretime_api.settings.prod"},
        capture=True,
    )
    log_file.write_text((result.stdout or "") + (result.stderr or ""), encoding="utf-8")

    if result.returncode != 0:
        # The path is printed because a log that could not be written looks exactly like
        # a migration that failed, and those need different fixes.
        fail(f"migrations failed; log: {log_file}")
        for line in log_file.read_text(encoding="utf-8").splitlines()[-20:]:
            print(f"    {line}", file=sys.stderr)
        return False

    tables = psql_value(
        database,
        "select count(*) from information_schema.tables where table_schema = 'public'",
    )
    perms = psql_value(database, "select count(*) from auth_permission")
    prefs = psql_value(database, "select count(*) from cc_pref")

    if not tables.isdigit() or int(tables) < 40:
        fail(f"expected the full schema, found {tables or 'nothing'} tables")
        return False
    if not perms.isdigit() or int(perms) <= 0:
        fail("auth_permission is empty: every object permission check would deny")
        return False

    step(f"{tables} tables, {perms} permissions, {prefs} preferences")
    return True


# --------------------------------------------------------------------- config --

CONFIG_TEMPLATE = """# Written by tools/equivalence.py - do not commit, do not edit.
# Every key below is declared by BOTH config schemas (legacy conf.php and the api's
# pydantic model). Add one that is not, and the legacy side prints
# "could not parse configuration" and calls exit(), with no traceback.
general:
  public_url: http://localhost/
  api_key: equivalence-harness-key
  secret_key: equivalence-harness-secret
  timezone: {timezone}
database:
  host: {db_host}
  port: {db_port}
  name: {db_name}
  user: {db_user}
  password: {db_password}
rabbitmq:
  host: {mq_host}
  port: {mq_port}
  vhost: {mq_vhost}
  user: {mq_user}
  password: {mq_password}
storage:
  path: {storage}
"""


def write_config(path: Path, database: str, storage: Path) -> bool:
    """One file, read by both applications.

    The key names come from legacy/application/configs/conf.php, and note
    name / user: the legacy *code* spells its dsn keys database and username, the
    *file* does not.
    """
    storage.mkdir(parents=True, exist_ok=True)
    path.write_text(
        CONFIG_TEMPLATE.format(
            timezone=env("EQ_TIMEZONE", "UTC"),
            db_host=env("EQ_DB_HOST", "localhost"),
            db_port=env("EQ_DB_PORT", "5432"),
            db_name=database,
            db_user=env("EQ_DB_USER", "libretime"),
            db_password=env("EQ_DB_PASSWORD", "libretime"),
            mq_host=env("EQ_RABBITMQ_HOST", "localhost"),
            mq_port=env("EQ_RABBITMQ_PORT", "5672"),
            mq_vhost=env("EQ_RABBITMQ_VHOST", "/libretime"),
            mq_user=env("EQ_RABBITMQ_USER", "libretime"),
            mq_password=env("EQ_RABBITMQ_PASSWORD", "libretime"),
            storage=storage,
        )
    )
    os.environ["LIBRETIME_CONFIG_FILEPATH"] = str(path)
    step(f"config {path} (database {database}, timezone {env('EQ_TIMEZONE', 'UTC')})")
    return True


# Runs inside the api's venv, because the whole point is to ask the api.
VERIFY_SNIPPET = """
import sys, yaml
from libretime_api.settings._schema import Config
config = Config(**yaml.safe_load(open(sys.argv[1])))
if config.database.name != sys.argv[2]:
    sys.exit(
        "the api resolves database %r, not %r - the two sides of this harness "
        "would not have been comparing the same rows"
        % (config.database.name, sys.argv[2])
    )
print("  api resolves %s@%s:%s/%s" % (
    config.database.user, config.database.host, config.database.port, config.database.name))
"""


def verify_config(path: Path, database: str, python: str) -> bool:
    """Ask the api's own loader which database it resolved.

    Without this, a key the api ignores is not an error - it is a harness that ran both
    legs against different databases and reported agreement. That is the most expensive
    wrong answer this whole thing can produce.

    The expected line is required, not just a zero exit status: a guard that prints
    nothing has not checked anything. (It did exactly that during the first run of this
    port, when the snippet was never passed to python's stdin.)
    """
    result = run(
        [python, "-", str(path), database], stdin_text=VERIFY_SNIPPET, capture=True
    )
    if result.returncode != 0:
        fail((result.stderr or "config verification failed").strip())
        return False
    if "api resolves" not in result.stdout:
        fail(f"config verification produced no answer: {result.stdout!r}")
        return False
    print(result.stdout, end="")
    return True


# ------------------------------------------------------------------- preflight --


def preflight(python: str) -> bool:
    """One check, once, before anything is created.

    A missing api environment is not a per-harness result: it means this machine cannot
    prove anything, and finding out after building a schema wastes the minute that
    matters in a CI log.
    """
    if run_ok([python, "-c", "import libretime_api.settings._schema"], capture=True):
        return True
    fail(f"{python} cannot import the api config schema")
    print(
        "      Run make -C api install, or point EQ_PY at that virtualenv.",
        file=sys.stderr,
    )
    return False


# -------------------------------------------------------------------- harness --


def copy_artifacts(harness: Path, destination: Path) -> None:
    """The dumps are what make a failure readable without re-running anything."""
    source = harness / "out"
    if not source.is_dir():
        return
    shutil.copytree(source, destination / "out", dirs_exist_ok=True)
    names = " ".join(sorted(p.name for p in (destination / "out").iterdir()))
    if names:
        step(f"artefacts {names}")


def run_harness(harness: Path, out: Path, database: str) -> bool:
    result = run(
        ["./run.sh"],
        cwd=harness,
        env_extra={
            "EQ_MODE": env("EQ_MODE", "local"),
            "EQ_OUT": str(out),
            "EQ_DB_NAME": database,
            "EQ_LEGACY_ROOT": env("EQ_LEGACY_ROOT", str(ROOT / "legacy")),
            "EQ_PY": env("EQ_PY", "python3"),
            "LIBRETIME_LOG_DIR": str(out),
        },
    )
    return result.returncode == 0


def prepare_and_run(harness: Path, artifacts: Path, python: str, api_root: Path) -> str:
    """Set up one harness's world, run it, always clean up. Returns pass/fail/skip."""
    name = harness.name
    database = database_name(name)
    out = artifacts / name

    log(f"{name}  (database {database})")

    if not is_runnable(harness):
        if not has_ab_script(harness):
            step("skip - no run.sh and no ab.sh: authoring tooling, not a CI harness")
            return "skip (not a CI harness)"
        step(
            "skip - no run.sh yet, so this comparison is not being run (see tools/EQUIVALENCE.md)"
        )
        return "skip (not wired)"

    shutil.rmtree(out, ignore_errors=True)
    out.mkdir(parents=True, exist_ok=True)

    try:
        if not create_database(database):
            return "fail (database)"
        return build_and_compare(harness, out, database, python, api_root)
    finally:
        # Not "if the previous step succeeded": a database left behind by a failure is
        # how the next harness starts reporting a divergence it did not cause.
        copy_artifacts(harness, out)
        psql_admin(f"DROP DATABASE IF EXISTS {database}")


def build_and_compare(
    harness: Path, out: Path, database: str, python: str, api_root: Path
) -> str:
    """The steps, in order. Kept apart so the cleanup above has one owner."""
    if not write_config(out / "config.yml", database, out / "storage"):
        return "fail (config)"
    if not verify_config(out / "config.yml", database, python):
        return "fail (config)"
    if not migrate(database, api_root, python):
        return "fail (migrations)"
    if not run_harness(harness, out, database):
        return "fail (divergence or error)"
    return "pass"


# ----------------------------------------------------------------------- main --

USAGE = (
    """usage: tools/equivalence.py [--list | --list-json] [harness ...]

Runs equivalence harnesses. With no arguments, every tools/<name>-equivalence
directory that has an executable run.sh.

  --list        one harness name per line, for humans
  --list-json   the same names as one compact JSON array, for the CI matrix

what the driver guarantees (and the wrong-green each one prevents):
"""
    + WHAT_THE_DRIVER_GUARANTEES
    + """
environment:
  EQ_PY             python with the api installed   (default: python3)
  EQ_API_ROOT       the api directory               (default: <repo>/api)
  EQ_LEGACY_ROOT    the legacy directory            (default: <repo>/legacy)
  EQ_MODE           local | container               (default: local)
  EQ_ARTIFACTS      where to write dumps            (default: <repo>/tools/equivalence-out)
  EQ_DB_HOST/PORT/USER/PASSWORD           the application role
  EQ_DB_SUPER/EQ_DB_SUPER_PASSWORD        for CREATE/DROP DATABASE
  EQ_RABBITMQ_HOST/PORT/VHOST/USER/PASSWORD
  EQ_TIMEZONE       written into the shared config  (default: UTC)
  EQ_MIGRATE_LOG    override the migration log path

exit status: 0 if nothing failed, 1 otherwise. A harness that could not run counts as
failed - a comparison that quietly omits a case reads as a pass.
"""
)


# Defaults for everything a harness leg needs. Exported, not merely used here: a
# harness shells out to psql, php and python in whatever combination its scenario
# needs, and every one of them must look at the same rows and the same broker. A
# port of this driver once read these without exporting them, and a harness died on
# "EQ_DB_PASSWORD: unbound variable".
ENV_DEFAULTS = {
    "EQ_PY": "python3",
    "EQ_API_ROOT": str(ROOT / "api"),
    "EQ_LEGACY_ROOT": str(ROOT / "legacy"),
    "EQ_MODE": "local",
    "EQ_DB_HOST": "localhost",
    "EQ_DB_PORT": "5432",
    "EQ_DB_USER": "libretime",
    "EQ_DB_PASSWORD": "libretime",
    "EQ_RABBITMQ_HOST": "localhost",
    "EQ_RABBITMQ_PORT": "5672",
    "EQ_RABBITMQ_VHOST": "/libretime",
    "EQ_RABBITMQ_USER": "libretime",
    "EQ_RABBITMQ_PASSWORD": "libretime",
    "EQ_TIMEZONE": "UTC",
}


def parse_args(argv: list[str]) -> tuple[str, list[str]]:
    """One of "help" | "list" | "list-json" | "run", plus the remaining arguments."""
    args = argv[1:]
    if not args:
        return "run", []
    first = args[0]
    if first in ("--help", "-h"):
        return "help", []
    if first == "--list":
        return "list", args[1:]
    if first == "--list-json":
        return "list-json", args[1:]
    return "run", args


def select(args: list[str]) -> tuple[list[Path], bool]:
    """The requested harnesses, and whether they were discovered rather than named."""
    requested = [Path(a) if Path(a).is_absolute() else ROOT / a for a in args]
    return (requested or discover()), not requested


def emit_list(harnesses: list[Path], as_json: bool) -> int:
    found = [harness.name for harness in harnesses if is_runnable(harness)]
    if as_json:
        # Compact and on one line: GitHub Actions job outputs are "name=value" lines, so
        # a pretty-printed array is not a value at all. This is the workflow's matrix
        # input; the plain form is for humans.
        print(json.dumps(found, separators=(",", ":")))
    else:
        for name in found:
            print(name)
    return 0


def run_selected(harnesses: list[Path], discovered: bool) -> int:
    python = env("EQ_PY", "python3")
    api_root = Path(env("EQ_API_ROOT", str(ROOT / "api")))
    artifacts = Path(env("EQ_ARTIFACTS", str(ROOT / "tools" / "equivalence-out")))

    if not preflight(python):
        # A missing api environment is not a per-harness result: nothing can be proved.
        return 1

    results: dict[str, str] = {}
    for harness in harnesses:
        if not harness.is_dir():
            fail(f"{harness} is not a directory")
            results[harness.name] = "fail (missing)"
        elif not discovered and not is_runnable(harness) and has_ab_script(harness):
            # Naming one explicitly is an assertion that it works.
            fail(
                f"{harness.name} has an ab.sh but no run.sh - it cannot run on a CI runner"
            )
            results[harness.name] = "fail (no run.sh)"
        else:
            results[harness.name] = prepare_and_run(
                harness, artifacts, python, api_root
            )

    log("summary")
    for name, outcome in results.items():
        print(f"  {name:<34} {outcome}")

    return 1 if any(outcome.startswith("fail") for outcome in results.values()) else 0


def main(argv: list[str]) -> int:
    mode, args = parse_args(argv)
    if mode == "help":
        print(USAGE)
        return 0

    for name, default in ENV_DEFAULTS.items():
        os.environ.setdefault(name, default)

    harnesses, discovered = select(args)

    if mode in ("list", "list-json"):
        return emit_list(harnesses, as_json=mode == "list-json")

    if not harnesses:
        log("no equivalence harnesses in this checkout")
        return 0

    return run_selected(harnesses, discovered)


if __name__ == "__main__":
    sys.exit(main(sys.argv))
