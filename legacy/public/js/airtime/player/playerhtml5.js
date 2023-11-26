function getRandomIdPlayer(max) {
  return "playerHtml5Libretime_" + Math.floor(Math.random() * Math.floor(max));
}

function playerhtml5_insert(settings) {
  atp = "";
  if (settings.autoplay == true) atp = "autoplay";
  if (settings.forceHTTPS == true && settings.url.indexOf("https") == -1)
    settings.url = settings.url.replace(/http/g, "https");
  if (
    settings.replacePort != "" &&
    settings.replacePort != false &&
    settings.replacePort != "false"
  ) {
    if (settings.replacePortTo != "")
      settings.replacePortTo = ":" + settings.replacePortTo;
    settings.url = settings.url.replace(
      ":" + settings.replacePort,
      settings.replacePortTo,
    );
  }
  if (settings.codec == "mp3") settings.codec = "mpeg";
  if (settings.codec == "ogg") {settings.codec = "x-mpegURL" ;}
  document.getElementById("html5player_skin").innerHTML +=
    '<div id="div_' +
    settings.elementId +
    '" style="" ><video autoplay preload controls id="' +
    settings.elementId +
    '" src="' +
    settings.url +
    '" ' +
    atp +
    ' type="application/' +
    settings.codec +
    '" class="video-js">' +
    "Your browser doesn't support HTML5 the <code>audio</code> tag." +
    "</video></div>";
}
