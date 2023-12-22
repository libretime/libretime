# Changelog

## [3.2.1](https://github.com/libretime/libretime/compare/3.2.0...3.2.1) (2023-12-22)


### Bug Fixes

* add parent function name in setValue exception ([#2777](https://github.com/libretime/libretime/issues/2777)) ([c764a5a](https://github.com/libretime/libretime/commit/c764a5a648ac6cf6c1f63cd9be6de9fe07c40988))
* **api:** enum schema description ([#2803](https://github.com/libretime/libretime/issues/2803)) ([976b70e](https://github.com/libretime/libretime/commit/976b70ed32a0e774cc0b72b8332372be32799ed1))
* **legacy:** allow uploading opus files ([#2804](https://github.com/libretime/libretime/issues/2804)) ([1206252](https://github.com/libretime/libretime/commit/1206252faadaa8ca61c7cb7becb36a7dd306d05f))

## [3.2.0](https://github.com/libretime/libretime/compare/3.1.0...3.2.0) (2023-10-16)

- [Release note](https://libretime.org/docs/releases/3.2.0/)

### Features

- **legacy:** move session store to database ([#2523](https://github.com/libretime/libretime/issues/2523))
- **api:** add email configuration
- add mobile devices stream config field ([#2744](https://github.com/libretime/libretime/issues/2744))

### Bug Fixes

- **playout:** liquidsoap aac output syntax errors
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.20.1 (stable) ([#2602](https://github.com/libretime/libretime/issues/2602))
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.21.2 ([#2612](https://github.com/libretime/libretime/issues/2612))
- libretime process leaks and lsof high cpu usage ([#2615](https://github.com/libretime/libretime/issues/2615))
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.22.1 ([#2633](https://github.com/libretime/libretime/issues/2633))
- libretime process leaks and lsof high cpu usage ([#2615](https://github.com/libretime/libretime/issues/2615))
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.23.1 (stable) ([#2656](https://github.com/libretime/libretime/issues/2656))
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.26.1 (stable) ([#2678](https://github.com/libretime/libretime/issues/2678))
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.26.1 (main) ([#2677](https://github.com/libretime/libretime/issues/2677))
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.26.2 ([#2686](https://github.com/libretime/libretime/issues/2686))
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.26.2 ([#2687](https://github.com/libretime/libretime/issues/2687))
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.27.1 (main) ([#2714](https://github.com/libretime/libretime/issues/2714))
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.27.1 (stable) ([#2715](https://github.com/libretime/libretime/issues/2715))
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.34.1 ([#2723](https://github.com/libretime/libretime/issues/2723))
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.35.2 ([#2738](https://github.com/libretime/libretime/issues/2738))
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.35.2 ([#2722](https://github.com/libretime/libretime/issues/2722))

### Documentation

- update chat links to point to matrix ([#2571](https://github.com/libretime/libretime/issues/2571))
- fix broken link ([#2616](https://github.com/libretime/libretime/issues/2616))

### Tests

- **playout:** check unsupported liquidsoap aac output

## [3.1.0](https://github.com/libretime/libretime/compare/3.0.2...3.1.0) (2023-05-26)

- [Release note](https://libretime.org/docs/releases/3.1.0/)

### Features

- drop Ubuntu Bionic support
- drop Python 3.6 support
- drop Debian Buster support
- drop Liquidsoap 1.1 support
- drop Liquidsoap 1.3 support
- drop Python 3.7 support
- drop cc_stream_setting table
- delete cc_pref stream preferences rows
- **legacy:** remove db allowed_cors_origins preference ([#2095](https://github.com/libretime/libretime/issues/2095))
- configure cue points analysis per track type
- **playout:** use jinja2 env for template loading
- **playout:** add jinja2 quote filter for liquidsoap
- **playout:** use liquidsoap interactive variables
- **playout:** remove unused liquidsoap outputs connection status
- **playout:** remove unused liquidsoap restart function
- **playout:** remove unused liquidsoap output namespace
- replace loguru with logging
- **playout:** use jinja to configure liquidsoap outputs
- **playout:** enable vorbis metadata per icecast output
- **playout:** use shared app for cli commands
- **installer:** configure timezone using timedatectl ([#2418](https://github.com/libretime/libretime/issues/2418))
- **playout:** don't serialize message twice
- add python packages version
- add sentry sdk
- use secret_key config field instead of api_key ([#2444](https://github.com/libretime/libretime/issues/2444))
- **api-client:** remove unused api v1 calls
- **api-client:** rewrite api-client v1 using abstract client
- **playout:** move liquidsoap auth to notify cli
- **playout:** replace schedule event dicts with objects
- **api:** add cors headers middleware ([#2479](https://github.com/libretime/libretime/issues/2479))
- **playout:** replace thread timeout with socket timeout
- remove dev files from tarball
- include tarball checksums in releases
- set icecast mount default charset to UTF-8
- **playout:** allow harbor ssl configuration
- **api:** install gunicorn/uvicorn from pip
- install inside a python3 venv

### Bug Fixes

- **deps:** update dependency adbario/php-dot-notation to v3 ([#2226](https://github.com/libretime/libretime/issues/2226))
- **deps:** update dependency league/uri to v6.7.2
- **legacy:** set platform requirements to php ^7.4
- **playout:** remove outdated liquidsoap code
- **playout:** add types
- **api:** allow single digit version for legacy schema
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.12.1
- remove systemd ProtectHome feature ([#2243](https://github.com/libretime/libretime/issues/2243))
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.13.1 ([#2249](https://github.com/libretime/libretime/issues/2249))
- **worker:** replace deprecated cgi.parse_header
- **installer:** install missing sudo
- **installer:** set home and login when running as postgres
- **legacy:** add log entry on task run ([#2316](https://github.com/libretime/libretime/issues/2316))
- **legacy:** log errors on connect check failure ([#2317](https://github.com/libretime/libretime/issues/2317))
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.13.2
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.13.3
- **legacy:** advanced search by track type id
- **legacy:** move forked deps to the libretime namespace
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.14.4
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.14.5
- **legacy:** ensure options is a dict during json encoding
- **legacy:** don't use dict assignment on object ([#2384](https://github.com/libretime/libretime/issues/2384))
- **playout:** quote escape strings in liquidsoap entrypoint
- **legacy:** do not delete audio file when removing artwork ([#2395](https://github.com/libretime/libretime/issues/2395))
- **playout:** use explicit ids for liquidsoap components
- **playout:** skip the identified queue instead of the current
- **playout:** use the same number of schedule queues
- **legacy:** on air light fails when no shows are scheduled
- **playout:** flush liquidsoap response before sending new
- **playout:** use package loader for liquidsoap templates
- **playout:** %else is not defined
- **playout:** when shows ends, next shows starts without fade-in/fade-out ([#2412](https://github.com/libretime/libretime/issues/2412))
- **playout:** legacy pushes non validated data
- **playout:** explicit ogg vorbis icecast encoder
- **playout:** prevent unbound variables
- **playout:** use int for liquidsoap queues map
- **shared:** return type confusion
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.15.2
- **api:** explicit FileImportStatusEnum in schema
- pin postgresql version in docker-compose
- pin rabbitmq version in docker-compose
- allow overriding docker-compose predefined environment
- move docker specific setup to dockerfile
- **api:** cast string value to int enum ([#2461](https://github.com/libretime/libretime/issues/2461))
- **playout:** quote incompatible <py3.9 type hints
- **installer:** bump setuptools to ~=67.3 ([#2387](https://github.com/libretime/libretime/issues/2387))
- **playout:** use new api-client v1
- **playout:** catch oserror in liquidsoap client
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.16.1 (main) ([#2490](https://github.com/libretime/libretime/issues/2490))
- **api:** require django >=4.2.0,<4.3
- **api:** upgrade psycopg to v3.1
- **playout:** remove unused ecasound package ([#2496](https://github.com/libretime/libretime/issues/2496))
- **installer:** ignore whitespace during diff
- **legacy:** don't print track_type id in show builder table ([#2510](https://github.com/libretime/libretime/issues/2510))
- **legacy:** remove composer superuser warning ([#2515](https://github.com/libretime/libretime/issues/2515))
- **legacy:** keep datatable settings between views ([#2519](https://github.com/libretime/libretime/issues/2519))
- **api:** upgrade django code (pre-commit)
- **analyzer:** remove unused python3 package
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.17.1 (main) ([#2556](https://github.com/libretime/libretime/issues/2556))

### Documentation

- **playout:** add simple inputs pipeline schema ([#2240](https://github.com/libretime/libretime/issues/2240))
- add DOCKER_BUILDKIT env variable for docker-compose v1 ([#2270](https://github.com/libretime/libretime/issues/2270))
- no need to update release note path
- adapt c4 to our workflows
- stop providing maintenance releases for old distributions
- add pulseaudio output in containers tutorial ([#2166](https://github.com/libretime/libretime/issues/2166))
- remove warning about docker install ([#2411](https://github.com/libretime/libretime/issues/2411))
- docker-compose env variables setup
- add instructions for the sentry setup ([#2441](https://github.com/libretime/libretime/issues/2441))
- upgrade by migrating to a new server
- fix database backup and restore commands
- move contributing to docs/contribute
- split developer and contributor manual
- extract dev workflows from contributing docs
- add some history notes
- move release docs in the release section
- fix broken links
- ignore range format during docs linting
- only use microsoft styling guide
- move configuration documentation
- rename setup to install
- split install guide per install method
- docker config template install with envsubst ([#2517](https://github.com/libretime/libretime/issues/2517))
- improve reverse proxy docs
- improve install guides
- add certbot setup guide
- ensure example values are replaced
- fix broken link ([#2532](https://github.com/libretime/libretime/issues/2532))
- add note about unused packages
- improve airtime migration guide ([#2564](https://github.com/libretime/libretime/issues/2564))
- split airtime migration into more steps ([#2565](https://github.com/libretime/libretime/issues/2565))
- remove setup without reverse proxy
- fix icecast certificates bundle command
- install using a reverse proxy by default
- be consistent with example domain ([#2568](https://github.com/libretime/libretime/issues/2568))
- add 3.1.x distribution releases support

### Tests

- liquidsoap package from ppa is version 1.4.2 ([#2223](https://github.com/libretime/libretime/issues/2223))
- **playout:** refresh snapshots after major upgrade
- re-enable pylint logging-fstring-interpolation
- **playout:** more entrypoint config test cases
- **playout:** generated liquidsoap script syntax
- **playout:** silence existing broad-exception-caught errors
- **playout:** allow pylint failure
- **playout:** check untyped defs with mypy
- **api:** fix linting errors
- **shared:** fix linting errors
- **api:** fix linting errors
- **shared:** fix linting errors
- **playout:** class creation
- **api-client:** allow linters failure
- **playout:** move liq_conn fixture to conftest
- **playout:** liquidsoap wait for version
- **api:** add django-upgrade pre-commit hook

### CI

- test project weekly
- enable renovate for 3.0.x ([#2277](https://github.com/libretime/libretime/issues/2277))
- sync docs with libretime/website repository
- pin vale version to v2.21.3
- don't squash commits during docs sync
- always print diff when schema changes
- check if locale are up to date
- update locales weekly, not for every commit ([#2403](https://github.com/libretime/libretime/issues/2403))
- use bake file for container build
- allow manual ci trigger
- use bot to update locales
- replace deprecated set-output ([#2408](https://github.com/libretime/libretime/issues/2408))
- update docker hub containers description
- replace stale bot with stale action ([#2421](https://github.com/libretime/libretime/issues/2421))
- allow Falso as a word in codespell
- allow Falso as a word in codespell
- run all tests on python tools changes
- don't run stale bot on feature requests ([#2527](https://github.com/libretime/libretime/issues/2527))

### Reverts

- chore(api): install django-rest-framework from git ([#2518](https://github.com/libretime/libretime/issues/2518))

## [3.0.2](https://github.com/libretime/libretime/compare/3.0.1...3.0.2) (2023-02-21)

- [Release note](https://libretime.org/docs/releases/3.0.2/)

### Bug Fixes

- **legacy:** advanced search by track type id
- **legacy:** refresh lock files
- **legacy:** move forked deps to the libretime namespace
- **legacy:** improve error messages and logs
- **installer:** allow different actions on template_file
- **installer:** print diff on file deployment
- **installer:** only setup nginx on first install
- **installer:** print unsupported distribution error ([#2368](https://github.com/libretime/libretime/issues/2368))
- **installer:** create systemd dirs if missing ([#2379](https://github.com/libretime/libretime/issues/2379))

### Documentation

- add DOCKER_BUILDKIT env variable for docker-compose v1 ([#2270](https://github.com/libretime/libretime/issues/2270))
- check logs before checking services status
- add small faq for troubleshooting

### Tests

- **playout:** refresh snapshots after major upgrade ([#2381](https://github.com/libretime/libretime/issues/2381))

### CI

- don't squash commits during docs sync
- test project weekly

## [3.0.1](https://github.com/libretime/libretime/compare/3.0.0...3.0.1) (2022-12-20)

- [Release note](https://libretime.org/docs/releases/3.0.1/)

### Bug Fixes

- remove systemd ProtectHome feature ([#2244](https://github.com/libretime/libretime/issues/2244))
- **installer:** install missing sudo
- **installer:** set home and login when running as postgres
- **legacy:** add log entry on task run ([#2316](https://github.com/libretime/libretime/issues/2316))
- **legacy:** log errors on connect check failure ([#2317](https://github.com/libretime/libretime/issues/2317))
- **worker:** replace deprecated cgi.parse_header

### Documentation

- no need to update release note path

### Tests

- liquidsoap package from ppa is version 1.4.2 ([#2233](https://github.com/libretime/libretime/issues/2233))

### CI

- run tests on 3.0.x
- enable renovate bot on 3.0.x
- sync docs with libretime/website repository
- pin vale version to v2.21.3

## [3.0.0](https://github.com/libretime/libretime/compare/3.0.0-beta.2...3.0.0) (2022-10-10)

- [Release note](https://libretime.org/docs/releases/3.0.0/)

### Bug Fixes

- clean exit by catching keyboard interrupt ([#2206](https://github.com/libretime/libretime/issues/2206))
- **legacy:** missing plupload uk_UA translation
- **legacy:** jquery i18n translations for plupload
- **legacy:** gracefully handle missing asset checksum
- disable some systemd security features on bionic ([#2219](https://github.com/libretime/libretime/issues/2219))

### Documentation

- **legacy:** how to add a new language

### Tests

- **analyzer:** fix wrong bit_rate values

## [3.0.0-beta.2](https://github.com/libretime/libretime/compare/3.0.0-beta.1...3.0.0-beta.2) (2022-10-03)

- [Release note](https://libretime.org/docs/releases/3.0.0-beta.2/)

### Features

- systemd service hardening ([#2186](https://github.com/libretime/libretime/issues/2186))
- extra systemd service hardening ([#2197](https://github.com/libretime/libretime/issues/2197))

### Bug Fixes

- start playout service after liquidsoap ([#2164](https://github.com/libretime/libretime/issues/2164))
- include version variable inside containers
- change version format
- **legacy:** add play button to stream player ([#2190](https://github.com/libretime/libretime/issues/2190))
- **legacy:** correct log levels ([#2196](https://github.com/libretime/libretime/issues/2196))

### Documentation

- remove breaking change warning ([#2180](https://github.com/libretime/libretime/issues/2180))
- fix vale linting errors
- fix vale linting error

### CI

- allow failure when linting /docs/releases
- use github.ref_name to get tag

## [3.0.0-beta.1](https://github.com/libretime/libretime/compare/3.0.0-beta.0...3.0.0-beta.1) (2022-09-23)

- [Release note](https://libretime.org/docs/releases/3.0.0-beta.1/)

### Features

- **legacy:** disable services check when missing systemctl ([#2160](https://github.com/libretime/libretime/issues/2160))
- **legacy:** invalidate cached assets using md5sum ([#2161](https://github.com/libretime/libretime/issues/2161))
- use libretime/icecast container image ([#2165](https://github.com/libretime/libretime/issues/2165))

### Bug Fixes

- **legacy:** number of tracks displayed initially ([#2168](https://github.com/libretime/libretime/issues/2168))
- **legacy:** rebuild favicon ([#2167](https://github.com/libretime/libretime/issues/2167))
- **worker:** configure celery timezone ([#2169](https://github.com/libretime/libretime/issues/2169))
- **legacy:** update or remove broken links
- **legacy:** prepend file id in tmp upload filename ([#2173](https://github.com/libretime/libretime/issues/2173))
- **legacy:** fail when uploading wma files ([#2172](https://github.com/libretime/libretime/issues/2172))

### Documentation

- remove UI timezone configuration suggestion ([#2158](https://github.com/libretime/libretime/issues/2158))
- add default user credentials
- first blog post, v3 beta.0 ([#1939](https://github.com/libretime/libretime/issues/1939))
- fix release commands

### CI

- ignore changelog for closed reference notifier
- don't check github.com/libretime/libretime/(issues|pulls) links
- run docs workflow on vale files changes

## [3.0.0-beta.0](https://github.com/libretime/libretime/compare/3.0.0-alpha.13...3.0.0-beta.0) (2022-09-16)

- [Release note](https://libretime.org/docs/releases/3.0.0-beta.0/)

### Features

- **playout:** use liquidsoap version functions
- **playout:** replace pytz with zoneinfo ([#1969](https://github.com/libretime/libretime/issues/1969))
- **installer:** remove allow-restart flag ([#1970](https://github.com/libretime/libretime/issues/1970))
- rename AirtimeApiClient to ApiClient
- **playout:** use single clients instance ([#1980](https://github.com/libretime/libretime/issues/1980))
- **api:** don't use trailing slashes ([#1982](https://github.com/libretime/libretime/issues/1982))
- **api:** cast StreamSetting raw_value to value ([#1991](https://github.com/libretime/libretime/issues/1991))
- **worker:** load callback details from config ([#1994](https://github.com/libretime/libretime/issues/1994))
- **analyzer:** load callback details from config and file_id ([#1993](https://github.com/libretime/libretime/issues/1993))
- **api-client:** rewrite api-client v2
- **playout:** integrate api-client v2 calls
- **api:** don't use hyperlinked serializers ([#1984](https://github.com/libretime/libretime/issues/1984))
- **shared:** load env config using jsonschema
- **installer:** use ed for config update ([#2013](https://github.com/libretime/libretime/issues/2013))
- move off_air_meta stream setting to pref table ([#2023](https://github.com/libretime/libretime/issues/2023))
- move stream liquisoap status to pref table
- move stream stats status to pref table
- **analyzer:** override paths using env variables
- **playout:** rewrite stats collector ([#2028](https://github.com/libretime/libretime/issues/2028))
- **legacy:** setup config schema validation
- **legacy:** add config dot notation access
- **shared:** pass config data via init ([#2042](https://github.com/libretime/libretime/issues/2042))
- **playout:** create liquidsoap client
- **playout:** integrate new liquisoap client
- **worker:** rename service and package to libretime-worker ([#2065](https://github.com/libretime/libretime/issues/2065))
- **playout:** improve generate\_\*\_events ([#2088](https://github.com/libretime/libretime/issues/2088))
- **api:** remove set passwords command
- remove cc_stream_setting models
- **installer:** deploy stream config
- **legacy:** read stream config from file
- **api:** add /info and /stream/\* endpoints
- **shared:** create stream config models
- **playout:** build liquidsoap entrypoint with stream config
- **playout:** stats collector using stream config
- **playout:** allow updating message_offline value
- **playout:** remove stream_setting update handler
- **playout:** liquidsoap bootstrap using new api endpoints
- **playout:** allow liquidsoap listen address configuration
- **api:** move /api-auth to /api/browser ([#2094](https://github.com/libretime/libretime/issues/2094))
- add container setup
- move timezone preference to config file ([#2096](https://github.com/libretime/libretime/issues/2096))
- **playout:** move message handling to main thread

### Bug Fixes

- **api-client:** get status_code from response
- **analyzer:** remove outdated urllib3 workaround
- **api-client:** fix base_url joining for client v2 ([#1998](https://github.com/libretime/libretime/issues/1998))
- **api:** update set_icecast_passwords StreamSetting fields ([#2001](https://github.com/libretime/libretime/issues/2001))
- **legacy:** get local logo file ([#1999](https://github.com/libretime/libretime/issues/1999))
- **installer:** clean legacy files before copying ([#2002](https://github.com/libretime/libretime/issues/2002))
- **legacy:** sanitize track_type_id when updating file ([#2003](https://github.com/libretime/libretime/issues/2003))
- **shared:** validator value type can be wrong
- **shared:** remove unused field from rabbitmq config ([#2012](https://github.com/libretime/libretime/issues/2012))
- **playout:** replace deprecated harbor.bind_addr ([#2025](https://github.com/libretime/libretime/issues/2025))
- **legacy:** do not rely on undefined SERVER_NAME ([#2031](https://github.com/libretime/libretime/issues/2031))
- **api-client:** remove unused v1 methods
- **playout:** use stream download when fetching files ([#2048](https://github.com/libretime/libretime/issues/2048))
- **playout:** add thread names ([#2056](https://github.com/libretime/libretime/issues/2056))
- **legacy:** args comma syntax error
- **legacy:** 404 on listeners stats
- **deps:** update dependency mdx-mermaid to v1.3.0 [security]
- **playout:** py36 compatibility broken typings
- **playout:** py39 compatibility zoneinfo import
- **api:** install gunicorn from pip for bionic
- **installer:** only upgrade pip packages if needed
- **installer:** fix compatibility with bionic
- **legacy:** look in /legacy for a VERSION file
- **playout:** missing live show events ([#2087](https://github.com/libretime/libretime/issues/2087))
- **legacy:** config default values are not sanitized
- **installer:** add liquidsoap config section
- **installer:** move non reusable fields from default output
- **legacy:** consistent with docs in outputs public_url generation
- **playout:** also shutdown on SIGTERM ([#2104](https://github.com/libretime/libretime/issues/2104))
- **installer:** simplify distro support notice ([#2106](https://github.com/libretime/libretime/issues/2106))
- **shared:** install tzdata distribution package ([#2105](https://github.com/libretime/libretime/issues/2105))
- **installer:** config dir should be read only
- **installer:** config should not be world readable
- **legacy:** track_type_id should cast to int not text ([#2112](https://github.com/libretime/libretime/issues/2112))
- **worker:** rewrite podcast download task
- **shared:** load env from oneOf union schema
- **deps:** update dependency friendsofphp/php-cs-fixer to <3.11.1
- nginx depends on legacy in docker-compose ([#2147](https://github.com/libretime/libretime/issues/2147))
- **playout:** remove shutdown_handler

### Documentation

- remove empty section in release note
- fix missing release date
- create development section
- add releases distributions support
- add release instructions ([#1995](https://github.com/libretime/libretime/issues/1995))
- use dedicated backup procedure for airtime
- add out of date warning to custom auth setup
- edit timezone during setup
- check system time config before installing ([#2019](https://github.com/libretime/libretime/issues/2019))
- add ubuntu bionic deprecation notice
- add debian buster deprecation notice
- add restore procedure ([#2071](https://github.com/libretime/libretime/issues/2071))
- improve config comments
- add missing storage configuration
- use brackets to refer to another field in the config
- file based stream configuration
- single restart notice for config changes ([#2098](https://github.com/libretime/libretime/issues/2098))
- add warning about icecast sources limit ([#2133](https://github.com/libretime/libretime/issues/2133))
- missing fields from stream config ([#2146](https://github.com/libretime/libretime/issues/2146))
- add docker-compose ps command ([#2150](https://github.com/libretime/libretime/issues/2150))
- add tip to use yq for configuration upgrade

### Tests

- **api:** ignore mypy missing imports
- **api:** always print logs while testing ([#1988](https://github.com/libretime/libretime/issues/1988))
- **api:** create api_client pytest fixture ([#1990](https://github.com/libretime/libretime/issues/1990))
- enable logs when running pytest ([#2008](https://github.com/libretime/libretime/issues/2008))
- **shared:** move config tests
- **api:** conftest at top level for global fixture access ([#2038](https://github.com/libretime/libretime/issues/2038))
- **legacy:** enable stdout logs
- **analyzer:** analyze large audio files ([#2050](https://github.com/libretime/libretime/issues/2050))
- **worker:** setup testing
- **playout:** use snapshot testing tool ([#2115](https://github.com/libretime/libretime/issues/2115))
- **worker:** allow pylint and bandit to fail

### CI

- website-preview use single comment ([#1996](https://github.com/libretime/libretime/issues/1996))
- add custom user for dev containers
- add missing gid when running dev container
- **legacy:** catch syntax errors on older php versions
- add pre-commit python cache
- add check-shell python cache
- add check-shell tools cache
- add docs lint tools cache
- add housekeeping lychee cache
- build test images for debian bookworm ([#2097](https://github.com/libretime/libretime/issues/2097))
- fix docusaurus monorepo config ([#2101](https://github.com/libretime/libretime/issues/2101))
- add shared packages to dev container
- add containers build job
- don't run all workflows in unrelated workflows changes ([#2142](https://github.com/libretime/libretime/issues/2142))
- enable containers concurrency group
- improve containers build caching
- add container tags

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

## [3.0.0-alpha.12](https://github.com/libretime/libretime/compare/3.0.0-alpha.11...3.0.0-alpha.12) (2022-03-29)

- [Release note](https://libretime.org/docs/releases/3.0.0-alpha.12/)

### Bug Fixes

- **playout:** add locales to libretime-playout-notify calls ([#1715](https://github.com/libretime/libretime/issues/1715))
- **worker:** enable logfile variable expansion in ExecStart ([#1717](https://github.com/libretime/libretime/issues/1717))

### Documentation

- add missing data to release note
- fix and update links ([#1714](https://github.com/libretime/libretime/issues/1714))

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
