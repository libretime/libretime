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

The pattern that has worked is one scenario in two addressing modes: `ab.sh` holds the
scenario and speaks to postgres, php and python either through the development containers
(`AB_MODE=container`, what it always did) or on this host (`AB_MODE=local`), and `run.sh`
is the two-line CI entry that selects the second. One scenario rather than two copies,
because a comparison CI runs that drifted from the one developers run is not the same
comparison.

The driver prepares the world, so that the parts that are easy to get wrong happen once,
identically, for every harness:

| provided | what it is |
| --- | --- |
| `EQ_DB_NAME` | a database created for this harness and dropped afterwards, built by running **the api's own migrations** (`migrate --run-syncdb`) against it. Not `schema.sql` on its own: the migrations after `0001` replay every change a station has been through, and the api's tables — `auth_permission`, `django_content_type`, `core_user`, `sessions` — exist only as Django models. Load the legacy SQL alone and every permission check answers `relation "auth_permission" does not exist`. |
| `LIBRETIME_CONFIG_FILEPATH`, `EQ_OUT/config.yml` | **one** config file pointing at that database and at the broker, read by both the php side and the python side. Two files would be two chances to compare a service against itself. |
| RabbitMQ on `localhost:5672`, vhost `/libretime` | a real broker, because the ports publish the way php published: a show edit tells playout to rebuild. A harness without one either fails for environmental reasons or has to stub the behaviour it is supposed to be proving. |
| `EQ_OUT` | directory for dumps, logs and the api's swallowed warnings. Uploaded as a CI artefact, always. |
| `EQ_LEGACY_ROOT`, `EQ_API_ROOT`, `EQ_PY`, `EQ_MODE` | where the legacy app is (with `vendor/` installed), where the api is, which python to use, and `local`. |

`run.sh` then, in the harness directory:

1. seed whatever fixture the scenario needs,
2. run the legacy service over it, dump the rows,
3. reset to the same starting point, run the python port, dump the rows,
4. compare, **exit non-zero on any difference it has not explicitly excused**, and write
   the diff into `$EQ_OUT` so it is readable from a CI log.

A missing `run.sh` is a **failure when named, and a warning when found** — see the driver's
discovery rules. Fixture files (a scenario list, a cases file) are read from `AB_FIXTURES`,
defaulting to the `/tools` mount container mode uses, so that both legs provably read the
same fixture rather than a copy of it.

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
- **A fixture must load into an empty database.** One harness inserted its `cc_schedule`
  rows before the `cc_files` rows they reference and passed for weeks, because the files
  were already in the station's database from another harness's run. A clean database is
  the better environment precisely because it refuses to hide that.

## A database built from migrations is not the same as a station's, and that is the point

CI cannot clone a station, so it builds a database the way a station's is maintained: the
api's migrations, from the installer schema through every upgrade since. That is closer to
a running station than the bare installer SQL is, and it changed what one harness had to
say.

`show-editor-equivalence` documents three divergences where php leaves rows behind because
a foreign key never arrived — `cc_show_days.show_id` and `cc_schedule.instance_id` have no
constraint on databases that were installed years ago and upgraded. On the migrated
schema, `cc_show_instances.show_id` **does** cascade, so php's own deletes remove the
children and the divergence disappears: all 20 scenarios come out identical, and 18 of them
match the datasets committed upstream as well. The port is correct under either schema,
because it deletes its children explicitly rather than trusting the constraint.

The ledger in that harness is deliberately one-directional — a documented difference that
does not occur is not a failure — so the same harness passes in both environments and
nobody is tempted to weaken it to make a run green. When reading a divergence, ask which
schema built the database before deciding who is wrong.

## Adding a harness

1. `mkdir tools/<area>-equivalence`, add a `README.md` stating what is being proved, the
   fixture, and the ledger of excused divergences.
2. Write the scenario in `ab.sh` (or your own entry point) so that postgres, php and
   python are addressed through a small set of functions, and add `run.sh` selecting
   `AB_MODE=local`. Reuse the sibling harnesses' shape: `run-php.php` / `run-py.py` /
   `compare.py` / `seed.sql` / `clean.sql`.
3. `tools/equivalence-run.sh tools/<area>-equivalence` until it passes, then break one
   assertion on purpose and watch it fail. A harness that has never failed is not proof
   of anything.
4. Nothing else to wire. Discovery picks it up.

The environment CI provides is reproducible on a workstation: php 8.2 with
`bcmath, curl, dom, intl, mbstring, pdo_pgsql, simplexml, xml, yaml` (bcmath is not
optional — `SchedulerService::findEndTime` calls `bcadd`), `make -C legacy vendor`,
`make -C api install`, and postgres and rabbitmq as containers. Two things the harnesses
need that a laptop usually lacks: `LIBRETIME_LOG_DIR` pointed somewhere writable, because
the legacy services log through Zend and will not start without it, and `PYTHONPATH` if
your virtualenv was built at a different path than the checkout it is being used against.
