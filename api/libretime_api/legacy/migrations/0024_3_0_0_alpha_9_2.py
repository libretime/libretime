# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
ALTER TABLE cc_files ADD COLUMN track_type VARCHAR(16);

CREATE TABLE IF NOT EXISTS "cc_track_types"
(
    "id" serial NOT NULL,
    "code" VARCHAR(16) NOT NULL,
    "type_name" VARCHAR(64),
    "description" VARCHAR(255),
    "visibility" boolean DEFAULT true NOT NULL,
    CONSTRAINT "cc_track_types_pkey" PRIMARY KEY ("id"),
    CONSTRAINT "cc_track_types_code_key" UNIQUE ("code")
);

-- INSERT INTO cc_track_types VALUES (1, 'MUS', 'Music', 'This is used for tracks containing music.', true);
-- INSERT INTO cc_track_types VALUES (2, 'SID', 'Station ID', 'This is used for Station IDs', true);
-- INSERT INTO cc_track_types VALUES (3, 'INT', 'Show Intro', 'This can be used for organizing all the show introductions.', true);
-- INSERT INTO cc_track_types VALUES (4, 'OUT', 'Show Outro', 'This can be used for organizing all the show outroductions.', true);
-- INSERT INTO cc_track_types VALUES (5, 'SWP', 'Sweeper', 'This is used for segues between songs.', true);
-- INSERT INTO cc_track_types VALUES (6, 'JIN', 'Jingle', 'A short song or tune, normally played during commercial breaks. Contains one or more hooks.', true);
-- INSERT INTO cc_track_types VALUES (7, 'PRO', 'Promo', 'For promotional use.', true);
-- INSERT INTO cc_track_types VALUES (8, 'SHO', 'Shout Out', 'A message of congratulation, greeting. support, or appreciation. ', true);
-- INSERT INTO cc_track_types VALUES (9, 'NWS', 'News', 'This is used for noteworthy information, announcements.', true);
-- INSERT INTO cc_track_types VALUES (10, 'COM', 'Commercial', 'This is used for commercial advertising.', true);
-- INSERT INTO cc_track_types VALUES (11, 'ITV', 'Interview', 'This is used for radio interviews', true);
-- INSERT INTO cc_track_types VALUES (12, 'VTR', 'Voice Tracking', 'Also referred as robojock or taped. Make announcements without actually being in the station.', true);
"""

DOWN = """
ALTER TABLE cc_files DROP COLUMN IF EXISTS track_type;

DROP TABLE IF EXISTS "cc_track_types" CASCADE;
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0023_3_0_0_alpha_9_1"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.9.2",
                sql=UP,
            )
        )
    ]
