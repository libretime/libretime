function setWatchedDirEvents() {
  $("#storageFolder-selection").serverBrowser({
    onSelect: function (path) {
      $("#storageFolder").val(path);
    },
    onLoad: function () {
      return $("#storageFolder").val();
    },
    width: 500,
    height: 250,
    position: ["center", "center"],
    //knownPaths: [{text:'Desktop', image:'desktop.png', path:'/home'}],
    knownPaths: [],
    imageUrl: "img/icons/",
    systemImageUrl: baseUrl + "css/img/",
    handlerUrl: baseUrl + "Preference/server-browse/format/json",
    title: $.i18n._("Choose Storage Folder"),
    basePath: "",
    requestMethod: "POST",
  });

  $("#watchedFolder-selection").serverBrowser({
    onSelect: function (path) {
      $("#watchedFolder").val(path);
    },
    onLoad: function () {
      return $("#watchedFolder").val();
    },
    width: 500,
    height: 250,
    position: ["center", "center"],
    //knownPaths: [{text:'Desktop', image:'desktop.png', path:'/home'}],
    knownPaths: [],
    imageUrl: "img/icons/",
    systemImageUrl: baseUrl + "css/img/",
    handlerUrl: baseUrl + "Preference/server-browse/format/json",
    title: $.i18n._("Choose Folder to Watch"),
    basePath: "",
    requestMethod: "POST",
  });

  $("#storageFolder-ok").click(function () {
    var url, chosen;

    if (
      confirm(
        sprintf(
          $.i18n._(
            "Are you sure you want to change the storage folder?\nThis will remove the files from your %s library!",
          ),
          PRODUCT_NAME,
        ),
      )
    ) {
      url = baseUrl + "Preference/change-stor-directory";
      chosen = $("#storageFolder").val();

      $.post(
        url,
        { format: "json", dir: chosen, element: "storageFolder" },

        function (json) {
          $("#watched-folder-section").empty();
          $("#watched-folder-section").append(json.subform);
          setWatchedDirEvents();
        },
      );
    } else {
      $("#storageFolder").val("");
    }
  });

  $("#watchedFolder-ok").click(function () {
    var url, chosen;

    url = baseUrl + "Preference/reload-watch-directory";
    chosen = $("#watchedFolder").val();

    $.post(
      url,
      { format: "json", dir: chosen, element: "watchedFolder" },

      function (json) {
        $("#watched-folder-section").empty();
        $("#watched-folder-section").append(
          "<h2>" + $.i18n._("Manage Media Folders") + "</h2>",
        );
        $("#watched-folder-section").append(json.subform);
        setWatchedDirEvents();
      },
    );
  });

  $(".selected-item")
    .find(".ui-icon-refresh")
    .click(function () {
      var folder = $(this).prev().text();
      $.get(baseUrl + "Preference/rescan-watch-directory", {
        format: "json",
        dir: folder,
      });
    });

  $(".selected-item")
    .find(".ui-icon-close")
    .click(function () {
      if (
        confirm($.i18n._("Are you sure you want to remove the watched folder?"))
      ) {
        var row = $(this).parent();
        var folder = row.find("#folderPath").text();

        url = baseUrl + "Preference/remove-watch-directory";

        $.post(
          url,
          { format: "json", dir: folder },

          function (json) {
            $("#watched-folder-section").empty();
            $("#watched-folder-section").append(
              "<h2>" + $.i18n._("Manage Media Folders") + "</h2>",
            );
            $("#watched-folder-section").append(json.subform);
            setWatchedDirEvents();
          },
        );
      }
    });
}

$(document).ready(function () {
  setWatchedDirEvents();
  $(".ui-icon-alert").qtip({
    content: {
      text: $.i18n._("This path is currently not accessible."),
    },
    position: {
      adjust: {
        resize: true,
        method: "flip flip",
      },
      at: "right center",
      my: "left top",
      viewport: $(window),
    },
    style: {
      classes: "ui-tooltip-dark",
    },
    show: "mouseover",
    hide: "mouseout",
  });
});
