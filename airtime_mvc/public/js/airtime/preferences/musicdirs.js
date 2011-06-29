function setWatchedDirEvents() {

    $('#storageFolder-selection').serverBrowser({
        onSelect: function(path) {
            $('#storageFolder').val(path);
        },
        onLoad: function() {
            return $('#storageFolder').val();
        },
        width: 500,
        height: 250,
        position: ['center', 'center'],
        //knownPaths: [{text:'Desktop', image:'desktop.png', path:'/home'}],
        knownPaths: [],
        imageUrl: 'img/icons/',
        systemImageUrl: 'img/browser/',
        handlerUrl: '/Preference/server-browse/format/json',
        title: 'Choose Storage Folder',
        basePath: '',
        requestMethod: 'POST',
    });

    $('#watchedFolder-selection').serverBrowser({
        onSelect: function(path) {
            $('#watchedFolder').val(path);
        },
        onLoad: function() {
            return $('#watchedFolder').val();
        },
        width: 500,
        height: 250,
        position: ['center', 'center'],
        //knownPaths: [{text:'Desktop', image:'desktop.png', path:'/home'}],
        knownPaths: [],
        imageUrl: 'img/icons/',
        systemImageUrl: 'img/browser/',
        handlerUrl: '/Preference/server-browse/format/json',
        title: 'Choose Folder to Watch',
        basePath: '',
        requestMethod: 'POST',
    });

    $('#storageFolder-ok').click(function(){
        var url, chosen;
        
        if(confirm("Are you sure you want to change the storage folder?")){
            url = "/Preference/change-stor-directory";
            chosen = $('#storageFolder').val();

            $.post(url,
                {format: "json", dir: chosen, element: "storageFolder"},

                function(json) {
                    $("#watched-folder-section").empty();
                    $("#watched-folder-section").append(json.subform);
                    setWatchedDirEvents();
                });
        }
    });

    $('#watchedFolder-ok').click(function(){
        var url, chosen;

        url = "/Preference/reload-watch-directory";
        chosen = $('#watchedFolder').val();

        $.post(url,
            {format: "json", dir: chosen, element: "watchedFolder"},

            function(json) {
                $("#watched-folder-section").empty();
                $("#watched-folder-section").append(json.subform);
                setWatchedDirEvents();
            });
    });

    $('.selected-item').find('.ui-icon-close').click(function(){
        if(confirm("Are you sure you want to remove the watched folder?")){
            var row = $(this).parent();
            var folder = $(this).prev().text();

            url = "/Preference/remove-watch-directory";

            $.post(url,
                {format: "json", dir: folder},

                function(json) {
                    $("#watched-folder-section").empty();
                    $("#watched-folder-section").append(json.subform);
                    setWatchedDirEvents();
                });
        }
    });
}

$(document).ready(function() {

    setWatchedDirEvents();

});
