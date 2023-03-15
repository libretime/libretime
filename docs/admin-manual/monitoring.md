---
title: Monitoring
sidebar_position: 85
---

This page provide some guidance to monitor LibreTime.

## Sentry

To gather and remotely monitor exceptions that may occur in your installation, you can use the Sentry library shipped in LibreTime to send reports to any Sentry compatible server ([Sentry](https://sentry.io/), [Glitchtip](https://glitchtip.com/)).

To configure Sentry in LibreTime, you need to:

- install the LibreTime Python packages with the `sentry` extra (the container images already ship the sentry extra),

  ```bash
  # Inside the LibreTime source dir
  sudo pip install ./analyzer[sentry]
  sudo pip install ./api[prod,sentry]
  sudo pip install ./playout[sentry]
  sudo pip install ./worker[sentry]
  ```

- set the [`SENTRY_DSN`](https://docs.sentry.io/product/sentry-basics/dsn-explainer/) environment variable on each of the LibreTime services you want to monitor.

See the [Sentry Python SDK configuration options documentation](https://docs.sentry.io/platforms/python/configuration/options/) to further configure your setup.
