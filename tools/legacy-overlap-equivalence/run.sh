#!/usr/bin/env bash
#
# CI entry point — see tools/EQUIVALENCE.md.
#
# EQ_SELFTEST=1 runs the negative control instead of the comparison, so the same
# harness can demonstrate both that the two sides agree and that this file would
# notice if they stopped doing so.
set -euo pipefail

here="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

export AB_MODE=local

if [ "${EQ_SELFTEST:-0}" = "1" ]; then
    exec "$here/ab.sh" selftest
fi

exec "$here/ab.sh"
