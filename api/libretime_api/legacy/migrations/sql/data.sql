INSERT INTO cc_subjs ("login", "type", "pass") VALUES ('admin', 'A', md5('admin'));
-- added in 2.3
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('off_air_meta', 'LibreTime - offline', 'string');
INSERT INTO cc_pref("keystr", "valstr") VALUES('enable_replay_gain', 1);
-- end of added in 2.3

-- added in 2.1
INSERT INTO cc_pref("keystr", "valstr") VALUES('scheduled_play_switch', 'on');

INSERT INTO cc_live_log("state", "start_time") VALUES('S', now() at time zone 'UTC');
-- end of added in 2.1

-- added in 2.0.0
INSERT INTO cc_pref("keystr", "valstr") VALUES('stream_type', 'ogg, mp3, opus, aac');
INSERT INTO cc_pref("keystr", "valstr") VALUES('stream_bitrate', '24, 32, 48, 64, 96, 128, 160, 192, 224, 256, 320');
INSERT INTO cc_pref("keystr", "valstr") VALUES('num_of_streams', '3');
INSERT INTO cc_pref("keystr", "valstr") VALUES('max_bitrate', '320');
INSERT INTO cc_pref("keystr", "valstr") VALUES('plan_level', 'disabled');

INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('output_sound_device', 'false', 'boolean');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('output_sound_device_type', 'ALSA', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('icecast_vorbis_metadata', 'false', 'boolean');

INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_enable', 'true', 'boolean');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_output', 'icecast', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_type', 'ogg', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_bitrate', '128', 'integer');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_host', '127.0.0.1', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_port', '8000', 'integer');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_user', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_pass', 'hackme', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_admin_user', 'admin', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_admin_pass', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_mount', 'main', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_url', 'https://libretime.org', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_description', 'LibreTime Radio! Stream #1', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_genre', 'genre', 'string');

INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_enable', 'false', 'boolean');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_output', 'icecast', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_type', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_bitrate', '', 'integer');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_host', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_port', '', 'integer');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_user', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_pass', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_admin_user', 'admin', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_admin_pass', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_mount', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_url', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_description', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_genre', '', 'string');

INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_enable', 'false', 'boolean');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_output', 'icecast', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_type', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_bitrate', '', 'integer');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_host', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_port', '', 'integer');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_user', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_pass', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_admin_user', 'admin', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_admin_pass', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_mount', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_url', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_description', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_genre', '', 'string');
-- end of added in 2.0.0


