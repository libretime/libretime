# Database schema creation and migrations

The method to maintain the database schema, is to write both a migration file for already installed databases and to update a `schema.sql` file for fresh databases. On fresh installation, the database is filled with the `schema.sql` and `data.sql` files and LibreTime won't run any sql migration on top of it. Previously, when LibreTime was upgraded, the missing migrations were run using a custom php based migration tool, those migrations are now handled by Django. The missing migrations are tracked using both a `schema_version` field in the `cc_pref` table and a Django migration id.

:::note

Since LibreTime forked, the `schema_version` in the `schema.sql` was locked on `3.0.0-alpha` and all the migrations were run during the first user connection. This has been fixed during the move to the Django based migrations.

:::

Django doesn't maintain a `schema.sql` file, it applies every migrations until it reaches the targeted schema represented by the code. The legacy `schema_version` has to be tracked until we remove the Propel schema generation and let Django handle all the schema migrations. Until then Propel generate the schema and Django handle migrations from already installed databases.

:::info

The first Django migration is the initial schema creation using the `schema.sql` and `data.sql` files.

:::

```mermaid
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
