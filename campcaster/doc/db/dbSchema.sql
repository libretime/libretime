--
-- PostgreSQL database dump
--

SET client_encoding = 'UNICODE';
SET check_function_bodies = false;

SET SESSION AUTHORIZATION 'postgres';

--
-- TOC entry 4 (OID 2200)
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
GRANT ALL ON SCHEMA public TO PUBLIC;


SET SESSION AUTHORIZATION 'test';

SET search_path = public, pg_catalog;

--
-- TOC entry 24 (OID 104453)
-- Name: schedule; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE schedule (
    id bigint NOT NULL,
    playlist bigint NOT NULL,
    starts timestamp without time zone NOT NULL,
    ends timestamp without time zone NOT NULL
);


--
-- TOC entry 25 (OID 104457)
-- Name: playlog; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE playlog (
    id bigint NOT NULL,
    audioclipid bigint NOT NULL,
    "timestamp" timestamp without time zone NOT NULL
);


--
-- TOC entry 26 (OID 196314)
-- Name: al_test_tree; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE al_test_tree (
    id integer NOT NULL,
    name character varying(255) DEFAULT ''::character varying NOT NULL,
    "type" character varying(255) DEFAULT ''::character varying NOT NULL,
    param character varying(255)
);


--
-- TOC entry 5 (OID 196323)
-- Name: al_test_tree_id_seq_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE al_test_tree_id_seq_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 27 (OID 196327)
-- Name: al_test_struct; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE al_test_struct (
    rid integer NOT NULL,
    objid integer NOT NULL,
    parid integer NOT NULL,
    "level" integer
);


--
-- TOC entry 6 (OID 196339)
-- Name: al_test_struct_id_seq_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE al_test_struct_id_seq_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 28 (OID 196348)
-- Name: al_test_classes; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE al_test_classes (
    id integer NOT NULL,
    cname character varying(20)
);


--
-- TOC entry 29 (OID 196354)
-- Name: al_test_cmemb; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE al_test_cmemb (
    objid integer NOT NULL,
    cid integer NOT NULL
);


--
-- TOC entry 30 (OID 196357)
-- Name: al_test_subjs; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE al_test_subjs (
    id integer NOT NULL,
    login character varying(255) DEFAULT ''::character varying NOT NULL,
    pass character varying(255) DEFAULT ''::character varying NOT NULL,
    "type" character(1) DEFAULT 'U'::bpchar NOT NULL,
    realname character varying(255) DEFAULT ''::character varying NOT NULL,
    lastlogin timestamp without time zone,
    lastfail timestamp without time zone
);


--
-- TOC entry 7 (OID 196370)
-- Name: al_test_subjs_id_seq_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE al_test_subjs_id_seq_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 31 (OID 196372)
-- Name: al_test_smemb; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE al_test_smemb (
    id integer NOT NULL,
    uid integer DEFAULT 0 NOT NULL,
    gid integer DEFAULT 0 NOT NULL,
    "level" integer DEFAULT 0 NOT NULL,
    mid integer
);


--
-- TOC entry 8 (OID 196380)
-- Name: al_test_smemb_id_seq_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE al_test_smemb_id_seq_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 32 (OID 196382)
-- Name: al_test_perms; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE al_test_perms (
    permid integer NOT NULL,
    subj integer,
    "action" character varying(20),
    obj integer,
    "type" character(1)
);


--
-- TOC entry 9 (OID 196393)
-- Name: al_test_perms_id_seq_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE al_test_perms_id_seq_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 33 (OID 196395)
-- Name: al_test_sess; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE al_test_sess (
    sessid character(32) NOT NULL,
    userid integer,
    login character varying(255),
    ts timestamp without time zone
);


--
-- TOC entry 34 (OID 206723)
-- Name: ls_tree; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE ls_tree (
    id integer NOT NULL,
    name character varying(255) DEFAULT ''::character varying NOT NULL,
    "type" character varying(255) DEFAULT ''::character varying NOT NULL,
    param character varying(255)
);


--
-- TOC entry 10 (OID 206732)
-- Name: ls_tree_id_seq_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE ls_tree_id_seq_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 35 (OID 206736)
-- Name: ls_struct; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE ls_struct (
    rid integer NOT NULL,
    objid integer NOT NULL,
    parid integer NOT NULL,
    "level" integer
);


