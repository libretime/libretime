ALTER TABLE imported_podcast DROP COLUMN IF EXISTS album_override;
ALTER TABLE third_party_track_references ALTER COLUMN file_id DROP DEFAULT;
ALTER TABLE third_party_track_references ALTER COLUMN file_id SET NOT NULL;
ALTER TABLE cc_show DROP COLUMN IF EXISTS autoplaylist_repeat;

