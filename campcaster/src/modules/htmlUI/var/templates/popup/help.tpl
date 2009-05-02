<html>

<head>
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<title>Campcaster Help</title>

<style>
{literal}
body {
    font-family: Arial, Verdana, Helvetica, sans-serif;
    font-size: 12px;
    color: #2C2C2C;
    margin: 0px;
    background: #fff;
}

.copyright {
    font-size: 9px;
}


img {
    border: none;
}

.container {
    width: 100%;
}


/* #################################### CONTENTAREA ############################### */


.content {
    margin: 19px;
    float: left;
    width: 90%;
}

.container_elements {
    border: 1px solid #cfcfcf;
    padding: 6px;
    margin-bottom: 21px;
}

.container_elements h1
    {
    font-size: 18px;
    margin: 20px  0 0 0;
    color: #666;
    }
.container_elements h2
    {
    font-size: 16px;
    margin: 20px 0 0 0;
    color: #666;
    }
.container_elements h3
    {
    font-size: 14px;
    margin: 10px 0 0 0;
    color: #666;
    }
.container_elements h4
    {
    font-size: 12px;
    margin: 5px 0 0 0;
    color: #666;
    }
DIV.blockquote
{
    padding-left: 20px;
}
{/literal}
</style>

</head>

<body>

<div class="container">
<div class="content">
<div class="container_elements">
<img src="img/logo_livesupport.png" border="0" />

<h1>Quick Start - {$UI_VERSION_FULLNAME}</h1>
<div class="copyright">
    {$UI_VERSION} &copy;{$UI_COPYRIGHT_DATE}
    <a href="http://www.mdlf.org" target="_blank">MDLF</a>
    - maintained and distributed under GNU/GPL by
    <a href="http://www.campware.org" target="_blank">CAMPWARE</a>
