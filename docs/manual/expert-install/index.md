These quick install steps are suitable for experienced GNU/Linux system administrators who have already followed the steps shown in the chapter *Preparing the server* earlier in this book. For a more detailed explanation of the steps below, please read the chapter *Automated installation*.

1. Edit the repositories file for your server:

    sudo nano /etc/apt/sources.list

For Ubuntu Precise \[or Quantal, Raring, Saucy\] servers, use the Sourcefabric repository:

    deb http://apt.sourcefabric.org/ precise main

substituting *precise* if appropriate. Make sure you have enabled the multiverse repository for MP3 encoding support:

    deb http://archive.ubuntu.com/ubuntu/ precise multiverse

For Debian wheezy \[or squeeze\] servers, use the Sourcefabric repository:

    deb http://apt.sourcefabric.org/ wheezy main

If using Debian squeeze, also enable the backports repository for MP3 encoding support:

    deb http://backports.debian.org/debian-backports squeeze-backports main

2. Install the Sourcefabric package signing key, then update again:

    sudo apt-get update
    sudo apt-get install sourcefabric-keyring
    sudo apt-get update 

3. Install the database management system (for a single server configuration):

    sudo apt-get install postgresql

4. Install the streaming media server (optional, it may be remote):

    sudo apt-get install icecast2

5. Remove PulseAudio, if installed:

    sudo apt-get purge pulseaudio

6. Install Airtime:

    sudo apt-get install airtime

Refer to the *Configuration* chapter for configuration options. Now you should be able to log in to the Airtime administration interface, as shown in the *Getting started* chapter.
