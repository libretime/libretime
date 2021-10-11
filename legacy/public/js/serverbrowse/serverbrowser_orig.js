/*
    author: ApmeM (artem.votincev@gmail.com)
    date: 9-June-2010
    version: 1.4
    download: http://code.google.com/p/jq-serverbrowse/
*/

(function($) {
    $.fn.serverBrowser = function(settings) {
        this.each(function() {

            var config = {
// Event function
// Appear when user click 'Ok' button, or doubleclick on file
                onSelect: function(file) {
                    alert('You select: ' + file);
                },
                onLoad: function() {
                    return config.basePath;
                },
                multiselect: false,
// Image parameters
// System images (loading.gif, unknown.png, folder.png and images from knownPaths) will be referenced to systemImageUrl
// if systemImageUrl is empty or not specified - imageUrl will be taken
// All other images (like images for extension) will be taken from imageUrl
                imageUrl: 'img/',
                systemImageUrl: '',
                showUpInList: false,
// Path properties
// Base path, that links should start from.
// If opened path is not under this path, alert will be shown and nothing will be opened
// Path separator, that will be used to split specified paths and join paths to a string
                basePath: 'C:',
                separatorPath: '/',
// Paths, that will be displayed on the left side of the dialog
// This is a link to specified paths on the server
                useKnownPaths: true,
                knownPaths: [{text:'Desktop', image:'desktop.png', path:'C:/Users/All Users/Desktop'},
                            {text:'Documents', image:'documents.png', path:'C:/Users/All Users/Documents'}],
// Images for known extension (like 'png', 'exe', 'zip'), that will be displayed with its real names
// Images, that is not in this list will be referenced to 'unknown.png' image
// If list is empty - all images is known.
                knownExt: [],
// Server path to this plugin handler
                handlerUrl: 'browserDlg.txt',
// JQuery-ui dialog settings
                title: 'Browse',
                width: 300,
                height: 300,
                position: ['center', 'top'],

// Administrative parameters used to
// help programmer or system administrator
                requestMethod: 'POST',
            };

            if (settings) $.extend(config, settings);
// Required configuration elements
// We need to set some configuration elements without user
// For example there should be 2 buttons on the bottom, 
// And dialog should be opened after button is pressed, not when it created
// Also we need to know about dialog resizing
            $.extend(config, {
                autoOpen: false,
                modal: true,
                buttons: [
                    {
                        text: "Cancel",
                        "class": "btn",
                        click: function() {
                            browserDlg.dialog("close");
                        }
                    },
                    {
                        text: "Open",
                        "class": "btn",
                        click: function() {
                            doneOk();
                        }
                    }
                ],
                resize: function(event, ui) {
                    recalculateSize(event, ui);
                },
            });
            
            function systemImageUrl()
            {
                if (config.systemImageUrl.length == 0) {
                    return config.imageUrl;
                } else{
                    return config.systemImageUrl;
                }
            }
            
            var privateConfig = {
// This stack array will store history navigation data
// When user open new directory, old directory will be added to this list
// If user want, he will be able to move back by this history
                browserHistory: [],

// This array contains all currently selected items
// When user select element, it will add associated path into this array
// When user deselect element - associated path will be removed
// Exception: if 'config.multiselect' is false, only one element will be stored in this array.
                selectedItems: [],
            }
            
// Main dialog div
// It will be converted into jQuery-ui dialog box using my configuration parameters
// It contains 3 divs
            var browserDlg = $('<div title="' + config.title + '"></div>').css({'overflow': 'hidden'}).appendTo(document.body);
            browserDlg.dialog(config);
            
// First div on the top
// It contains textbox field and buttons
// User can enter any paths he want to open in this textbox and press enter
// There is 3 buttons on the panel:
            var enterPathDiv = $('<div></div>').addClass('ui-widget-content').appendTo(browserDlg).css({'height': '30px', 'width': '100%', 'padding-top': '7px'});
            
            var enterButton = $('<div></div>').css({'float': 'left', 'vertical-align': 'middle', 'margin-left': '6px'}).addClass('ui-corner-all').hover(
                function() { $(this).addClass('ui-state-hover'); },
                function() { $(this).removeClass('ui-state-hover'); }
            );

            var enterLabel = $('<span></span>').text('Look in: ').appendTo(enterButton.clone(false).appendTo(enterPathDiv));

            var enterText = $('<input type="text">').keypress(function(e) {
                if (e.keyCode == '13') {
                    e.preventDefault();
                    loadPath(enterText.val());
                }
            }).appendTo(enterButton.clone(false).appendTo(enterPathDiv));
            

// Back button. 
// When user click on it, 2 last elements of the history pop from the list, and reload second of them.
            var enterBack = $('<div></div>').addClass('ui-corner-all ui-icon ui-icon-circle-arrow-w').click(function(){
                privateConfig.browserHistory.pop(); // Remove current element. It is not required now.
                var backPath = config.basePath;
                if(privateConfig.browserHistory.length > 0){
                    backPath = privateConfig.browserHistory.pop();
                }
                loadPath(backPath);
            }).appendTo(enterButton.clone(true).appendTo(enterPathDiv));

// Level Up Button
// When user click on it, last element of the history will be taken, and '..' will be applied to the end of the array.
            var enterUp = $('<div></div>').addClass('ui-corner-all ui-icon ui-icon-arrowreturnthick-1-n').click(function(){
                backPath = privateConfig.browserHistory[privateConfig.browserHistory.length - 1];
                if(backPath != config.basePath){
                    loadPath(backPath + config.separatorPath + '..');
                }
            }).appendTo(enterButton.clone(true).appendTo(enterPathDiv));
            
// Second div is on the left
// It contains images and texts for pre-defined paths
// User just click on them and it will open pre-defined path
            var knownPathDiv = $('<div></div>').addClass('ui-widget-content').css({'text-align':'center', 'overflow': 'auto', 'float': 'left', 'width': '100px'});
            if(config.useKnownPaths){
                knownPathDiv.appendTo(browserDlg);
                $.each(config.knownPaths, function(index, path) {
                    var knownDiv = $('<div></div>').css({'margin':'10px'}).hover(
                        function() { $(this).addClass('ui-state-hover'); },
                        function() { $(this).removeClass('ui-state-hover'); }
                    ).click(function() {
                        loadPath(path.path);
                    }).appendTo(knownPathDiv);

                    $('<img />').attr({ src: systemImageUrl() + config.separatorPath + path.image }).css({ width: '32px', margin: '5px 10px 5px 5px' }).appendTo(knownDiv);
                    $('<br/>').appendTo(knownDiv);
                    $('<span></span>').text(path.text).appendTo(knownDiv);
                });
            }
            
// Third div is everywhere :)
// It show files and folders in the current path
// User can click on path to select or deselect it
// Doubleclick on path will open it
// Also doubleclick on file will select this file and close dialog
            var browserPathDiv = $('<div></div>').addClass('ui-widget-content').css({'float': 'right', 'overflow': 'auto'}).appendTo(browserDlg);
            
// Now everything is done
// When user will be ready - he just click on the area you select for this plugin and dialog will appear
            $(this).click(function() {
                privateConfig.browserHistory = [];
                var startpath = removeBackPath(config.onLoad());
                
                startpath = startpath.split(config.separatorPath);
                startpath.pop();
                startpath = startpath.join(config.separatorPath);
                
                if(!checkBasePath(startpath)){
                    startpath = config.basePath;
                }
                loadPath(startpath);
                browserDlg.dialog('open');
                recalculateSize();
            });

// Function check if specified path is a child path of a 'config.basePath'
// If it is not - user should see message, that path invalid, or path should be changed to valid.
            function checkBasePath(path){
                if(config.basePath == '')
                    return true;
                var confPath = config.basePath.split(config.separatorPath);
                var curPath = path.split(config.separatorPath);
                if(confPath.length > curPath.length)
                    return false;
                var result = true;
                $.each(confPath, function(index, partConfPath) { 
                    if(partConfPath != curPath[index]){
                        result = false;
                    }
                });
                return result;
            }

// Function remove '..' parts of the path
// Process depend on config.separatorPath option
// On the server side you need to check / or \ separators
            function removeBackPath(path){
                var confPath = config.basePath.split(config.separatorPath);
                var curPath = path.split(config.separatorPath);
                var newcurPath = [];
                $.each(curPath, function(index, partCurPath) { 
                    if(partCurPath == ".."){
                        newcurPath.pop();
                    }else{
                        newcurPath.push(partCurPath);
                    }
                });
                return newcurPath.join(config.separatorPath);
            }

// This function will be called when user click 'Open' 
// It check if any path is selected, and call config.onSelect function with path list
            function doneOk(){
                var newCurPath = [];
                $.each(privateConfig.selectedItems, function(index, item) {
                    newCurPath.push($.data(item, 'path'));
                });
                if(newCurPath.length == 0) {
                    newCurPath.push(privateConfig.browserHistory.pop());
                }
                
                if(config.multiselect)
                    config.onSelect(newCurPath);
                else {
                    if(newCurPath.length == 1) {
                        config.onSelect(newCurPath[0]);
                    } else if(newCurPath.length > 1){
                        alert('Plugin work incorrectly. If error repeat, please add issue into http://code.google.com/p/jq-serverbrowse/issues/list with steps to reproduce.');
                        return;
                    }
                }
                browserDlg.dialog("close");
            }
            
// Function recalculate and set new width and height for left and right div elements
// height have '-2' because of the borders
// width have '-4' because of a border an 2 pixels space between divs
            function recalculateSize(event, ui){
                knownPathDiv.css({'height' : browserDlg.height() - enterPathDiv.outerHeight(true) - 2});
                browserPathDiv.css({'height' : browserDlg.height() - enterPathDiv.outerHeight(true) - 2,
                                    'width' : browserDlg.width() - knownPathDiv.outerWidth(true) - 4});
            }

// Function adds new element into browserPathDiv element depends on file parameters
// If file.isError is set, error message will be displayed instead of clickable area
// Clickable div contain image from extension and text from file parameter
            function addElement(file){
                var itemDiv = $('<div></div>').css({ margin: '2px' }).appendTo(browserPathDiv);
                if(file.isError)
                {
                    itemDiv.addClass('ui-state-error ui-corner-all').css({padding: '0pt 0.7em'});
                    var p = $('<p></p>').appendTo(itemDiv);
                    $('<span></span>').addClass('ui-icon ui-icon-alert').css({'float': 'left', 'margin-right': '0.3em'}).appendTo(p);
                    $('<span></span>').text(file.name).appendTo(p);
                }else
                {
                    var fullPath = file.path + config.separatorPath + file.name;
                    itemDiv.hover(
                        function() { $(this).addClass('ui-state-hover'); },
                        function() { $(this).removeClass('ui-state-hover'); }
                    );
                    var itemImage = $('<img />').css({ width: '16px', margin: '0 5px 0 0' }).appendTo(itemDiv);
                    var itemText = $('<span></span>').text(file.name).appendTo(itemDiv);
                    if (file.isFolder)
                        itemImage.attr({ src: systemImageUrl() + 'folder.png' });
                    else {
                        ext = file.name.split('.').pop();
                        var res = '';
                        if (ext == '' || ext == file.name || (config.knownExt.length > 0 && $.inArray(ext, config.knownExt) < 0))
                            itemImage.attr({ src: systemImageUrl() + 'unknown.png' });
                        else
                            itemImage.attr({ src: config.imageUrl + ext + '.png' });
                    }
                    $.data(itemDiv, 'path', fullPath);
                    itemDiv.unbind('click').bind('click', function(e) {
                        if(!$(this).hasClass('ui-state-active')) {
                            if(!config.multiselect && privateConfig.selectedItems.length > 0) {
                                $(privateConfig.selectedItems[0]).click();
                            }
                            privateConfig.selectedItems.push(itemDiv);
                        }else{
                            var newCurPath = [];
                            $.each(privateConfig.selectedItems, function(index, item) {
                                if($.data(item, 'path') != fullPath)
                                    newCurPath.push(item);
                            });
                            privateConfig.selectedItems = newCurPath;
                        }
                        $(this).toggleClass('ui-state-active');
                    });

                    itemDiv.unbind('dblclick').bind('dblclick', function(e) {
                        if (file.isFolder){
                            loadPath(fullPath);
                        } else {
                            privateConfig.selectedItems = [itemDiv];
                            doneOk();
                        }
                    });
                }
            }

// Main plugin function
// When user enter path manually, select it from pre-defined path, or doubleclick in browser this function will call
// It send a request on the server to retrieve child directories and files of the specified path
// If path is not under 'config.basePath', alert will be shown and nothing will be opened
            function loadPath(path) {
                privateConfig.selectedItems = [];
                
                // First we need to remove all '..' parts of the path
                path = removeBackPath(path);
                
                // Then we need to check, if path based on 'config.basePath'
                if(!checkBasePath(path)) {
                    alert('Path should be based from ' + config.basePath);
                    return;
                }
                
                // Then we can put this path into history
                privateConfig.browserHistory.push(path);
                
                // Show it to user
                enterText.val(path);
                
                // And load
                $.ajax({
                    url: config.handlerUrl,
                    type: config.requestMethod,
                    data: {
                        action: 'browse',
                        path: path,
                        time: new Date().getTime()
                    },
                    beforeSend: function() {
                        browserPathDiv.empty().css({ 'text-align': 'center' });
                        $('<img />').attr({ src: systemImageUrl() + 'loading.gif' }).css({ width: '32px' }).appendTo(browserPathDiv);
                    },
                    success: function(files) {
                        browserPathDiv.empty().css({ 'text-align': 'left' });
                        if(path != config.basePath && config.showUpInList){
                            addElement({name: '..', isFolder: true, isError: false, path: path});
                        }
                        $.each(files, function(index, file) {
                            addElement($.extend(file, {path: path}));
                        });
                    },
                    dataType: 'json'
                });
            }
        });
        return this;
    };
})(jQuery);
