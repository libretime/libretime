# Advanced Installation

 This guide is for administrators who need to install LibreTime using a more hands-on method, such as
 manually configuring depenencies and services or containerizing LibreTime. Running LibreTime in the cloud can be manually set up using the same steps in the
 [Quick Install](quickstart). Please complete the [Preparing the server](preparing-the-server) and
[Setting the server time](manual/setting-the-server-time/index) guides before proceeding.

## Ubuntu Package
LibreTime maintains amd64 .deb packages for Ubuntu 16.04 (Xenial) and 18.04
(Bionic). These can be downloaded using their PPA in apt or your favorite package manager.

```
sudo add-apt-repository ppa:libretime/libretime
sudo apt-get update
sudo apt-get install libretime icecast2
```

## Alternative OS installations
Athough these are less tested, it is possible to install LibreTime on

- CentOS 7
- Ubuntu 16.04 LTS
- Debian 9 and 10
- Raspbian 9 and 10

Follow the [Quick Install] instructions for these OSes. If something goes wrong, please open a Github
[issue request](https://github.com/LibreTime/libretime/issues).

## Containerization using Docker

If you would like to try LibreTime in a Docker image,
Odclive has instructions [here](https://github.com/kessibi/libretime-docker) for setting up a test image
and a more persistant install.

## Reverse proxy connections

Instuctions for setting up a reverse proxy can be found [here](reverse-proxy).

## Manual configuration

If you need to manually configure dependencies or services for LibreTime, below is a list of the options
the LibreTime installer can use.

```
Usage: sudo bash install [options]
    -h, --help, -?
                Display usage information
    -V, --version
                Display version information
    -v, --verbose
                More output
    -q, --quiet, --silent
                No output except errors
    -f, --force
                Turn off interactive prompts
    --distribution=DISTRIBUTION
                Linux distribution the installation is being run on
    --release=RELEASE
                Distribution release
    -d, --ignore-dependencies
                Don't install binary dependencies
    -w, --web-user=WEB_USER
                Set the apache web user. Defaults to www-data. Only change
                this setting if you've changed the default apache web user
    -r, --web-root=WEB_ROOT
                Set the web root for Airtime files
                This will copy the Airtime application files, but you will need
                to give your web user access to the given directory if it is 
                not accessible
    --web-port=WEB_PORT
                Set what port the LibreTime interface should run on.
    -I, --in-place
                Set the current Airtime directory as the web root
                Note that you will need to give your web user permission to 
                access this directory if it is not accessible
    -p, --postgres
                Create a default postgres user named 'airtime' with password
                'airtime'
    -a, --apache
                Install apache and deploy a basic configuration for Airtime
    -i, --icecast
                Install Icecast 2 and deploy a basic configuration for Airtime
    --selinux
                Run restorecon on directories and files that need tagging to
                allow the WEB_USER access
    --no-postgres
                Skips all postgres related install tasks (Useful if you configure
                postgresql as part of another script / docker builds)
    --no-rabbitmq
                Skips all rabbitmq related install tasks.
                "
```

Plans are in the works for `.rpm` packages, as well as Docker and AWS images.