--
-- TOC entry 11 (OID 206748)
-- Name: ls_struct_id_seq_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE ls_struct_id_seq_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 36 (OID 206757)
-- Name: ls_classes; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE ls_classes (
    id integer NOT NULL,
    cname character varying(20)
);


--
-- TOC entry 37 (OID 206763)
-- Name: ls_cmemb; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE ls_cmemb (
    objid integer NOT NULL,
    cid integer NOT NULL
);


--
-- TOC entry 38 (OID 206766)
-- Name: ls_subjs; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE ls_subjs (
    id integer NOT NULL,
    login character varying(255) DEFAULT ''::character varying NOT NULL,
    pass character varying(255) DEFAULT ''::character varying NOT NULL,
    "type" character(1) DEFAULT 'U'::bpchar NOT NULL,
    realname character varying(255) DEFAULT ''::character varying NOT NULL,
    lastlogin timestamp without time zone,
    lastfail timestamp without time zone
);


--
-- TOC entry 12 (OID 206779)
-- Name: ls_subjs_id_seq_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE ls_subjs_id_seq_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 39 (OID 206781)
-- Name: ls_smemb; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE ls_smemb (
    id integer NOT NULL,
    uid integer DEFAULT 0 NOT NULL,
    gid integer DEFAULT 0 NOT NULL,
    "level" integer DEFAULT 0 NOT NULL,
    mid integer
);


--
-- TOC entry 13 (OID 206789)
-- Name: ls_smemb_id_seq_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE ls_smemb_id_seq_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 40 (OID 206791)
-- Name: ls_perms; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE ls_perms (
    permid integer NOT NULL,
    subj integer,
    "action" character varying(20),
    obj integer,
    "type" character(1)
);


--
-- TOC entry 14 (OID 206802)
-- Name: ls_perms_id_seq_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE ls_perms_id_seq_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 41 (OID 206804)
-- Name: ls_sess; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE ls_sess (
    sessid character(32) NOT NULL,
    userid integer,
    login character varying(255),
    ts timestamp without time zone
);


--
-- TOC entry 42 (OID 206815)
-- Name: ls_files; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE ls_files (
    id integer NOT NULL,
    gunid bigint NOT NULL,
    name character varying(255) DEFAULT ''::character varying NOT NULL,
    mime character varying(255) DEFAULT ''::character varying NOT NULL,
    ftype character varying(128) DEFAULT ''::character varying NOT NULL,
    state character varying(128) DEFAULT 'empty'::character varying NOT NULL,
    currentlyaccessing integer DEFAULT 0 NOT NULL,
    editedby integer
);


--
-- TOC entry 15 (OID 206832)
-- Name: ls_mdata_id_seq_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE ls_mdata_id_seq_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 43 (OID 206834)
-- Name: ls_mdata; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE ls_mdata (
    id integer NOT NULL,
    gunid bigint,
    subjns character varying(255),
    subject character varying(255) DEFAULT ''::character varying NOT NULL,
    predns character varying(255),
    predicate character varying(255) NOT NULL,
    predxml character(1) DEFAULT 'T'::bpchar NOT NULL,
    objns character varying(255),
    object text
);


--
-- TOC entry 44 (OID 206845)
-- Name: ls_access; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE ls_access (
    gunid bigint,
    token bigint,
    chsum character(32) DEFAULT ''::bpchar NOT NULL,
    ext character varying(128) DEFAULT ''::character varying NOT NULL,
    "type" character varying(20) DEFAULT ''::character varying NOT NULL,
    ts timestamp without time zone
);


--
-- TOC entry 45 (OID 207013)
-- Name: ls_trans; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE ls_trans (
    id integer NOT NULL,
    trtok character(16) NOT NULL,
    direction character varying(128) NOT NULL,
    state character varying(128) NOT NULL,
    trtype character varying(128) NOT NULL,
    gunid bigint,
    pdtoken bigint,
    url character varying(255),
    fname character varying(255),
    localfile character varying(255),
    expectedsum character(32),
    realsum character(32),
    expectedsize integer,
    realsize integer,
    uid integer,
    parid integer,
    ts timestamp without time zone
);


