---
title: How to setup high availability playout
---

This tutorial walks you through the steps required to setup playout for high availability.

:::info

We assume you have a way to consume multiple audio inputs (for example icecast). For terrestrial HA you might consider
having an audio processor with switching and silence detection capabilities.

:::

## 1. Initial LibreTime setup

When initially setting up LibreTime, first setup the components in
the [Create the schedule block](../../contributor-manual/design/architecture.md#create-the-schedule) along with
the message queue.

If you are going provide multiple icecast or shoutcast streams, please make sure to set all streams you want visible
from the LibreTime widget to enabled in this blocks config file.

## 2. Setup your playout blocks

Create your [Play the schedule blocks](../../contributor-manual/design/architecture.md#play-the-schedule).

You will need one for each replication you want, and any additional playout blocks you may want. For example, if you
are running a terrestrial radio station, you may have two playout blocks for your radio processor, and an additional
one for a non-HA icecast instance.

The playout blocks should each contain the LibreTime playout service as well as a liquidsoap service. They should be
able to talk to the message queue and the LibreTime API.

## 3. Configure each playout block

Create a config file for each playout block. Only enable the output you want from this block. For example, you may
configure two blocks for system output to an audio card, and another block for streaming to icecast.

The sections that need configured are

- [General](../configuration.md#general)
    - Only `public_url` and `api_key` are needed. `cache_ahead_hours` currently has
      no effect as playout is hardcoded to cache ahead 1 day.
- [RabbitMQ](../configuration.md#rabbitmq)
- [Playout](../configuration.md#playout)
    - Make sure to configure the associated liquidsoap host for this block.
- [Liquidsoap](../configuration.md#liquidsoap)
- [Stream](../configuration.md#stream)
    - Configure the stream outputs you want this playout block to provide and disable the rest.

## 4. Testing

Start all the playout blocks and confirm they get the schedule from the LibreTime web server. Additionally and verify
their outputs are correct.
