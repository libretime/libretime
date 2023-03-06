import os
import urllib

import requests
from django.contrib.auth import authenticate, login, logout
from django.http import HttpRequest, HttpResponse, HttpResponseRedirect
from django.shortcuts import render

from libretime_api.settings.prod import CONFIG


# Create your views here.
def login_view(request: HttpRequest) -> HttpResponse:
    if request.method == "POST":
        username = request.POST["username"]
        password = request.POST["password"]
        locale = request.POST["locale"]

        # authenticate the user,
        user = authenticate(request, username=username, password=password)

        if user is not None:
            # create django session
            login(request, user)

            # set session cookie for the legacy app
            try:
                legacy_session_key = login_to_legacy(username, password, locale)

                response: HttpResponse = HttpResponseRedirect("/showbuilder")
                response.set_cookie(
                    key="PHPSESSID",
                    value=legacy_session_key,
                    max_age=60,
                    samesite="Strict",
                    httponly=True,
                    secure=CONFIG.general.public_url.startswith("https://"),
                )
                return response

            except requests.HTTPError:
                # If the login to the legacy did not work terminate the local
                # session as well
                logout(request)
                return render(
                    request,
                    "login.html",
                    {"message": "Login to legacy failed, exception"},
                )

        else:
            # Show login page if the login failed
            return render(request, "login.html", {"message": "Login to django failed"})
    else:
        # show login page on get request
        return render(request, "login.html", {})


def logout_view(request: HttpRequest) -> HttpResponse:
    logout(request)

    # Redirect to the startpage
    response = HttpResponseRedirect("/")

    if "PHPSESSID" in request.COOKIES:
        try:
            logout_from_legacy(request.COOKIES["PHPSESSID"])
        except requests.HTTPError:
            # If we can't terminate the session we should at lease unset the
            # session key in the cookie
            response.delete_cookie(key="PHPSESSID", samesite="Strict")
    return response


"""
Returns the base url which the legacy can be reached
During development the publi_url is set to localhost,
but as legacy and api may be running in
different containers they need to proxy through nginx
If public_url is as fqdm this should not happen
as the request then are always external
"""


def get_legacy_base_url():
    if "LIBRETIME_LEGACY_URL" in os.environ:
        return os.environ["LIBRETIME_LEGACY_URL"]
    else:
        return CONFIG.general.public_url


"""
Queries the legacy app with the login credentials to
create a php session
Returns the session_key
"""


def login_to_legacy(username, password, locale):
    base_url = get_legacy_base_url()
    abs_url = urllib.parse.urljoin(base_url, "/login")
    headers = {
        # If the Referer is not the configured base_url legacy will ignore the request
        "Referer": str(CONFIG.general.public_url),
        "Content-Type": "application/x-www-form-urlencoded",
    }
    data = {
        "username": username,
        "password": password,
        "locale": locale,
        "submit": "Login",
    }

    response: requests.Response = requests.post(
        abs_url, data=data, headers=headers, allow_redirects=False
    )

    # The response should be a 302 redirect
    if response.status_code == 302:
        return response.cookies["PHPSESSID"]
    else:
        raise requests.HTTPError()


"""
    Query the legacy app with a session_cookie to
    terminate an active session
"""


def logout_from_legacy(session_key):
    base_url = get_legacy_base_url()
    abs_url = urllib.parse.urljoin(base_url, "/logout")
    cookies = {"PHPSESSID": session_key}

    response = requests.get(abs_url, cookies=cookies)

    if response.stauts_code != 302:
        raise requests.HTTPError()
