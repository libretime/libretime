# shellcheck shell=bash
#
# Shared pieces for the equivalence harnesses.
#
# A harness proves that the python port leaves the same database rows as the legacy
# php service, by running the same fixture through both and diffing the result. They
# were written against long-lived development containers, where the station's own
# database is available to clone a schema from. A CI runner has no station — so this
# library builds the same starting point out of what the repository already contains,
# which is the exact pair of files the legacy test suite installs
# (legacy/tests/application/helpers/AirtimeInstall.php reads them from the api).
#
# Sourcing this file does nothing on its own; see equivalence-run.sh for the driver.

EQ_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
EQ_API_ROOT="${EQ_API_ROOT:-$EQ_ROOT/api}"

eq_log() { printf '\n=== %s\n' "$*"; }
eq_die() {
           printf 'equivalence: %s\n' "$*" >&2
                                                exit 1
}

# ------------------------------------------------------------------ database ---

# The harness gets its own database, always. Not optional: the show editor writes the
# global shows_populated_until preference, and a station — or a shared CI database —
# that woke up believing its calendar was already built to 2044 would stop populating
# shows. That is a self-inflicted outage, not a test.
eq_db_create() {
    local dbname="$1"

    eq_log "database $dbname"
    eq_psqladmin -q -c "DROP DATABASE IF EXISTS $dbname" > /dev/null 2>&1
    # Owned by the application role, not created by the superuser and left that way:
    # since PostgreSQL 15 the PUBLIC role no longer has CREATE on schema public, so a
    # database owned by postgres is one the application can connect to and not use. The
    # legacy CI setup hits the same thing and fixes it with ALTER DATABASE ... OWNER TO;
    # creating it owned is the same fact stated once, earlier.
    eq_psqladmin -q -c "CREATE DATABASE $dbname OWNER ${EQ_DB_USER:-libretime}"
}

# Built with the api's own migrations rather than by loading the installer SQL directly.
#
# The difference matters, and the permission harness is what found it:
# legacy/migrations/0001 installs sql/schema.sql and sql/data.sql, and the migrations
# after it replay every schema change a station has been through since — so migrating
# produces the schema a running station actually has, not the one a first install had.
# More importantly, the api's own tables (django_content_type, auth_permission,
# core_user, sessions) exist only as Django models: load the legacy SQL alone and every
# permission check in the api raises `relation "auth_permission" does not exist`.
#
# --run-syncdb because the api's apps deliberately ship no migration files, and it is
# also the flag the Django test runner passes when it builds a test database — so this
# is the same build the api's own suite relies on.
# Runs the api's migrations against the database named by the config file the driver just
# wrote, from the api directory, and restores the working directory afterwards.
#
# Not a `( … ) > file` group: shfmt's -kp pads the opening paren of a grouped redirection
# with trailing whitespace, `trim trailing whitespace` then takes it away, and the two
# hooks never agree on the same file. cd-and-restore is the same thing stated plainly.
_run_migrations() {
    local log="$1" previous status

    previous="$PWD"

    cd "$EQ_API_ROOT" || return 1

    DJANGO_SETTINGS_MODULE=libretime_api.settings.prod \
        "$EQ_PY" -c "from libretime_api.manage import main; main()" \
        migrate --run-syncdb > "$log" 2>&1

    status=$?

    cd "$previous" || return 1

    return "$status"
}

