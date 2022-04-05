# Database schema creation and migrations

The previous method to maintain the database schema, was to write both a migration file for older databases and to update the `schema.sql` file. On fresh installation, the `legacy service` then created the schema from the `schema.sql` file and didn't run any sql migration on top of it. When LibreTime was upgraded the missing migrations were run using a custom php based migration tool. The missing migrations were tracked using a `schema_version` field in the `cc_pref` table.

> Since LibreTime forked, the `schema_version` in the `schema.sql` was stuck on `3.0.0-alpha` and wasn't bumped each time changes were added. This has been fixed during the move to the django based migrations.

Some important details about the legacy `schema_version`:

- The initial schema creation sets directly the version to the latest `3.0.0-alpha.12`, thus the migrations before `3.0.0-alpha.12` will not run on fresh installations.
- Migrations between version `2.5.2` and `3.0.0-alpha` are for Airtime users only.
- Migrations between version `3.0.0-alpha` and `3.0.0-alpha.12` are for any user upgrading from a version older than `3.0.0-alpha.12`.

Django does not maintain a `schema.sql` file, it applies every migrations until it reaches the targeted schema represented by the code. Now that migrations are handled by Django, the legacy `schema_version` is not tracked anymore and the `schema.sql` should only be changed for fixes.

```
stateDiagram-v2
    state is_django_migration_applied <<choice>>
    [*] --> is_django_migration_applied: Is the django migration ID in the DB ?

    is_django_migration_applied --> [*]: Yes, ignoring...

    state "Apply django migration" as apply_django_migration
    is_django_migration_applied --> apply_django_migration: No

    state apply_django_migration {
        state is_legacy_migration <<choice>>
        [*] --> is_legacy_migration: Is it a legacy migration ?

        state "Run django migration" as run_django_migration
        state "Apply changes" as run_django_migration
        state "Save migration ID in DB" as run_django_migration
        is_legacy_migration --> run_django_migration: No
        run_legacy_migration --> run_django_migration
        run_django_migration --> [*]

        state is_legacy_migration_applied <<choice>>
        is_legacy_migration_applied --> [*]: Yes, ignoring...

        state "Run legacy migration" as run_legacy_migration
        state "Apply changes" as run_legacy_migration
        state "Bump legacy schema version" as run_legacy_migration
        is_legacy_migration_applied --> run_legacy_migration: No
        is_legacy_migration --> is_legacy_migration_applied:  Yes, is the DB schema version >= legacy migration schema version ?
    }

    apply_django_migration --> [*]
```
