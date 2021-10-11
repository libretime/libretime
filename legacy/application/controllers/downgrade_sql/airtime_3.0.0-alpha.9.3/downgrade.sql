CREATE TABLE "cc_smemb"
(
    "id" INTEGER NOT NULL,
    "uid" INTEGER DEFAULT 0 NOT NULL,
    "gid" INTEGER DEFAULT 0 NOT NULL,
    "level" INTEGER DEFAULT 0 NOT NULL,
    "mid" INTEGER,
    PRIMARY KEY ("id"),
    CONSTRAINT "cc_smemb_id_idx" UNIQUE ("id")
);
