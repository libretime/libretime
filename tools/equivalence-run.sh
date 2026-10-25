#!/usr/bin/env bash
#
# Run one or more equivalence harnesses.
#
#     tools/equivalence-run.sh                       # every harness in this checkout
#     tools/equivalence-run.sh tools/show-editor-equivalence
#     tools/equivalence-run.sh --list                # what would run, then exit
#
# This is the whole contract between a harness and CI. The driver prepares the world a
# harness needs — a database built from the repository's own installer SQL, one shared
# config file pointing at it, directories for artefacts — and then runs the harness's
# `run.sh`. Everything that is easy to get subtly wrong happens once, here, identically
# for all harnesses, rather than being re-invented per directory.
#
# Exits non-zero if any harness failed. A harness that cannot run is a failure too,
# never a skip: a comparison that quietly omits a case is worse than no comparison,
# because it reads as a pass.

set -uo pipefail

HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT="$(dirname "$HERE")"
# shellcheck source=tools/equivalence-lib.sh
source "$HERE/equivalence-lib.sh"

MODE="${EQ_MODE:-local}"
ARTIFACTS="${EQ_ARTIFACTS:-$ROOT/tools/equivalence-out}"
LEGACY_ROOT="${EQ_LEGACY_ROOT:-$ROOT/legacy}"
PYTHON="${EQ_PY:-${VIRTUAL_ENV:+$VIRTUAL_ENV/bin/python}}"
PYTHON="${PYTHON:-python3}"
export EQ_PY="$PYTHON"

# The database coordinates are exported, not just used here: a harness shells out to
# psql, php and python in whatever combination its scenario needs, and every one of
# them must be looking at the same rows. Passing the connection down by name would leave
# each harness to guess at how CI configures it.
export EQ_DB_HOST="${EQ_DB_HOST:-localhost}"
export EQ_DB_PORT="${EQ_DB_PORT:-5432}"
export EQ_DB_USER="${EQ_DB_USER:-libretime}"
export EQ_DB_PASSWORD="${EQ_DB_PASSWORD:-libretime}"

list_only=false
case "${1:-}" in
--list)
    list_only=true
    shift
    ;;
esac

harnesses=("$@")
discovered=false
if [ ${#harnesses[@]} -eq 0 ]; then
    discovered=true
    # Discovery rather than a hard-coded list in the workflow. The branches carrying
    # this work are stacked, so a harness added by a lower PR is present — and must be
    # exercised — in every PR above it, and any matrix written by hand would either
    # conflict on rebase or lie about what exists.
    while IFS= read -r dir; do
        harnesses+=("$dir")
    done < <(find "$ROOT/tools" -maxdepth 1 -type d -name '*-equivalence' | sort)
fi

if [ "$list_only" = true ]; then
    # Only harnesses that are actually runnable. A directory that predates this contract
    # has an ab.sh that shells into development containers and no way to run on a CI
    # runner; listing it would hand the workflow a job that cannot do anything. The
    # workflow says so out loud instead — see the warning in equivalence.yml — and
    # naming such a directory explicitly still fails, because asking for it directly is
    # an assertion that it works.
    for h in "${harnesses[@]}"; do
        [ -x "$h/run.sh" ] && basename "$h"
    done
    exit 0
fi

if [ ${#harnesses[@]} -eq 0 ]; then
    eq_log "no equivalence harnesses in this checkout"
    exit 0
fi

declare -A results
failed=0

# A harness that dies halfway leaves a database whose name the next run would reuse, and
# in a parallel matrix that reads as a mysterious divergence in an unrelated harness.
preflight_done=false
CURRENT_DB=""
trap '[ -n "$CURRENT_DB" ] && eq_db_drop "$CURRENT_DB" >/dev/null 2>&1' EXIT

for harness in "${harnesses[@]}"; do
    [ -d "$harness" ] || harness="$ROOT/$harness"
    name="$(basename "$harness")"

    # The database is named after the harness because the CI matrix runs harnesses in
    # parallel against one server; two harnesses sharing a name would each drop the
    # other's rows mid-run and report a divergence neither caused.
    dbname="eq_$(echo "$name" | sed 's/-equivalence$//' | tr -cd 'a-z0-9_' | cut -c1-20)"
    out="$ARTIFACTS/$name"

    eq_log "$name ($MODE mode)"

    if [ ! -x "$harness/run.sh" ]; then
        # A harness that predates this contract can only run inside the development
        # containers. Finding one while sweeping the tree is reported and left alone, so
        # that "run everything" works on a branch mid-migration; naming one explicitly is
        # an assertion that it should run, and that fails.
        if [ "$discovered" = true ]; then
            printf '  skip no run.sh (not wired for CI yet; see tools/EQUIVALENCE.md)\n'
            results["$name"]="skip (not wired)"
            continue
        fi
        printf '  FAIL no executable run.sh — not wired for CI yet; see tools/EQUIVALENCE.md\n'
        results["$name"]="fail (no run.sh)"
        failed=1
        continue
    fi

    rm -rf "$out"
    mkdir -p "$out"

    if [ "$preflight_done" = false ]; then
        eq_preflight || {
            printf '  FAIL the api environment is not importable; nothing can be proved\n'
            failed=1
            break
        }
        preflight_done=true
    fi

    CURRENT_DB="$dbname"
    if ! eq_db_prepare "$dbname"; then
        printf '  FAIL could not build the harness database\n'
        eq_db_drop "$dbname"
        results["$name"]="fail (database)"
        failed=1
        continue
    fi

    eq_config_write "$out/config.yml" "$dbname" "$out/storage"
    eq_config_verify "$out/config.yml" "$dbname"

    # run.sh sees the environment it needs and nothing else; it is responsible for
    # running the legacy service and the python port over the same rows and comparing
    # the dumps, exiting non-zero on any difference it has not explicitly excused.
    if (
        cd "$harness" &&
            EQ_MODE="$MODE" \
                EQ_OUT="$out" \
                EQ_DB_NAME="$dbname" \
                EQ_LEGACY_ROOT="$LEGACY_ROOT" \
                EQ_PY="$PYTHON" \
                ./run.sh
    ); then
        results["$name"]="pass"
    else
        results["$name"]="fail (divergence or error)"
        failed=1
    fi

    eq_artifacts "$out" "$harness"
    eq_db_drop "$dbname"
    CURRENT_DB=""
done

eq_log "summary"
for name in "${!results[@]}"; do
    printf '  %-34s %s\n' "$name" "${results[$name]}"
done

exit "$failed"
