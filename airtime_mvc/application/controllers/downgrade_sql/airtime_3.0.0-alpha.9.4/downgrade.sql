-- we can restore the schema here but you'll need to restore data from a backup
ALTER TABLE cc_files ADD COLUMN soundcloud_id INTEGER;
ALTER TABLE cc_files ADD COLUMN soundcloud_error_code INTEGER;
ALTER TABLE cc_files ADD COLUMN soundcloud_error_msg VARCHAR(512);
ALTER TABLE cc_files ADD COLUMN soundcloud_link_to_file VARCHAR(4096);
ALTER TABLE cc_files ADD COLUMN soundcloud_upload_time TIMESTAMP(6);