function showErrorSections() {
  $(".errors").each(function (i) {
    if ($(this).length > 0) {
      var div = $(this).closest("div");
      if (div.attr("class") == "stream-setting-content") {
        $(this).closest("div").show();
        $(this).closest("fieldset").removeClass("closed");
        $(window).scrollTop($(this).closest("div").position().top);
      }
    }
  });
}
function restrictOggBitrate(ele, on) {
  var div = ele.closest("div");
  if (on) {
    if (parseInt(div.find("select[id$=data-bitrate]").val(), 10) < 48) {
      div
        .find("select[id$=data-bitrate]")
        .find("option[value='48']")
        .attr("selected", "selected");
    }
    div
      .find("select[id$=data-bitrate]")
      .find("option[value='24']")
      .attr("disabled", "disabled");
    div
      .find("select[id$=data-bitrate]")
      .find("option[value='32']")
      .attr("disabled", "disabled");
  } else {
    div
      .find("select[id$=data-bitrate]")
      .find("option[value='24']")
      .removeAttr("disabled");
    div
      .find("select[id$=data-bitrate]")
      .find("option[value='32']")
      .removeAttr("disabled");
  }
}
function hideForShoutcast(ele) {
  var div = ele.closest("div");
  div.find("#outputMountpoint-label").hide();
  div.find("#outputMountpoint-element").hide();
  div.find("#outputUser-label").hide();
  div.find("#outputUser-element").hide();
  div
    .find("select[id$=data-type]")
    .find("option[value='mp3']")
    .attr("selected", "selected");
  div
    .find("select[id$=data-type]")
    .find("option[value='ogg']")
    .attr("disabled", "disabled");
  div
    .find("select[id$=data-type]")
    .find("option[value='opus']")
    .attr("disabled", "disabled");

  restrictOggBitrate(ele, false);
}

function validate(ele, evt) {
  var theEvent = evt || window.event;
  var key = theEvent.keyCode || theEvent.which;
  if (
    (ele.val().length >= 5 || key < 48 || key > 57) &&
    !(key == 8 || key == 9 || key == 13 || key == 37 || key == 39 || key == 46)
  ) {
    theEvent.returnValue = false;
    if (theEvent.preventDefault) theEvent.preventDefault();
  }
}

function showForIcecast(ele) {
  var div = ele.closest("div");
  div.find("#outputMountpoint-label").show();
  div.find("#outputMountpoint-element").show();
  div.find("#outputUser-label").show();
  div.find("#outputUser-element").show();
  div
    .find("select[id$=data-type]")
    .find("option[value='ogg']")
    .removeAttr("disabled");
  div
    .find("select[id$=data-type]")
    .find("option[value='opus']")
    .removeAttr("disabled");
}

function checkLiquidsoapStatus() {
  var url = baseUrl + "Preference/get-liquidsoap-status/format/json";
  var id = $(this).attr("id");
  $.post(url, function (json_obj) {
    for (var i = 0; i < json_obj.length; i++) {
      var obj = json_obj[i];
      var id;
      var status;
      for (var key in obj) {
        if (key == "id") {
          id = obj[key];
        }
        if (key == "status") {
          status = obj[key];
        }
      }
      var html;
      if (status == "OK") {
        html =
          '<div class="stream-status status-good"><p>' +
          $.i18n._("Connected to the streaming server") +
          "</p></div>";
      } else if (status == "N/A") {
        html =
          '<div class="stream-status status-disabled"><p>' +
          $.i18n._("The stream is disabled") +
          "</p></div>";
      } else if (status == "waiting") {
        html =
          '<div class="stream-status status-info"><p>' +
          $.i18n._("Getting information from the server...") +
          "</p></div>";
      } else {
        html =
          '<div class="stream-status status-error"><p>' +
          $.i18n._("Can not connect to the streaming server") +
          "</p><p>" +
          status +
          "</p></div>";
      }
      $("#s" + id + "Liquidsoap-error-msg-element").html(html);
    }

    setTimeout(checkLiquidsoapStatus, 2000);
  });
}

