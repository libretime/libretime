/**
 * Get the tooltip message to be displayed
 */
function getContent() {
    var diff = getVersionDiff();
    var link = getLatestLink();
    
    var msg = "";
    // See file airtime_mvc/application/views/helpers/VersionNotify.php for more info
    if(isUpToDate()) {
        msg = $.i18n._("You are running the latest version");
    } else if (diff < 20) {  
        msg = $.i18n._("New version available: ") + link;
    } else if (diff < 30) {
        msg = $.i18n._("This version will soon be obsolete.")+"<br/>"+$.i18n._("Please upgrade to ") + link;
    } else {
        msg = $.i18n._("This version is no longer supported.")+"<br/>"+$.i18n._("Please upgrade to ") + link;
    }
    
    return msg;
}

/**
 * Get major version difference b/w current and latest version, in int
 */
function getVersionDiff() {
    return parseInt($("#version-diff").html());
}

/**
 * Get the current version
 */
function getCurrentVersion() {
    return $("#version-current").html();
}

/**
 * Get the latest version
 */
function getLatestVersion() {
    return $("#version-latest").html();
}

/**
 * Returns the download link to latest release in HTML
 */
function getLatestLink() {
    return "<a href='' onclick='openLatestLink();'>" + getLatestVersion() + "</a>";
}

/**
 * Returns true if current version is up to date
 */
function isUpToDate() {
    var diff = getVersionDiff();
    var current = getCurrentVersion();
    var latest = getLatestVersion();
    var temp = (diff == 0 && current == latest) || diff < 0;
    return (diff == 0 && current == latest) || diff < 0;
}

/**
 * Opens the link in a new window
 */
function openLatestLink() {
    window.open($("#version-link").html());
}

/**
 * Sets up the tooltip for version notification
 */
function setupVersionQtip(){
    var qtipElem = $('#version-icon');
    if (qtipElem.length > 0){
        qtipElem.qtip({
            id: 'version',
            content: {
                text: getContent(),
                title: {
                    text: getCurrentVersion(),
                    button: isUpToDate() ? false : true
                }
            },
            hide: {
                event: isUpToDate() ? 'mouseleave' : 'unfocus'
            },
            position: {
                my: "top right",
                at: "bottom left"
            },
            style: {
                border: {
                    width: 0,
                    radius: 4
                },
                classes: "ui-tooltip-dark ui-tooltip-rounded"
            }
        });
    }
}

$(document).ready(function() {
    if($('#version-icon').length > 0) {
        setupVersionQtip();
    }
});