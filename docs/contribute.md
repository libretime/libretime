# Contributing to LibreTime

First and foremost, thank you! We appreciate that you want to contribute to
LibreTime, your time is valuable, and your contributions mean a lot to us.

Before any contribution, read and be prepared to adhere to our
[code of conduct](https://github.com/libretime/organization/blob/main/CODE_OF_CONDUCT.md).

In addition, LibreTime follow the standardized
[C4 development process](https://rfc.zeromq.org/spec:42/c4/), in which you can
find explanation about most of the development workflows for LibreTime.

**How to contribute**

- [Reporting bugs](#reporting-bugs)
- [Suggesting enhancements](#suggesting-enhancements)
- [Financially](https://libretime.org/contribute#financial)
- [Contributing to documentation](https://libretime.org/contribute#write-documentation)
- [Contributing to code](#code)

## Reporting bugs

This section guides you through submitting a bug report for LibreTime.
Following these guidelines helps maintainers and the community understand your
report, reproduce the behavior, and find related reports.

Before creating bug reports, please check the following list, to be sure that
you need to create one:

- **Check the [LibreTime forum](https://discourse.libretime.org/)** for existing
  questions and discussion.
- **Check that your issue does not already exist in the
  [issue tracker](https://github.com/libretime/libretime/issues?q=is%3aissue+label%3abug)**.

> **Note:** If you find a **Closed** issue that seems like it is the same thing
> that you're experiencing, open a new issue and include a link to the original
> issue in the body of your new one.

When you are creating a bug report, please include as many details as possible.
Fill out the [required template](https://github.com/libretime/libretime/issues/new?labels=bug&template=bug_report.md),
the information it asks helps the maintainers resolve the issue faster.

Bugs are tracked on the [official issue tracker](https://github.com/libretime/libretime/issues).

## Suggesting enhancements

This section guides you through submitting an enhancement suggestion for
LibreTime, including completely new features and minor improvements to existing
functionality. Following these guidelines helps maintainers and the community
understand your suggestion and find related suggestions.

Before creating enhancement suggestions, please check the following list, as you
might find out that you don't need to create one:

- **Check the [LibreTime forum](https://discourse.libretime.org/)** for existing
  questions and discussion.
- **Check that your issue does not already exist in the
  [issue tracker](https://github.com/libretime/libretime/issues?q=is%3aissue+label%3afeature-request)**.

When you are creating an enhancement suggestion, please include as many details
as possible. Fill in [the template](https://github.com/libretime/libretime/issues/new?labels=feature-request&template=feature_request.md),
including the steps that you imagine you would take if the feature you're
requesting existed.

## Code

Are you familiar with coding in PHP or Python? Have you made projects in
Liquidsoap and some of the other services we use? Take a look at the
[list of bugs and feature requests](https://github.com/libretime/libretime/issues),
and then fork our repo and have a go! Just use the **Fork** button at the top of
our [GitHub page](https://github.com/libretime/libretime), clone the forked repo
to your desktop, open up a favorite editor and make some changes, and then
commit, push, and open a pull request.

Knowledge on how to use [Github](https://guides.github.com/activities/hello-world/)
and [Git](https://git-scm.com/docs/gittutorial) will suit you well, use the
links for a quick 101.

LibreTime uses the [black](https://github.com/psf/black) coding style for Python
and you must ensure that your code follows it. If not, the CI will fail and your
Pull Request will not be merged. Similarly, the Python import statements are
sorted with [isort](https://github.com/pycqa/isort). There is configuration
provided for [pre-commit](https://pre-commit.com/), which will ensure that code
matches the expected style and conventions when you commit changes. It is set up
by running:

```bash
sudo apt install pre-commit
pre-commit install
```

You can also run it anytime using:

```bash
pre-commit run --all-files
```

## Testing and CI/CD

Before submitting code to the project, it's a good idea to test it first. To do
this, it's easiest to install LibreTime in a virtual machine on your local
system or in a cloud VM. We have instructions for setting up a virtual instance
of LibreTime with [Vagrant](/docs/vagrant) and [Multipass](/docs/multipass).

If you would like to try LibreTime in a Docker image, Odclive has instructions
[here](https://github.com/kessibi/libretime-docker) for setting up a test image
and a more persistent install.

## Modifying the Database

LibreTime is designed to work with a [PostgreSQL](https://www.postgresql.org/)
database server running locally. LibreTime uses [PropelORM](https://github.com/propelorm/Propel)
to interact with the ZendPHP components and create the database. The version 2
API uses Django to interact with the same database.

If you are a developer seeking to add new columns to the database here are the steps.

1. Modify `legacy/build/schema.xml` with any changes.
2. Run `dev_tools/propel_generate.sh`
3. Update the upgrade.sql under `legacy/application/controllers/upgrade_sql/VERSION` for example
   `ALTER TABLE imported_podcast ADD COLUMN album_override boolean default 'f' NOT NULL;`
4. Update the models under `api/libretime_api/models/` to reflect the new
   changes.

## Documentation and financial contributions

More information about how to contribute documentation or financially
through our [OpenCollective](https://opencollective.com/libretime) can be found
on our [website](https://libretime.org/contribute).
