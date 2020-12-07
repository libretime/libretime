The complete LibreTime documentation is available at libretime.org.

The full tarball for the 3.0.0-alpha.8 release of LibreTime is available here.

Since this is an alpha release there will be bugs in the code.

Please report new issues and/or feature requests in the issue tracker. Join our discourse or chat us up on Slack if you need help and for general discussion.
Table of Contents

    Features
    Bugfixes
    Deprecated Features
    Contributors
    Installation
    Updating
    Known Issues
        Installer Issues
            Media-Monitor config needs manual removing
            Outdated silan reports unreliable cue in/out information
        Liquidsoap support
        No watched folder support
        No Line In recording support
        Playout wont work if locale is missing
        Lack of i18n toolchain is disturbing

Features

    The LibreTime project now has a proper logo!
    New "Show Listener Stats" in "Analytics" contains listeners statistics on specific shows.
    Display time of last podcast import in downloaded podcasts view.
    Allow sorting by last play date in smartblocks, makes creating playlists that play the least played track possible.
    Preliminary support for Debian Buster (Remember to patch the liquidsoap scripts if you use Buster).

Bugfixes

    Widgets now use user specified timezones as they should.
    Podcast view now display proper number of downloaded podcasts rather than just the first 25 podcasts.
    Fix using non-ascii characters in podcast publishing service.
    Fix canceling current show for "linked" shows.
    Fix empty schedule page when previous track is empty.
    Fix focus jumping to search in advanced search.

Deprecated Features

    We are phasing out support for Debian 8 (Jessie). See the announcement in the 3.0.0-alpha.7 release notes for more details.
    Liquidsoap 1.1.1 support. 3.0.0-alpha.8 is most likely the last version to support liquidsoap 1.1.1 out of the box. The number of distros that install a current version of liquidsoap are gaining the majority and once Ubuntu releases a version of their distro that has liquidsoap 1.3.x we will switch to native liquidsoap 1.3.x support. Users still on liquidsoap 1.1.1 will need to apply a patch to their liquidsoap scripts (or update liquidsoap).

Contributors

The LibreTime project wants to thank the following contributors for adding PRs to this release:

    @ciaby
    @hairmare
    @learning-more
    @mirzazulfan
    @paddatrapper
    @Robbt

Installation

The main installation docs may be found at http://libretime.org/install/. They describe a "developer" install using the bundled install script.

We are preparing packages for supported distros and you can take those for a spin if you would like to. Usually the packages get built pretty soon after a release is published. If the current version is not available from the below sources you should wait for a while until they get uploaded.

    Ubuntu packages
    Debian packages
    CentOS packages

Please reference these links for further information on how to install from packages. The install docs will get updated to show how to install packages once we have validated that the packages work properly and when the packages are available from a repository allowing you to automate updating to a new version.
Updating

See the docs for complete information on updating. Please ensure that you have proper backups and a rollback scenario in place before updating.
If the update does not go smoothly, it may cause significant downtime, so you should always have a fallback system available during the update to ensure broadcast continuity.

If you installed from GitHub you can git pull in your local working copy and re-run the ./install script with the same --web-root and --web-user arguments you used during the initial install. Tarball users can leave out the git pull part and just call the new version of the install script.
Updating from 3.0.0-alpha, 3.0.0-alpha.1 and, 3.0.0-alpha.2

The configuration file structure has changed. Please move the contents of the /etc/airtime/cloud_storage.conf and /etc/airtime/rabbitmq-analyzer.ini files into the main /etc/airtime/airtime.conf. In all known cases you need to add the following section to the file.

[current_backend]
storage_backend=file

You can then remove the files and the symlink.

rm /etc/airtime/cloud_storage.conf \
   /etc/airtime/rabbitmq-analyzer.ini \
   /etc/airtime/production

While you're at you may also want to remove the amazon_s3 section if it was in any of the files.

Analyzer grabs all the needed info from the main airtime.conf file starting with 3.0.0-alpha.8.
Known Issues

The following issues may need a workaround for the time being. Please search the issues before reporting problems not listed below.
Installer Issues

The installer is generally a bit unstable, we hope to be able to offer some reasonable packages at some point. Some of the GUI driven parts before the first login are also in a somewhat questionable state, ymmv.

For now the installer distro selection is pretty good at auto-detecting your os and usually does an ok job depending on your distro. You should usually not need to pass a --distribution and --release parameter, those are still supported for the time being but their use is not recommended.

