# How to Setup Genre based AutoDJ
* YouTube video

[![How to Setup Genre based AutoDJ](http://img.youtube.com/vi/XNVIDnczrOk/0.jpg)](https://www.youtube-nocookie.com/embed/XNVIDnczrOk "How to Setup Genre based AutoDJ")

## Text-based Step-by-step

In this tutorial we will focus on how to build a traditional auto DJ system
where we use a feature called smart blocks and auto loading playlists to play
random music tracks of a certain genre during a show. This tutorial is
primarily focused on program managers but users who are DJs can also benefit
from smart blocks but will only be able to manually schedule them.

So for the purposes of this tutorial we are going to assume that you
have uploaded a number of files of music with the genre metadata all
matching a specific genre, for instance, Alternative. This can be done
ahead of time, but LibreTime has no way of automatically grouping sub
genres and so it is important that you spend some time curating the
track metadata before you upload it to LibreTime as there is not
currently a way to bulk edit tracks from inside LibreTime. Also once
tracks are uploaded the metadata of the track itself isn’t changed but
just how it is represented in the LibreTime database. This might change
in the future but would only affect you if you were downloading tracks
and uploading them to another instance and hoped that the changes you
made were saved.

So we can bulk upload a number of creative commons licensed tracks that
have their genre all set to specific genres as one way of getting tracks
if you don’t have a mp3 collection of your own to draw from. See links
below for some spots where you can procure tracks where the creators
gave permission for everyone to redistribute play and share them.

One the tracks are uploaded to the system and imported we can now create
a smartblock that will select the tracks that match a certain genre. For
this we will use Genre contains Jazz – this will match any track that
has genre anywhere in its genre so ‘big band jazz’ and ‘swing jazz’
would match as well as ‘acid jazz’. Any subgenre that doesn’t include
jazz explicitly such as ‘be bop’ would need to be added as a new
modifier.

For the purposes of this smart block we want to select 4 random items.
We also want to avoid really long tracks that are longer than 10
minutes. So we will add the modifier Length is less than 00:10:00

and now this smart block will pull 4 smart blocks. Be sure to preview it
to make sure that you have some matches in your library. This will also
save it.

Now we are going to create a new smart block that plays one promo or
station ID.

Click smartblock and new and then type in the name promo OR ID and then
change the criteria Genre to is promo and now click new modifier and
then is TOTHID.

And then change limit to 1 items.

Now we are going to create a playlist that contains these two smart
blocks.

Click new and then type Jazz with promos

and then click smartblocks and add the Jazz Songs followed by the Promo
or ID smart block.

Now this playlist can be dragged into a schedule show to add 3 random
jazz songs followed by a promo. You can manually repeat the selection in
the playlist to create a long auto DJ playlist. For instance lets add 3
more Jazz Songs and then add 3 Jazz Songs again followed by a Promo or
ID. Now lets save this playlist and go and add it to a scheduled show.

This is a quick and easy way for you to manually schedule a large chunk
of time. You can also use this as an autoloading playlist.

Lets assign this to a new 3 hour show. Called lots of Jazz. Now lets go
under Autoloading Playlist and enable that and in this spot we will
check repeat until full. This will mean that the system will keep
scheduling this playlist until the show is completely full. The only
problem here is that at the end of the show it is almost assured that a
track will be cut off. If you are a web station and you just want to
schedule music for long lengths of time you can schedule shows that are
up to 24 hours long.

On the other hand if you are an FCC licensed station you are required to
have top of the hour IDs as close as possible to the top of the hour. In
this case the random selection of tracks might not suffice for
compliance.

If you have a station ID playlist smartblock in your intro playlist (see
previous tutorial linked below) and you have your tracks broken up
hourly this shouldn’t be an issue but it still might result in tracks
being cut of in the middle.

So the best option currently is to change the music tracks to fill the
remaining show from the previous selection of 3 items. And then be sure
to add a promos or musical sound bridges at the end that are also time
remaining but allow overfill.

It is possible that we could improve the way the autoloading playlists
work in the future, please check out LibreTime.org for the latest
release notes and feel free to ask any questions at our forum at
[https://discourse.LibreTime.org](https://discourse.LibreTime.org/)

Thanks for tuning in to another LibreTime tutorial. Our next tutorial
will show you how to use autoloading playlists to add show specific
underwriting or advertisements.
