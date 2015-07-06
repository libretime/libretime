ALTER TABLE cc_pref ALTER COLUMN subjid SET NULL;
ALTER TABLE cc_pref ALTER COLUMN subjid SET DEFAULT NULL;
CREATE UNIQUE INDEX cc_pref_key_idx ON cc_pref (keystr) WHERE subjid IS NULL;
ANALYZE cc_pref;