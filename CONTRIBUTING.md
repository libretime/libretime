# Contributing to LibreTime

First and foremost, thank you! We appreciate that you want to contribute to LibreTime, your time is valuable, and your contributions mean a lot to us.

Before any contibution, read and be prepared to adhere to our [code of conduct](https://github.com/libretime/code-of-conduct/blob/master/code_of_conduct.md).

In addition, LibreTime follow the standardized [C4 development process](https://rfc.zeromq.org/spec:42/c4/), in which you can find explanation about most of the development workflows for LibreTime.

**How to contribute**

- [Reporting bugs](#reporting-bugs)
- [Suggesting enhancements](#suggesting-enhancements)
- [Contributing to documentation](#contributing-to-documentation)
- [Contributing to code](#contributing-to-code)

## Reporting bugs

This section guides you through submitting a bug report for LibreTime.
Following these guidelines helps maintainers and the community understand your report, reproduce the behavior, and find related reports.

Before creating bug reports, please check the following list, to be sure that you need to create one:

- **Check the [LibreTime forum](https://discourse.libretime.org/)** for existing questions and dicscussion.
- **Check that your issue does not already exist in the [issue tracker](https://github.com/libretime/libretime/issues?q=is%3aissue+label%3abug)**.

> **Note:** If you find a **Closed** issue that seems like it is the same thing that you're experiencing, open a new issue and include a link to the original issue in the body of your new one.

When you are creating a bug report, please include as many details as possible. Fill out the [required template](https://github.com/libretime/libretime/issues/new?labels=bug&template=bug_report.md), the information it asks helps the maintainers resolve the issue faster.

Bugs are tracked on the [official issue tracker](https://github.com/libretime/libretime/issues).

## Suggesting enhancements

This section guides you through submitting an enhancement suggestion for LibreTime, including completely new features and minor improvements to existing functionality. Following these guidelines helps maintainers and the community understand your suggestion and find related suggestions.

Before creating enhancement suggestions, please check the following list, as you might find out that you don't need to create one:

- **Check the [LibreTime forum](https://discourse.libretime.org/)** for existing questions and dicscussion.
- **Check that your issue does not already exist in the [issue tracker](https://github.com/libretime/libretime/issues?q=is%3aissue+label%3afeature-request)**.

When you are creating an enhancement suggestion, please include as many details as possible. Fill in [the template](https://github.com/libretime/libretime/issues/new?labels=feature-request&template=feature_request.md), including the steps that you imagine you would take if the feature you're requesting existed.

## Contributing to documentation

One of the simplest ways to get started contributing to a project is through improving documentation. LibreTime is constantly evolving, this means that sometimes our documentation has gaps. You can help by adding missing sections, editing the existing content so it is more accessible or creating new content (tutorials, FAQs, etc).

Issues pertaining to the documentation are usually marked with the [Documentation](https://github.com/libretime/libretime/labels/documentation) label.

## Contributing to code

LibreTime uses the [black](https://github.com/psf/black) coding style and you must ensure that your code follows it. If not, the CI will fail and your Pull Request will not be merged.

Similarly, the import statements are sorted with [isort](https://github.com/pycqa/isort) and special care must be taken to respect it. If you don't, the CI will fail as well.

To make sure that you don't accidentally commit code that does not follow the coding style, you can install a [`pre-commit`](https://pre-commit.com/) hook that will check that everything is in order:

```bash
pre-commit install
```

You can also run it anytime using:

```bash
pre-commit run --all-files
```
