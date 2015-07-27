$(document).ready(function() {

    var dialog = $("#lang-timezone-popup");

    dialog.dialog({
            autoOpen: false,
            width: 500,
            resizable: false,
            modal: true,
            position:['center',50],
            buttons: [
                {
                    id: "setup-later",
                    text: $.i18n._("Set Later"),
                    "class": "btn",
                    click: function() {
                        $(this).dialog("close");
                    }
                },
                {
                    id: "help_airtime",
                    text: $.i18n._("OK"),
                    "class": "btn",
                    click: function() {
                        var formValues = $("#lang-timezone-form").serializeArray();
                        $.post(baseUrl+"setup/setup-language-timezone",
                            {
                                format: "json",
                                data: formValues
                            }, function(json) {
                                console.log(json);
                                $("#lang-timezone-popup").dialog("close");
                            });
                    }
                }
            ]
    });

    dialog.dialog('open');
});
