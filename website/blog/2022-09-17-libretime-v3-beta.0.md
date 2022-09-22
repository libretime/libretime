---
title: LibreTime 3.0.0 beta.0, finally!
authors:
  - jooola
tags: [release]
---

Greetings LibreTime enthusiast's and friends!

We are happy to announce that we [released LibreTime `v3.0.0-beta.0` yesterday](/docs/releases/3.0.0-beta.0/)! :tada:

This is our very first blog post, and they should be as irregular as LibreTime releases! We changed from the monthly community meetings to something less timezone sensitive, such as blog posts.

<!--truncate-->

We will go through the notable changes in the recent release of LibreTime.

A **lot** of work has been put into LibreTime to finalize the forking from AirTime, and provide a comfortable base for future works.

## Versions, and supported distributions releases

We drafted a [supported distributions releases and versions support policy](/docs/developer-manual/development/releases#distributions-releases-support), that will help us to know when to drop support for distributions releases, and help you to plan for a distributions upgrades.

The important bits are:

- New LibreTime releases will only target the current stable distributions releases.
- Maintenance only releases will provide bug and security fixes for old stable distributions releases until they reach their end of life.

Note that the [release note for LibreTime `v3.0.0-beta.0`](/docs/releases/3.0.0-beta.0/#-deprecation-and-removal) announced some major deprecation.

## What is missing from AirTime ?

I would expect that most of AirTime users already migrated to LibreTime using one of the many alpha versions, for those who didn't migrated yet, you will have to install LibreTime `<3.1` before upgrading to any newer versions.

Some incomplete or non functional features from AirTime were removed in LibreTime:

- [The **My podcasts** feature has been removed.](https://github.com/libretime/libretime/pull/1327)
- [The **Line in recording** feature was not added.](https://github.com/libretime/libretime/issues/42)
- (I probably missed a few others...)

Those feature might be reimplemented in the future, any contribution are welcome!

## Running inside containers

This has been awaited for quite some time: you can now [run LibreTime inside containers](/docs/admin-manual/setup/install#using-docker-compose) :tada: ! A [docker-compose example](https://github.com/libretime/libretime/tree/main/docker/example) is available in the repository.

Please help us test it and give us feedback, we really appreciate any help!

## A new configuration file

A lot has moved with regards to configuring LibreTime. A lot of settings from the database moved to the configuration file to simplify the configuration of LibreTime.

This will allow us to add more options for advanced use cases, such as ssl input harbors, HLS outputs or your own Liquidsoap scripts.

## Improvement for development

LibreTime might be hard to contribute to without having a good understanding of the project in general. A lot of work has been made to clean and modernize parts of LibreTime, by simplifying the file structure of the project and the naming, adding typing definitions or development tools to help you write good code. To keep improving the developer experience, new contributions will have to include typings and tests.

Though, with the new docker-compose setup, setting up a development environment is really easy! Feel free to test it by making a contribution!

## Now what ?

We expect to cut the LibreTime 3.0.0 release in the next weeks. So please help us test LibreTime so we can fix bugs!

A few exiting features that are will be worked on once 3.0.0 is out:

- Liquidsoap 2.0 support
- SSL input harbors
- HLS streaming
- Start embedding a new UI in parts of the legacy web application.

If you feel [like paying us a beer, or a month rent](https://opencollective.com/libretime), we will really appreciate!

Cheers !

Jo