--
-- TOC entry 16 (OID 207018)
-- Name: ls_trans_id_seq_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE ls_trans_id_seq_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 17 (OID 207023)
-- Name: ls_pref_id_seq_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE ls_pref_id_seq_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 46 (OID 207025)
-- Name: ls_pref; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE ls_pref (
    id integer NOT NULL,
    subjid integer,
    keystr character varying(255),
    valstr text
);


--
-- TOC entry 47 (OID 207039)
-- Name: as_tree; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE as_tree (
    id integer NOT NULL,
    name character varying(255) DEFAULT ''::character varying NOT NULL,
    "type" character varying(255) DEFAULT ''::character varying NOT NULL,
    param character varying(255)
);


--
-- TOC entry 18 (OID 207048)
-- Name: as_tree_id_seq_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE as_tree_id_seq_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 48 (OID 207052)
-- Name: as_struct; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE as_struct (
    rid integer NOT NULL,
    objid integer NOT NULL,
    parid integer NOT NULL,
    "level" integer
);


--
-- TOC entry 19 (OID 207064)
-- Name: as_struct_id_seq_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE as_struct_id_seq_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 49 (OID 207073)
-- Name: as_classes; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE as_classes (
    id integer NOT NULL,
    cname character varying(20)
);


--
-- TOC entry 50 (OID 207079)
-- Name: as_cmemb; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE as_cmemb (
    objid integer NOT NULL,
    cid integer NOT NULL
);


--
-- TOC entry 51 (OID 207082)
-- Name: as_subjs; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE as_subjs (
    id integer NOT NULL,
    login character varying(255) DEFAULT ''::character varying NOT NULL,
    pass character varying(255) DEFAULT ''::character varying NOT NULL,
    "type" character(1) DEFAULT 'U'::bpchar NOT NULL,
    realname character varying(255) DEFAULT ''::character varying NOT NULL,
    lastlogin timestamp without time zone,
    lastfail timestamp without time zone
);


--
-- TOC entry 20 (OID 207095)
-- Name: as_subjs_id_seq_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE as_subjs_id_seq_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 52 (OID 207097)
-- Name: as_smemb; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE as_smemb (
    id integer NOT NULL,
    uid integer DEFAULT 0 NOT NULL,
    gid integer DEFAULT 0 NOT NULL,
    "level" integer DEFAULT 0 NOT NULL,
    mid integer
);


--
-- TOC entry 21 (OID 207105)
-- Name: as_smemb_id_seq_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE as_smemb_id_seq_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 53 (OID 207107)
-- Name: as_perms; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE as_perms (
    permid integer NOT NULL,
    subj integer,
    "action" character varying(20),
    obj integer,
    "type" character(1)
);


--
-- TOC entry 22 (OID 207118)
-- Name: as_perms_id_seq_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE as_perms_id_seq_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 54 (OID 207120)
-- Name: as_sess; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE as_sess (
    sessid character(32) NOT NULL,
    userid integer,
    login character varying(255),
    ts timestamp without time zone
);


--
-- TOC entry 55 (OID 207131)
-- Name: as_files; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE as_files (
    id integer NOT NULL,
    gunid bigint NOT NULL,
    name character varying(255) DEFAULT ''::character varying NOT NULL,
    mime character varying(255) DEFAULT ''::character varying NOT NULL,
    ftype character varying(128) DEFAULT ''::character varying NOT NULL,
    state character varying(128) DEFAULT 'empty'::character varying NOT NULL,
    currentlyaccessing integer DEFAULT 0 NOT NULL,
    editedby integer
);


--
-- TOC entry 23 (OID 207148)
-- Name: as_mdata_id_seq_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE as_mdata_id_seq_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 56 (OID 207150)
-- Name: as_mdata; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE as_mdata (
    id integer NOT NULL,
    gunid bigint,
    subjns character varying(255),
    subject character varying(255) DEFAULT ''::character varying NOT NULL,
    predns character varying(255),
    predicate character varying(255) NOT NULL,
    predxml character(1) DEFAULT 'T'::bpchar NOT NULL,
    objns character varying(255),
    object text
);


--
-- TOC entry 57 (OID 207161)
-- Name: as_access; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE as_access (
    gunid bigint,
    token bigint,
    chsum character(32) DEFAULT ''::bpchar NOT NULL,
    ext character varying(128) DEFAULT ''::character varying NOT NULL,
    "type" character varying(20) DEFAULT ''::character varying NOT NULL,
    ts timestamp without time zone
);


