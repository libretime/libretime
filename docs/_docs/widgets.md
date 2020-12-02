---
title: Widgets
category: interface
layout: article
---

Bring your Libretime broadcast to your website with embeddable widgets! Libretime comes with two widgets: a streaming player and a schedule. Both widgets use iframes to display and can be placed wherever embeddable code can on a website.

## Getting Started

Before using the widgets, make sure Libretime's Public API is enabled in **Settings** > **General**.

![](/img/widgets_settings.png)

> **Note:** Your Libretime instance needs to be accessible to the public *without the use of a VPN or SSH tunneling* in order for the widgets to work.

## Streaming Player Widget

The streaming player widget inserts your Libretime stream into your website. One example is from [WRCS Community Radio](http://wcrsfm.org/) in Columbus, Ohio, USA.

<iframe frameborder="0" width="400" height="235" src="http://broadcast.wcrsfm.org/embed/player?stream=auto&title=Now Playing"></iframe>

![](/img/widgets_player.png)

From **Widgets** > **Player**, enter a title for your streaming widget and select what stream you'd like to use. All selectible streams must first be configured in **Settings** > **Streams** (see [Settings](/docs/settings)). **Auto detect** should be fine for most.

## Show Schedule Widget

![](/img/widgets_schedule.png)

The show schedule widget displays the upcoming shows for the next seven days. There are no customizable settings for this widget.