eq_db_migrate() {
    local dbname="$1" log tables perms prefs

    # The process id is in the name so that a log left behind by somebody else's run —
    # mine, in a container, as root — cannot make this one fail on the redirect instead
    # of on the migration.
    log="${EQ_MIGRATE_LOG:-${TMPDIR:-/tmp}/equivalence-migrate-$$-$dbname.log}"

    eq_log "migrating $dbname (api migrations, with syncdb)"

    # The database named by the config file the driver wrote a moment ago, which is why
    # this runs after eq_config_write and not before it.
    if ! _run_migrations "$log"; then
        # The path is named because a redirect that could not be created looks exactly
        # like a migration that failed, and that distinction is the whole diagnostic.
        printf '  migrations failed; last lines of %s:\n' "$log" >&2
        tail -20 "$log" >&2
        return 1
  fi

    tables=$(eq_psql -d "$dbname" -tAc \
        "select count(*) from information_schema.tables where table_schema = 'public'")
    perms=$(eq_psql -d "$dbname" -tAc "select count(*) from auth_permission")
    prefs=$(eq_psql -d "$dbname" -tAc "select count(*) from cc_pref")

    # A half-built database is the failure worth refusing to run against: the port would
    # write rows into a schema it does not expect, and the comparison would then report
    # agreement about the wrong thing.
    if [ "${tables:-0}" -lt 40 ]; then
        printf '  expected the full schema, found %s tables\n' "${tables:-0}" >&2
        return 1
  fi

    if [ "${perms:-0}" -le 0 ]; then
        printf '  auth_permission is empty: every object permission would deny\n' >&2
        return 1
  fi

    printf '  %s tables, %s permissions, %s preferences\n' "$tables" "$perms" "$prefs"
}

eq_db_drop() {
    eq_log "dropping $1"
    eq_psqladmin -q -c "DROP DATABASE IF EXISTS $1" > /dev/null || true
}

# psql as the superuser role, for CREATE/DROP DATABASE, and as the application role for
# everything else. The two are separate because the legacy code connects as the
# application and must not be able to create databases.
eq_psqladmin() {
    PGHOST="${EQ_DB_HOST:-localhost}" PGPORT="${EQ_DB_PORT:-5432}" \
    PGPASSWORD="${EQ_DB_SUPER_PASSWORD:-}" \
        psql -U "${EQ_DB_SUPER:-postgres}" -d postgres -v ON_ERROR_STOP=1 "$@"
}

eq_psql() {
    PGHOST="${EQ_DB_HOST:-localhost}" PGPORT="${EQ_DB_PORT:-5432}" \
    PGPASSWORD="${EQ_DB_PASSWORD:-libretime}" \
        psql -U "${EQ_DB_USER:-libretime}" "$@"
}

# -------------------------------------------------------------------- config ---

# One config file, read by both applications — LibreTime's config format is shared
# between php and python on purpose, and the harness depends on it: two files would be
# two chances for the two sides to be pointed at different databases while the harness
# cheerfully compares a service against itself.
#
# The keys are exactly the ones both schemas declare, and that is not a style choice:
# the legacy side validates its config (legacy/application/configs/conf.php builds a
# Symfony config tree) and on an undeclared key it prints "could not parse
# configuration" and calls exit() — a harness dying with no traceback, in a CI log, three
# steps away from the cause. The api side is a pydantic model that ignores unknown keys, so a typo
# there is worse than a typo in php: it does not fail, it silently falls back to a
# default, which is exactly how a harness ends up pointing one side at the station's
# database. Hence eq_config_verify below.
#
# Verified key names:
#   general.public_url|api_key|secret_key|timezone  conf.php:35-48 (required, no default)
#   database.host|port|name|user|password           conf.php:51-57, shared DatabaseConfig
#   rabbitmq.host|port|vhost|user|password          conf.php:60-66
#   storage.path                                    conf.php:74-78, and Config::load()
#                                                   quits unless that directory exists
#                                                   and is writable
# Note "name"/"user", not "dbname"/"username": the legacy *code* uses $CC_CONFIG['dsn']
# keys called database and username, but the *file* spells them name and user.
eq_config_write() {
    local path="$1" dbname="$2" storagedir="$3"

    mkdir -p "$storagedir"

    cat > "$path" << CONFIG
# Generated by tools/equivalence-lib.sh — do not commit, do not edit.
# Every key here is declared by BOTH config schemas; add one and the legacy side exits.
general:
  public_url: http://localhost/
  api_key: equivalence-harness-key
  secret_key: equivalence-harness-secret
  timezone: ${EQ_TIMEZONE:-UTC}
database:
  host: ${EQ_DB_HOST:-localhost}
  port: ${EQ_DB_PORT:-5432}
  name: $dbname
  user: ${EQ_DB_USER:-libretime}
  password: ${EQ_DB_PASSWORD:-libretime}
rabbitmq:
  host: ${EQ_RABBITMQ_HOST:-localhost}
  port: ${EQ_RABBITMQ_PORT:-5672}
  vhost: ${EQ_RABBITMQ_VHOST:-/libretime}
  user: ${EQ_RABBITMQ_USER:-libretime}
  password: ${EQ_RABBITMQ_PASSWORD:-libretime}
storage:
  path: $storagedir
CONFIG

    export LIBRETIME_CONFIG_FILEPATH="$path"
    printf '  config: %s (database %s, timezone %s)\n' "$path" "$dbname" "${EQ_TIMEZONE:-UTC}"
}

