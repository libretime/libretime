Promoting Your Station
----------------------

If you have an Icecast server, you can put a link to the Icecast status page (by default at port 8000) on your station's homepage,
to provide an overview of available streams. See the chapter *Interface customization* for tips on theming the
Icecast status page. You can also use Now Playing widgets (see the chapter *Exporting the schedule*) or HTML5 stream players (see the chapter *Stream player for your website*) to help grow your audience.

On an Icecast server, you can uncomment the `<directory>` section in the _/etc/icecast2/icecast.xml_ file to have
your station automatically listed on the Icecast directory website <http://dir.xiph.org> which could help you pick
up more listeners.

        <!-- Uncomment this if you want directory listings -->

        <directory>
            <yp-url-timeout>15</yp-url-timeout>
            <yp-url>http://dir.xiph.org/cgi-bin/yp-cgi</yp-url>
        </directory>

The Indymedia stream directory at <http://radio.indymedia.org/en/yp> links to grassroots independent radio projects around the world. You can add your station to their list with an additional *<directory>* section, as follows:

        <directory>
             <yp-url-timeout>15</yp-url-timeout>
             <yp-url>http://radio.indymedia.org/cgi-bin/yp-cgi</yp-url>
        </directory>

Another stream directory service is provided by the Liquidsoap Flows! site <http://flows.liquidsoap.fm/>. The following section can be added to the file */usr/lib/airtime/pypo/bin/liquidsoap\_scripts/ls\_script.liq* after *add\_skip\_command(s)* on line 174, for a stream named '*ourstation*':

    ourstation = register_flow(
      radio="Rock 'n Roll Radio",
      website="http://radio.example.com/",
      description="Canada's most rockin' radio!",
      genre="Rock",
      user="",
      password="",
      streams=[("ogg/128k","http://streaming.example.com/libretime_128")],
      ourstation)

For the time being, a stream can be registered on the Liquidsoap Flows! site with any username and password. Authenticated services may be offered in future.
