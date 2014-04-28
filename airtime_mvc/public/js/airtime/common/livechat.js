var __lc = {};
__lc.license = 1083735;

function setupLiveChat() {
    // this is where we pass custom variables to livechat;
    // only pass the client id assigned by WHMCS for now
    __lc.params = [
        { name: 'client_id', value: livechat_client_id }
    ];
    
    var lc = document.createElement('script'); lc.type = 'text/javascript'; lc.async = true;
    lc.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.livechatinc.com/tracking.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(lc, s);
}

$(document).ready(function() {
    setupLiveChat();
});