# The api side is asked what database it thinks it is talking about, in its own loader.
# Without this the failure mode of a renamed key is not an error but a harness that ran
# both legs against different databases and reported agreement — the single most
# expensive kind of test result to be wrong about.
#
# Non-zero rather than exit: the caller owns cleanup, and a harness that cannot be
# trusted to have aimed both sides at one database should be reported as a failed
# harness and have its database dropped, not abort the run and leave that database for
# the next harness to trip over.
eq_config_verify() {
    local path="$1" dbname="$2"

    # "-" so that python reads the program from stdin and the two arguments land in
    # sys.argv; handing it the config path instead makes python try to *execute* the yaml,
    # which is a confusing way to discover the guard was never running.
    "$EQ_PY" - "$path" "$dbname" << 'PY'
import sys

import yaml

try:
    from libretime_api.settings._schema import Config
except ImportError:
    sys.exit("EQ_PY cannot import libretime_api.settings._schema")

config = Config(**yaml.safe_load(open(sys.argv[1])))
if config.database.name != sys.argv[2]:
    sys.exit(
        f"the api would connect to database {config.database.name!r}, not "
        f"{sys.argv[2]!r} — the two sides of this harness would not have been "
        f"comparing the same rows"
    )
print(
    f"  api side: {config.database.user}@{config.database.host}"
    f":{config.database.port}/{config.database.name}"
)
PY
}

# Checked once, before anything is created: a missing api environment is not a per-harness
# result to be tabulated, it means the machine is not set up, and discovering that after
# building a schema wastes the minute that matters in a CI job.
eq_preflight() {
    "$EQ_PY" -c "import libretime_api.settings._schema" > /dev/null 2>&1 || {
        printf 'equivalence: %s cannot import the api config schema.\n' "$EQ_PY" >&2
        printf '           Run make -C api install, or point EQ_PY at that virtualenv.\n' >&2
        printf '           The harnesses compare php against python over one database, and\n' >&2
        printf '           without the api importable they cannot prove that they did.\n' >&2
        return 1
  }
}

# ----------------------------------------------------------------- artefacts ---

# The dumps are what make a failure readable from a CI log: "the weekly scenario
# differs" is not an actionable sentence, and a reviewer should not have to re-run
# anything to find out which column.
eq_artifacts() {
    local dest="$1" harness="$2"

    mkdir -p "$dest"
    if [ -d "$harness/out" ]; then
        # -R, not -r: shellcheck points out that -r's behaviour with symlinks is
        # implementation-defined, and the dumps directory is exactly where a stray link
        # would turn a copied artefact into a copied whole filesystem.
        cp -R "$harness/out" "$dest/" 2> /dev/null || true
        printf '  artefacts: %s\n' "$(ls "$dest/out" | tr '\n' ' ')"
  fi
}
