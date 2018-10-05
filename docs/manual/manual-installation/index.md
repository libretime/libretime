***Beware, here be dragons!*** 

These Airtime instructions are outdated, see [install.md](../../install.md) for LibreTime instructions.

You do not normally need to install Airtime manually, unless you are testing a development version of the software. Versions of Airtime recommended for production use are available for download and upgrade via *secure apt*, as shown in the *Automated installation* chapter.

Dependencies provided by Sourcefabric
-------------------------------------

The <http://apt.sourcefabric.org/> repository contains up-to-date Debian and Ubuntu packages of Airtime dependencies such as **liquidsoap** and **silan** which you may find convenient to install, even if you are installing Airtime manually. Refer to the chapter *Automated installation* for repository setup details.

Airtime requires a version of **python-virtualenv** later than 1.4.8, but Ubuntu Lucid includes the older version 1.4.5 of this package. Before performing a manual installation on Lucid, you should update **python-virtualenv** using the backported package available from the <http://apt.sourcefabric.org/> repository. This step is not necessary when performing an automated installation, in which dependencies are resolved automatically.

Full install
------------

The **airtime-full-install** script is designed to configure your server for you, using typical default settings.

1. In the server terminal or console, download Airtime from <https://github.com/sourcefabric/Airtime/releases> with **wget**. For example, to download version 2.5.1, you could use the command:

    wget https://github.com/sourcefabric/Airtime/archive/airtime-2.5.1-ga.tar.gz

Then unzip the downloaded file in your home directory. This action will create a subdirectory called *airtime-2.5.1*:

    tar -xvzf airtime-2.5.1-ga.tar.gz -C ~/

Alternatively, clone the git repository if you wish to work on the latest Airtime source code:

    git clone https://github.com/sourcefabric/Airtime.git

In order to return your code improvements to Airtime, you will need to create your own fork of the Airtime git repository and send pull requests. See the GitHub help page <https://help.github.com/articles/fork-a-repo> for details.

2. Run the **airtime-full-install** script, for example on Ubuntu:

    sudo ~/airtime-2.5.1/install_full/ubuntu/airtime-full-install

The installation script will indicate which files are being installed on your system, and the directories they are being unpacked into. Finally, it will run the **airtime-check-system** script to confirm that your server environment is set up correctly.

    *** Verifying your system environment, running airtime-check-system ***
    AIRTIME_STATUS_URL             = http://airtime.example.com:80/api/status/format/json/api_key/%%api_key%%
    AIRTIME_SERVER_RESPONDING      = OK
    KERNEL_VERSION                 = 3.2.0-4-amd64
    MACHINE_ARCHITECTURE           = x86_64
    TOTAL_MEMORY_MBYTES            = 2963688
    TOTAL_SWAP_MBYTES              = 7812092
    AIRTIME_VERSION                = 2.5.1
    OS                             = Debian GNU/Linux 7.1 (wheezy) x86_64
    CPU                            = AMD Turion(tm) II Neo N40L Dual-Core Processor
    WEB_SERVER                     = Apache/2.2.22 (Debian)
    PLAYOUT_ENGINE_PROCESS_ID      = 4446
    PLAYOUT_ENGINE_RUNNING_SECONDS = 55
    PLAYOUT_ENGINE_MEM_PERC        = 0.5%
    PLAYOUT_ENGINE_CPU_PERC        = 0.4%
    LIQUIDSOAP_PROCESS_ID          = 4685
    LIQUIDSOAP_RUNNING_SECONDS     = 49
    LIQUIDSOAP_MEM_PERC            = 0.7%
    LIQUIDSOAP_CPU_PERC            = 7.4%
    MEDIA_MONITOR_PROCESS_ID       = 4410
    MEDIA_MONITOR_RUNNING_SECONDS  = 55
    MEDIA_MONITOR_MEM_PERC         = 0.5%
    MEDIA_MONITOR_CPU_PERC         = 0.0%
    -- Your installation of Airtime looks OK!

    ************ Install Complete ************

You are now ready to proceed to the *Configuration* chapter.

Minimal install
---------------

The alternative **airtime-install** script does not attempt to configure your server, an option which you may find more suitable if you have special requirements.

1. In the server terminal or console, install the list of dependencies. For example, on Ubuntu you could enter the command:

    sudo apt-get install postgresql python-virtualenv apache2 coreutils \
    curl ecasound flac gzip libapache2-mod-php5 libcamomile-ocaml-data \
    liquidsoap locales lsof monit mp3gain multitail patch php5-cli \
    php5-curl php5-gd php5-json php5-pgsql php-apc php-pear pwgen \
    python rabbitmq-server silan sudo sysv-rc tar unzip \
    vorbisgain vorbis-tools libzend-framework-php

On Debian, install *zendframework* instead of the *libzend-framework-php* package.

2. Check that the Apache web server modules that Airtime requires are enabled:

    sudo a2enmod php5 rewrite

The server should respond:

    Module php5 already enabled
    Module rewrite already enabled

3. Create a directory to contain the Airtime web interface:

    sudo mkdir -p /usr/share/airtime/public

