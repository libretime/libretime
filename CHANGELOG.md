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
