# legacy-overlap-equivalence

A small harness that ships with the plumbing, for two reasons:

1. **It proves the pipeline before any port depends on it.** This is the harness that makes
   `tools/equivalence.py` and `.github/workflows/equivalence.yml` verifiable in their own pull
   request instead of "trust us, the feature branches will exercise it".
2. **It measures code that already exists on `main`.** `Schedule.is_file_scheduled_in_the_future`
   (python) and `Application_Model_Schedule::IsFileScheduledInTheFuture` (php) are two live
   implementations of one rule, both in the repository today, both used before a file is deleted.
   Nothing has ever compared them.

Run it:

```bash
make -C api install && make -C legacy vendor
python3 tools/equivalence.py tools/legacy-overlap-equivalence
python3 tools/equivalence.py tools/legacy-overlap-equivalence   # with EQ_SELFTEST=1
```

The second command is the **negative control**: it flips one expected answer and requires the
comparison to notice. A harness that has never failed proves nothing, and this one can be shown
failing on demand in about a second.
