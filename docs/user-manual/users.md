---
title: Managing users
---

:::danger

It's strongly recommended not to use the default `admin` account in production, especially if your LibreTime server is accessible from the internet.

:::

## User account types

To add further user accounts to the system, one for each of your station staff that need access to Airtime, click the **New User** button with the plus icon. Enter a user name, password and contact details, and then select the **User Type** from the drop down menu, which can be _Admin_, _Program Manager_, _DJ_, or _Guest_.

### Guests

- Can view shows and the playout log on the Calendar and Dashboard, respectively
- Listen to the output stream without leaving the interface

### DJs

- Everything Guests can do, plus
- Upload media (music, PSAs, underwriting, shows, etc.) to their own library (DJs can't view other libraries)
- Edit metadata, delete, and schedule media in their own library to shows they're assigned to
- Preview uploaded media _without_ affecting the live playout
- Create Playlists, Smart Blocks, and connect Podcasts and Webstreams to LibreTime

### Program managers

- Everything DJs can do, plus
- Manage other users' libraries in addition to their own
- Create, edit, and delete color-coded shows on the Calendar and assign them to DJs (if needed)
- Shows can be scheduled to repeat, with the option of linking content between the shows (helpful if a DJ livestreams in each week)
- View listener statistics
- Export playout logs for analysis or reporting for music royalties

### Administrators

- Everything Program Managers can do, plus
- Manage all user accounts, including the ability to reset passwords
- Configure Track Types for easy sorting of uploaded content
- Change system settings

## Editing or deleting user accounts

To edit a user account, click on that user's row in the table, change the user's details in the box on the
right side, and then click the **Save** button. To remove a user account, click the small **x** icon to the right
side of its row in the table. You can't delete your own user account, and usernames can't be changed once created.

![](./users-user-edit.png)

Users can update their own password, and their contact, language and time zone details, by clicking their username on the
right side of the main menu bar, next to the **Logout** link.
