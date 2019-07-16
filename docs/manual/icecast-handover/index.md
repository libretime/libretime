In a typical radio station configuration, the live output from the broadcast studio and the scheduled output from LibreTime are mixed together before being sent further along the broadcast chain, to a transmitter or streaming media server on the Internet. (This may not be the case if your LibreTime server is remote from the studio, and you are using the **Show Source Mount Point** or **Master Source Mount Point** to mix live and scheduled content. See the *Stream Settings* chapter for details).

If your **Icecast** server is hosted in a remote data centre, you may not have the option to handover the streaming media source manually, because you have no physical access to connect a broadcast mixer to the server. Disconnecting the stream and beginning another is less than ideal, because the audience's media players will also be disconnected when that happens.

The **Icecast** server has a *fallback-mount* feature which can be used to move clients (media players used by listeners or viewers) from one source to another, as new sources become available. This makes it possible to handover from LibreTime output to a show from another source, and handover to LibreTime again once the other show has ended.

To enable fallback mounts, edit the main Icecast configuration file to define the mount points you will use, and the relationship between them.

    sudo nano /etc/icecast2/icecast.xml

The example *<mount>* section provided in the *icecast.xml* file is commented out by default. Before or after the commented section, add three mount point definitions. The default mount point used by LibreTime is */airtime\_128* which is shown in the */etc/airtime/liquidsoap.cfg* file. You must also define a mount point for the live source (called */live.ogg* in this example) and a mount point for the public to connect to (called */stream.ogg* in this example).

       <mount>
            <mount-name>/airtime_128</mount-name>
            <hidden>0</hidden>
       </mount>

       <mount>
            <mount-name>/live.ogg</mount-name>
            <fallback-mount>/airtime_128</fallback-mount>
            <fallback-override>1</fallback-override>
            <hidden>0</hidden>
       </mount>

       <mount>
            <mount-name>/stream.ogg</mount-name>
            <fallback-mount>/live.ogg</fallback-mount>
            <fallback-override>1</fallback-override>
            <hidden>0</hidden>
       </mount>

These mount point definitions mean that a client connecting to a URL such as *http://icecast.example.com:8000/stream.ogg* will first fall back to the */live.ogg* mount point if it is available. If not, the client will fall back in turn to the */airtime\_128* mount point for LibreTime playout.

Setting the value of *<fallback-override>* to 1 (enabled) means that when the */live.ogg* mount point becomes available again, the client will be re-connected to it.  If you wish to hide the */airtime\_128* and */live.ogg* mount points from the public Icecast web interface, set the value of *<hidden>* in each of these definitions to 1.

Source configuration
--------------------

Connect the other source to the Icecast server with the same parameters defined in the */etc/airtime/liquidsoap.cfg* file, except for the mount point. This should one of the mount points you have defined in the */etc/icecast2/icecast.xml* file, such as */live.ogg* in the example above.

To configure **Mixxx** for streaming to Icecast, click *Options*, *Preferences*, then *Live Broadcasting*. For server *Type*, select the default of *Icecast 2* when streaming to Debian or Ubuntu servers, as this is the current version of Icecast supplied with those GNU/Linux distributions.

![](static/Screenshot123-Mixxx_Preferences.png) 

By default, Icecast streams are buffered to guard against network problems, which causes latency for remote listeners. When monitoring the stream from a remote location, you may have to begin the live stream a few seconds before the previous stream ends to enable a smooth transition.
