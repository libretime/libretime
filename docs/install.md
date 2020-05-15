# Advanced Installation

 This guide is for administrators who need to install LibreTime using a more hands-on method, such as
 manually configuring depenencies and services, installing using a deb package,
 or containerizing LibreTime. Running LibreTime in the cloud can be manually set up using the same steps in the
 [Quick Install](quickstart). Please complete the [Preparing the server](preparing-the-server) and
[Setting the server time](manual/setting-the-server-time/index) guides before proceeding.

## Reverse proxy connections

Instuctions for setting up a reverse proxy can be found [here](reverse-proxy).

## Ubuntu Package
LibreTime maintains amd64 .deb packages for Ubuntu 16.04 (Xenial) and 18.04
(Bionic). These can be downloaded [here](https://github.com/LibreTime/libretime-debian-packaging/releases).
Issues with installation of these packages should be reported to the
[LibretTime/libretime-debian-packaging](https://github.com/LibreTime/libretime-debian-packaging)
repository.

These are installed by running the following from the command line (the `./` in
front of the libretime package is important):

```
sudo apt install icecast2 ./libretime_<version>_amd64.deb
```
`<version>` is replaced by the version of the package downloaded.

Plans are in the works for `.rpm` packages, as well as Docker and AWS images.

## Alternative OS installations
Athough these are less tested, it is possible to install LibreTime on

- CentOS 7
- Ubuntu 16.04 LTS
- Debian 9 and 10
- Raspbian 9 and 10

## Containerization using Docker

If you would like to try LibreTime in a Docker image,
Odclive has instructions [here](https://github.com/kessibi/libretime-docker) for setting up a test image
and a more persistant install.