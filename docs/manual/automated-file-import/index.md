The *airtime-import* script can be combined with the standard *SFTP* (secure FTP) program and *cron* daemon on a GNU/Linux server to enable automated file import from multiple remote computers. This could save time for your station staff when using distributed production methods, or content syndication.

Traditional FTP servers use plain text passwords (without encryption) and are therefore not recommended for upload accounts on Airtime servers accessible from the public Internet. SFTP is a cross-platform protocol which works with many desktop programs including **gFTP** for GNU/Linux (<http://www.gftp.org/>). This program can be installed on Debian or Ubuntu desktop computers with the command:

     sudo apt-get install gftp

Other popular SFTP clients include **FileZilla** for Windows (<http://filezilla-project.org/>) and **Cyberduck** for Mac and Windows (<http://cyberduck.ch/>).

To enable SFTP uploads, first invoke the **adduser** command to create the *uploads* account on the server. For security reasons this user account is restricted to using SFTP only; it cannot be used for executing other commands in a login shell.

    sudo adduser --home /srv/airtime/uploads --shell /usr/lib/sftp-server uploads

The server will then invite you to type in the password for the new *uploads* user, and once again for confirmation. The security of your Airtime server depends on the strength of the password that you set, so be sure to use a long and complex password with upper case, lower case and numerical characters. It is not necessary to set a full name or other details for this account. 

    Adding user `uploads' ...
    Adding new group `uploads' (1003) ...
    Adding new user `uploads' (1002) with group `uploads' ...
    Creating home directory `/srv/airtime/uploads' ...
    Copying files from `/etc/skel' ...
    Enter new UNIX password:
    Retype new UNIX password:
    passwd: password updated successfully
    Changing the user information for uploads
    Enter the new value, or press ENTER for the default
        Full Name []:
        Room Number []:
        Work Phone []:
        Home Phone []:
        Other []:
    Is the information correct? [Y/n] Y

 Next, create a folder to contain the incoming files:

     sudo mkdir /srv/airtime/uploads/incoming/

Then create a script to run once per hour:

     sudo nano /etc/cron.hourly/airtime-upload

The script should import the newly uploaded files from the incoming folder specified, using the *copy* option, and then remove the original uploaded files. This step, rather than simply using the *watch* option on the */srv/airtime/uploads/incoming/* folder, ensures that the *uploads* SFTP account does not have direct write access to the Airtime storage archive. That could be a security risk if the password was compromised.   

    #!/bin/sh

    # Run the import script on fresh uploads

    airtime-import copy /srv/airtime/uploads/incoming/

    # Clean the incoming directory to save disk space

    rm -r /srv/airtime/uploads/incoming/*

Finally, the script should be made executable so that the cron daemon can run it.

    sudo chmod +x /etc/cron.hourly/airtime-upload

By default, Debian and Ubuntu GNU/Linux run *cron.hourly* tasks at 17 minutes past each hour. This value can be adjusted in the file */etc/crontab* on the server, if required.

Remote users should connect to the Airtime server using their client software of choice, making sure that they specify an SFTP rather than FTP connection. The remote directory for the clients to use would be */srv/airtime/uploads/incoming/* as configured above.*
*

![](static/Screenshot118-gFTP_upload.png)

For additional security, you could configure your Airtime server to use an encryption key pair for the *uploads* account, instead of a password. See <https://help.ubuntu.com/community/SSH/OpenSSH/Keys> for details of how to do this on an Ubuntu server.
