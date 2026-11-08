-- Fixture for the legacy-overlap harness.
--
-- Both implementations answer the same question about a file: "does any cc_schedule row for
-- it still lie in the future?" The offsets below are the interesting cases, and they are all
-- comfortably larger than the few seconds between seeding and running a leg — the bug class
-- worth catching here is an hours-scale timezone disagreement, not clock drift.
--
-- Ids are in a range nothing else uses; rows are deleted first, so the file is idempotent,
-- and ab.sh re-seeds before each leg so both legs see the same offsets from their own now().

BEGIN;

DELETE FROM cc_schedule WHERE instance_id BETWEEN 9000 AND 9099;
DELETE FROM cc_show_instances WHERE id BETWEEN 9000 AND 9099;
DELETE FROM cc_show WHERE id BETWEEN 9000 AND 9099;
DELETE FROM cc_files WHERE id BETWEEN 9000 AND 9099;

INSERT INTO cc_show (id, name) VALUES (9000, 'Equivalence overlap harness');

INSERT INTO cc_show_instances (id, show_id, starts, ends, created)
VALUES (9000, 9000,
        now() AT TIME ZONE 'UTC' - INTERVAL '1 day',
        now() AT TIME ZONE 'UTC' + INTERVAL '1 day',
        now() AT TIME ZONE 'UTC');

-- 9001..9009: one file per case; 9009 deliberately has no schedule rows at all.
INSERT INTO cc_files (id, name, filepath) VALUES
    (9001, 'eq ended ten minutes ago',        'organize/9001.mp3'),
    (9002, 'eq ended an hour ago',            'organize/9002.mp3'),
    (9003, 'eq ends in ten minutes',          'organize/9003.mp3'),
    (9004, 'eq ends in six hours',            'organize/9004.mp3'),
    (9005, 'eq spans now',                    'organize/9005.mp3'),
    (9006, 'eq aired and airs again',         'organize/9006.mp3'),
    (9007, 'eq only ever aired',              'organize/9007.mp3'),
    (9008, 'eq overbooked tail',              'organize/9008.mp3'),
    (9009, 'eq never scheduled',              'organize/9009.mp3');

-- starts and ends are `timestamp without time zone`, so strip the offset deliberately:
-- the php side compares against NOW() AT TIME ZONE 'UTC', the python side against a
-- tz-aware now(); the whole point is to find out whether those agree.
INSERT INTO cc_schedule (starts, ends, file_id, instance_id, cue_in, cue_out) VALUES
    (now() AT TIME ZONE 'UTC' - INTERVAL '2 hours',  now() AT TIME ZONE 'UTC' - INTERVAL '10 minutes', 9001, 9000, INTERVAL '0 seconds', INTERVAL '3 minutes'),
    (now() AT TIME ZONE 'UTC' - INTERVAL '3 hours',  now() AT TIME ZONE 'UTC' - INTERVAL '59 minutes', 9002, 9000, INTERVAL '0 seconds', INTERVAL '3 minutes'),
    (now() AT TIME ZONE 'UTC' - INTERVAL '1 hour',   now() AT TIME ZONE 'UTC' + INTERVAL '10 minutes', 9003, 9000, INTERVAL '0 seconds', INTERVAL '3 minutes'),
    (now() AT TIME ZONE 'UTC' + INTERVAL '5 hours',  now() AT TIME ZONE 'UTC' + INTERVAL '6 hours',    9004, 9000, INTERVAL '0 seconds', INTERVAL '3 minutes'),
    (now() AT TIME ZONE 'UTC' - INTERVAL '1 day',    now() AT TIME ZONE 'UTC' + INTERVAL '1 day',      9005, 9000, INTERVAL '0 seconds', INTERVAL '3 minutes'),
    (now() AT TIME ZONE 'UTC' - INTERVAL '4 hours',  now() AT TIME ZONE 'UTC' - INTERVAL '3 hours',    9006, 9000, INTERVAL '0 seconds', INTERVAL '3 minutes'),
    (now() AT TIME ZONE 'UTC' + INTERVAL '2 hours',  now() AT TIME ZONE 'UTC' + INTERVAL '3 hours',    9006, 9000, INTERVAL '0 seconds', INTERVAL '3 minutes'),
    (now() AT TIME ZONE 'UTC' - INTERVAL '8 hours',  now() AT TIME ZONE 'UTC' - INTERVAL '7 hours',    9007, 9000, INTERVAL '0 seconds', INTERVAL '3 minutes'),
    (now() AT TIME ZONE 'UTC' - INTERVAL '6 hours',  now() AT TIME ZONE 'UTC' - INTERVAL '5 hours',    9007, 9000, INTERVAL '0 seconds', INTERVAL '3 minutes'),
    -- starts after the instance ends: overbooked, still in the future, must count
    (now() AT TIME ZONE 'UTC' + INTERVAL '1 day',    now() AT TIME ZONE 'UTC' + INTERVAL '1 day' + INTERVAL '30 minutes', 9008, 9000, INTERVAL '0 seconds', INTERVAL '3 minutes');

COMMIT;
