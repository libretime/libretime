---
layout: article
title: Scheduling Shows
git: scheduling-shows.md
---

## Scheduling Shows

<html>
    <br>
    <iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/TJtWUzAlP08" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
</html>

Log in using your username and password using the link in the upper right corner. (If you just installed
LibreTime, your username/password is admin/admin.)

The main workflow in LibreTime is **Upload** media -> create a show on the **Calendar** -> **Schedule Tracks**.

Once you log in, click on the big blue button on the left navigation that says **Upload**.

![](img/Select_files.png)

Select the type of media you are uploading (Music, Station IDs, etc.) by using the dropdown box
at the top of the pane. After that, either drag and drop media into the area below or click the
dashed rectangle to open a file browser.

Once your files have uploaded and have been successfully imported (as shown in the pane on the right),
click on **Calendar** on the left navigation.

![](img/Screenshot558-Add_Show.png)

Click on the blue **+ New Show** button to add a new show.

![](img/Screenshot560-Show_when.png)

At the very minimum, fill out the show's name and when the show will take place. If the show will repeat regularly,
check the **Repeats?** box and fill out the repeat information. A description of all fields of the New Show box
are in the table below. Finially, click on the grey **+ Add this show** button at the top
of the pane to add your show to the calendar.

| Field | Description |
|-------|-------|
| _What_ | |
| Name (Required) | The name of your show |
| URL | The URL of your show. Not used on the public page. |
| Genre | The genre of your show. Not used on the public page. |
| Description | Description of your show. Not used on the public page. |
| _When_ | |
| Start Time (Required) | The time and date the show starts. Note that the time element is in 24 hour time. If the **Now** option is selected, the show will be created to immediately start. |
| End Time (Required) | The time and date the show ends. Defaults to a time one hour after the start time, which can be seen in the **Duration** field, which is uneditable. |
| Repeats? | If checked, allows for options to schedule a repeated show. Shows can repeat weekly up to monthly in increments of one week and can be scheduled on multiple days of the same week. An end date can be set, otherwise the show can be deleted by clicking on its entry in the calendar and clicking Delete > Future Occurances. If **Linked ?** is checked, the playlist scheduled for the next show will also play for all future shows. |
| _Autoloading Playlist_ | |
| Add Autoloading Playlist? | If checked, allows for the following options |
| Select Playlist | Select the playlist the show will autofill from (shows autofill exactly one hour before air). If you wish to use a smartblock you must add it to a playlist and then select that playlist. This can be used to auto-schedule new podcast episodes to air. |
| Repeat Playlist Until Show Is Full | If checked, the playlist will be added to the show multiple times until the slot is full. Useful for applying a one-hour music playlist made up of smartblocks to a two-hour show. |
| _Live Stream Input_ | |
| Use LibreTime/Custom Authentication | |
| Show Source | |
| _Who_ | |
| Search Users, DJs | Program Managers and Admins may assign DJs to a show, giving DJs access to schedule tracks for said show. DJs cannot create shows on their own. |
| _Style_ | |
| Background/Text Color | Set the color of the background and text of entries on the calendar. If not set, LibreTime will select contrasting colors for easy readability. |
| Show Logo | If desired, you can upload a show logo here. The logo does not appear on the public page. |

Once your show is created, click on it to open its context menu. Select **Schedule Tracks** to open the track scheduler.

![](img/Screenshot561-Add_show_content.png)

The track scheduler behaves similar to iTunes or Windows Media Player: media browser on the left, playlist on the right.
Find the tracks that you'd like to schedule by using the search box or sorting columns and then drag them
into the playlist.

![](img/Screenshot562-Drag_show_content.png)

The bar at the end of the show's playlist will show the amount of time the show is underscheduled or overscheduled.
Shows that are underscheduled will have dead air at the end and shows that are overscheduled
will fade out exactly when the show is over (the orange colored entry), meaning tracks scheduled to start
after this point will not play (dark red colored entries). Click the **Ok** button in the bottom right to save.

Show playback will start and end as per each show's start and end times, allowing you to rely completely on
LibreTime for running your station or using LibreTime as a part of your live setup to cover when DJs are not present.
When media is playing, the **On Air** indicator at the top will turn red.

