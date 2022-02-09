---
sidebar_position: 1
---

# Introduction

Libretime is an open-source radio automation system. It can be used for music and show playout in a radio studio or start an internet radio station on its own. Libretime runs on Ubuntu and Debian Linux; [we're also working on making Docker images](https://github.com/LibreTime/libretime/issues/949).

## Getting Started

Get started by installing Libretime on your server. Open up your terminal and enter

```bash
git clone https://github.com/LibreTime/libretime.git
cd libretime

sudo ./install -fiap
```

After the installer is done, head to your server's IP address in a web browser and complete the setup wizard.

### Upload some music

Login using the default username and password and click on the blue **Upload** button in the left pane. Drag and drop files onto the upload area or click to browse for files on your computer.

![](./guides/scheduling-shows-select_files.png)

Once the files have uploaded and finished being analyzed, you can schedule your first show.

### Schedule your first show

Navigate to the Calendar and click the **+ New Show** button on the toolbar.

![](./guides/scheduling-shows-screenshot560-show_when.png)

Fill out the **What** (name, description of your show) and **When** (start time, end time) blocks, then click the grey **+ Add This Show** button at the top.

Click on the new show in the Calendar to open its context menu and select **Schedule Tracks**.

![](./guides/scheduling-shows-screenshot561-add_show_content.png)

Your uploaded tracks, playlists, smartblocks, and webstreams appear in the left pane; drag them to the right pane in the order you want them to play back for your show. The bar at the bottom of the right playlist tells you how much time you have left in your show.

![](./guides/scheduling-shows-screenshot562-drag_show_content.png)

When you're done, click **Ok** at the bottom of the window.

### That's it!

You just scheduled your first show! It will automatically begin playing back at the time your show is set to start. You can listen to what is playing live by clicking the **Listen** button under the On Air indicator in the top right corner.

## Next Steps

- Make sure you [set the time on your server](/docs/getting-started/set-server-time)
- Learn how to [work with podcasts](/docs/guides/podcasts)
- [Create user accounts](/docs/guides/users) for your DJs, Program Managers, and Administrators
- Learn how to [broadcast live](/docs/guides/live-broadcast) from your studio
