/**
 * Get the tooltip message to be displayed
 */
function getContent() {
  var link = getLatestLink();
  var hasPatch = getHasPatch();
  var hasMinor = getHasMinor();
  var hasMajor = getHasMajor();
  var hasMultiMajor = getHasMultiMajor();
  var isPreRelease = getIsPreRelease();

  var msg = "";
  // See file legacy/application/views/helpers/VersionNotify.php for more info
  if (isUpToDate()) {
    msg = $.i18n._("You are running the latest version");
  } else {
    msg = $.i18n._("New version available: ") + link + "<ul>";
    if (isPreRelease) {
      msg +=
        "<li>" +
        $.i18n._("You have a pre-release version of LibreTime intalled.");
    }
    if (hasPatch) {
      msg +=
        "<li>" +
        $.i18n._(
          "A patch update for your LibreTime installation is available.",
        );
    }
    if (hasMinor) {
      msg +=
        "<li>" +
        $.i18n._(
          "A feature update for your LibreTime installation is available.",
        );
    }
    if (hasMajor && !hasMultiMajor) {
      msg +=
        "<li>" +
        $.i18n._(
          "A major update for your LibreTime installation is available.",
        );
    }
    if (hasMultiMajor) {
      msg +=
        "<li>" +
        $.i18n._(
          "Multiple major updates for LibreTime installation are available. Please upgrade as soon as possible.",
        );
    }
    msg += "</ul>";
  }

  return msg;
}

/**
 * Get if patch is available
 */
function getHasPatch() {
  return versionNotifyInfo.hasPatch;
}

/**
 * Get if minor update is available
 */
function getHasMinor() {
  return versionNotifyInfo.hasMinor;
}

/**
 * Get if major update is available
 */
function getHasMajor() {
  return versionNotifyInfo.hasMajor;
}

/**
 * Get if multiple major updates are available
 */
function getHasMultiMajor() {
  return versionNotifyInfo.hasMultiMajor;
}

/**
 * Get if pre-release was installed
 */
function getIsPreRelease() {
  return versionNotifyInfo.isPreRelease;
}

/**
 * Get the current version
 */
function getCurrentVersion() {
  return versionNotifyInfo.current;
}

/**
 * Get the latest version
 */
function getLatestVersion() {
  return versionNotifyInfo.latest;
}

/**
 * Returns the download link to latest release in HTML
 */
function getLatestLink() {
  return (
    "<a href='' onclick='openLatestLink();'>" + getLatestVersion() + "</a>"
  );
}

/**
 * Returns true if current version is up to date
 */
function isUpToDate() {
  return !getHasPatch() && !getHasMinor() && !getHasMajor();
}

/**
 * Opens the link in a new window
 */
function openLatestLink() {
  window.open(versionNotifyInfo.link);
}

/**
 * Sets up the tooltip for version notification
 */
function setupVersionQtip() {
  var qtipElem = $("#version-icon");
  if (qtipElem.length > 0) {
    qtipElem.qtip({
      id: "version",
      content: {
        text: getContent(),
        title: {
          text: getCurrentVersion(),
          button: isUpToDate() ? false : true,
        },
      },
      hide: {
        event: isUpToDate() ? "mouseleave" : "unfocus",
      },
      position: {
        my: "top right",
        at: "bottom left",
      },
      style: {
        border: {
          width: 0,
          radius: 4,
        },
        classes: "ui-tooltip-dark ui-tooltip-rounded",
      },
    });
  }
}

$(document).ready(function () {
  if ($("#version-icon").length > 0) {
    setupVersionQtip();
  }
});