</div>


	<H2>Navigation</H2>
	<H3>Context menus</H3>
	<P>The Campcaster web client uses context menus extensively
	throughout the program. For example, by clicking on the ScratchPad,
	you can perform a number of operations on your file, including
	adding it to a playlist and removing it from the ScratchPad. &nbsp;
	</P>
	<H3>ScratchPad</H3>
	<P>The ScratchPad provides you with a list of all the files (both
	sound files and playlists) you have worked with recently. This
	serves as both a &quot;history&quot; as well as a &quot;clipboard&quot;
	for working with files between windows. You can listen to a sound
	file from the ScratchPad by right-clicking 'Listen', which will send
	the sound file to you for playback. &nbsp;
	</P>
	<H3>Playlists and ScratchPad</H3>
	<P>Playlists appear in the ScratchPad differently than sound files.
	A playlist that is currently open by you will appear in <B>bold</B>.
	You can 'close' a playlist directly from the ScratchPad (see the
	Playlist section for more on opened/closed playlists). A playlist
	that is opened by somebody else in marked as locked by small icon,
	and this which isn't opened will appear in normal text.
	</P>
	<H2>Playlists</H2>
	<P>Playlists are at the core of how Campcaster works. You add sound
	files to a playlist, and then schedule that playlist to be played at
	a date and time of your choosing. Playlists are edited in the
	Playlist Editor. You can include an unlimited number of playlists
	inside playlists; for example, if you have a one-hour show and want
	to have two commercial breaks, each made up of their own playlists.</P>
	<H3>Adding a file from the ScratchPad to the open playlist</H3>
	<P>You can add a file (either a sound file or a playlist file) from
	the ScratchPad to an playlist you opened before by right-clicking it
	in the ScratchPad and selecting &quot;Add to playlist&quot;. This
	will add it to the end of the playlist. &nbsp;
	</P>
	<H3>Changing file order in the playlist</H3>
	<P>You can change a file's order in the playlist by clicking on the
	up or down arrows on the right.<BR>As of Campcaster 1.0.1, you can
	drag and drop playlist items to change their order. This can be done
	by  &quot;;Re-arrange playlist&quot;; in the playlist editor. &nbsp;
		</P>
	<H3>Change transition</H3>
	<P>Transitions between sound files are set to zero milliseconds by
	default. You can change this by moving your mouse over the &quot;Fade&quot;
	line in the Playlist window and selecting &quot;Change Transition&quot;
	for a single transition, or by using the checkboxes in combination
	with the &quot;Change fades&rdquo; button. In this popup window, you
	can set the length of the transition or fade in/out. &nbsp;
	</P>
	<H3>Opening and closing a playlist</H3>
	<P>Playlists can have 'opened' status. While a playlist is 'opened',
	only you can edit the playlist, and other people are prevented from
	making changes and scheduling. A closed playlist is one that isn't
	currently edited by someone, so it can be used for broadcast and can
	be added to the scheduler. An 'opened' playlist can continue to be
	edited. Playlists can be opened for editing by right-clicking &quot;Edit&quot;
	in the ScratchPad menu. This will take you to the playlist editor.
	Just after a playlist have closed (and is on the ScratchPad), it
	will appear in a list of playlists available for scheduling.
	</P>
	<H2>Schedule</H2>
	<P>Once you've created a playlist, schedule it for playback using
	the Schedule window. (Please remember that only closed playlists can
	be added to the schedule.)</P>
	<H3>Adding a playlist to the schedule</H3>
	<P>You can add it to the schedule by opening the Schedule window and
	choosing a schedule view that suits you. Then you can add your
	playlist to the schedule by right-clicking and choosing &quot;Insert
	playlist here&quot;. A popup window will then appear allowing you to
	select the exact start time for your playlist, as well as a
	pull-down menu listing available closed playlists. &nbsp;
	</P>
	<H3>Removing a playlist from the schedule</H3>
	<P>You can remove a playlist from the schedule by right clicking on
	its time [soon to be its title] and choosing &quot;Remove Playlist&quot;.
	This does not delete the playlist from the database, however. It
	will remain in the system.
	</P>
	<H2>Files</H2>
	<P>Sound files are added to the Campcaster system in two steps.</P>
	<H3>Uploading and analyzing</H3>
	<P>The first step is to select the sound file you want to upload
	into the Campcaster system. This is handled in the Upload -&gt; New
	file menu. You select the file to upload by clicking on the &quot;Browse&quot;
	button, choosing your sound file, then clicking &quot;Submit&quot;.
	Campcaster automatically analyzes the sound file for any
	information that may be stored in ID3 tags.<BR>See the
	&quot;Troubleshooting&quot; section if you cannot upload files.
	</P>
	<H3>Describing your file using metadata</H3>
	<P>The second step allows you to edit the information used to
	describe the file (called &quot;metadata&quot;) or to add your own.
	If your sound file contains music, you have a number of options for
	entering metadata under the &quot;Music&quot; tab. If your sound
	file is a news report or other talk, choose the &quot;Talk&quot;
	tab. The Talk tab allows reporters to include the time and date
	their report is about, the organizations covered in the report, and
	the location the report takes place in. Good metadata will help you
	and other colleagues to later find and use the material you upload.
	It's in your best interest to be as thorough as possible in
	inputting this metadata.
	</P>
	<H2>Browse and Search</H2>
	<P>The browse and search functions are designed to be both easy to
	use and powerful, helping you to search not only file titles but
	also other metadata. Both browse and search let you search for both
	sound files as well as playlists.
	</P>
	<P>Search works more or less like a regular search engine. You can
	type in a word to be searched, and the results will appear below the
	search input window. The difference is that you are searching the
	metadata that you and your colleagues input to describe the sound
	files you put in. Good metadata will mean better search results.</P>
	<H3>Using the browse function</H3>
	<P>The browse function is a powerful feature that lets you browse
	all files according to general criteria you specify. The first
	column lets you choose a category to browse from, such as &quot;;Genre&quot;;.
	Under that category, you can choose one of the options that appears
	in that category, which refines the number of files displayed. The
	second and third columns work in the same ways, and let you continue
	to refine your browsing.
	</P>
	<H3>Using multiple search terms</H3>
	<P>Let's say you want to finding all files created by &quot;;John
	Doe&quot;; in the year 2005. Do the following:
	</P>
	<UL>
		<LI><P STYLE="margin-bottom: 0in">Select first the field to search
		and input its value. In this case it would be to pull down the
		&quot;;Creator&quot;; field and type in &quot;;John Doe&quot;; in
		the window.
		</P>
		<LI><P STYLE="margin-bottom: 0in">Then press the &quot;;Add One
		Row&quot;; button and an additional search row appears. Select the
		&quot;;Year&quot;; field and type in 2005 in the window.
		</P>
		<LI><P>Press &quot;;Submit&quot;; and your results - if any - will
		appear below.
		</P>
	</UL>
	<H3>Working with the files you find</H3>
	<P>You can add the files you find - in both browse and search -
	either to your ScratchPad or directly to a new playlist.
	Right-clicking on a file in the results window gives you a number of
	options, such as:
	</P>
	<UL>
		<LI><P STYLE="margin-bottom: 0in">Add to ScratchPad
		</P>
		<LI><P STYLE="margin-bottom: 0in">Listen
		</P>
		<LI><P STYLE="margin-bottom: 0in">New playlist using file
		</P>
		<LI><P STYLE="margin-bottom: 0in">Edit
		</P>
		<LI><P>Delete
		</P>
	</UL>
	<H2>System Preferences</H2>
	<P>Your station's system administrator can change a number of
	system-wide settings in the System Preferences menu. In the System
	Preferences you can:</P>
	<UL>
		<LI><P STYLE="margin-bottom: 0in">Add your station's logo and
		frequency
		</P>
		<LI><P STYLE="margin-bottom: 0in">Set the number of items the
		ScratchPad displays</P>
		<LI><P STYLE="margin-bottom: 0in">Set the maximum file size that
		can be uploaded to the system
		</P>
		<LI><P>Administer users and groups for the system, including
		assigning their access privileges
		</P>
	</UL>

	<H2>Troubleshooting</H2>
	<H3>File Upload</H3>
	<P>If you encounter the message <i>The uploaded file is bigger than allowed in system settings.</i> most probably the file size is bigger than
	allowed in the PHP system settings of the machine you are running
	Campcaster on. Please increase the settings of
	&quot;upload_max_filesize&quot; and &quot;post_max_size&quot; in
	php.ini. For more information, consult the <A HREF="http://manuals.campware.org/campcaster" TARGET="_blank">Campcaster
	manual</A> chapter 4.12.
	</P>
	<H2>Where to go for help</H2>
	<P>Campcaster has <a href="http://www.campware.org/en/camp/campcaster_news/649/">mailing lists and forums for support-related questions</a>.</P>
    </P>

    {if tra('_Name of translator') !== '_Name of translator'}
	<H2>Credits</H2>
	<H3>Localization</H3>
	<P>The translation to <i>##_Native language name##</i> was done by <i>##_Name of translator##</i>.
    </P>
    {/if}

</div>
</div>
</div>

</body>

</html>
