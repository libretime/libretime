var _playlist_jplayer;
var _idToPostionLookUp;
var URL_BAR_HEIGHT = 32;

/**
 *When the page loads the ready function will get all the data it can from the hidden span elements
 *and call one of three functions depending on weather the window was open to play an audio file,
 *or a playlist or a show.
 */
$(document).ready(function () {
  $.jPlayer.timeFormat.showHour = true;

  var audioUri = $(".audioUri").text();
  var audioMime = $(".audioMime").text();
  var playlistID = $(".playlistID").text();
  var playlistIndex = $(".playlistIndex").text();
  var showID = $(".showID").text();
  var showIndex = $(".showIndex").text();
  var blockId = $(".blockId").text();
  var blockIndex = $(".blockIndex").text();

  _playlist_jplayer = new jPlayerPlaylist(
    {
      jPlayer: "#jquery_jplayer_1",
      cssSelectorAncestor: "#jp_container_1",
    },
    [], //array of songs will be filled with below's json call
    {
      swfPath: baseUrl + "js/jplayer",
      supplied: "oga, mp3, m4v, m4a, wav, flac",
      size: {
        width: "0px",
        height: "0px",
        cssClass: "jp-video-270p",
      },
      playlistOptions: {
        autoPlay: false,
        loopOnPrevious: false,
        shuffleOnLoop: true,
        enableRemoveControls: false,
        displayTime: 0,
        addTime: 0,
        removeTime: 0,
        shuffleTime: 0,
      },
      ready: function () {
        if (playlistID != "" && playlistID !== "") {
          playAllPlaylist(playlistID, playlistIndex);
        } else if (audioUri != "") {
          playOne(audioUri, audioMime);
        } else if (showID != "") {
          playAllShow(showID, showIndex);
        } else if (blockId != "" && blockIndex != "") {
          playBlock(blockId, blockIndex);
        }
      },
    },
  );

  $("#jp_container_1").on("mouseenter", "ul.jp-controls li", function (ev) {
    $(this).addClass("ui-state-hover");
  });

  $("#jp_container_1").on("mouseleave", "ul.jp-controls li", function (ev) {
    $(this).removeClass("ui-state-hover");
  });
});

/**
 * Sets up the jPlayerPlaylist to play.
 *  - Get the playlist info based on the playlistID give.
 *  - Update the playlistIndex to the position in the pllist to start playing.
 *  - Select the element played from and start playing. If playlist is null then start at index 0.
 **/
function playAllPlaylist(p_playlistID, p_playlistIndex) {
  var viewsPlaylistID = $(".playlistID").text();

  if (_idToPostionLookUp !== undefined && viewsPlaylistID == p_playlistID) {
    play(p_playlistIndex);
  } else {
    buildplaylist(
      baseUrl + "audiopreview/get-playlist/playlistID/" + p_playlistID,
      p_playlistIndex,
    );
  }
}

function playBlock(p_blockId, p_blockIndex) {
  var viewsBlockId = $(".blockId").text();

  if (_idToPostionLookUp !== undefined && viewsBlockId == p_blockId) {
    play(p_blockIndex);
  } else {
    buildplaylist(
      baseUrl + "audiopreview/get-block/blockId/" + p_blockId,
      p_blockIndex,
    );
  }
}

/**
 * Sets up the show to play.
 *  checks with the show id given to the show id on the page/view
 *      if the show id and the page or views show id are the same it means the user clicked another
 *          file in the same show, so don't refresh the show content tust play the track from the preloaded show.
 *      if the the ids are different they we'll need to get the show's context so create the uri
 *      and call the controller.
 **/
function playAllShow(p_showID, p_index) {
  var viewsShowID = $(".showID").text();
  if (_idToPostionLookUp !== undefined && viewsShowID == p_showID) {
    play(p_index);
  } else {
    buildplaylist(
      baseUrl + "audiopreview/get-show/showID/" + p_showID,
      p_index,
    );
  }
}

/**
 * This function will call the AudiopreviewController to get the contents of
 * either a show or playlist Looping throught the returned contents and
 * creating media for each track.
 *
 * Then trigger the jplayer to play the list.
 */
