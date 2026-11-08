# Tools

This folder contains scripts/tools to manage the project.

## Equivalence harnesses

The harnesses under `tools/*-equivalence/` prove that a python port leaves the same
database rows as the legacy php code it replaces. See [EQUIVALENCE.md](EQUIVALENCE.md)
for the contract a harness has to satisfy, and `tools/equivalence-run.sh` to run them.
