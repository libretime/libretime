---
layout: article
title: Troubleshooting
category: admin
---

Is something not working for your Libretime installation? Here's a quick guide to help you 
troubleshoot most issues you'll run into.

## 1. Let's check the basics

Is your server on? (We hate to ask.) Is it connected to the internet? Is it connected to your
broadcast console or mixer if being used for soundcard output? If you're using a cloud host, 
does your cloud provider's status page indicate any system outages?

Once you know your physical (or virtual) system is functional, was a show scheduled for the 
current time with tracks or an autoplaylist scheduled?

## 2. Are all services working?

If you can log in to Libretime, go to **Settings** > **Status** to see the service indicators.
A fully working server should have green checkmarks next to all services.

![](/img/Screenshot521-System_status_240.png)

If one of the services isn't working, text will display with a terminal command to restart the service 
or get status information for a particular service. For example (for Ubuntu 18.04), the following 
commands would restart or check the status of Libretime's Liquidsoap instance, respectively.

```
sudo systemctl restart libretime-liquidsoap
sudo systemctl status libretime-liquidsoap
```

If the service isn't wanting to restart, look at its status for clues as to why it stopped working.

> If you find yourself constantly needing to restart a service, there's a chance it was never set to autostart on system boot. Use `sudo systemctl enable servicename` to fix this problem.

## 3. Basic troubleshooting

If you have one of these issues, please try to resolve it with the instructions below before moving on in the 
troubleshooting checklist.

- **File not importing successfully?** Libretime has been known to work with MP3 and WAV files, encoded using 41,100 Hz. Variable Bit Rate (VBR) files are currently hit or miss with the importer. Please convert your file to an MP3 or WAV at 41,100 Hz. and try uploading again.
- **Podcast hosted by Anchor.fm not importing?** There is no known work-around at this time. Ask your producers to provide their show files manually or check with the show's distributer.
- **Tracks won't publish?** We know the Publish screen is broken and we're working on it. A potential work-around is to use an external podcast host like [Anchor.fm](https://www.anchor.fm) or [Blubrry](https://blubrry.com/).
- **Can't hear any sound coming from your soundcard (for analog audio output)?** If you are using ALSA as your audio driver, use `alsamixer` to see the current volume your system is set to. If stil nothing, go to **Settings** > **Streams** and make sure **Hardware Audio Output** is checked. If you need to play a tone to help you troubleshoot, you can use `speaker-test` (does not come installed with Libretime).

## 4. Read the docs

Our main documentation listing is [here](/docs) and can be searched [here](/search).

## 5. Reach out to the developers

Libretime is still in active development, meaning bugs and issues are expected to pop up every so often. 
See if an issue is still open by looking at our [Issues page](https://github.com/LibreTime/libretime/issues). 
If you don't get the help you need, please [open an issue](https://github.com/LibreTime/libretime/issues/new/choose) 
so we can take a look at it.