--
-- TOC entry 60 (OID 196325)
-- Name: al_test_tree_id_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX al_test_tree_id_idx ON al_test_tree USING btree (id);


--
-- TOC entry 61 (OID 196326)
-- Name: al_test_tree_name_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX al_test_tree_name_idx ON al_test_tree USING btree (name);


--
-- TOC entry 69 (OID 196341)
-- Name: al_test_struct_rid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX al_test_struct_rid_idx ON al_test_struct USING btree (rid);


--
-- TOC entry 64 (OID 196342)
-- Name: al_test_struct_objid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX al_test_struct_objid_idx ON al_test_struct USING btree (objid);


--
-- TOC entry 67 (OID 196343)
-- Name: al_test_struct_parid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX al_test_struct_parid_idx ON al_test_struct USING btree (parid);


--
-- TOC entry 63 (OID 196344)
-- Name: al_test_struct_level_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX al_test_struct_level_idx ON al_test_struct USING btree ("level");


--
-- TOC entry 65 (OID 196345)
-- Name: al_test_struct_objid_level_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX al_test_struct_objid_level_idx ON al_test_struct USING btree (objid, "level");


--
-- TOC entry 66 (OID 196346)
-- Name: al_test_struct_objid_parid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX al_test_struct_objid_parid_idx ON al_test_struct USING btree (objid, parid);


--
-- TOC entry 71 (OID 196352)
-- Name: al_test_classes_id_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX al_test_classes_id_idx ON al_test_classes USING btree (id);


--
-- TOC entry 70 (OID 196353)
-- Name: al_test_classes_cname_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX al_test_classes_cname_idx ON al_test_classes USING btree (cname);


--
-- TOC entry 73 (OID 196356)
-- Name: al_test_cmemb_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX al_test_cmemb_idx ON al_test_cmemb USING btree (objid, cid);


--
-- TOC entry 74 (OID 196368)
-- Name: al_test_subjs_id_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX al_test_subjs_id_idx ON al_test_subjs USING btree (id);


--
-- TOC entry 75 (OID 196369)
-- Name: al_test_subjs_login_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX al_test_subjs_login_idx ON al_test_subjs USING btree (login);


--
-- TOC entry 77 (OID 196379)
-- Name: al_test_smemb_id_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX al_test_smemb_id_idx ON al_test_smemb USING btree (id);


--
-- TOC entry 80 (OID 196390)
-- Name: al_test_perms_permid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX al_test_perms_permid_idx ON al_test_perms USING btree (permid);


--
-- TOC entry 82 (OID 196391)
-- Name: al_test_perms_subj_obj_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX al_test_perms_subj_obj_idx ON al_test_perms USING btree (subj, obj);


--
-- TOC entry 79 (OID 196392)
-- Name: al_test_perms_all_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX al_test_perms_all_idx ON al_test_perms USING btree (subj, "action", obj);


--
-- TOC entry 85 (OID 196403)
-- Name: al_test_sess_sessid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX al_test_sess_sessid_idx ON al_test_sess USING btree (sessid);


--
-- TOC entry 86 (OID 196404)
-- Name: al_test_sess_userid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX al_test_sess_userid_idx ON al_test_sess USING btree (userid);


--
-- TOC entry 83 (OID 196405)
-- Name: al_test_sess_login_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX al_test_sess_login_idx ON al_test_sess USING btree (login);


--
-- TOC entry 87 (OID 206734)
-- Name: ls_tree_id_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX ls_tree_id_idx ON ls_tree USING btree (id);


--
-- TOC entry 88 (OID 206735)
-- Name: ls_tree_name_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX ls_tree_name_idx ON ls_tree USING btree (name);


--
-- TOC entry 96 (OID 206750)
-- Name: ls_struct_rid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX ls_struct_rid_idx ON ls_struct USING btree (rid);


--
-- TOC entry 91 (OID 206751)
-- Name: ls_struct_objid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX ls_struct_objid_idx ON ls_struct USING btree (objid);


--
-- TOC entry 94 (OID 206752)
-- Name: ls_struct_parid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX ls_struct_parid_idx ON ls_struct USING btree (parid);