INSERT INTO cc_country (isocode, name) VALUES ('AFG', 'Afghanistan ');
INSERT INTO cc_country (isocode, name) VALUES ('ALA', 'Åland Islands');
INSERT INTO cc_country (isocode, name) VALUES ('ALB', 'Albania ');
INSERT INTO cc_country (isocode, name) VALUES ('DZA', 'Algeria ');
INSERT INTO cc_country (isocode, name) VALUES ('ASM', 'American Samoa ');
INSERT INTO cc_country (isocode, name) VALUES ('AND', 'Andorra ');
INSERT INTO cc_country (isocode, name) VALUES ('AGO', 'Angola ');
INSERT INTO cc_country (isocode, name) VALUES ('AIA', 'Anguilla ');
INSERT INTO cc_country (isocode, name) VALUES ('ATG', 'Antigua and Barbuda ');
INSERT INTO cc_country (isocode, name) VALUES ('ARG', 'Argentina ');
INSERT INTO cc_country (isocode, name) VALUES ('ARM', 'Armenia ');
INSERT INTO cc_country (isocode, name) VALUES ('ABW', 'Aruba ');
INSERT INTO cc_country (isocode, name) VALUES ('AUS', 'Australia ');
INSERT INTO cc_country (isocode, name) VALUES ('AUT', 'Austria ');
INSERT INTO cc_country (isocode, name) VALUES ('AZE', 'Azerbaijan ');
INSERT INTO cc_country (isocode, name) VALUES ('BHS', 'Bahamas ');
INSERT INTO cc_country (isocode, name) VALUES ('BHR', 'Bahrain ');
INSERT INTO cc_country (isocode, name) VALUES ('BGD', 'Bangladesh ');
INSERT INTO cc_country (isocode, name) VALUES ('BRB', 'Barbados ');
INSERT INTO cc_country (isocode, name) VALUES ('BLR', 'Belarus ');
INSERT INTO cc_country (isocode, name) VALUES ('BEL', 'Belgium ');
INSERT INTO cc_country (isocode, name) VALUES ('BLZ', 'Belize ');
INSERT INTO cc_country (isocode, name) VALUES ('BEN', 'Benin ');
INSERT INTO cc_country (isocode, name) VALUES ('BMU', 'Bermuda ');
INSERT INTO cc_country (isocode, name) VALUES ('BTN', 'Bhutan ');
INSERT INTO cc_country (isocode, name) VALUES ('BOL', 'Bolivia (Plurinational State of) ');
INSERT INTO cc_country (isocode, name) VALUES ('BES', 'Bonaire, Saint Eustatius and Saba');
INSERT INTO cc_country (isocode, name) VALUES ('BIH', 'Bosnia and Herzegovina ');
INSERT INTO cc_country (isocode, name) VALUES ('BWA', 'Botswana ');
INSERT INTO cc_country (isocode, name) VALUES ('BRA', 'Brazil ');
INSERT INTO cc_country (isocode, name) VALUES ('VGB', 'British Virgin Islands ');
INSERT INTO cc_country (isocode, name) VALUES ('BRN', 'Brunei Darussalam ');
INSERT INTO cc_country (isocode, name) VALUES ('BGR', 'Bulgaria ');
INSERT INTO cc_country (isocode, name) VALUES ('BFA', 'Burkina Faso ');
INSERT INTO cc_country (isocode, name) VALUES ('BDI', 'Burundi ');
INSERT INTO cc_country (isocode, name) VALUES ('KHM', 'Cambodia ');
INSERT INTO cc_country (isocode, name) VALUES ('CMR', 'Cameroon ');
INSERT INTO cc_country (isocode, name) VALUES ('CAN', 'Canada ');
INSERT INTO cc_country (isocode, name) VALUES ('CPV', 'Cape Verde ');
INSERT INTO cc_country (isocode, name) VALUES ('CYM', 'Cayman Islands ');
INSERT INTO cc_country (isocode, name) VALUES ('CAF', 'Central African Republic ');
INSERT INTO cc_country (isocode, name) VALUES ('TCD', 'Chad ');
INSERT INTO cc_country (isocode, name) VALUES ('CHL', 'Chile ');
INSERT INTO cc_country (isocode, name) VALUES ('CHN', 'China ');
INSERT INTO cc_country (isocode, name) VALUES ('HKG', 'China, Hong Kong Special Administrative Region');
INSERT INTO cc_country (isocode, name) VALUES ('MAC', 'China, Macao Special Administrative Region');
INSERT INTO cc_country (isocode, name) VALUES ('COL', 'Colombia ');
INSERT INTO cc_country (isocode, name) VALUES ('COM', 'Comoros ');
INSERT INTO cc_country (isocode, name) VALUES ('COG', 'Congo ');
INSERT INTO cc_country (isocode, name) VALUES ('COK', 'Cook Islands ');
INSERT INTO cc_country (isocode, name) VALUES ('CRI', 'Costa Rica ');
INSERT INTO cc_country (isocode, name) VALUES ('CIV', 'Côte d''Ivoire ');
INSERT INTO cc_country (isocode, name) VALUES ('HRV', 'Croatia ');
INSERT INTO cc_country (isocode, name) VALUES ('CUB', 'Cuba ');
INSERT INTO cc_country (isocode, name) VALUES ('CUW', 'Curaçao');
INSERT INTO cc_country (isocode, name) VALUES ('CYP', 'Cyprus ');
INSERT INTO cc_country (isocode, name) VALUES ('CZE', 'Czech Republic ');
INSERT INTO cc_country (isocode, name) VALUES ('PRK', 'Democratic People''s Republic of Korea ');
INSERT INTO cc_country (isocode, name) VALUES ('COD', 'Democratic Republic of the Congo ');
INSERT INTO cc_country (isocode, name) VALUES ('DNK', 'Denmark ');
INSERT INTO cc_country (isocode, name) VALUES ('DJI', 'Djibouti ');
INSERT INTO cc_country (isocode, name) VALUES ('DMA', 'Dominica ');
INSERT INTO cc_country (isocode, name) VALUES ('DOM', 'Dominican Republic ');
INSERT INTO cc_country (isocode, name) VALUES ('ECU', 'Ecuador ');
INSERT INTO cc_country (isocode, name) VALUES ('EGY', 'Egypt ');
INSERT INTO cc_country (isocode, name) VALUES ('SLV', 'El Salvador ');
INSERT INTO cc_country (isocode, name) VALUES ('GNQ', 'Equatorial Guinea ');
INSERT INTO cc_country (isocode, name) VALUES ('ERI', 'Eritrea ');
INSERT INTO cc_country (isocode, name) VALUES ('EST', 'Estonia ');
INSERT INTO cc_country (isocode, name) VALUES ('ETH', 'Ethiopia ');
INSERT INTO cc_country (isocode, name) VALUES ('FRO', 'Faeroe Islands ');
INSERT INTO cc_country (isocode, name) VALUES ('FLK', 'Falkland Islands (Malvinas) ');
INSERT INTO cc_country (isocode, name) VALUES ('FJI', 'Fiji ');
INSERT INTO cc_country (isocode, name) VALUES ('FIN', 'Finland ');
INSERT INTO cc_country (isocode, name) VALUES ('FRA', 'France ');
INSERT INTO cc_country (isocode, name) VALUES ('GUF', 'French Guiana ');
INSERT INTO cc_country (isocode, name) VALUES ('PYF', 'French Polynesia ');
INSERT INTO cc_country (isocode, name) VALUES ('GAB', 'Gabon ');
INSERT INTO cc_country (isocode, name) VALUES ('GMB', 'Gambia ');
INSERT INTO cc_country (isocode, name) VALUES ('GEO', 'Georgia ');
INSERT INTO cc_country (isocode, name) VALUES ('DEU', 'Germany ');
INSERT INTO cc_country (isocode, name) VALUES ('GHA', 'Ghana ');
INSERT INTO cc_country (isocode, name) VALUES ('GIB', 'Gibraltar ');
INSERT INTO cc_country (isocode, name) VALUES ('GRC', 'Greece ');
INSERT INTO cc_country (isocode, name) VALUES ('GRL', 'Greenland ');
INSERT INTO cc_country (isocode, name) VALUES ('GRD', 'Grenada ');
INSERT INTO cc_country (isocode, name) VALUES ('GLP', 'Guadeloupe ');
INSERT INTO cc_country (isocode, name) VALUES ('GUM', 'Guam ');
INSERT INTO cc_country (isocode, name) VALUES ('GTM', 'Guatemala ');
INSERT INTO cc_country (isocode, name) VALUES ('GGY', 'Guernsey');
INSERT INTO cc_country (isocode, name) VALUES ('GIN', 'Guinea ');
INSERT INTO cc_country (isocode, name) VALUES ('GNB', 'Guinea-Bissau ');
INSERT INTO cc_country (isocode, name) VALUES ('GUY', 'Guyana ');
INSERT INTO cc_country (isocode, name) VALUES ('HTI', 'Haiti ');
INSERT INTO cc_country (isocode, name) VALUES ('VAT', 'Holy See ');
INSERT INTO cc_country (isocode, name) VALUES ('HND', 'Honduras ');
INSERT INTO cc_country (isocode, name) VALUES ('HUN', 'Hungary ');
INSERT INTO cc_country (isocode, name) VALUES ('ISL', 'Iceland ');
INSERT INTO cc_country (isocode, name) VALUES ('IND', 'India ');
INSERT INTO cc_country (isocode, name) VALUES ('IDN', 'Indonesia ');
INSERT INTO cc_country (isocode, name) VALUES ('IRN', 'Iran (Islamic Republic of)');
INSERT INTO cc_country (isocode, name) VALUES ('IRQ', 'Iraq ');
INSERT INTO cc_country (isocode, name) VALUES ('IRL', 'Ireland ');
INSERT INTO cc_country (isocode, name) VALUES ('IMN', 'Isle of Man ');
INSERT INTO cc_country (isocode, name) VALUES ('ISR', 'Israel ');
INSERT INTO cc_country (isocode, name) VALUES ('ITA', 'Italy ');
INSERT INTO cc_country (isocode, name) VALUES ('JAM', 'Jamaica ');
INSERT INTO cc_country (isocode, name) VALUES ('JPN', 'Japan ');
INSERT INTO cc_country (isocode, name) VALUES ('JEY', 'Jersey');
INSERT INTO cc_country (isocode, name) VALUES ('JOR', 'Jordan ');
INSERT INTO cc_country (isocode, name) VALUES ('KAZ', 'Kazakhstan ');
INSERT INTO cc_country (isocode, name) VALUES ('KEN', 'Kenya ');
INSERT INTO cc_country (isocode, name) VALUES ('KIR', 'Kiribati ');
INSERT INTO cc_country (isocode, name) VALUES ('KWT', 'Kuwait ');
INSERT INTO cc_country (isocode, name) VALUES ('KGZ', 'Kyrgyzstan ');
INSERT INTO cc_country (isocode, name) VALUES ('LAO', 'Lao People''s Democratic Republic ');
INSERT INTO cc_country (isocode, name) VALUES ('LVA', 'Latvia ');
INSERT INTO cc_country (isocode, name) VALUES ('LBN', 'Lebanon ');
INSERT INTO cc_country (isocode, name) VALUES ('LSO', 'Lesotho ');
INSERT INTO cc_country (isocode, name) VALUES ('LBR', 'Liberia ');
INSERT INTO cc_country (isocode, name) VALUES ('LBY', 'Libyan Arab Jamahiriya ');
INSERT INTO cc_country (isocode, name) VALUES ('LIE', 'Liechtenstein ');
INSERT INTO cc_country (isocode, name) VALUES ('LTU', 'Lithuania ');
INSERT INTO cc_country (isocode, name) VALUES ('LUX', 'Luxembourg ');
INSERT INTO cc_country (isocode, name) VALUES ('MDG', 'Madagascar ');
INSERT INTO cc_country (isocode, name) VALUES ('MWI', 'Malawi ');
INSERT INTO cc_country (isocode, name) VALUES ('MYS', 'Malaysia ');
INSERT INTO cc_country (isocode, name) VALUES ('MDV', 'Maldives ');
INSERT INTO cc_country (isocode, name) VALUES ('MLI', 'Mali ');
INSERT INTO cc_country (isocode, name) VALUES ('MLT', 'Malta ');
INSERT INTO cc_country (isocode, name) VALUES ('MHL', 'Marshall Islands ');
INSERT INTO cc_country (isocode, name) VALUES ('MTQ', 'Martinique ');
INSERT INTO cc_country (isocode, name) VALUES ('MRT', 'Mauritania ');
INSERT INTO cc_country (isocode, name) VALUES ('MUS', 'Mauritius ');
INSERT INTO cc_country (isocode, name) VALUES ('MYT', 'Mayotte');
INSERT INTO cc_country (isocode, name) VALUES ('MEX', 'Mexico ');
INSERT INTO cc_country (isocode, name) VALUES ('FSM', 'Micronesia (Federated States of)');
INSERT INTO cc_country (isocode, name) VALUES ('MCO', 'Monaco ');
INSERT INTO cc_country (isocode, name) VALUES ('MNG', 'Mongolia ');
INSERT INTO cc_country (isocode, name) VALUES ('MNE', 'Montenegro');
INSERT INTO cc_country (isocode, name) VALUES ('MSR', 'Montserrat ');
INSERT INTO cc_country (isocode, name) VALUES ('MAR', 'Morocco ');
INSERT INTO cc_country (isocode, name) VALUES ('MOZ', 'Mozambique ');
INSERT INTO cc_country (isocode, name) VALUES ('MMR', 'Myanmar ');
INSERT INTO cc_country (isocode, name) VALUES ('NAM', 'Namibia ');
INSERT INTO cc_country (isocode, name) VALUES ('NRU', 'Nauru ');
INSERT INTO cc_country (isocode, name) VALUES ('NPL', 'Nepal ');
INSERT INTO cc_country (isocode, name) VALUES ('NLD', 'Netherlands ');
INSERT INTO cc_country (isocode, name) VALUES ('NCL', 'New Caledonia ');
INSERT INTO cc_country (isocode, name) VALUES ('NZL', 'New Zealand ');
INSERT INTO cc_country (isocode, name) VALUES ('NIC', 'Nicaragua ');
INSERT INTO cc_country (isocode, name) VALUES ('NER', 'Niger ');
INSERT INTO cc_country (isocode, name) VALUES ('NGA', 'Nigeria ');
INSERT INTO cc_country (isocode, name) VALUES ('NIU', 'Niue ');
INSERT INTO cc_country (isocode, name) VALUES ('NFK', 'Norfolk Island ');
INSERT INTO cc_country (isocode, name) VALUES ('MNP', 'Northern Mariana Islands ');
INSERT INTO cc_country (isocode, name) VALUES ('NOR', 'Norway ');
INSERT INTO cc_country (isocode, name) VALUES ('PSE', 'Occupied Palestinian Territory ');
INSERT INTO cc_country (isocode, name) VALUES ('OMN', 'Oman ');
INSERT INTO cc_country (isocode, name) VALUES ('PAK', 'Pakistan ');
INSERT INTO cc_country (isocode, name) VALUES ('PLW', 'Palau ');
INSERT INTO cc_country (isocode, name) VALUES ('PAN', 'Panama ');
INSERT INTO cc_country (isocode, name) VALUES ('PNG', 'Papua New Guinea ');
INSERT INTO cc_country (isocode, name) VALUES ('PRY', 'Paraguay ');
INSERT INTO cc_country (isocode, name) VALUES ('PER', 'Peru ');
INSERT INTO cc_country (isocode, name) VALUES ('PHL', 'Philippines ');
INSERT INTO cc_country (isocode, name) VALUES ('PCN', 'Pitcairn ');
INSERT INTO cc_country (isocode, name) VALUES ('POL', 'Poland ');
INSERT INTO cc_country (isocode, name) VALUES ('PRT', 'Portugal ');
INSERT INTO cc_country (isocode, name) VALUES ('PRI', 'Puerto Rico ');
INSERT INTO cc_country (isocode, name) VALUES ('QAT', 'Qatar ');
INSERT INTO cc_country (isocode, name) VALUES ('KOR', 'Republic of Korea ');
INSERT INTO cc_country (isocode, name) VALUES ('MDA', 'Republic of Moldova');
INSERT INTO cc_country (isocode, name) VALUES ('REU', 'Réunion ');
INSERT INTO cc_country (isocode, name) VALUES ('ROU', 'Romania ');
INSERT INTO cc_country (isocode, name) VALUES ('RUS', 'Russian Federation ');
INSERT INTO cc_country (isocode, name) VALUES ('RWA', 'Rwanda ');
INSERT INTO cc_country (isocode, name) VALUES ('BLM', 'Saint-Barthélemy');
INSERT INTO cc_country (isocode, name) VALUES ('SHN', 'Saint Helena ');
INSERT INTO cc_country (isocode, name) VALUES ('KNA', 'Saint Kitts and Nevis ');
INSERT INTO cc_country (isocode, name) VALUES ('LCA', 'Saint Lucia ');
INSERT INTO cc_country (isocode, name) VALUES ('MAF', 'Saint-Martin (French part)');
INSERT INTO cc_country (isocode, name) VALUES ('SPM', 'Saint Pierre and Miquelon ');
INSERT INTO cc_country (isocode, name) VALUES ('VCT', 'Saint Vincent and the Grenadines ');
INSERT INTO cc_country (isocode, name) VALUES ('WSM', 'Samoa ');
INSERT INTO cc_country (isocode, name) VALUES ('SMR', 'San Marino ');
INSERT INTO cc_country (isocode, name) VALUES ('STP', 'Sao Tome and Principe ');
INSERT INTO cc_country (isocode, name) VALUES ('SAU', 'Saudi Arabia ');
INSERT INTO cc_country (isocode, name) VALUES ('SEN', 'Senegal ');
INSERT INTO cc_country (isocode, name) VALUES ('SRB', 'Serbia ');
INSERT INTO cc_country (isocode, name) VALUES ('SYC', 'Seychelles ');
INSERT INTO cc_country (isocode, name) VALUES ('SLE', 'Sierra Leone ');
INSERT INTO cc_country (isocode, name) VALUES ('SGP', 'Singapore ');
INSERT INTO cc_country (isocode, name) VALUES ('SXM', 'Sint Maarten (Dutch part)');
INSERT INTO cc_country (isocode, name) VALUES ('SVK', 'Slovakia ');
INSERT INTO cc_country (isocode, name) VALUES ('SVN', 'Slovenia ');
INSERT INTO cc_country (isocode, name) VALUES ('SLB', 'Solomon Islands ');
INSERT INTO cc_country (isocode, name) VALUES ('SOM', 'Somalia ');
INSERT INTO cc_country (isocode, name) VALUES ('ZAF', 'South Africa ');
INSERT INTO cc_country (isocode, name) VALUES ('ESP', 'Spain ');
INSERT INTO cc_country (isocode, name) VALUES ('LKA', 'Sri Lanka ');
INSERT INTO cc_country (isocode, name) VALUES ('SDN', 'Sudan ');
INSERT INTO cc_country (isocode, name) VALUES ('SUR', 'Suriname ');
INSERT INTO cc_country (isocode, name) VALUES ('SJM', 'Svalbard and Jan Mayen Islands ');
INSERT INTO cc_country (isocode, name) VALUES ('SWZ', 'Swaziland ');
INSERT INTO cc_country (isocode, name) VALUES ('SWE', 'Sweden ');
INSERT INTO cc_country (isocode, name) VALUES ('CHE', 'Switzerland ');
INSERT INTO cc_country (isocode, name) VALUES ('SYR', 'Syrian Arab Republic ');
INSERT INTO cc_country (isocode, name) VALUES ('TJK', 'Tajikistan ');
INSERT INTO cc_country (isocode, name) VALUES ('THA', 'Thailand ');
INSERT INTO cc_country (isocode, name) VALUES ('MKD', 'The former Yugoslav Republic of Macedonia ');
INSERT INTO cc_country (isocode, name) VALUES ('TLS', 'Timor-Leste');
INSERT INTO cc_country (isocode, name) VALUES ('TGO', 'Togo ');
INSERT INTO cc_country (isocode, name) VALUES ('TKL', 'Tokelau ');
INSERT INTO cc_country (isocode, name) VALUES ('TON', 'Tonga ');
INSERT INTO cc_country (isocode, name) VALUES ('TTO', 'Trinidad and Tobago ');
INSERT INTO cc_country (isocode, name) VALUES ('TUN', 'Tunisia ');
INSERT INTO cc_country (isocode, name) VALUES ('TUR', 'Turkey ');
INSERT INTO cc_country (isocode, name) VALUES ('TKM', 'Turkmenistan ');
INSERT INTO cc_country (isocode, name) VALUES ('TCA', 'Turks and Caicos Islands ');
INSERT INTO cc_country (isocode, name) VALUES ('TUV', 'Tuvalu ');
INSERT INTO cc_country (isocode, name) VALUES ('UGA', 'Uganda ');
INSERT INTO cc_country (isocode, name) VALUES ('UKR', 'Ukraine ');
INSERT INTO cc_country (isocode, name) VALUES ('ARE', 'United Arab Emirates ');
INSERT INTO cc_country (isocode, name) VALUES ('GBR', 'United Kingdom of Great Britain and Northern Ireland');
INSERT INTO cc_country (isocode, name) VALUES ('TZA', 'United Republic of Tanzania ');
INSERT INTO cc_country (isocode, name) VALUES ('USA', 'United States of America');
INSERT INTO cc_country (isocode, name) VALUES ('VIR', 'United States Virgin Islands ');
INSERT INTO cc_country (isocode, name) VALUES ('URY', 'Uruguay ');
INSERT INTO cc_country (isocode, name) VALUES ('UZB', 'Uzbekistan ');
INSERT INTO cc_country (isocode, name) VALUES ('VUT', 'Vanuatu ');
INSERT INTO cc_country (isocode, name) VALUES ('VEN', 'Venezuela (Bolivarian Republic of)');
INSERT INTO cc_country (isocode, name) VALUES ('VNM', 'Viet Nam ');
INSERT INTO cc_country (isocode, name) VALUES ('WLF', 'Wallis and Futuna Islands ');
INSERT INTO cc_country (isocode, name) VALUES ('ESH', 'Western Sahara ');
INSERT INTO cc_country (isocode, name) VALUES ('YEM', 'Yemen ');
INSERT INTO cc_country (isocode, name) VALUES ('ZMB', 'Zambia ');
INSERT INTO cc_country (isocode, name) VALUES ('ZWE', 'Zimbabwe ');