The UI works best if you don't use it in an opinionated fashion and change just the bare minimal.

If you want a secure environment you should work through the preparing the server docs (up until the dragons) and be prepared to fix some of the issues the installer gets wrong manually by hacking the config file after the fact.

If you want to skip the installer GUI completely you can configure LibreTime using airtime_mvc/build/airtime.example.conf as an template. Due to some python/PHP differences you must remove all comments from the example to use it 😞. You'll also have to create some folder structures manually and check if the music dir got properly created directly in the database. Referencing a second install -fiap install on a non productive system for reference can help with this type of bootstrap.
Media-Monitor config needs manual removing

If you are using the install script you should most likely remove the [media-monitor] config section from your /etc/airtime/airtime.conf file to ensure you do not run into the problems described in
#450. We recommend you do this before running the update since there are no known LibreTime releases that depend on the config value.
Outdated silan reports unreliable cue in/out information

Out of the box the installer installs a broken, outdated version of silan (0.3.2) on some Debian based Platforms (ie. Ubuntu). This affects Ubuntu 16.04, Debian Jessie and Debian Stretch. CentOS does not have upstream packages and you may either install from source or use the 0.3.3 packages from RaBe APEL.

Check your version of silan by running silan --version. This should report 0.3.3 or higher. If not please see the Silan Installation wiki page for more details & workarounds.

To date silan 0.3.3 or higher is in Debian testing & Ubuntu Bionic. You can check the upstream progress the Debian PTS and Ubuntu launchpad. This section will get removed once the package is in stable.

tldr: Silan Installation
Liquidsoap Support

Libretime currently only supports liquidsoap < 1.3.0 out of the box. If you install a current version of liquidsoap using OPAM or through the Rabe Liquidsoap Distribution for CentOS (RaBe LSD) you will most likely have liquidsoap 1.3.2 or 1.3.3 installed.

#352 reports that liquidsoap < 1.3.0 can exhibit issues on Debian Stretch installs. One fix for the issue is to install liquidsoap 1.3.0 and to use the following patching steps.

You can check your liquidsoap version by running liquidsoap --version.

If you already have liquidsoap >= 1.3.0 you have a couple of options.
Liquidsoap 1.3.0 Patchset (#192)

You can patch your installation of LibreTime to support liquidsoap 1.3.0.

An up to date patch is available through GitHub and can be applied to an unpacked tarball as follows.

cd libretime-3.0.0-alpha.8/
curl -L https://github.com/LibreTime/libretime/compare/master...radiorabe:feature/liquidsoap-1.3-for-3.0.0-alpha.7.patch | patch -p1

Git users can pull from the branch at master...radiorabe:feature/liquidsoap-1.3.0-for-3.0.0-alpha.7 directly.
Install old liquidsoap from opam (#192)

You can downgrade an OPAM install of liquidsoap by running the following command.

opam install "liquidsoap<1.3.0"

No watched folder support

Currently LibreTime does not support watching folders. Uploading files through the web interface works fine and can be automated via a REST API. Re-implementing watched folder support is on the roadmap. Please consider helping out with the code to help speed things along if you want to use the feature.
No line in support

This feature went missing from LibreTime due to the fact that we based our code off of the saas-dev branch of legacy upstream and support for recording hasn't been ported to the new airtime analyzer ingest system. #42 currently tracks the progress being made on line in recording.
Playout wont work if locale is missing

Some minimal OS installs do not have a default locale configured. This only seems to affect some VPS installs as they often do not have a locale setup in the default images provided.

You can set up the locale using a combination of the following commands. You might also want to consult the documentation of your VPS provider as it may contain an official way to set up locales when provisioning a VPS.

# Set locale using systemds localectl
localectl set-locale LANG="en_US.utf8"

These instructions do not seem to work on all Debian based distros so you might need to use update-locale as follows.

#Purge all locales but en_US.UTF-8
sudo locale-gen --purge en_US.UTF-8
#Populate LANGUAGE=
sudo update-locale LANGUAGE="en_US.UTF-8"

Lack of i18n toolchain is disturbing

Some translations might miss the tarball. They didn't get lost, but the build chain needs fixing. Work is in #301 and additional work is needed as it has become clear that we probably want to support bidirectional translation syncing with zanata.