![](img/on-air-status.png)

You can listen to your stream by going to `yourserverIP:8000` or by clicking the **Listen** button under the On Air
indicator.

---

## Calendar Functions {#calendar}

The Calendar page of the LibreTime administration interface has three views: **day**, **week** and **month**, which can be switched using the grey buttons in the top right corner. By default, the **month** view is shown, with today's date highlighted by a pale grey background.

![](img/Screenshot451-Calendar.png)

In the top left corner of the page, you can go back or forward through the **Calendar** by clicking on the buttons which have a small grey triangle in a white circle. Click the **today** button to jump to today's date in the current view. (The **today** button will be greyed out if you are already viewing that date). In the **day** or **week** views, there is also a drop-down menu which allows you to set the resolution displayed for the calendar, ranging from one minute per row to sixty minutes per row.

![](img/Screenshot452-Calendar_resolution.png)

### Editing a show

Show configuration and metadata can be changed at any time, except for **Date/Time Start** and **Record from Line In?** options, which are fixed after broadcast of that show commences. Click the show in the Calendar, and select **Edit Show** from the pop-up context menu. This opens the **Update Show** box, which is almost exactly the same as the **Add this Show** box. Click the **+ Update show** button at the top or bottom of the box when you are done.

![](img/Screenshot459-Update_show.png)

Episodes of repeating shows also have an **Instance Description** field in which you can add details for that particular episode. Click the episode in the Calendar, click **Edit** on the pop-up menu, then click **Edit this instance**. After entering an Instance Description, click the **+ Update show** button.

![](img/Screenshot583-Show_instance_description_vC9ooiT.png)

Alternatively, individual shows can be clicked on and dragged to new days and times in the calendar. However, LibreTime will not allow you to drag a future show into the past, or drag and drop instances of a repeated show. In the **Day** and **Week** views, show length can be adjusted by clicking on the lower edge of the show box, and dragging the edge of the box upwards or downwards. The new show length is calculated automatically.

### Adding content to a show

To add content to a show, click the show in any view on the Calendar, and select **Schedule Tracks** from the pop-up menu. Shows that do not yet contain any scheduled content are marked with a red exclamation mark icon, to the right of the show start and end times in the top bar. Shows partially filled with content have a yellow exclamation mark icon. During playout of the show, a green play icon will also be shown in the top bar.

![](img/Screenshot488-Add_remove_content.png)

The **Schedule Tracks** action opens a window with the name of the show. Like when using the **Now Playing** page, you can search for content items and add them to the show schedule on the right side of the page. Refer to the *Now Playing* chapter for details.

When your show has all the required content, click the **OK** button in the bottom right corner to close the window. Back in the **Calendar**, click the show and select **View** from the pop-up menu to view a list of content now included in the show.

![](img/Screenshot489-Show_Content.png)

The **Contents of Show** window is a read-only interface featuring an orange bar which indicates how much media has been added to the show. Click the **OK** button in the bottom right corner, or the white **x** icon in the top right corner, to close the window.

![](img/Screenshot353-Contents_of_show.png)

### Removing content from a show

To remove an individual item from a show, click on the show in the **Calendar**, and select **Schedule Tracks** from the pop-up menu. In the window which opens, click any item you wish to remove from the show, then click **Delete** on the pop-up menu, or check the box in the item's row then click the **Remove** icon at the top of the table. To remove all files and playlists from a show, click on the show in the **Calendar**, and select **Clear Show** from the pop-up menu. 

### Deleting an upcoming show

To delete an upcoming instance of a repeating show, click on the show in the **Calendar**, and select **Delete**, then **Delete Instance** from the pop-up menu. If you wish to delete all future instances of a repeating show, select **Delete Instance and All Following** from the pop-up menu.

![](img/Screenshot490-Delete_this_instance.png)

You cannot delete or remove content from shows that have already played out. These shows have only one option on the pop-up menu, which is **View**.

### Cancelling playout

If you wish to cancel playout of a show while it is running, click on the show in the **Calendar** and select **Cancel Show** from the pop-up menu. This action cannot be undone.