--
-- TOC entry 90 (OID 206753)
-- Name: ls_struct_level_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX ls_struct_level_idx ON ls_struct USING btree ("level");


--
-- TOC entry 92 (OID 206754)
-- Name: ls_struct_objid_level_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX ls_struct_objid_level_idx ON ls_struct USING btree (objid, "level");


--
-- TOC entry 93 (OID 206755)
-- Name: ls_struct_objid_parid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX ls_struct_objid_parid_idx ON ls_struct USING btree (objid, parid);


--
-- TOC entry 98 (OID 206761)
-- Name: ls_classes_id_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX ls_classes_id_idx ON ls_classes USING btree (id);


--
-- TOC entry 97 (OID 206762)
-- Name: ls_classes_cname_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX ls_classes_cname_idx ON ls_classes USING btree (cname);


--
-- TOC entry 100 (OID 206765)
-- Name: ls_cmemb_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX ls_cmemb_idx ON ls_cmemb USING btree (objid, cid);


--
-- TOC entry 101 (OID 206777)
-- Name: ls_subjs_id_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX ls_subjs_id_idx ON ls_subjs USING btree (id);


--
-- TOC entry 102 (OID 206778)
-- Name: ls_subjs_login_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX ls_subjs_login_idx ON ls_subjs USING btree (login);


--
-- TOC entry 104 (OID 206788)
-- Name: ls_smemb_id_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX ls_smemb_id_idx ON ls_smemb USING btree (id);


--
-- TOC entry 107 (OID 206799)
-- Name: ls_perms_permid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX ls_perms_permid_idx ON ls_perms USING btree (permid);


--
-- TOC entry 109 (OID 206800)
-- Name: ls_perms_subj_obj_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX ls_perms_subj_obj_idx ON ls_perms USING btree (subj, obj);


--
-- TOC entry 106 (OID 206801)
-- Name: ls_perms_all_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX ls_perms_all_idx ON ls_perms USING btree (subj, "action", obj);


--
-- TOC entry 112 (OID 206812)
-- Name: ls_sess_sessid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX ls_sess_sessid_idx ON ls_sess USING btree (sessid);


--
-- TOC entry 113 (OID 206813)
-- Name: ls_sess_userid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX ls_sess_userid_idx ON ls_sess USING btree (userid);


--
-- TOC entry 110 (OID 206814)
-- Name: ls_sess_login_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX ls_sess_login_idx ON ls_sess USING btree (login);


--
-- TOC entry 115 (OID 206829)
-- Name: ls_files_id_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX ls_files_id_idx ON ls_files USING btree (id);


--
-- TOC entry 114 (OID 206830)
-- Name: ls_files_gunid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX ls_files_gunid_idx ON ls_files USING btree (gunid);


--
-- TOC entry 116 (OID 206831)
-- Name: ls_files_name_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX ls_files_name_idx ON ls_files USING btree (name);


--
-- TOC entry 118 (OID 206841)
-- Name: ls_mdata_id_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX ls_mdata_id_idx ON ls_mdata USING btree (id);


--
-- TOC entry 117 (OID 206842)
-- Name: ls_mdata_gunid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX ls_mdata_gunid_idx ON ls_mdata USING btree (gunid);


--
-- TOC entry 120 (OID 206843)
-- Name: ls_mdata_subj_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX ls_mdata_subj_idx ON ls_mdata USING btree (subjns, subject);


--
-- TOC entry 119 (OID 206844)
-- Name: ls_mdata_pred_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX ls_mdata_pred_idx ON ls_mdata USING btree (predns, predicate);


--
-- TOC entry 122 (OID 206850)
-- Name: ls_access_token_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX ls_access_token_idx ON ls_access USING btree (token);


--
-- TOC entry 121 (OID 206851)
-- Name: ls_access_gunid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX ls_access_gunid_idx ON ls_access USING btree (gunid);


--
-- TOC entry 124 (OID 207020)
-- Name: ls_trans_id_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX ls_trans_id_idx ON ls_trans USING btree (id);


--
-- TOC entry 125 (OID 207021)
-- Name: ls_trans_trtok_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX ls_trans_trtok_idx ON ls_trans USING btree (trtok);


