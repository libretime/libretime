---
title: Library management
sidebar_position: 30
---

This page describe the available options to manage the LibreTime library.

## Files bulk import

To scan a directory and import the files into the library, you can use the following command:

```bash
sudo -u libretime libretime-api bulk_import --path PATH_THE_DIRECTORY_TO_SCAN
```

If you're running from a docker environment, use
```bash
docker-compose run --rm api libretime-api bulk_import --path PATH_THE_DIRECTORY_TO_SCAN
```

See the command usage to get available options, or run
```bash
sudo -u libretime libretime-api bulk_import --help
# or for docker
docker-compose run --rm api libretime-api bulk_import --help
```