function setupEventListeners() {
  if (!$("#output_sound_device").is(":checked")) {
    $("select[id=output_sound_device_type]").attr("disabled", "disabled");
  } else {
    $("select[id=output_sound_device_type]").removeAttr("disabled");
  }

  $("#output_sound_device").change(function () {
    if ($(this).is(":checked")) {
      $("select[id=output_sound_device_type]").removeAttr("disabled");
    } else {
      $("select[id=output_sound_device_type]").attr("disabled", "disabled");
    }
  });

  $("select[id$=data-type]").change(function () {
    if ($(this).val() == "ogg") {
      restrictOggBitrate($(this), true);
    } else {
      restrictOggBitrate($(this), false);
    }
  });

  $("select[id$=data-type]").each(function () {
    if ($(this).val() == "ogg") {
      restrictOggBitrate($(this), true);
    }
  });

  $("select[id$=data-output]").change(function () {
    if ($(this).val() == "shoutcast") {
      hideForShoutcast($(this));
    } else {
      showForIcecast($(this));
    }
  });

  $("select[id$=data-output]").each(function () {
    if ($(this).val() == "shoutcast") {
      hideForShoutcast($(this));
    }
  });

  $(".toggle legend").click(function () {
    $(this).parent().toggleClass("closed");
    return false;
  });

  $(".collapsible-header").click(function () {
    $(this).next().toggle("fast");
    $(this).toggleClass("closed");
    return false;
  });

  showErrorSections();
  checkLiquidsoapStatus();

  var userManualAnchorOpen =
    "<a target='_blank' href='" + USER_MANUAL_URL + "'>";

  // qtip for help text
  $(".override_help_icon").qtip({
    content: {
      text:
        sprintf(
          $.i18n._(
            "If %s is behind a router or firewall, you may need to configure port forwarding and this field information will be incorrect. In this case you will need to manually update this field so it shows the correct host/port/mount that your DJ's need to connect to. The allowed range is between 1024 and 49151.",
          ),
          PRODUCT_NAME,
        ) +
        " " +
        sprintf(
          $.i18n._("For more details, please read the %s%s Manual%s"),
          userManualAnchorOpen,
          PRODUCT_NAME,
          "</a>",
        ),
    },
    hide: {
      delay: 500,
      fixed: true,
    },
    style: {
      border: {
        width: 0,
        radius: 4,
      },
      classes: "ui-tooltip-dark ui-tooltip-rounded",
    },
    position: {
      my: "left bottom",
      at: "right center",
    },
  });

  $(".icecast_metadata_help_icon").qtip({
    content: {
      text: $.i18n._(
        "Check this option to enable metadata for OGG streams (stream metadata is the track title, artist, and show name that is displayed in an audio player). VLC and mplayer have a serious bug when playing an OGG/VORBIS stream that has metadata information enabled: they will disconnect from the stream after every song. If you are using an OGG stream and your listeners do not require support for these audio players, then feel free to enable this option.",
      ),
    },
    hide: {
      delay: 500,
      fixed: true,
    },
    style: {
      border: {
        width: 0,
        radius: 4,
      },
      classes: "ui-tooltip-dark ui-tooltip-rounded",
    },
    position: {
      my: "left bottom",
      at: "right center",
    },
  });

  $("#auto_transition_help").qtip({
    content: {
      text: $.i18n._(
        "Check this box to automatically switch off Master/Show source upon source disconnection.",
      ),
    },
    hide: {
      delay: 500,
      fixed: true,
    },
    style: {
      border: {
        width: 0,
        radius: 4,
      },
      classes: "ui-tooltip-dark ui-tooltip-rounded",
    },
    position: {
      my: "left bottom",
      at: "right center",
    },
  });

  $("#auto_switch_help").qtip({
    content: {
      text: $.i18n._(
        "Check this box to automatically switch on Master/Show source upon source connection.",
      ),
    },
    hide: {
      delay: 500,
      fixed: true,
    },
    style: {
      border: {
        width: 0,
        radius: 4,
      },
      classes: "ui-tooltip-dark ui-tooltip-rounded",
    },
    position: {
      my: "left bottom",
      at: "right center",
    },
  });

  $(".stream_username_help_icon").qtip({
    content: {
      text: $.i18n._(
        "If your Icecast server expects a username of 'source', this field can be left blank.",
      ),
    },
    hide: {
      delay: 500,
      fixed: true,
    },
    style: {
      border: {
        width: 0,
        radius: 4,
      },
      classes: "ui-tooltip-dark ui-tooltip-rounded",
    },
    position: {
      my: "left bottom",
      at: "right center",
    },
  });

  $(".admin_username_help_icon").qtip({
    content: {
      text: $.i18n._(
        "This is the admin username and password for Icecast/SHOUTcast to get listener statistics.",
      ),
    },
    hide: {
      delay: 500,
      fixed: true,
    },
    style: {
      border: {
        width: 0,
        radius: 4,
      },
      classes: "ui-tooltip-dark ui-tooltip-rounded",
    },
    position: {
      my: "left bottom",
      at: "right center",
    },
  });

  $(".master_username_help_icon").qtip({
    content: {
      text: $.i18n._(
        "If your live streaming client does not ask for a username, this field should be 'source'.",
      ),
    },
    hide: {
      delay: 500,
      fixed: true,
    },
    style: {
      border: {
        width: 0,
        radius: 4,
      },
      classes: "ui-tooltip-dark ui-tooltip-rounded",
    },
    position: {
      my: "left bottom",
      at: "right center",
    },
  });

  $(".stream_type_help_icon").qtip({
    content: {
      text: sprintf(
        $.i18n._(
          "Some stream types require extra configuration. Details about enabling %sAAC+ Support%s or %sOpus Support%s are provided.",
        ),
        "<a target='_blank' href='https://wiki.sourcefabric.org/x/NgPQ'>",
        "</a>",
        "<a target='_blank' href='https://wiki.sourcefabric.org/x/KgPQ'>",
        "</a>",
      ),
    },
    hide: {
      delay: 500,
      fixed: true,
    },
    style: {
      border: {
        width: 0,
        radius: 4,
      },
      classes: "ui-tooltip-dark ui-tooltip-rounded",
    },
    position: {
      my: "left bottom",
      at: "right center",
    },
  });
}

