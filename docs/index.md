Welcome to LibreTime
====================

LibreTime makes it easy to run your own online or terrestrial radio station. Check out some [features](features.md) and [screenshots](features.md#screenshots), then [install it](install.md) and start broadcasting!

LibreTime is Free/Libre and Open Source Software (FLOSS). Among other things, this means that you have the freedom to:

* Run it royalty-free for as long as you like.
* Read and alter the code that makes it work (or hire someone to do this for you!)
* Contribute documentation, bug-fixes, etc. so that everyone in the community benefits.

LibreTime is a fork of AirTime due to stalled development of the FLOSS version. For background on this, see this [open letter to the Airtime community](https://gist.github.com/hairmare/8c03b69c9accc90cfe31fd7e77c3b07d).


Getting Started
---------------

The easiest way to check out LibreTime for yourself is to run a local instance in a virtual machine. Assuming you already have Git, Vagrant and Virtualbox installed, just run:

```bash
git clone https://github.com/libretime/libretime.git
cd libretime
vagrant up ubuntu-trusty
```

If everything works out, you will find LibreTime on [port 8080](http://localhost:8080), icecast on [port 8000](http://localhost:8000) and the docs on [port 8888](http://localhost:8888).

Of course, this setup isn't appropriate for production use. For that, check out our [installation instructions](install.md). More information on the vagrant setup are in [the docs](vagrant.md).

