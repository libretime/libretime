---
title: Quick Install
sidebar: installer
---

Up and running in 10 minutes!
----------------------------

LibreTime is quick and easy to install and get running. Follow this guide to go from zero
to full internet radio station in 10 minutes!

## Minimum System Requirements

| On-Premises Install  (FM + Internet Radio) | Cloud Install (Internet Radio Only) |
|---------------------|---------------|
| 1 Ghz Processor| 1vCPU |
| 2 GB RAM | 2 GB RAM |
| Wired ethernet connection | 2 TB of data transfer/month |
| Ubuntu Server 18.04 LTS | Ubuntu Server 18.04 LTS |
| git, ntp | git, ntp |

[DigitalOcean](https://www.digitalocean.com/pricing/#Compute) and [Linode](https://www.linode.com/pricing/#row--compute)
 have similar plans that meet Cloud Install requirements. Both plans cost $10/month.

## Preparing the server

Configure the server to have a static IP address by modifying the Netplan configuration.
If you're using a cloud VM, you likely already have a static IP address. Check with your provider to confirm this.

```
cd /etc/netplan && ls  # find the netplan filename
sudo nano ##-netcfg.yaml
```

If the Netplan configuration is empty, fill in the file with the example below. Otherwise,
input the IP address reserved for the server in `xxx.xxx.xxx.xxx/yy` format, the gateway (the IP address
of your router), and the DNS nameserver. If you don't have a nameserver on your network,
feel free to use Cloudflare's: `1.1.1.1` and `1.0.0.1`.

```
network:
  version: 2
  renderer: networkd
  ethernets:
    enp3s0:
      addresses: [192.168.88.8/24]
      gateway4: 192.168.88.1
      nameservers:
        addresses: 192.168.88.1
```

Next, configure Ubuntu's firewall by running:

```
sudo ufw enable
sudo ufw allow 80/tcp
sudo ufw allow 8000/tcp
```

If you plan on broadcasting live with Libretime, also unblock ports 8001 and 8002.

```
sudo ufw enable 8001/tcp
sudo ufw enable 8002/tcp
```

## Installing LibreTime

Installing LibreTime consists of running these commands in the terminal:

```
git clone https://github.com/LibreTime/libretime.git
cd libretime
sudo ./install -fiap
```

After the install is completed, head to the IP address of the server LibreTime was just installed on
to complete the welcome wizard. While not strictly necessary, it is recommended that you change the passwords prompted in the welcome wizard if you intend on accessing the server from the Internet. The welcome wizard will
walk you through the rest of the installation process.

## Scheduling your first show

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
check the **Repeats?** box and fill out the repeat information. Click on the grey **+ Add this show** button at the top
of the pane to add your show to the calendar.

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

Congratulations! You've successfully set up LibreTime!

## Next Steps

Now that the install is complete, use these guides to help you continue to set up your LibreTime server

- [Host Configuration](host-configuration)
- [Setting the Server Time](setting-the-server-time)
- [Configuring Track Types](track-types)
- [Setting up SSL](ssl-config)
