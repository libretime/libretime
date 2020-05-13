---
layout: default
title: Home
---


Welcome to LibreTime
====================

LibreTime makes it easy to run your own online or terrestrial radio station. Check out some features below and then [install it](install) and start broadcasting!
Don't believe us? Check out our [On Air in 60 Seconds](on-air-in-60-seconds/index) page to see just how easy it is to use LibreTime!

Some of LibreTime's main features include:

* *Web-based remote station management* - authorized personnel can add
   programme material, create playlists or smart blocks, and stream in live,
   all via a web interface.
* *Automation* - LibreTime has a scheduler function that enables users to
   create shows with content for playback at the exact date and time specified.
   Playlists, smart blocks and remote stream URLs can be used multiple times.
* *Solid playout* - LibreTime uses the open source Liquidsoap streaming language
   for reliable and precise playback to multiple outputs.
* *Open, extensible architecture* - stations are free to extend and alter
   all parts of the program code, under the GNU AGPLv3 license.
* *Multilingual* - supports over 15 languages both in the interface and inside file metadata
* *Low system requirements*
  * For servers: 1Ghz processor, 2 GB RAM, 500 MB, and a wired ethernet connection with a static IP address
  * For end-users: a modern version of Firefox, Chrome, or Safari, and a screen resolution of at least 1280x768

LibreTime is Free/Libre and Open Source Software (FLOSS). Among other things, this means that you have the freedom to:

* Run it royalty-free for as long as you like.
* Read and alter the code that makes it work (or hire someone to do this for you!)
* Contribute documentation, bug-fixes, etc. so that everyone in the community benefits.

LibreTime is a fork of AirTime due to stalled development of the FLOSS version. For background on this, see this [open letter to the Airtime community](https://gist.github.com/hairmare/8c03b69c9accc90cfe31fd7e77c3b07d).

We have a number of [how-to guides](tutorials) that contain step-by-step instructions for various common tasks for both end users and administrators.

There are currently no companies offering turn-key LibreTime hosting so if you are interested in running it you will need to have some familiarity with running a linux server. You can always reach out to help from the community at our [forum](http://discourse.libretime.org). You can also join our [Mattermost instance](https://chat.libretime.org/) and talk with other developers and users.

### Proud Users

![Rabe95.6](static/stations/rabe956.svg) ![RadioCampus93.3](static/stations/radiocampus933.png) ![WRCS92.7](static/stations/wrcs927.png) ![WRIR 97.3](static/stations/wrir973.png)

Getting Started (for Developers and Admins)
---------------

The easiest way to check out LibreTime for yourself is to run a local instance in a virtual machine. Assuming you already have Git, Vagrant and Virtualbox installed, just run:

```bash
git clone https://github.com/libretime/libretime.git
cd libretime
vagrant up ubuntu-bionic
```

If everything works out, you will find LibreTime on [port 8080](http://localhost:8080), icecast on [port 8000](http://localhost:8000) and the docs on [port 8888](http://localhost:8888).

Of course, this setup isn't appropriate for production use. For that, check out our [installation instructions](install). More information on the vagrant setup are in [the docs](vagrant).
