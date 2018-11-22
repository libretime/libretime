LibreTime-import is a service that runs in the background and provides an easy way to add files to your system without using the web interface.

During installation LibreTime creates a new directory underneath your */srv/airtime/stor/* folder named uploads. LibreTime will attempt to import any file you copy into this directory and by default this directory is made writeable by anyone who has a login shell to your system. It is recommended that you copy vs. move files into this directory because it deletes them from the uploads directory after processing. If they are succesfully imported by the airtime analyzer then they will show up automatically in the Tracks section of your library. Files that fail to import currently remain in your /srv/airtime/stor/organize folder but there is a bug report to address this on [github](https://github.com/LibreTime/libretime/issues/508) and this will be updated when it is fixed.

Bulk importing tracks
---------------------

The most likely scenario you will be using libretime-import for is during your initial setup of your system. If you have a lot of audio files you want to add to LibreTime you can do this via the command line and even copy a whole folder of files into libretime-import. Do this by typing ```cp -R FOLDERNAME /srv/airtime/stor/uploads``` and LibreTime should import your files. How you get your files on the server in the first place will require additional steps such as setting up ssh or scp. This maybe added to the manual at a later date but is well documented elsewhere on the Internet.


Changing the import folder
---------------------------

To change the folder that LibreTime imports files from you can modify the upload_dir setting in /etc/airtime/airtime.conf config file and set a new directory. **Be aware that libretime_import won't scan an existing folder to import files**. It also will delete any files copied or moved into the import folder. So for most use cases you won't need to change the import folder. You will also need to ensure that the user your apache web server runs off of can read and write the directory as this is the user that the libretime-import runs as by default. 

Importing via FTP
-----------------

If you setup a FTP server and give it write access to the uploads directory then your users can upload files via FTP. We haven't documented a specific set of steps to accomplish this at this point as we don't plan on bundling a FTP server with LibreTime anytime soon. If you want to figure out how to do this and add to the documentation we are a volunteer ran project and your contributions are welcome.

Installing Libretime-Import on a pre-existing LibreTime install
---------------------------------------------------------------

libretime-import was added after libretime was already in use by a number of stations. To install just the service navigate in the command line to libretime import folder under python_apps and run ```sudo python setup.py install --install-scripts=/usr/bin``` and this will install libretime import. You will need to add the upload_dir configuration manually to your /etc/airtime/airtime.conf file if you decide to use a different import folder.