--
-- TOC entry 123 (OID 207022)
-- Name: ls_trans_gunid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX ls_trans_gunid_idx ON ls_trans USING btree (gunid);


--
-- TOC entry 126 (OID 207034)
-- Name: ls_pref_id_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX ls_pref_id_idx ON ls_pref USING btree (id);


--
-- TOC entry 127 (OID 207035)
-- Name: ls_pref_subj_key_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX ls_pref_subj_key_idx ON ls_pref USING btree (subjid, keystr);


--
-- TOC entry 128 (OID 207036)
-- Name: ls_pref_subjid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX ls_pref_subjid_idx ON ls_pref USING btree (subjid);


--
-- TOC entry 129 (OID 207050)
-- Name: as_tree_id_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX as_tree_id_idx ON as_tree USING btree (id);


--
-- TOC entry 130 (OID 207051)
-- Name: as_tree_name_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX as_tree_name_idx ON as_tree USING btree (name);


--
-- TOC entry 138 (OID 207066)
-- Name: as_struct_rid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX as_struct_rid_idx ON as_struct USING btree (rid);


--
-- TOC entry 133 (OID 207067)
-- Name: as_struct_objid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX as_struct_objid_idx ON as_struct USING btree (objid);


--
-- TOC entry 136 (OID 207068)
-- Name: as_struct_parid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX as_struct_parid_idx ON as_struct USING btree (parid);


--
-- TOC entry 132 (OID 207069)
-- Name: as_struct_level_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX as_struct_level_idx ON as_struct USING btree ("level");


--
-- TOC entry 134 (OID 207070)
-- Name: as_struct_objid_level_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX as_struct_objid_level_idx ON as_struct USING btree (objid, "level");


--
-- TOC entry 135 (OID 207071)
-- Name: as_struct_objid_parid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX as_struct_objid_parid_idx ON as_struct USING btree (objid, parid);


--
-- TOC entry 140 (OID 207077)
-- Name: as_classes_id_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX as_classes_id_idx ON as_classes USING btree (id);


--
-- TOC entry 139 (OID 207078)
-- Name: as_classes_cname_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX as_classes_cname_idx ON as_classes USING btree (cname);


--
-- TOC entry 142 (OID 207081)
-- Name: as_cmemb_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX as_cmemb_idx ON as_cmemb USING btree (objid, cid);


--
-- TOC entry 143 (OID 207093)
-- Name: as_subjs_id_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX as_subjs_id_idx ON as_subjs USING btree (id);


--
-- TOC entry 144 (OID 207094)
-- Name: as_subjs_login_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX as_subjs_login_idx ON as_subjs USING btree (login);


--
-- TOC entry 146 (OID 207104)
-- Name: as_smemb_id_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX as_smemb_id_idx ON as_smemb USING btree (id);


--
-- TOC entry 149 (OID 207115)
-- Name: as_perms_permid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX as_perms_permid_idx ON as_perms USING btree (permid);


--
-- TOC entry 151 (OID 207116)
-- Name: as_perms_subj_obj_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX as_perms_subj_obj_idx ON as_perms USING btree (subj, obj);


--
-- TOC entry 148 (OID 207117)
-- Name: as_perms_all_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX as_perms_all_idx ON as_perms USING btree (subj, "action", obj);


--
-- TOC entry 154 (OID 207128)
-- Name: as_sess_sessid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX as_sess_sessid_idx ON as_sess USING btree (sessid);


--
-- TOC entry 155 (OID 207129)
-- Name: as_sess_userid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX as_sess_userid_idx ON as_sess USING btree (userid);


--
-- TOC entry 152 (OID 207130)
-- Name: as_sess_login_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX as_sess_login_idx ON as_sess USING btree (login);


--
-- TOC entry 157 (OID 207145)
-- Name: as_files_id_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX as_files_id_idx ON as_files USING btree (id);


--
-- TOC entry 156 (OID 207146)
-- Name: as_files_gunid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX as_files_gunid_idx ON as_files USING btree (gunid);


--
-- TOC entry 158 (OID 207147)
-- Name: as_files_name_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX as_files_name_idx ON as_files USING btree (name);


--
-- TOC entry 160 (OID 207157)
-- Name: as_mdata_id_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE UNIQUE INDEX as_mdata_id_idx ON as_mdata USING btree (id);


