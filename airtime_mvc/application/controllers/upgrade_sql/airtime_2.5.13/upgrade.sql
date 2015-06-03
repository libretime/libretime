CREATE TABLE IF NOT EXISTS "third_party_track_references"
(
    "id" serial NOT NULL,
    "service" VARCHAR(512) NOT NULL,
    "foreign_id" INTEGER NOT NULL,
    "file_id" INTEGER NOT NULL,
    "status" VARCHAR(256) NOT NULL,
    PRIMARY KEY ("id")
);

ALTER TABLE "third_party_track_references" ADD CONSTRAINT "track_reference_fkey"
    FOREIGN KEY ("file_id")
    REFERENCES "cc_files" ("id")
    ON DELETE CASCADE;