function setSliderForReplayGain() {
  $("#slider-range-max").slider({
    range: "max",
    min: -10,
    max: 10,
    value: $("#rg_modifier_value").html(),
    slide: function (event, ui) {
      $("#replayGainModifier").val(ui.value);
      $("#rg_modifier_value").html(ui.value);
    },
  });
  $("#replayGainModifier").val($("#slider-range-max").slider("value"));
}

function setSliderForLUFS() {
  $("#lufs-range-max").slider({
    range: "max",
    min: -25,
    max: -10,
    value: $("#mm_lufs_value").html(),
    slide: function (event, ui) {
      $("#masterMeLufs").val(ui.value);
      $("#mm_lufs_value").html(ui.value);
    },
  });
  $("#masterMeLufs").val($("#lufs-range-max").slider("value"));
}

function setPseudoAdminPassword(s1, s2, s3, s4) {
  if (s1) {
    $("#s1_data-admin_pass").val("xxxxxx");
  }
  if (s2) {
    $("#s2_data-admin_pass").val("xxxxxx");
  }
  if (s3) {
    $("#s3_data-admin_pass").val("xxxxxx");
  }
  if (s4) {
    $("#s4_data-admin_pass").val("xxxxxx");
  }
}

function getAdminPasswordStatus() {
  $.ajax({
    url: baseUrl + "Preference/get-admin-password-status/format/json",
    dataType: "json",
    success: function (data) {
      setPseudoAdminPassword(data.s1, data.s2, data.s3, data.s4);
    },
  });
}

$(document).ready(function () {
  setupEventListeners();
  setSliderForReplayGain();
  setSliderForLUFS();
  getAdminPasswordStatus();
  var s = $("[name^='customStreamSettings']:checked");

  $("#masterMe-element label input").change(function (e) {
    var x = $('label[for="' + e.target.id + '"]').html();
    try {
      x = parseInt(x.match(/([0-9-]+).LUFS/)[1]);
    } catch (err) {
      x = -16; // default
    }
    $("#masterMeLufs").val(x);
    $("#lufs-range-max").slider({ value: x });
    $("#mm_lufs_value").html(x);
  });

  $("[id^='stream_save'], [name^='customStreamSettings']").live(
    "click",
    function () {
      var e = $(this);
      if (e[0] == s[0]) {
        return;
      }
      var data = $("#stream_form").serialize();
      var url = baseUrl + "Preference/stream-setting";

      $.post(url, { format: "json", data: data }, function (json) {
        $("#content").empty().append(json.html);
        if (json.valid) {
          window.location.reload();
        }
        setupEventListeners();
        setSliderForReplayGain();
        setSliderForLUFS();
        getAdminPasswordStatus();
      });
    },
  );
});
