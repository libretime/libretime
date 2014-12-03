/**
 * Do some cleanup when we get a success response from a POST request
 * during setup
 * @param data the POST request return data
 */
function cleanupStep(data, e) {
    showFeedback(data);
    // If there are no errors, we can continue with
    // the installation process
    if (data.errors.length == 0) {
        // Call nextSlide from the submit button's context
        nextSlide.call($(e.target));
    }
    removeOverlay();
}

/**
 * Display the form feedback when we get POST results
 * @param data the POST request return data
 */
function showFeedback(data) {
    if (data.errors.length > 0) {
        $(".help-message").addClass("has-error");
        $(".form-control-feedback").show();
    } else {
        $(".help-message").addClass("has-success");
    }
    toggleMessage(data.message);
    for (var i = 0; i < data.errors.length; i++) {
        $("#" + data.errors[i]).parent().addClass("has-error has-feedback");
    }
}

/**
 * Reset form feedback when resubmitting
 */
function resetFeedback() {
    $(".form-control-feedback").hide();
    $("#helpBlock").html("");
    $(".has-error, .has-feedback").removeClass("has-error has-feedback");
}

/**
 * Show the return message from the POST request, then set a timeout to hide it again
 * @param msg the return message from the POST request
 */
function toggleMessage(msg) {
    /*
     * Since setting display:none; on this element causes odd behaviour
     * with bootstrap, hide() the element so we can slide it in.
     * This is only really only necessary the first time this
     * function is called after page load.
     */
    $(".help-message").hide();
    $(".help-message").html(msg);
    $(".help-message").slideDown(200);
    window.setTimeout(function() {
        $(".help-message").slideUp(200);
    }, 3000);
}

/**
 * Show the overlay and loading gif
 */
function addOverlay() {
    $("body").append("<div id='overlay'></div><img src='css/images/file_import_loader.gif' id='loadingImage'/>");
}

/**
 * Remove the overlay and loading gif
 */
function removeOverlay() {
    var overlay = $("#overlay, #loadingImage");
    $("#loadingImage").fadeOut(250);
    $("#overlay").fadeOut(500, function() {
        overlay.remove();
    });
}

/**
 * Fade out the previous setup step and fade in the next one
 */
function nextSlide() {
    $(".btn").attr("disabled", "disabled");
    $(".form-slider").animate({left: "-=100%"}, 500, function() {
        $(".btn").removeAttr("disabled");
    });
    var stepCount = parseInt($("#stepCount").html());
    $("#stepCount").html(stepCount + 1);
}

/**
 * Fade out the current setup step and fade in the previous one
 */
function prevSlide() {
    $(".btn").attr("disabled", "disabled");
    $(".form-slider").animate({left: "+=100%"}, 500, function() {
        $(".btn").removeAttr("disabled");
    });
    var stepCount = parseInt($("#stepCount").html());
    $("#stepCount").html(stepCount - 1);
}

$(function() {
    $(".form-slider").draggable({
        revert: true,
        axis: 'x',
        snap: ".viewport",
        snapMode: "both",
    });

    window.onresize = function() {
        var headerHeight = $(".header").outerHeight(),
            viewport = $(".viewport"),
            viewportHeight = viewport.outerHeight();
        // If the viewport would go outside the page bounds,
        // shrink it to fit the window
        if (viewportHeight + headerHeight > window.innerHeight) {
            viewport.css("height", window.innerHeight - headerHeight);
        }
        // Otherwise, go back to what we have in the stylesheet
        else {
            viewport.css("height", "");
        }
    };
});