--
-- TOC entry 159 (OID 207158)
-- Name: as_mdata_gunid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX as_mdata_gunid_idx ON as_mdata USING btree (gunid);


--
-- TOC entry 162 (OID 207159)
-- Name: as_mdata_subj_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX as_mdata_subj_idx ON as_mdata USING btree (subjns, subject);


--
-- TOC entry 161 (OID 207160)
-- Name: as_mdata_pred_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX as_mdata_pred_idx ON as_mdata USING btree (predns, predicate);


--
-- TOC entry 164 (OID 207166)
-- Name: as_access_token_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX as_access_token_idx ON as_access USING btree (token);


--
-- TOC entry 163 (OID 207167)
-- Name: as_access_gunid_idx; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX as_access_gunid_idx ON as_access USING btree (gunid);


--
-- TOC entry 58 (OID 104455)
-- Name: schedule_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY schedule
    ADD CONSTRAINT schedule_pkey PRIMARY KEY (id);


--
-- TOC entry 59 (OID 104459)
-- Name: playlog_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY playlog
    ADD CONSTRAINT playlog_pkey PRIMARY KEY (id);


--
-- TOC entry 62 (OID 196321)
-- Name: al_test_tree_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY al_test_tree
    ADD CONSTRAINT al_test_tree_pkey PRIMARY KEY (id);


--
-- TOC entry 68 (OID 196329)
-- Name: al_test_struct_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY al_test_struct
    ADD CONSTRAINT al_test_struct_pkey PRIMARY KEY (rid);


--
-- TOC entry 72 (OID 196350)
-- Name: al_test_classes_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY al_test_classes
    ADD CONSTRAINT al_test_classes_pkey PRIMARY KEY (id);


--
-- TOC entry 76 (OID 196366)
-- Name: al_test_subjs_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY al_test_subjs
    ADD CONSTRAINT al_test_subjs_pkey PRIMARY KEY (id);


--
-- TOC entry 78 (OID 196377)
-- Name: al_test_smemb_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY al_test_smemb
    ADD CONSTRAINT al_test_smemb_pkey PRIMARY KEY (id);


--
-- TOC entry 81 (OID 196384)
-- Name: al_test_perms_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY al_test_perms
    ADD CONSTRAINT al_test_perms_pkey PRIMARY KEY (permid);


--
-- TOC entry 84 (OID 196397)
-- Name: al_test_sess_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY al_test_sess
    ADD CONSTRAINT al_test_sess_pkey PRIMARY KEY (sessid);


--
-- TOC entry 89 (OID 206730)
-- Name: ls_tree_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY ls_tree
    ADD CONSTRAINT ls_tree_pkey PRIMARY KEY (id);


--
-- TOC entry 95 (OID 206738)
-- Name: ls_struct_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY ls_struct
    ADD CONSTRAINT ls_struct_pkey PRIMARY KEY (rid);


--
-- TOC entry 99 (OID 206759)
-- Name: ls_classes_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY ls_classes
    ADD CONSTRAINT ls_classes_pkey PRIMARY KEY (id);


--
-- TOC entry 103 (OID 206775)
-- Name: ls_subjs_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY ls_subjs
    ADD CONSTRAINT ls_subjs_pkey PRIMARY KEY (id);


--
-- TOC entry 105 (OID 206786)
-- Name: ls_smemb_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY ls_smemb
    ADD CONSTRAINT ls_smemb_pkey PRIMARY KEY (id);


--
-- TOC entry 108 (OID 206793)
-- Name: ls_perms_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY ls_perms
    ADD CONSTRAINT ls_perms_pkey PRIMARY KEY (permid);


--
-- TOC entry 111 (OID 206806)
-- Name: ls_sess_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY ls_sess
    ADD CONSTRAINT ls_sess_pkey PRIMARY KEY (sessid);


--
-- TOC entry 131 (OID 207046)
-- Name: as_tree_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY as_tree
    ADD CONSTRAINT as_tree_pkey PRIMARY KEY (id);


--
-- TOC entry 137 (OID 207054)
-- Name: as_struct_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY as_struct
    ADD CONSTRAINT as_struct_pkey PRIMARY KEY (rid);


