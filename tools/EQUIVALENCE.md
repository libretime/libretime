# Equivalence harnesses

A harness proves that a python port leaves the **same database rows** as the legacy php
code it replaces. Not the same JSON, not the same intent — the same rows, column by
column, including the ones nothing reads. `tools/<area>-equivalence/`.

They exist because "the endpoint returns 201" says nothing about whether the scheduler,
the populator and playout will find what they expect in the tables an hour later. Each
harness is the gate that turns that from a review question into a command.

## Running them

```bash
tools/equivalence-run.sh                    # every harness in this checkout
tools/equivalence-run.sh tools/show-editor-equivalence
tools/equivalence-run.sh --list             # what would run, one per line
```

`.github/workflows/equivalence.yml` runs the same command — one matrix job per harness
directory it finds, on pull requests, on pushes to `main`, and weekly.

## The contract

A harness is a directory `tools/<name>-equivalence/` containing an executable `run.sh`.
That is all the driver knows about it, and all it needs to know.

The driver prepares the world, so that the parts that are easy to get wrong happen once,
identically, for every harness:

| provided | what it is |
| --- | --- |
| `EQ_DB_NAME` | a database, freshly created and loaded with the repository's own installer SQL — `api/libretime_api/legacy/migrations/sql/{schema,data}.sql`, the same pair `legacy/tests/.../AirtimeInstall.php` installs. Dropped afterwards. |
| `LIBRETIME_CONFIG_FILEPATH`, `EQ_OUT/config.yml` | **one** config file pointing at that database, read by both the php side and the python side. Two files would be two chances to compare a service against itself. |
| `EQ_OUT` | directory for dumps and logs. Uploaded as a CI artefact, always. |
| `EQ_LEGACY_ROOT`, `EQ_PY`, `EQ_MODE` | where the legacy app is (with `vendor/` installed), which python to use, and `local`. |

`run.sh` then, in the harness directory:

1. seed whatever fixture the scenario needs,
2. run the legacy service over it, dump the rows,
3. reset to the same starting point, run the python port, dump the rows,
4. compare, **exit non-zero on any difference it has not explicitly excused**, and write
   the diff into `$EQ_OUT` so it is readable from a CI log.

A missing `run.sh` is a **failure, not a skip**. A comparison that quietly omits a case
reads as a pass, which is the worst thing a test suite can do.

## Rules that apply to every harness

- **Own database, always.** The show editor writes the global `shows_populated_until`
  preference. A harness that ran against a station's database — or a shared CI one —
  could leave it believing the calendar was built to 2044, and that station would stop
  populating shows. Self-inflicted outage, not a test.
- **Name the divergence, in the ledger, with a reason.** Where the port deliberately
  differs from php, the harness must say so and still pass. Silent tolerance is how a
  real bug becomes expected behaviour.
- **Check the php leg against the project's own expectation datasets before writing
  python.** If those disagree, that is a finding about the repository, not noise in the
  harness.
- **IDs are not compared.** Sequences advance differently between the two runs; the
  parent/child relationships are what carry meaning, and the standard `compare.py`
  remaps them.

## One difference between CI and a station, on purpose and in the open

CI builds its database from the installer schema. A station's database was built years
ago and **upgraded**, and some upgrades never added the constraint — for shows,
`cc_show_days.show_id` and `cc_schedule.instance_id` have no foreign key on a real
station, while the installer schema cascades `cc_show_instances.show_id`.

So a harness run in CI and the same run against a live stack can legitimately disagree
about whether deleting a show orphans its days and its schedule rows. Both are worth
knowing and neither should be hidden: the port deletes its children explicitly, which is
correct under both schemas, and the harness ledgers record what each environment proves.
When reading a divergence, check which schema built the database before believing
anything about who is wrong.

## Adding a harness

1. `mkdir tools/<area>-equivalence`, add a `README.md` stating what is being proved, the
   fixture, and the ledger of excused divergences.
2. Write `run.sh` against the environment above. Reuse the sibling harnesses' shape:
   `run-php.php` / `run-py.py` / `compare.py` / `seed.sql` / `clean.sql`.
3. `tools/equivalence-run.sh tools/<area>-equivalence` until it passes, then break one
   assertion on purpose and watch it fail. A harness that has never failed is not proof
   of anything.
4. Nothing else to wire. Discovery picks it up.
