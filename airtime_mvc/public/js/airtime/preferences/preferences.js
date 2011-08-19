function showErrorSections() {

    if($("soundcloud-settings .errors").length > 0) {
        $("#soundcloud-settings").show();
        $(window).scrollTop($("soundcloud-settings .errors").position().top);
    }
}

$(document).ready(function() {

	$('.collapsible-header').live('click',function() {
        $(this).next().toggle('fast');
        $(this).toggleClass("close");
        return false;
    }).next().hide();

    showErrorSections();
});