--
-- TOC entry 141 (OID 207075)
-- Name: as_classes_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY as_classes
    ADD CONSTRAINT as_classes_pkey PRIMARY KEY (id);


--
-- TOC entry 145 (OID 207091)
-- Name: as_subjs_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY as_subjs
    ADD CONSTRAINT as_subjs_pkey PRIMARY KEY (id);


--
-- TOC entry 147 (OID 207102)
-- Name: as_smemb_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY as_smemb
    ADD CONSTRAINT as_smemb_pkey PRIMARY KEY (id);


--
-- TOC entry 150 (OID 207109)
-- Name: as_perms_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY as_perms
    ADD CONSTRAINT as_perms_pkey PRIMARY KEY (permid);


--
-- TOC entry 153 (OID 207122)
-- Name: as_sess_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY as_sess
    ADD CONSTRAINT as_sess_pkey PRIMARY KEY (sessid);


--
-- TOC entry 165 (OID 196331)
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY al_test_struct
    ADD CONSTRAINT "$1" FOREIGN KEY (objid) REFERENCES al_test_tree(id) ON DELETE CASCADE;


--
-- TOC entry 166 (OID 196335)
-- Name: $2; Type: FK CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY al_test_struct
    ADD CONSTRAINT "$2" FOREIGN KEY (parid) REFERENCES al_test_tree(id) ON DELETE CASCADE;


--
-- TOC entry 167 (OID 196386)
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY al_test_perms
    ADD CONSTRAINT "$1" FOREIGN KEY (subj) REFERENCES al_test_subjs(id) ON DELETE CASCADE;


--
-- TOC entry 168 (OID 196399)
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY al_test_sess
    ADD CONSTRAINT "$1" FOREIGN KEY (userid) REFERENCES al_test_subjs(id) ON DELETE CASCADE;


--
-- TOC entry 169 (OID 206740)
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY ls_struct
    ADD CONSTRAINT "$1" FOREIGN KEY (objid) REFERENCES ls_tree(id) ON DELETE CASCADE;


--
-- TOC entry 170 (OID 206744)
-- Name: $2; Type: FK CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY ls_struct
    ADD CONSTRAINT "$2" FOREIGN KEY (parid) REFERENCES ls_tree(id) ON DELETE CASCADE;


--
-- TOC entry 171 (OID 206795)
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY ls_perms
    ADD CONSTRAINT "$1" FOREIGN KEY (subj) REFERENCES ls_subjs(id) ON DELETE CASCADE;


--
-- TOC entry 172 (OID 206808)
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY ls_sess
    ADD CONSTRAINT "$1" FOREIGN KEY (userid) REFERENCES ls_subjs(id) ON DELETE CASCADE;


--
-- TOC entry 173 (OID 206825)
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY ls_files
    ADD CONSTRAINT "$1" FOREIGN KEY (editedby) REFERENCES ls_subjs(id);


--
-- TOC entry 174 (OID 207030)
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY ls_pref
    ADD CONSTRAINT "$1" FOREIGN KEY (subjid) REFERENCES ls_subjs(id) ON DELETE CASCADE;


--
-- TOC entry 175 (OID 207056)
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY as_struct
    ADD CONSTRAINT "$1" FOREIGN KEY (objid) REFERENCES as_tree(id) ON DELETE CASCADE;


--
-- TOC entry 176 (OID 207060)
-- Name: $2; Type: FK CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY as_struct
    ADD CONSTRAINT "$2" FOREIGN KEY (parid) REFERENCES as_tree(id) ON DELETE CASCADE;


--
-- TOC entry 177 (OID 207111)
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY as_perms
    ADD CONSTRAINT "$1" FOREIGN KEY (subj) REFERENCES as_subjs(id) ON DELETE CASCADE;


--
-- TOC entry 178 (OID 207124)
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY as_sess
    ADD CONSTRAINT "$1" FOREIGN KEY (userid) REFERENCES as_subjs(id) ON DELETE CASCADE;


--
-- TOC entry 179 (OID 207141)
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY as_files
    ADD CONSTRAINT "$1" FOREIGN KEY (editedby) REFERENCES as_subjs(id);


SET SESSION AUTHORIZATION 'postgres';

--
-- TOC entry 3 (OID 2200)
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'Standard public schema';



-- End of dump.
