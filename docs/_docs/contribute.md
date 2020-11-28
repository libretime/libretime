---
title: Contribute to Libretime
layout: article
category: dev
permalink: /contribute
---

> LibreTime is a fork of AirTime due to stalled development of the open source version. For background on this,
see this [open letter to the Airtime community](https://gist.github.com/hairmare/8c03b69c9accc90cfe31fd7e77c3b07d).

## Bug reporting

If you think you've found a bug, please report it on our [Github issues page](https://github.com/LibreTime/libretime/issues/new/choose).
Create a bug report by selecting **Get Started** next to **Bug Report**. That way, the LibreTime team can keep track of
your problem and notify you when it has been fixed. You can also suggest
improvements and new features for LibreTime on that site.

## Feature requests

Have an idea that would make Libretime even better than it is right now? Start a **Feature request** on our
[Github issues page](https://github.com/LibreTime/libretime/issues/new/choose).

## Translation

LibreTime can run in over 15 different languages due to the gracious help of our volunteers. Is your language not
supported? Follow [this guide](/docs/interface-localization) to add your language to LibreTime!

## Write documentation

Our site is now built by Jekyll, which has an installation guide [here](https://jekyllrb.com/docs/installation/) to help get you started.
 After cloning our repo locally, enter the `docs/` directory and run

```
bundle install
jekyll serve
```

## Code

Are you familar with coding in PHP? Have you made projects in Liquidsoap and some of the other services we use?
Take a look at the [list of bugs and feature requests](https://github.com/LibreTime/libretime/issues), and then
fork our repo and have a go! Just use the **Fork** button at the top of our **Code** page, clone the forked repo to
your desktop, open up a favorite editor and make some changes, and then commit, push, and open a pull request.
Knowledge on how to use [Github](https://guides.github.com/activities/hello-world/) and [Git](https://git-scm.com/docs/gittutorial)
will suit you well, use the links for a quick 101.

## Testing and CI/CD

Before submitting code to the project, it's a good idea to test it first. To do this, it's easiest to install
LibreTime in a virtural machine on your local system or in a cloud VM. We have instructions for setting up a virtural
instance of LibreTime with [Vagrant](/docs/vagrant) and [Multipass](/docs/multipass).

If you would like to try LibreTime in a Docker image,
Odclive has instructions [here](https://github.com/kessibi/libretime-docker) for setting up a test image
and a more persistant install.

## Modifying the Database
LibreTime is designed to work with a [PostgreSQL](https://www.postgresql.org/) database server running locally.
LibreTime uses [PropelORM](http://propelorm.org) to interact with the ZendPHP components and create the database.

If you are a developer seeking to add new columns to the database here are the steps.

1. Modify `airtime_mvc/build/schema.xml` with any changes.
2. Run `dev_tools/propel_generate.sh`
3. Update the upgrade.sql under `airtime_mvc/application/controllers/upgrade_sql/VERSION` for example
 `ALTER TABLE imported_podcast ADD COLUMN album_override boolean default 'f' NOT NULL;`