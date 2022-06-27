---
title: Podcasts
---

The Podcasts page allows you add subscriptions to podcasts which are often used to syndicated audio files using a URL called a RSS feed. This allows your LibreTime instance to automatically download new shows from the web.

:::info

All podcasts available on iTunes have a RSS feed but it is sometimes hidden. See issue [#510](https://github.com/libretime/libretime/issues/510) for more information. RSS feeds that do not end in _.xml_ may be accepted by LibreTime yet fail to download episodes. A work-around is to download the episode using a podcast client such as [gpodder](https://gpodder.github.io/) and then manually upload and schedule the episode.

Podcast feeds coming from Anchor.fm have been known to have a similar issue.

:::

The podcast interfaces provides you with the ability to generate [Smartblocks](./playlists.md) that can be used in conjunction with autoloading playlists to schedule the newest episode of a podcast without human intervention.

<iframe
    width="560"
    height="315"
    src="https://www.youtube-nocookie.com/embed/g-4UcD8qvR8"
    frameborder="0"
    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
    allowfullscreen
></iframe>

### Podcasts dashboard

![](./podcasts-podcasts_dashboard.png)

The podcasts dashboard is similar to the tracks view, allowing you to add, edit, and remove
podcasts by the toolbar, in addition to sorting by columns.

To add a podcast, click on the **+ Add** button on the toolbar and provide the podcast's RSS feed, which usually ends in _.xml_.
Once the podcast's feed is recognized, the editor pane opens for the podcast.

### Editor

![](./podcasts-podcasts_editor.png)

In the podcasts editor, you can rename the podcast, update settings for the podcast, and manage episodes.
A search box is available to search for episodes within the feed.

- To import an episode directly into LibreTime, double-click on an episode or select it and click **+ Import**. The podcast will appear under tracks with the Podcast Name as the Album.
- To delete an episode from LibreTime, select the episode and click on the red trash can on the toolbar.
- If you would like LibreTime to automatically download the latest episodes of a podcast, make sure _Download latest episodes_ is checked. This can be used in conjunction with Smartblocks and Playlists to automate downloading and scheduling shows that are received via podcast feed.
