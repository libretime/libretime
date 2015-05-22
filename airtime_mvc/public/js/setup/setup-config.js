/**
 * Do some cleanup when we get a success response from a POST request
 * during setup
 * @param data the POST request return data
 * @param e the jquery event
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
    toggleMessage(data.message);
    for (var i = 0; i < data.errors.length; i++) {
        $("#" + data.errors[i]).parent().addClass("has-error has-feedback");
    }
    if (data.errors.length > 0) {
        $(".help-message").addClass("has-error");
        $(".has-error .form-control-feedback").show();
    } else {
        $(".help-message").addClass("has-success");
    }
}

/**
 * Reset form feedback when resubmitting
 */
function resetFeedback() {
    $(".form-control-feedback").hide();
    $(".has-success, .has-error, .has-feedback").removeClass("has-success has-error has-feedback");
}

/**
 * Show the return message from the POST request, then set a timeout to hide it again
 * @param msg the return message from the POST request
 */
function toggleMessage(msg) {
    /*
     * Since setting display:none; on this element causes odd behaviour
     * with bootstrap, hide() the element so we can formSlide it in.
     * This is only really only necessary the first time this
     * function is called after page load.
     */
    var help = $(".help-message");
    help.html(msg).show();
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

function formSlide(dir) {
    var delta = (dir == "next") ? "-=100%" : "+=100%",
        parent = $(this).parents("div.form-wrapper"),
        toForm = (dir == "next") ? parent.next() : parent.prev();

    parent.find(".btn").attr("disabled", "disabled");
    toForm.find(".btn").removeAttr("disabled");
    toForm.find(":input :first").focus();

    $(".form-slider").animate({left: delta}, 500);
    var stepCount = $("#stepCount"),
        steps = parseInt(stepCount.html());
    stepCount.html((dir == "next") ? (steps + 1) : (steps - 1));
    hideRMQForm();
}

/**
 * Fade out the previous setup step and fade in the next one
 */
function nextSlide() {
    formSlide.call($(this), "next");
}

/**
 * Fade out the current setup step and fade in the previous one
 */
function prevSlide() {
    formSlide.call($(this), "prev");
}

/**
 * Hide the RMQ form when the slider is called to avoid showing
 * scrollbars on slider panels that fit vertically
 */
function hideRMQForm() {
    $("#rmqFormBody").slideUp(500);
    $("#advCaret").removeClass("caret-up");
}

function submitForm(e, obj) {
    resetFeedback();
    e.preventDefault();
    var d = $(e.target).serializeArray();
    addOverlay();
    // Append .promise().done() rather than using a
    // callback to avoid call duplication
    $("#overlay, #loadingImage").fadeIn(500).promise().done(function() {
        // Proxy function for passing the event to the cleanup function
        var cleanupProxy = function(data) {
            cleanupStep.call(this, data, e);
        };
        $.post('setup/setup-functions.php?obj=' + obj, d, cleanupProxy, "json");
    });
}

$(function() {
    // Stop the user from dragging the slider
    $(".form-slider").draggable('disable');
    $(".btn").attr("disabled", "disabled");
    $("form:first .btn").removeAttr("disabled");

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
