ALTER TABLE cc_files ADD COLUMN filesize integer NOT NULL
CONSTRAINT filesize_default DEFAULT 0
