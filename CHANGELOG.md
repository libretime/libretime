<a name="3.0.0-alpha.13"></a>

## [3.0.0-alpha.13](https://github.com/libretime/libretime/compare/3.0.0-alpha.12...3.0.0-alpha.13) (2022-07-15)

- [Release note](https://libretime.org/docs/releases/3.0.0-alpha.13/)

### Features

- remove unused python3-venv package ([#1728](https://github.com/libretime/libretime/issues/1728))
- **api:** split api into multiple apps ([#1626](https://github.com/libretime/libretime/issues/1626))
- improve apache configuration ([#1784](https://github.com/libretime/libretime/issues/1784))
- **api:** replace uwsgi with gunicorn ([#1785](https://github.com/libretime/libretime/issues/1785))
- replace exploded base\_\* with public_url
- **shared:** compress logs with gz ([#1827](https://github.com/libretime/libretime/issues/1827))
- **shared:** remove unused abstract app ([#1828](https://github.com/libretime/libretime/issues/1828))
- replace click envar with auto_envvar_prefix ([#1829](https://github.com/libretime/libretime/issues/1829))
- **shared:** allow to disable log rotate/retention
- **legacy:** change logrotate config deploy path
- rotate logs using libretime user
- **legacy:** improve rabbitmq check ([#1839](https://github.com/libretime/libretime/issues/1839))
- **worker:** don't run with a dedicated user
- **playout:** remove unused liquidsoap_prepare_terminate.py ([#1854](https://github.com/libretime/libretime/issues/1854))
- **legacy:** check services using systemctl ([#1856](https://github.com/libretime/libretime/issues/1856))
- change config file format to yaml
- change config filename to config.yml
- change config dir path to /etc/libretime
- **installer:** rewrite install script
- replace php migration with django migration
- drop cc_locale table
- **api:** create set_icecast_passwords command
- **installer:** post install setup instructions
- add systemd libretime.target
- move allowed cors url to configuration file
- remove php web installer
- move storage path setting to configuration file
- **installer:** rename shared files path to /usr/share/libretime
- **shared:** add config trailing slash sanitizer ([#1870](https://github.com/libretime/libretime/issues/1870))
- rename default stream mount point to main
- **api:** rename user model fields ([#1902](https://github.com/libretime/libretime/issues/1902))
- remove unused cc_sess table ([#1907](https://github.com/libretime/libretime/issues/1907))
- remove unused cc_perms table ([#1909](https://github.com/libretime/libretime/issues/1909))
- **api:** rename podcasts models fields ([#1910](https://github.com/libretime/libretime/issues/1910))
- **analyzer:** move compute_md5 to shared library
- **api:** create bulk_import command
- **legacy:** compute md5 during early upload
- **api:** rename track type to library
- **legacy:** add Ukrainian language
- **legacy:** don't guess cors url from server
- **installer:** load .env file to persistent install config
- use dedicated 'libretime' user
- replace apache2 with nginx + php-fpm
- **api:** listen on unix socket with gunicorn
- **api:** use uvicorn as asgi server with gunicorn
- set default locale to en_US
- remove unused cc_country table

### Bug Fixes

- add gettext for legacy locale generation ([#1720](https://github.com/libretime/libretime/issues/1720))
- **installer:** install software-properties-common when required
- **installer:** always install fresh packages
- **api:** prevent timing attacke on api key ([#1771](https://github.com/libretime/libretime/issues/1771))
- **legacy:** load vendors during config init
- **legacy:** bypass config validation during django migration
- **legacy:** the ini config parser requires a .conf ext
- **playout:** disable playout-notify log rotation
- don't set log folder permissions recursively
- **shared:** allow list settings ([#1837](https://github.com/libretime/libretime/issues/1837))
- **legacy:** station url always has a trailing slash ([#1838](https://github.com/libretime/libretime/issues/1838))
- **legacy:** check if libretime-api is running ([#1841](https://github.com/libretime/libretime/issues/1841))
- don't add track types only on migration
- correct cc_file.artwork column size
- **legacy:** remove not null constraint when using default
- **api:** missing build-essential to build psycopg2
- drop unused sequences
- **api:** allow updating default_icecast_password ([#1872](https://github.com/libretime/libretime/issues/1872))
- **deps:** update dependency [@cmfcmf](https://github.com/cmfcmf)/docusaurus-search-local to ^0.11.0 ([#1873](https://github.com/libretime/libretime/issues/1873))
- **legacy:** remove file directory metadata ([#1887](https://github.com/libretime/libretime/issues/1887))
- **api:** update model fields in set_icecast_passwords ([#1903](https://github.com/libretime/libretime/issues/1903))
- **api:** cascade when dropping table ([#1908](https://github.com/libretime/libretime/issues/1908))
- **legacy:** station-metadata api endpoint
- **legacy:** don't log 'could not obtain lock' exception ([#1943](https://github.com/libretime/libretime/issues/1943))
- **legacy:** check empty before iteration on files
- use constrained foreign key for files track_type
- **deps:** update dependency mermaid to v9.1.2 [security] ([#1948](https://github.com/libretime/libretime/issues/1948))
- **installer:** update version file unless tarball ([#1950](https://github.com/libretime/libretime/issues/1950))
- prevent data loose on track_types_id migration ([#1949](https://github.com/libretime/libretime/issues/1949))
- use track_type_id in smartblock criteria
- **legacy:** no invalid track type in smartblock criteria

### Documentation

- create 3.0.0-alpha.12 docs
- add version nav dropdown
- add urls redirects ([#1581](https://github.com/libretime/libretime/issues/1581))
- fix broken links to celery project ([#1744](https://github.com/libretime/libretime/issues/1744))
- draft architecture design document ([#1736](https://github.com/libretime/libretime/issues/1736))
- start troubleshooting with syslog over libretime logs ([#1783](https://github.com/libretime/libretime/issues/1783))
- update apache log filepath ([#1811](https://github.com/libretime/libretime/issues/1811))
- explicitly mention lack of 22.04 support ([#1858](https://github.com/libretime/libretime/issues/1858))
- add pre upgrade procedure warnings
- update install procedure
- add missing storage config upgrade procedure ([#1871](https://github.com/libretime/libretime/issues/1871))
- remove packages based install ([#1883](https://github.com/libretime/libretime/issues/1883))
- provide uninstall guidance ([#1938](https://github.com/libretime/libretime/issues/1938))
- rename icecast to stream-configuration
- rename Libretime to LibreTime
- double 'the'
- rewrite reverse proxy guide
- uninstall /usr/lib systemd services
- troubleshoot webserver services ([#1961](https://github.com/libretime/libretime/issues/1961))
- reorder upgrade steps
- add missing allowed_cors_origins config

### Tests

- **analyzer:** recent liquidsoap version do not fail playability check
- **legacy:** use default rabbitmq settings ([#1855](https://github.com/libretime/libretime/issues/1855))
- **shared:** remove bad disable no-self-use ([#1862](https://github.com/libretime/libretime/issues/1862))
- **api:** use pytest to run api tests ([#1645](https://github.com/libretime/libretime/issues/1645))
- **api:** allow f string format for logging
- **api:** disable too-few-public-methods lint error
- **api:** fix lint errors
- **api:** disable too-many-arguments lint error
- **api:** ignore tests file coverage

### CI

- add missing python3-venv package for testing ([#1729](https://github.com/libretime/libretime/issues/1729))
- generate schema and push to api-client repo ([#1740](https://github.com/libretime/libretime/issues/1740))
- update api-schema generation commit message
- don't run api-schema generation on forks
- build test images for ubuntu jammy ([#1743](https://github.com/libretime/libretime/issues/1743))
- fix dev images creation script ([#1745](https://github.com/libretime/libretime/issues/1745))
- simplify legacy test matrix ([#1749](https://github.com/libretime/libretime/issues/1749))
- test on ubuntu jammy
- ignore versioned_docs with find_closed_references ([#1753](https://github.com/libretime/libretime/issues/1753))
- move docs linting to docs workflow
- only run website workflow for main branch
- add website build cache
- add website-preview workflow
- use GH actions bot for schema generation committer ([#1756](https://github.com/libretime/libretime/issues/1756))
- setup command dispatcher ([#1759](https://github.com/libretime/libretime/issues/1759))
- update workflow dispatch input description ([#1762](https://github.com/libretime/libretime/issues/1762))
- do not cache website preview build ([#1763](https://github.com/libretime/libretime/issues/1763))
- fix website preview cleanup branch ([#1793](https://github.com/libretime/libretime/issues/1793))
- reduce usage ([#1804](https://github.com/libretime/libretime/issues/1804))
- rename api-client repo ([#1805](https://github.com/libretime/libretime/issues/1805))
- add link to next version of the docs in preview comment ([#1824](https://github.com/libretime/libretime/issues/1824))
- specify python version
- replace link checker report with failing job
- check and dispatch api schema changes
- fix python packages caching ([#1893](https://github.com/libretime/libretime/issues/1893))
- fix schema update commit author ([#1912](https://github.com/libretime/libretime/issues/1912))
- **api:** fail job on linting error
- report pytest coverage in PR via codecov
- add missing codecov flags
- add api coverage report using codecov
- setup carryforward for coverage
- disable codecov project status check
- disable codecov patch status check

<a name="3.0.0-alpha.12"></a>

## [3.0.0-alpha.12](https://github.com/libretime/libretime/compare/3.0.0-alpha.11...3.0.0-alpha.12) (2022-03-29)

- [Release note](https://libretime.org/docs/releases/3.0.0-alpha.12/)

### Bug Fixes

- **playout:** add locales to libretime-playout-notify calls ([#1715](https://github.com/libretime/libretime/issues/1715))
- **worker:** enable logfile variable expansion in ExecStart ([#1717](https://github.com/libretime/libretime/issues/1717))

### Documentation

- add missing data to release note
- fix and update links ([#1714](https://github.com/libretime/libretime/issues/1714))

<a name="3.0.0-alpha.11"></a>

## [3.0.0-alpha.11](https://github.com/libretime/libretime/compare/3.0.0-alpha.10...3.0.0-alpha.11) (2022-03-28)

- [Release note](https://libretime.org/docs/releases/3.0.0-alpha.11/)

### Features

- run API tests in CI ([#1421](https://github.com/libretime/libretime/issues/1421))
- add support for Ubuntu Focal 20.04 ([#1168](https://github.com/libretime/libretime/issues/1168))
- debian 11 support ([#1292](https://github.com/libretime/libretime/issues/1292))
- create libretime_shared package ([#1349](https://github.com/libretime/libretime/issues/1349))
- enhance libretime shared ([#1491](https://github.com/libretime/libretime/issues/1491))
- **shared:** let user provide the log level ([#1493](https://github.com/libretime/libretime/issues/1493))
- replace verbosity flag with log-level flag ([#1496](https://github.com/libretime/libretime/issues/1496))
- **playout:** enhance playout logging ([#1495](https://github.com/libretime/libretime/issues/1495))
- **api:** update env var settings loading
- **api:** allow to run without log file for dev
- **analyzer:** enhance analyzer cli and logging ([#1507](https://github.com/libretime/libretime/issues/1507))
- **playout:** migrate notify cli to click ([#1519](https://github.com/libretime/libretime/issues/1519))
- **shared:** allow loading from ini config file
- **shared:** allow cli parametrized decorators ([#1527](https://github.com/libretime/libretime/issues/1527))
- **shared:** add suffix to shared config models
- **analyzer:** load config using shared helpers
- **playout:** change playout working directory
- **playout:** load config using shared helpers
- **analyzer:** analyze replaygain using ffmpeg
- **analyzer:** analyze cuepoint using ffmpeg
- **playout:** change liquidsoap working dir ([#1547](https://github.com/libretime/libretime/issues/1547))
- **legacy:** rename log filepath ([#1551](https://github.com/libretime/libretime/issues/1551))
- **shared:** add url/dsn property to config classes ([#1553](https://github.com/libretime/libretime/issues/1553))
- remove locale generation from installer ([#1560](https://github.com/libretime/libretime/issues/1560))
- **legacy:** consolidate constants ([#1558](https://github.com/libretime/libretime/issues/1558))
- **legacy:** add db config defaults and allow custom port ([#1559](https://github.com/libretime/libretime/issues/1559))
- remove unused ubuntu ppa ([#1591](https://github.com/libretime/libretime/issues/1591))
- **analyzer:** do verify ssl certs on requests
- **analyzer:** rework analyze_metadata step
- **api:** improve uwsgi systemd integration ([#1614](https://github.com/libretime/libretime/issues/1614))
- **analyzer:** rework organise_file using pathlib
- **shared:** load config from str filepath
- **shared:** create general config model
- **shared:** create time functions
- **shared:** return log level and filepath
- remove unused web_server_user config entry
- **legacy:** clean config parsing and add defaults
- **api_client:** load config using shared helpers
- **worker:** load config using shared helpers
- **shared:** do not exit on missing config file
- **api:** remove admin app and static files
- **api:** load config using shared helpers
- **legacy:** replace massivescale/celery-php with jooola/celery-php
- **worker:** set celery timezone to UTC
- **api:** include id in file/webstream serializers
- remove uninstall script ([#1682](https://github.com/libretime/libretime/issues/1682))
- **worker:** add service log filepath ([#1640](https://github.com/libretime/libretime/issues/1640))

### Bug Fixes

- remove rogue buster reference
- correct vagrantfile function call
- declare pypo.notify module
- revert removal of eval for shell commands
- add missing dependencies to celery module
- assume api client is installed
- **shared:** fix tests
- change filepath options type to pathlib.Path ([#1506](https://github.com/libretime/libretime/issues/1506))
- **legacy:** validate id param in show image controller ([#1510](https://github.com/libretime/libretime/issues/1510))
- **playout:** optional log_file for liquidsoap
- **shared:** require click >=8.0.3
- **legacy:** correct linting issues
- make vagrant source.list update idempotent ([#1520](https://github.com/libretime/libretime/issues/1520))
- **api:** duplicate exception raising and close file
- **legacy:** api migration config variable name ([#1522](https://github.com/libretime/libretime/issues/1522))
- **shared:** prevent child override by empty dict
- **shared:** tmp_path fixture type mismatch
- **analyzer:** install missing steps package
- **shared:** type is required for default config submodel ([#1536](https://github.com/libretime/libretime/issues/1536))
- **legacy:** default values when array is null
- **legacy:** do not catch too broad exceptions
- **legacy:** add more null check in api live info
- **legacy:** only render if img creation succeed ([#1540](https://github.com/libretime/libretime/issues/1540))
- **shared:** pin loguru version
- **legacy:** clean sql migrations files ([#1545](https://github.com/libretime/libretime/issues/1545))
- **shared:** set logger encoding and dont assume encoding
- **playout:** proper logger format string
- **playout:** only exclude ended file event
- **api_client:** use same date format as schedule key
- **api_client:** properly enclose events in media dict
- **playout:** properly populate scheduled_now_webstream
- **legacy:** revert default storage path ([#1563](https://github.com/libretime/libretime/issues/1563))
- **legacy:** update setup with new db config schema ([#1567](https://github.com/libretime/libretime/issues/1567))
- **shared:** do not strip vhost slash ([#1594](https://github.com/libretime/libretime/issues/1594))
- **analyzer:** remove bad attributes in shutdown handler ([#1605](https://github.com/libretime/libretime/issues/1605))
- **analyzer:** update docstring for organise_file
- **shared:** fix missing port in public_url
- change celery user in worker service file ([#1638](https://github.com/libretime/libretime/issues/1638))
- **api:** model_bakery is a dev dependency
- **api:** static_url settings is required in dev mode ([#1662](https://github.com/libretime/libretime/issues/1662))
- **api_client:** comply to legacy schedule events
- **playout:** remove stream_buffer_start in event dispatch
- add PPA for newer liquidsoap version on Ubuntu
- upgrade python packages during install ([#1707](https://github.com/libretime/libretime/issues/1707))
- **installer:** test and create correct log path
- **installer:** remove rougue reference to /var/log/airtime ([#1710](https://github.com/libretime/libretime/issues/1710))
- **installer:** remove /var/tmp/airtime reference
- **worker:** drop logfile reference until environment variable expansion works correctly

### Documentation

- recommend current LTS or stable distro ([#1564](https://github.com/libretime/libretime/issues/1564))
- replace jekyll with docusaurus
- rename documentation files
- restructure and backup pictures
- rework docs into the new website
- fix broken links
- fix prose linting errors
- remove mention of self signed certificate
- update reverse-proxy example variables
- update structure and create links between pages ([#1611](https://github.com/libretime/libretime/issues/1611))
- fix deploy to LibreTime website
- import releases notes
- update releases notes
- update configuration schema
- prevent user to clone wrong repo ([#1657](https://github.com/libretime/libretime/issues/1657))
- clean cloned repo before upgrading ([#1676](https://github.com/libretime/libretime/issues/1676))
- unsure we restart service after upgrade ([#1677](https://github.com/libretime/libretime/issues/1677))
- adjust formatting
- reload systemd service on upgrade ([#1685](https://github.com/libretime/libretime/issues/1685))
- add mermaid graph generation ([#1686](https://github.com/libretime/libretime/issues/1686))
- always run django migration on upgrade ([#1687](https://github.com/libretime/libretime/issues/1687))

### Tests

- **shared:** assert key is from file
- **shared:** check config using optional sections
- **analyzer:** rename and remove unused imports
- **analyzer:** update fixtures
- **shared:** ignore pylint warning
- **shared:** fix linting
- allow to set python linters to fail per app
- **tools:** fix mypy linters
- require lint to succeed for shared/ and tools/
- **api:** add bandit linter check
- **api_client:** add bandit linter check
- **playout:** add bandit linter check
- **shared:** add missing format lint check
- **shared:** add bandit linter check
- **worker:** add bandit linter check
- **analyzer:** fix inconsistent return statement
- **analyzer:** set test logging level to trace
- **analyzer:** use pathlib for tmp paths
- **shared:** config with required submodel ([#1616](https://github.com/libretime/libretime/issues/1616))

### CI

- add missing focal database test run
- add closed references notificier workflow ([#1467](https://github.com/libretime/libretime/issues/1467))
- add semantic pull request linting ([#1472](https://github.com/libretime/libretime/issues/1472))
- pin action-semantic-pull-request version
- add shared to allowed commit scopes ([#1494](https://github.com/libretime/libretime/issues/1494))
- cancel duplicate test workflow ([#1513](https://github.com/libretime/libretime/issues/1513))
- add website deploy workflow
- add dependabot check on website
- only cancel same worklow
- add link-checker workflow
- setup docs prose linting with vale
- lowercase org name ([#1656](https://github.com/libretime/libretime/issues/1656))
- install git in libretime-dev testing image ([#1706](https://github.com/libretime/libretime/issues/1706))
- run test container as root
- use ppa in all ubuntu distributions
- don't run linting in custom testing container
- use current release notes
