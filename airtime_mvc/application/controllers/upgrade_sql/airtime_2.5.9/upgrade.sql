CREATE TABLE cloud_file
(
    id serial NOT NULL,
    resource_id text NOT NULL default nextval('cloud_file_id_seq'::regclass),
    cc_file_id integer,
    CONSTRAINT cloud_file_pkey PRIMARY KEY (id),
    CONSTRAINT "cloud_file_FK_1" FOREIGN KEY (cc_file_id)
        REFERENCES cc_files (id) MATCH SIMPLE
        ON UPDATE NO ACTION ON DELETE CASCADE
)