4. Next, create the Airtime virtual host configuration file for Apache:

    sudo nano /etc/apache2/sites-available/airtime.conf

and enter the information below, substituting your server's hostname for *airtime.example.com* and your system administrator's email address for *admin@example.com*. Make sure you set the *DocumentRoot* and *Directory* paths correctly. This should normally match the *public* directory that the installer will unpack the web interface into, which by default is the */usr/share/airtime/public/* directory. From Airtime 2.3.0 onwards, the web interface can be installed in a subdirectory of the *DocumentRoot* if you require it to be.

For Apache 2.2, you can use the following syntax:

    <VirtualHost *:80>
       ServerName airtime.example.com
       ServerAdmin admin@example.com
       DocumentRoot /usr/share/airtime/public
       php_admin_value upload_tmp_dir /tmp

      <Directory /usr/share/airtime/public>
          DirectoryIndex index.php
          AllowOverride all
          Order allow,deny
          Allow from all
      </Directory>
    </VirtualHost>

Apache 2.4 uses a different access control syntax in the Directory stanza:

    <VirtualHost *:80>
       ServerName airtime.example.com
       ServerAdmin admin@example.com
       DocumentRoot /usr/share/airtime/public
       php_admin_value upload_tmp_dir /tmp

      <Directory /usr/share/airtime/public>
          DirectoryIndex index.php
          AllowOverride all
          Require all granted
      </Directory>
    </VirtualHost>

Press Ctrl+O to save the file, then Ctrl+X to exit the **nano** editor.

5. Create the PHP configuration file */etc/airtime/airtime.ini* in nano:

    sudo nano /etc/airtime/airtime.ini

with the following contents:

    [PHP]
    memory_limit = 512M
    magic_quotes_gpc = Off
    file_uploads = On
    upload_tmp_dir = /tmp
    apc.write_lock = 1
    apc.slam_defense = 0

Save and exit nano, then link this file to the system's PHP configuration with the command:

    sudo ln -s /etc/airtime/airtime.ini /etc/php5/conf.d/airtime.ini

6. Enable the new configuration by entering the command:

    sudo a2ensite airtime

The server should respond:

    Enabling site airtime.
    Run '/etc/init.d/apache2 reload' to activate new configuration!

You may also need to disable the default site configuration, which may otherwise interfere with your Airtime installation:

    sudo a2dissite default

As suggested by the output of the command above, reload the web server configuration.

    sudo /etc/init.d/apache2 reload

The server should respond:

     * Reloading web server config apache2

7. Download Airtime from <https://github.com/sourcefabric/Airtime/releases> with **wget**, and unzip the downloaded file in your home directory. This action will create a subdirectory called *airtime-2.5.1*:

    wget https://github.com/sourcefabric/Airtime/archive/airtime-2.5.1-ga.tar.gz
    tar -xvzf airtime-2.5.1-ga.tar.gz -C ~/

Alternatively, clone the Airtime git repository as shown above.

8. Monit is a utility which Airtime uses to make sure that the system runs smoothly. Enable it by opening the */etc/default/monit* file in the **nano** editor: 

    sudo nano /etc/default/monit

Find the line that begins with *START* and change the value to *yes*:

    START=yes

Save the file with Ctrl+O and close nano with Ctrl+X. Now copy the Monit configuration from the Airtime install directory to the */etc/monit/conf.d/* directory:

    sudo cp ~/airtime-2.5.1/python_apps/monit/airtime-monit.cfg /etc/monit/conf.d/

Open the */etc/monit/monitrc* file in **nano**:

    sudo nano /etc/monit/monitrc

At the end of the file, add the line:

    include /etc/monit/conf.d/*

Save the file with Ctrl+O and close nano with Ctrl+X. Then start Monit with:

    sudo invoke-rc.d monit start

More information about monit is available in the chapter *Using Monit*.

9. On Debian squeeze, make sure the rabbitmq-server daemon has started:

    sudo invoke-rc.d rabbitmq-server start

10. Finally, run the minimal **airtime-install** script: 

    sudo ~/airtime-2.5.1/install_minimal/airtime-install 

Once the **airtime-check-system** script confirms that the install has been successful, you should now be able to log in to the Airtime administration interface, as shown in the *Getting started* chapter, with the username *admin* and the password *admin*. See the *Configuration* chapter for advanced settings.

Install script options
----------------------

By default, the **airtime-install** script preserves any existing configuration or installation that it finds on the server. However, it is also possible to dictate the behaviour of the script with a command line option, as follows:

    --help|-h            Displays usage information.
    --overwrite|-o       Overwrite any existing config files.
    --preserve|-p        Keep any existing config files.
    --no-db|-n           Turn off database install.
    --reinstall|-r       Force a fresh install of this Airtime version.

Manual uninstall
----------------

To manually uninstall Airtime from the server, run the **airtime-uninstall** script from the minimal installation directory, for example:

    sudo ~/airtime-2.5.1/install_minimal/airtime-uninstall

Optionally, you can also delete the Airtime storage and configuration folders, if you have backups and are not going to need the data on this particular server again. The **rm** command should be used with caution, because it has no undo feature.

    sudo rm -r /srv/airtime
    sudo rm -r /etc/airtime
