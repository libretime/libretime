$(document).ready(function () {
  $("#facebook-login").click(function () {
    AIRTIME.facebook.promptForFacebookPage();
  });
});

window.fbAsyncInit = function () {
  FB.init({
    appId: FACEBOOK_APP_ID,
    xfbml: true,
    version: "v2.4",
  });
};

var AIRTIME = (function (AIRTIME) {
  //Module initialization
  if (AIRTIME.facebook === undefined) {
    AIRTIME.facebook = {};
  }

  var mod = AIRTIME.facebook;

  (function (d, s, id) {
    var js,
      fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {
      return;
    }
    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  })(document, "script", "facebook-jssdk");

  mod.promptForFacebookPage = function () {
    FB.login(
      function (response) {
        if (response.authResponse) {
          mod.getPagesOwnedByUser(
            response.authResponse.userID,
            response.authResponse.accessToken,
          );
          mod.addPageTab();
        } else {
          console.log("Authorization failed.");
        }
      },
      { scope: "manage_pages" },
    );
  };

  mod.getPagesOwnedByUser = function (userId, accessToken) {
    FB.api(
      "/" + userId + "/accounts",
      function (response) {
        console.log(response);
      },
      { access_token: accessToken },
    );
  };

  mod.addPageTab = function () {
    FB.ui(
      { method: "pagetab" },
      function (resp) {
        console.log("response:");
        console.log(resp);
        var pageIdList = [];
        var tabs = resp["tabs_added"];

        if (tabs != undefined && Object.keys(tabs).length > 0) {
          for (var pageId in tabs) {
            pageIdList.push(pageId);
          }

          //POST these back to Airtime, which will then proxy it over to our social app. (multiple requests from Airtime)
          $.post(
            "facebook-tab-success",
            { pages: JSON.stringify(pageIdList) },
            function () {
              alert("Successfully added to your Facebook page!");
            },
          )
            .done(function () {})
            .fail(function () {
              alert(
                "Sorry, an error occurred and we were unable to add the widget to your Facebook page.",
              );
            });
        }
      },
      {
        app_id: FACEBOOK_APP_ID,
        //redirect_uri: 'https://localhost'
      },
    );
  };

  return AIRTIME;
})(AIRTIME || {});