-- added in 2.2
INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s1_name', 'LibreTime!', 'string');
INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s2_name', '', 'string');
INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s3_name', '', 'string');


INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s1_channels', 'stereo', 'string');
INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s2_channels', 'stereo', 'string');
INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s3_channels', 'stereo', 'string');
-- end of added in 2.2


-- added in 2.3
INSERT INTO cc_pref("keystr", "valstr") VALUES('locale', 'en_CA');

INSERT INTO cc_pref("subjid", "keystr", "valstr") VALUES(1, 'user_locale', 'en_CA');

-- end of added in 2.3

-- added in 2.5.2

INSERT INTO cc_pref (keystr, valstr) VALUES ('timezone', 'UTC');
-- We don't want to set the user timezone by default - it should instead use the station timezone
-- until the user changes it manually.
-- INSERT INTO cc_pref (subjid, keystr, valstr) VALUES (1, 'user_timezone', 'UTC');

INSERT INTO cc_pref (keystr, valstr) VALUES ('import_timestamp', '0');

--end added in 2.5.2

INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_enable', 'false', 'boolean');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_output', 'icecast', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_name', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_type', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_bitrate', '', 'integer');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_host', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_port', '', 'integer');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_user', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_pass', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_admin_user', 'admin', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_admin_pass', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_mount', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_url', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_description', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_genre', '', 'string');
INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s4_channels', 'stereo', 'string');

-- added in 2.5.14 - this can't be set up in Propel's XML schema, so we need to do it here -- Duncan

ALTER TABLE cc_pref ALTER COLUMN subjid SET DEFAULT NULL;
CREATE UNIQUE INDEX cc_pref_key_idx ON cc_pref (keystr) WHERE subjid IS NULL;
ANALYZE cc_pref; -- this validates the new partial index

--end added in 2.5.14

-- For now, just needs to be truthy - to be updated later; we should find a better way to implement this...
INSERT INTO cc_pref("keystr", "valstr") VALUES('whats_new_dialog_viewed', 1);

--added for LibreTime to turn on podcast album override by default 3.0.0.alpha6
INSERT INTO cc_pref("keystr", "valstr") VALUES('podcast_album_override', 1);
INSERT INTO cc_pref("keystr", "valstr") VALUES('podcast_auto_smartblock', 0);
-- end

INSERT INTO cc_pref("keystr", "valstr") VALUES('default_stream_mount_point', 'main');
