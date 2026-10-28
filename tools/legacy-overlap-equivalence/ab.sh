#!/usr/bin/env bash
#
# Compare the two implementations of "is this file still scheduled in the future".
#
#   ab.sh              both sides, then compare
#   ab.sh php          legacy side only
#   ab.sh python       python side only
#   ab.sh selftest     both sides, then prove the comparison can fail
#
# This scenario needs a real environment for both runtimes, which
# tools/equivalence.py provides: a database it built, one config file both sides read,
# EQ_PY for the api's virtualenv and EQ_DB_* for the connection. There is deliberately
# no container mode - a proof-of-concept that only runs on one person's laptop is the
# thing this whole setup exists to replace.
set -euo pipefail

here="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
out="$here/out"

: "${EQ_DB_NAME:?run this through tools/equivalence.py, which sets the database}"
: "${LIBRETIME_CONFIG_FILEPATH:?run this through tools/equivalence.py, which writes the config}"
: "${EQ_OUT:?run this through tools/equivalence.py, which sets the artefacts directory}"

legacy_root="$(cd "${EQ_LEGACY_ROOT:-$here/../../legacy}" && pwd)"
api_root="$(cd "${EQ_API_ROOT:-$here/../../api}" && pwd)"
py="${EQ_PY:-python3}"
php="${EQ_PHP:-php}"

mode="${1:-all}"

if [ ! -f "$legacy_root/vendor/autoload.php" ]; then
    echo "legacy dependencies not installed: run make -C legacy vendor" >&2
    exit 1
fi

psql_app() {
    timeout 120 env PGPASSWORD="$EQ_DB_PASSWORD" psql -h "$EQ_DB_HOST" \
        -p "$EQ_DB_PORT" -U "$EQ_DB_USER" -d "$EQ_DB_NAME" \
        -v ON_ERROR_STOP=1 -q "$@"
}

step() { printf '\n--- %s\n' "$1"; }

mkdir -p "$out" "$out/logs"

run_php() {
    timeout 300 env LEGACY_ROOT="$legacy_root" \
        LIBRETIME_LOG_DIR="$out/logs" \
        "$php" "$here/run-php.php" > "$out/php.answers"
    wc -l < "$out/php.answers" | xargs echo "php answered:"
}

run_python() {
    # cd into the api directory: the app resolves its own settings package from there.
    timeout 300 sh -c 'cd "$1" && env DJANGO_SETTINGS_MODULE=libretime_api.settings.prod "$2" "$3"' \
        _ "$api_root" "$py" "$here/run-py.py" > "$out/python.answers"
    wc -l < "$out/python.answers" | xargs echo "python answered:"
}

# Re-seeded before each leg on purpose: the fixture is expressed as offsets from now(),
# so both legs must see the same offsets relative to their own clock, or a slow leg
# would disagree with a fast one for reasons that have nothing to do with the code.
seed() {
    step "seed fixtures"
    psql_app -f "$here/seed.sql"
    psql_app -tAc "select 'files: '||count(*) from cc_files where id between 9000 and 9099"
    psql_app -tAc "select 'schedule rows: '||count(*) from cc_schedule where instance_id = 9000"
}

case "$mode" in
    php)
        seed
        step "run legacy"
        run_php
        ;;
    python)
        seed
        step "run python"
        run_python
        ;;
    all | selftest)
        seed
        step "run legacy"
        run_php

        seed
        step "run python"
        run_python

        step "compare"
        if [ "$mode" = "selftest" ]; then
            EQ_SELFTEST=1 "$py" "$here/compare.py" "$out/php.answers" "$out/python.answers"
    else
            "$py" "$here/compare.py" "$out/php.answers" "$out/python.answers"
    fi
        ;;
    *)
        echo "usage: ab.sh [all|php|python|selftest]" >&2
        exit 2
        ;;
esac
