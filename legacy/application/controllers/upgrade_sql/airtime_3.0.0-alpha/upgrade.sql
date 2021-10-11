ALTER TABLE cc_show ADD COLUMN has_autoplaylist boolean default 'f' NOT NULL;
ALTER TABLE cc_show ADD COLUMN autoplaylist_id integer DEFAULT NULL;
ALTER TABLE cc_show_instances ADD COLUMN autoplaylist_built boolean default 'f' NOT NULL;
