Airtime 2.5.x versions support upgrading from version 2.3.0 and above. If you are running a production server with a version of Airtime prior to 2.3.0, you should upgrade it to version 2.3.0 before continuing. 

Before upgrading a production Airtime server, you should back up both the PostgreSQL database and the storage server used by Airtime. This is especially important if you have not already set up a regular back up routine. This extra back up is a safety measure in case of accidental data loss during the upgrade, for example due to the wrong command being entered when moving files. See the chapter *Backing up the server* in this book for details of how to perform these back ups.

If you have deployed Airtime using the method shown in the *Automated installation* chapter, you can upgrade in the same way. A new Airtime package available in the Sourcefabric repository can be installed with:

    sudo apt-get update
    sudo apt-get upgrade

If you have used the method shown in the *Manual installation* chapter, you should repeat the installation steps of downloading and unpacking the tarball to an installation directory, or pulling from the git repository, and running the **airtime-install** or **airtime-full-install** script. The installation script will detect an existing Airtime deployment and back up any configuration files that it finds.

After the upgrade has completed, you may need to clear your web browser's cache before logging into the new version of the Airtime administration interface. If the playout engine starts up and detects that a show should be playing at the current time, it will skip to the correct point in the current item and start playing.

In Airtime 1.9.0 onwards, the concept of *linked files* was replaced with the concept of *watched folders*. If you are upgrading from a version of Airtime earlier than 1.9.0 and you have previously linked files, the folders they are in will not be watched until you add them to your watched folder list. See the chapter *Media Folders* for more details.

Upgrading the server distribution
---------------------------------

After your Airtime server has been deployed for a few years, you may need to upgrade the GNU/Linux distribution that it runs in order to maintain security update support. If the upgrade does not go smoothly, it may cause significant downtime, so you should always have a fallback system available during the upgrade to ensure broadcast continuity.

After upgrading a server from Ubuntu Lucid 10.04 LTS to Ubuntu Precise 12.04 LTS, you may experience problems with Monit failing to start, due to a change in the format of its configuration files. If so, a double equals sign can be changed to single equals in line 39 of /etc/init.d/monit so that it reads:

      if [ "$1" = "start" ]

Also, the file /etc/default/monit should contain the line:

    START=yes

instead of how it was configured in Ubuntu Lucid:

    startup=1

This modification is sometimes necessary because during distribution upgrade it is normal to keep any locally modified configuration files.
