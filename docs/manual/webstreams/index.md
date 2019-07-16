Adding a web stream
-------------------

A web stream URL and metadata can be added to the LibreTime library, so that a remote stream can be searched for and scheduled to be *pulled* into a show. For example, at the top of the hour your station may pull a news report from journalists working in another studio. This is a different concept from **Master Source** and **Show Source** remote streams which are *pushed* into the LibreTime playout schedule.

To add a web stream, click the **New** button on the right side of the Library page, and select **New Webstream** from the pop-up menu. Like a playlist, web streams in the Library can have a title and **Description**, which may help you find them in searches later.

![](static/Screenshot516-New_remote_webstream.png)

The **Stream URL** setting must include the *port number* (such as 8000) and *mount point* (such as remote\_stream) of the remote stream, in addition to the streaming server name. A **Default Length** for the remote stream can also be set. If the stream is added at the end of a show which becomes overbooked as a result, it will be faded out when the show ends.
