
ALTER TABLE cc_blockcontents
	ADD COLUMN trackoffset double precision DEFAULT 0 NOT NULL;

ALTER TABLE cc_files
	ADD COLUMN is_scheduled boolean DEFAULT false,
	ADD COLUMN is_playlist boolean DEFAULT false;

ALTER TABLE cc_playlistcontents
	ADD COLUMN trackoffset double precision DEFAULT 0 NOT NULL;

ALTER TABLE cc_schedule
	ADD COLUMN "position" integer DEFAULT 0 NOT NULL;

ALTER TABLE cc_show
	ADD COLUMN linked boolean DEFAULT false NOT NULL,
	ADD COLUMN is_linkable boolean DEFAULT true NOT NULL;
