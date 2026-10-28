# Equivalence harnesses

A harness proves one thing: **the python port leaves the same database rows as the legacy php
service.** It runs a fixture through both and diffs the dumps. Response bodies are not the
evidence — "the endpoint returns 201" says nothing about whether playout finds what it expects in
`cc_schedule` in an hour.

## Run one

```bash
make -C api install                              # the venv the driver compares against
make -C legacy vendor                            # Propel classes for the php leg
python3 tools/equivalence.py tools/criteria-equivalence
```

With no argument it runs every harness in the checkout that is wired for CI.
`--list` prints what would run. `--help` explains the environment variables.

Locally it needs a PostgreSQL and, for harnesses whose ported code publishes, a RabbitMQ. The
variables below point at them; in CI both are provided by the workflow.

## Write one

A harness is a directory `tools/<name>-equivalence/` containing:

| file         | purpose                                                                                                                                                                                                       |
| ------------ | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `seed.sql`   | the fixture: identical starting rows for both legs                                                                                                                                                            |
| `ab.sh`      | the scenario. `AB_MODE=local` (CI, or the driver) uses the environment it is given; `AB_MODE=container` shells into development containers, which is how these were written first and still works for a human |
| `dump.sh`    | dumps the affected tables to CSV in `$EQ_OUT/php` and `$EQ_OUT/python`, re-seeding between the legs                                                                                                           |
| `compare.py` | diffs them; exits non-zero on any difference that is not in the ledger                                                                                                                                        |
| `run.sh`     | the two-line CI entry: `AB_MODE=local ./ab.sh && ./dump.sh && python3 compare.py`                                                                                                                             |

`run.sh` is what makes it a CI harness. A directory with an `ab.sh` and no `run.sh` is reported as
**not wired** — a warning naming it, never a silent skip, because a comparison that quietly omits a
case reads as a pass.

What the harness receives: `EQ_OUT`, `EQ_DB_NAME`, `EQ_MODE`, `EQ_PY`, `EQ_LEGACY_ROOT`, the
`EQ_DB_*` and `EQ_RABBITMQ_*` connection values, `LIBRETIME_CONFIG_FILEPATH`, and
`LIBRETIME_LOG_DIR` set inside the artefacts directory (legacy code logs warnings instead of
failing, and that log is often the only explanation).

## What the driver guarantees, and the wrong-green each one prevents

| guarantee                                                                                     | the failure it prevents                                                                                   |
| --------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------- |
| refuses to start unless `EQ_PY` can import the api                                            | a harness "passing" on a machine that cannot run the port at all                                          |
| one database per harness, named after it                                                      | two parallel matrix jobs dropping each other's rows                                                       |
| the database is created **owned by the application role**                                     | PostgreSQL 15 removed `PUBLIC`'s `CREATE` on schema `public`: app can connect, cannot use it              |
| **one** `config.yml`, read by php _and_ python                                                | two files, two databases, one harness comparing a service against itself and reporting agreement          |
| asks the api's own loader which database it resolved, and requires an answer                  | python silently ignoring a key it does not declare — the same failure, caught before it can produce green |
| schema built by `migrate --run-syncdb`, not by loading the installer SQL                      | the legacy SQL alone has no `auth_permission`, so every object permission answers with a database error   |
| asserts the schema exists (≥40 tables, `auth_permission` non-empty)                           | comparing rows in a half-built world                                                                      |
| drops the database on every exit path, and prints the log path when a step dies on a redirect | leftover databases; an unwritable log reading exactly like a failed migration                             |
| uploads the dumps and the php log even when the harness fails                                 | "test_weekly differs" being unactionable without re-running                                               |

## Where and when it runs

`.github/workflows/equivalence.yml`: one job per discovered harness, on pull requests touching
`api/`, `legacy/` or the harnesses, on push to `main`, and weekly. The php and postgres setup is the
same shape the `Legacy` job already builds; the api venv is the same `make install` the `API` job
uses; the only new part is a RabbitMQ service. It is **not** a required check yet — see the comments
at the top of that file.

## What this does not do

- It does not compare HTTP responses. It compares rows; the payload vocabulary is a design decision,
  the rows are a fact.
- It does not prove the php side is _correct_, only that the port matches it. Where php is wrong and
  the port deliberately diverges, `compare.py` carries a ledger entry with a reason — and the ledger
  is one-directional, so an excused difference that did not happen is itself a failure.
- It does not run in the development containers in CI, and does not touch a station's database. It
  always creates and drops its own.
