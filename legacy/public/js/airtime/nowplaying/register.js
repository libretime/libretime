$(document).ready(function () {
  function doNotShowPopup() {
    $.get(baseUrl + "Usersettings/donotshowregistrationpopup", {
      format: "json",
    });
  }

  var dialog = $("#register_popup");

  dialog.dialog({
    autoOpen: false,
    width: 500,
    resizable: false,
    modal: true,
    position: ["center", 50],
    close: doNotShowPopup,
    buttons: [
      {
        id: "remind_me",
        text: $.i18n._("Remind me in 1 week"),
        class: "btn",
        click: function () {
          var url = baseUrl + "Usersettings/remindme";
          $.ajax({
            url: url,
            data: { format: "json" },
          });
          $(this).dialog("close");
        },
      },
      {
        id: "remind_never",
        text: $.i18n._("Remind me never"),
        class: "btn",
        click: function () {
          var url = baseUrl + "Usersettings/remindme-never";
          $.ajax({
            url: url,
            data: { format: "json" },
          });
          $(this).dialog("close");
        },
      },
      {
        id: "help_airtime",
        text: sprintf($.i18n._("Yes, help %s"), PRODUCT_NAME),
        class: "btn",
        click: function () {
          $("#register-form").submit();
        },
      },
    ],
  });

  var button = $("#help_airtime");

  if ($("#link_to_terms_and_condition").length > 0) {
    button.removeAttr("disabled").removeClass("ui-state-disabled");
  } else {
    button.attr("disabled", "disabled").addClass("ui-state-disabled");
  }
  dialog.dialog("open");

  $(".collapsible-header")
    .live("click", function () {
      $(this).next().toggle("fast");
      $(this).toggleClass("close");
      return false;
    })
    .next()
    .hide();

  $("#SupportFeedback").live("click", function () {
    var pub = $("#Publicise");
    var privacy = $("#Privacy");
    var button = $("#help_airtime");
    if (!$(this).is(":checked")) {
      pub.removeAttr("checked");
      pub.attr("disabled", true);
      $("#public-info").hide();
      button.attr("disabled", "disabled").addClass("ui-state-disabled");
    } else {
      pub.removeAttr("disabled");
      if (privacy.length == 0 || privacy.is(":checked")) {
        button.removeAttr("disabled").removeClass("ui-state-disabled");
      }
    }
  });

  var promote = $("#Publicise");
  promote.live("click", function () {
    if ($(this).is(":checked")) {
      $("#public-info").show();
    } else {
      $("#public-info").hide();
    }
  });
  if (promote.is(":checked")) {
    $("#public-info").show();
  }

  $("#Privacy").live("click", function () {
    var support = $("#SupportFeedback");
    var button = $("#help_airtime");
    if ($(this).is(":checked") && support.is(":checked")) {
      button.removeAttr("disabled").removeClass("ui-state-disabled");
    } else {
      button.attr("disabled", "disabled").addClass("ui-state-disabled");
    }
  });

  if (
    $("#SupportFeedback").is(":checked") &&
    ($("#Privacy").length == 0 || $("#Privacy").is(":checked"))
  ) {
    button.removeAttr("disabled").removeClass("ui-state-disabled");
  } else {
    button.attr("disabled", "disabled").addClass("ui-state-disabled");
  }

  $(".toggle legend").live("click", function () {
    $(".toggle").toggleClass("closed");
    return false;
  });

  $("#Logo").live("change", function (ev) {
    var content, res, logoEl;

    content = $(this).val();
    res = content.match(/(jpg|jpeg|png|gif)$/gi);
    logoEl = $("#Logo-element");

    //not an accepted image extension.
    if (!res) {
      var ul, li;

      ul = logoEl.find(".errors");
      li = $("<li/>").append(
        $.i18n._("Image must be one of jpg, jpeg, png, or gif"),
      );

      //errors ul has already been created.
      if (ul.length > 0) {
        ul.empty().append(li);
      } else {
        logoEl.append('<ul class="errors"></ul>').find(".errors").append(li);
      }

      $(this).val("");
    } else {
      logoEl.find(".errors").remove();
    }
  });
});

function resizeImg(ele, targetWidth, targetHeight) {
  var img = $(ele);

  var width = ele.width;
  var height = ele.height;

  // resize img proportionaly
  if (width > height && width > targetWidth) {
    var ratio = targetWidth / width;
    img.css("width", targetHeight + "px");
    var newHeight = height * ratio;
    img.css("height", newHeight);
  } else if (width < height && height > targetHeight) {
    var ratio = targetHeight / height;
    img.css("height", targetHeight + "px");
    var newWidth = width * ratio;
    img.css("width", newWidth);
  } else if (width == height && width > targetWidth) {
    img.css("height", targetHeight + "px");
    img.css("width", targetWidth + "px");
  }
}
