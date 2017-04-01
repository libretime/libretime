ALTER TABLE imported_podcast ADD COLUMN album_override boolean default 'f' NOT NULL;
ALTER TABLE third_party_track_references ALTER COLUMN file_id SET DEFAULT 0;
ALTER TABLE third_party_track_references ALTER COLUMN file_id DROP NOT NULL;
ALTER TABLE cc_show ADD COLUMN autoplaylist_repeat boolean default 'f' NOT NULL;
