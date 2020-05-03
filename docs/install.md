# Installing LibreTime
There two methods of installing LibreTime - [Source](#source) or
[Ubuntu package](#ubuntu-package).

## Source
LibreTime releases can be downloaded [here](https://github.com/LibreTime/libretime/releases).

Recommendations:

- LibreTime should generally be installed on a dedicated host running Ubuntu 16.04 LTS (Xenial Xerus).
- LibreTime is undergoing active development, and is currently in ALPHA.
- It is not recommended that you install LibreTime on the same computer you are using as a desktop.
- Please review the release notes of the version you are planning on installing.

Once you have downloaded and extracted LibreTime, run the installation script by navigating into the
folder containing the LibreTime codebase, and run its install script from the command line:

```
sudo ./install -fiap
```

The installer will install and configure all dependencies only if the `-fiap` flag is added. If you would prefer to configure dependencies manually, omit the flag.

A great tutorial video on how to install LibreTime is [here](https://www.youtube.com/watch?v=Djo_55LgjXE).

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

## Alternative OS installations
Installation in Debian 9 and other Linux distributions is possible, but these
are less tested. Installation on Ubuntu 14.04.5 LTS (Trusty Tahr) is also working, but deprecated due to the
fact that this version will reach its official end of life in April 2019.

Plans are in the works for `.rpm` packages, as well as Docker and AWS images. If you would like to try LibreTime in a Docker image, [odclive's (unofficial) image](https://hub.docker.com/r/odclive/libretime-docker) is a great place to start.

Please note that the install script does not take care to ensure that any
packages installed are set up in a secure manner. Please see the chapter on
[preparing the server](manual/preparing-the-server) for more details on
how to set up a secure installation.
