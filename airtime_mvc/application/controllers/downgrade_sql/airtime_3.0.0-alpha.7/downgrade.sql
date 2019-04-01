ALTER TABLE cc_block ALTER COLUMN type SET DEFAULT 'static';
ALTER TABLE podcast_episodes DROP COLUMN IF EXISTS episode_title;
ALTER TABLE podcast_episodes DROP COLUMN IF EXISTS episode_description;
ALTER TABLE cc_blockcriteria DROP COLUMN IF EXISTS criteriagroup;

