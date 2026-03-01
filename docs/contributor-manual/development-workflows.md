---
title: Development workflows
---

## Git workflow

LibreTime uses [Github pull requests to manage changes](https://docs.github.com/en/get-started/quickstart/contributing-to-projects). The workflow looks like this:

- [Create a fork of the project](https://docs.github.com/en/get-started/quickstart/fork-a-repo).
- Check out the `main` branch.
- Create a new branch based on the checked out branch.
- Work on your changes locally. Try to keep each commit small to make reviews easier.
- Lint and test the codebase, for example using the `make lint` or `make test` commands inside the app folder you want to check.
- Push your branch.
- Create a pull request in Github.
- Your request will be reviewed and feedback provided.

## Project layout

The LibreTime repository is split into multiple tools/services/apps at the root of the project. You will find `Makefile` in each of the component of the project. Those `Makefile` describe the different commands available to develop the project.

Here is a small description of the different components in the repository:

```bash
.
├── analyzer      # The LibreTime Analyzer service
├── api           # The LibreTime API service
├── api-client    # The API clients used internally by other services
├── docker        # The docker related files
├── docs          # The documentation
├── install       # The install script
├── installer     # The install script extra files
├── legacy        # The LibreTime Legacy service
├── playout       # The LibreTime Playout service
├── shared        # A shared library using by our python based services
├── tools         # Set of tools used to maintain the project
├── website       # Website repository that is cloned when developing the documentation
└── worker        # The LibreTime Worker service
```

For example, to lint and test the `analyzer` service, you can run the commands:

```bash
make -C analyzer lint test

# Or by changing into the analyzer directory
cd analyzer
make lint test
```

## Pre-commit

LibreTime uses [pre-commit](https://pre-commit.com/) to ensure that the files you commit are properly formatted, follow best practice, and don’t contain syntax or spelling errors.

You can install and setup pre-commit using the [quick-start guide on the pre-commit documentation](https://pre-commit.com/#quick-start). Make sure to install pre-commit and setup the git pre-commit hook so pre-commit runs before you commit any changes to the repository.

The workflow looks like this:

- Install pre-commit.
- After cloning the repository, setup the pre-commit git hooks:

  ```bash
  pre-commit install
  ```

- Make your changes and commit them.
- If pre-commit fails to validate your changes, the commit process stops. Fix any reported errors and try again.

:::info

You can also run pre-commit anytime on all the files using:

```bash
pre-commit run --all-files
```

:::
