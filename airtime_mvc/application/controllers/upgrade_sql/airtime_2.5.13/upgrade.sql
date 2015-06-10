CREATE TABLE IF NOT EXISTS "third_party_track_references"
(
    "id" serial NOT NULL,
    "service" VARCHAR(256) NOT NULL,
    "foreign_id" VARCHAR(256),
    "broker_task_id" VARCHAR(256),
    "broker_task_name" VARCHAR(256),
    "broker_task_dispatch_time" TIMESTAMP,
    "file_id" INTEGER NOT NULL,
    "status" VARCHAR(256) NOT NULL,
    PRIMARY KEY ("id"),
    CONSTRAINT "broker_task_id_unique" UNIQUE ("broker_task_id"),
    CONSTRAINT "foreign_id_unique" UNIQUE ("foreign_id")
);

ALTER TABLE "third_party_track_references" ADD CONSTRAINT "track_reference_fkey"
    FOREIGN KEY ("file_id")
    REFERENCES "cc_files" ("id")
    ON DELETE CASCADE;