function buildplaylist(p_url, p_playIndex) {
  _idToPostionLookUp = Array();
  $.getJSON(p_url, function (data) {
    // get the JSON array produced by my PHP
    var myPlaylist = new Array();
    var media;
    var index;
    var total = 0;
    var skipped = 0;

    for (index in data) {
      if (data[index]["type"] == 0) {
        if (data[index]["element_mp3"] != undefined) {
          media = {
            title: data[index]["element_title"],
            artist: data[index]["element_artist"],
            mp3: data[index]["uri"],
          };
        } else if (data[index]["element_oga"] != undefined) {
          media = {
            title: data[index]["element_title"],
            artist: data[index]["element_artist"],
            oga: data[index]["uri"],
          };
        } else if (data[index]["element_m4a"] != undefined) {
          media = {
            title: data[index]["element_title"],
            artist: data[index]["element_artist"],
            m4a: data[index]["uri"],
          };
        } else if (data[index]["element_wav"] != undefined) {
          media = {
            title: data[index]["element_title"],
            artist: data[index]["element_artist"],
            wav: data[index]["uri"],
          };
        } else if (data[index]["element_flac"] != undefined) {
          media = {
            title: data[index]["element_title"],
            artist: data[index]["element_artist"],
            flac: data[index]["uri"],
          };
        } else {
          // skip this track since it's not supported
          console.log("continue");
          skipped++;
          continue;
        }
      } else if (data[index]["type"] == 1) {
        var mime = data[index]["mime"];
        if (mime.search(/mp3/i) > 0 || mime.search(/mpeg/i) > 0) {
          key = "mp3";
        } else if (mime.search(/og(g|a)/i) > 0 || mime.search(/vorbis/i) > 0) {
          key = "oga";
        } else if (mime.search(/mp4/i) > 0) {
          key = "m4a";
        } else if (mime.search(/wav/i) > 0) {
          key = "wav";
        } else if (mime.search(/flac/i) > 0) {
          key = "flac";
        }

        if (key) {
          media = {
            title: data[index]["element_title"],
            artist: data[index]["element_artist"],
          };
          media[key] = data[index]["uri"];
        }
      }
      if (media && isAudioSupported(data[index]["mime"])) {
        // javascript doesn't support associative array with numeric key
        // so we need to remove the gap if we skip any of tracks due to
        // browser incompatibility.
        myPlaylist[index - skipped] = media;
      }
      // we should create a map according to the new position in the
      // player itself total is the index on the player
      _idToPostionLookUp[data[index]["element_id"]] = total;
      total++;
    }
    _playlist_jplayer.setPlaylist(myPlaylist);
    _playlist_jplayer.option("autoPlay", true);
    play(p_playIndex);

    window.scrollbars = false;

    var container = $("#jp_container_1");
    // Add 2px to account for borders
    window.resizeTo(
      container.width() + 2,
      container.height() + URL_BAR_HEIGHT + 2,
    );
  });
}

/**
 *Function simply plays the given index, for playlists index can be different so need to look up the
 *right index.
 */
function play(p_playlistIndex) {
  playlistIndex = _idToPostionLookUp[p_playlistIndex];
  if (playlistIndex == undefined) {
    playlistIndex = 0;
  }
  //_playlist_jplayer.select(playlistIndex);
  _playlist_jplayer.play(playlistIndex);
}

/**
 * Playing one audio track occurs from the library. This function will create the media, setup
 * jplayer and play the track.
 */
function playOne(uri, mime) {
  var playlist = new Array();

  var media = null;
  var key = null;
  if (mime.search(/mp3/i) > 0 || mime.search(/mpeg/i) > 0) {
    key = "mp3";
  } else if (mime.search(/og(g|a)/i) > 0 || mime.search(/vorbis/i) > 0) {
    key = "oga";
  } else if (mime.search(/mp4/i) > 0) {
    key = "m4a";
  } else if (mime.search(/wav/i) > 0) {
    key = "wav";
  } else if (mime.search(/flac/i) > 0) {
    key = "flac";
  }

  if (key) {
    media = {
      title:
        $(".audioFileTitle").text() != "null"
          ? $(".audioFileTitle").text()
          : "",
      artist:
        $(".audioFileArtist").text() != "null"
          ? $(".audioFileArtist").text()
          : "",
    };
    media[key] = uri;
  }

  if (media) {
    _playlist_jplayer.option("autoPlay", true);
    playlist[0] = media;
    _playlist_jplayer.setPlaylist(playlist);
    _playlist_jplayer.play(0);
  }

  var container = $("#jp_container_1");
  // Add 2px to account for borders
  window.resizeTo(
    container.width() + 2,
    container.height() + URL_BAR_HEIGHT + 2,
  );
}
