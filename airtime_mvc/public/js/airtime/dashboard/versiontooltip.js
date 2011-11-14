/**
 * Get the tooltip message to be displayed,
 * which is stored inside a pair of hidden div tags
 */
function getContent() {
    return $("#version_message").html();
}

/**
 * Get the current version,
 * which is stored inside a pair of hidden div tags
 */
function getCurrentVersion() {
    return $("#version_current").html();
}

/**
 * Sets up the tooltip for version notification
 */
function setupVersionQtip(){
    var qtipElem = $('#version_icon');
    if (qtipElem.length > 0){
        qtipElem.qtip({
            id: 'version',
            content: {
                text: getContent(),
                title: {
                    text: getCurrentVersion(),
                    button: true
                }
            },
            hide: false,    /* Don't hide on mouseout */
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
    if($('#version_message').length > 0) {
        setupVersionQtip();
    }
});