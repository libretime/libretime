# Installing LibreTime

LibreTime should generally be installed on a dedicated host running Ubuntu 16.04 LTS (Xenial Xerus). 

You can download LibreTime from our [github repo](https://github.com/LibreTime/libretime) In order to run the latest codebase you can click the green Clone or download button. It is recommended to type ``` git clone https://github.com/LibreTime/libretime.git ``` (if you Debian based system lacks git then you can type ``` sudo apt-get install git ```) and this will download the source code to your repository. You can also click Releases and download a tarball of our latest releases. You will then need to extract this archive into a directory on your server. It is not recommended that you install LibreTime on the same computer you are using as a desktop. So you will probably need to use wget for instance ```wget https://github.com/LibreTime/libretime/releases/download/3.0.0-alpha.6/libretime-3.0.0-alpha.6.tar.gz``` and then extract it by typing ```tar -xvzf libretime-3.0.0-alpha.6.tar.gz``` in the directory you downloaded it into. 

Once you have downloaded and extracted the LibreTime repository, run the instalation script by navigating into the folder containing the LibreTime codebase, and run it's install script from the command line:

    :::bash
    sudo ./install

By default, the installer will install and configure all dependencies.

## Alternative OS installations
Instalation in Debian 9 and other Linux distributions is possible, but multiple outstanding issues have yet to be resolved. Instalation on Ubuntu 14.04.5 LTS (Trusty Tahr) is also working, but deprecated due to the fact that this version will reach its official end of life in April 2019.

Plans are in the works for `.deb` and `.rpm` packages, as well as Docker and AWS images.

Please note that the install script does not take care to ensure that any
packages installed are set up in a secure manner. Please see the chapter on
[preparing the server](manual/preparing-the-server/) for more details on
how to set up a secure installation.
