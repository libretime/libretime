# Installing LibreTime

LibreTime should generally be installed on a dedicated host. By default, its installer will install and configure all its dependencies. At the moment, the installer works best on Ubuntu 16.04 LTS (Xenial Xerus), or Ubuntu 14.04.5 LTS (Trusty Tahr).

    :::bash
    sudo ./install

Instalation in Debian 9 and other Linux distributions is possible, but multiple outstanding issues have yet to be resolved.

Plans are in the works for `.deb` and `.rpm` packages, as well as Docker and AWS images.

Please note that the install script does not take care to ensure that any
packages installed are set up in a secure manner. Please see the chapter on
[preparing the server](manual/preparing-the-server/) for more details on
how to set up a secure installation.
