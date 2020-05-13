# Installing LibreTime
There two methods of installing LibreTime - [Source](#source) or
[Ubuntu package](#ubuntu-package).

## Source

Requirements:

- LibreTime should generally be installed on a dedicated host running Ubuntu Server 18.04 LTS, have at least 1 GHz of processor power, at least 2 GB of system RAM, and a static IP address.
- LibreTime is undergoing active development, and is currently in ALPHA. Make sure it is working for your needs well before you begin to use it in a live environment.
- Please review the release notes of the version you are planning on installing.
- Complete the [Preparing the server](preparing-the-server) guide before proceeding

The easiest way to install LibreTime is by cloning the repository using git, and
then running the installer.

1. If you don't have git installed already, run `sudo apt install git -y`
2. Create a folder in your home directory for the download and change to it: `cd ~ && mkdir LibreTime && cd LibreTime`
3. Clone the repo: `git clone https://github.com/LibreTime/libretime.git`
4. Run the installer: `sudo ./install -fiap`
5. After the installer is finished, follow the instructions to proceed to the [setup wizard](manual/getting-started/index.md)

It's recommended to use the `-fiap` flag to install LibreTime on a fresh server install. This way,
all dependencies will be installed and configured by the installer without needing user input.
For those who plan to manually configure LibreTime, run `sudo ./install -f` to see all installer options.

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
are less tested.

Plans are in the works for `.rpm` packages, as well as Docker and AWS images. If you would like to try LibreTime in a Docker image, [odclive's (unofficial) image](https://hub.docker.com/r/odclive/libretime-docker) is a great place to start.

Please note that the install script does not take care to ensure that any
packages installed are set up in a secure manner. Please see the chapter on
[preparing the server](manual/preparing-the-server) for more details on
how to set up a secure installation.
