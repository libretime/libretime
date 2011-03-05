from __future__ import with_statement
import os
import tempfile
import itertools
from ConfigParser import SafeConfigParser
import pkg_resources
import scapi
import scapi.authentication
import logging
import webbrowser

logger = logging.getLogger(__name__)
logger.setLevel(logging.DEBUG)
_logger = logging.getLogger("scapi")
#_logger.setLevel(logging.DEBUG)

RUN_INTERACTIVE_TESTS = False
USE_OAUTH = True

TOKEN  = "FjNE9aRTg8kpxuOjzwsX8Q"
SECRET = "NP5PGoyKcQv64E0aZgV4CRNzHfPwR4QghrWoqEgEE"
CONSUMER = "EEi2URUfM97pAAxHTogDpQ"
CONSUMER_SECRET = "NFYd8T3i4jVKGZ9TMy9LHaBQB3Sh8V5sxBiMeMZBow"
API_HOST = "api.soundcloud.dev:3000"
USER = ""
PASSWORD = ""

CONFIG_NAME = "soundcloud.cfg"

CONNECTOR = None
ROOT = None
def setup():
    global CONNECTOR, ROOT
    # load_config()
    #scapi.ApiConnector(host='192.168.2.101:3000', user='tiga', password='test')
    #scapi.ApiConnector(host='sandbox-api.soundcloud.com:3030', user='tiga', password='test')
    scapi.USE_PROXY = False
    scapi.PROXY = 'http://127.0.0.1:10000/'

    if USE_OAUTH:
        authenticator = scapi.authentication.OAuthAuthenticator(CONSUMER, 
                                                                CONSUMER_SECRET,
                                                                TOKEN, 
                                                                SECRET)
    else:
        authenticator = scapi.authentication.BasicAuthenticator(USER, PASSWORD, CONSUMER, CONSUMER_SECRET)
    
    logger.debug("API_HOST: %s", API_HOST)
    CONNECTOR = scapi.ApiConnector(host=API_HOST, 
                                    authenticator=authenticator)
    ROOT = scapi.Scope(CONNECTOR)

def load_config(config_name=None):
    global TOKEN, SECRET, CONSUMER_SECRET, CONSUMER, API_HOST, USER, PASSWORD
    if config_name is None:
        config_name = CONFIG_NAME
    parser = SafeConfigParser()
    current = os.getcwd()
    while current:
        name = os.path.join(current, config_name)
        if os.path.exists(name):
            parser.read([name])
            TOKEN = parser.get('global', 'accesstoken')
            SECRET = parser.get('global', 'accesstoken_secret')
            CONSUMER = parser.get('global', 'consumer')
            CONSUMER_SECRET = parser.get('global', 'consumer_secret')
            API_HOST = parser.get('global', 'host')
            USER = parser.get('global', 'user')
            PASSWORD = parser.get('global', 'password')
            logger.debug("token: %s", TOKEN)
            logger.debug("secret: %s", SECRET)
            logger.debug("consumer: %s", CONSUMER)
            logger.debug("consumer_secret: %s", CONSUMER_SECRET)
            logger.debug("user: %s", USER)
            logger.debug("password: %s", PASSWORD)
            logger.debug("host: %s", API_HOST)
            break
        new_current = os.path.dirname(current)
        if new_current == current:
            break
        current = new_current
    

def test_load_config():
    base = tempfile.mkdtemp()
    oldcwd = os.getcwd()
    cdir = os.path.join(base, "foo")
    os.mkdir(cdir)
    os.chdir(cdir)
    test_config = """
[global]
host=host
consumer=consumer
consumer_secret=consumer_secret
accesstoken=accesstoken
accesstoken_secret=accesstoken_secret
user=user
password=password
"""
    with open(os.path.join(base, CONFIG_NAME), "w") as cf:
        cf.write(test_config)
    load_config()
    assert TOKEN == "accesstoken" and SECRET == "accesstoken_secret" and API_HOST == 'host'
    assert CONSUMER == "consumer" and CONSUMER_SECRET == "consumer_secret"
    assert USER == "user" and PASSWORD == "password"
    os.chdir(oldcwd)
    load_config()
    
    
def test_connect():
    sca = ROOT
    quite_a_few_users = list(itertools.islice(sca.users(), 0, 127))

    logger.debug(quite_a_few_users)
    assert isinstance(quite_a_few_users, list) and isinstance(quite_a_few_users[0], scapi.User)
    user = sca.me()
    logger.debug(user)
    assert isinstance(user, scapi.User)
    contacts = list(user.contacts())
    assert isinstance(contacts, list)
    assert isinstance(contacts[0], scapi.User)
    logger.debug(contacts)
    tracks = list(user.tracks())
    assert isinstance(tracks, list)
    assert isinstance(tracks[0], scapi.Track)
    logger.debug(tracks)


def test_access_token_acquisition():
    """
    This test is commented out because it needs user-interaction.
    """
    if not RUN_INTERACTIVE_TESTS:
        return
    oauth_authenticator = scapi.authentication.OAuthAuthenticator(CONSUMER, 
                                                                  CONSUMER_SECRET,
                                                                  None, 
                                                                  None)

    sca = scapi.ApiConnector(host=API_HOST, authenticator=oauth_authenticator)
    token, secret = sca.fetch_request_token()
    authorization_url = sca.get_request_token_authorization_url(token)
    webbrowser.open(authorization_url)
    raw_input("please press return")
    oauth_authenticator = scapi.authentication.OAuthAuthenticator(CONSUMER, 
                                                                  CONSUMER_SECRET,
                                                                  token, 
                                                                  secret)

    sca = scapi.ApiConnector(API_HOST, authenticator=oauth_authenticator)
    token, secret = sca.fetch_access_token()
    logger.info("Access token: '%s'", token)
    logger.info("Access token secret: '%s'", secret)
    oauth_authenticator = scapi.authentication.OAuthAuthenticator(CONSUMER, 
                                                                  CONSUMER_SECRET,
                                                                  token, 
                                                                  secret)

    sca = scapi.ApiConnector(API_HOST, authenticator=oauth_authenticator)
    test_track_creation()

def test_track_creation():
    sca = ROOT
    track = sca.Track.new(title='bar')
    assert isinstance(track, scapi.Track)

def test_track_update():
    sca = ROOT
    track = sca.Track.new(title='bar')
    assert isinstance(track, scapi.Track)
    track.title='baz'
    track = sca.Track.get(track.id)
    assert track.title == "baz"

def test_scoped_track_creation():
    sca = ROOT
    user = sca.me()
    track = user.tracks.new(title="bar")
    assert isinstance(track, scapi.Track)

def test_upload():
    assert pkg_resources.resource_exists("scapi.tests.test_connect", "knaster.mp3")
    data = pkg_resources.resource_stream("scapi.tests.test_connect", "knaster.mp3")
    sca = ROOT
    user = sca.me()
    logger.debug(user)
    asset = sca.assets.new(filedata=data)
    assert isinstance(asset, scapi.Asset)
    logger.debug(asset)
    tracks = list(user.tracks())
    track = tracks[0]
    track.assets.append(asset)

def test_contact_list():
    sca = ROOT
    user = sca.me()
    contacts = list(user.contacts())
    assert isinstance(contacts, list)
    assert isinstance(contacts[0], scapi.User)

def test_permissions():
    sca = ROOT
    user = sca.me()
    tracks = itertools.islice(user.tracks(), 1)
    for track in tracks:
        permissions = list(track.permissions())
        logger.debug(permissions)
        assert isinstance(permissions, list)
        if permissions:
            assert isinstance(permissions[0], scapi.User)

def test_setting_permissions():
    sca = ROOT
    me = sca.me()
    track = sca.Track.new(title='bar', sharing="private")
    assert track.sharing == "private"
    users = itertools.islice(sca.users(), 10)
    users_to_set = [user  for user in users if user != me]
    assert users_to_set, "Didn't find any suitable users"
    track.permissions = users_to_set
    assert set(track.permissions()) == set(users_to_set)

def test_setting_comments():
    sca = ROOT
    user = sca.me()
    track = sca.Track.new(title='bar', sharing="private")
    comment = sca.Comment.create(body="This is the body of my comment", timestamp=10)
    track.comments = comment
    assert track.comments().next().body == comment.body
    

def test_setting_comments_the_way_shawn_says_its_correct():
    sca = ROOT
    track = sca.Track.new(title='bar', sharing="private")
    cbody = "This is the body of my comment"
    track.comments.new(body=cbody, timestamp=10)
    assert list(track.comments())[0].body == cbody

def test_contact_add_and_removal():
    sca = ROOT
    me = sca.me()
    for user in sca.users():
        if user != me:            
            user_to_set = user
            break

    contacts = list(me.contacts())
    if user_to_set in contacts:
        me.contacts.remove(user_to_set)

    me.contacts.append(user_to_set)

    contacts = list(me.contacts() )
    assert user_to_set.id in [c.id for c in contacts]

    me.contacts.remove(user_to_set)

    contacts = list(me.contacts() )
    assert user_to_set not in contacts


def test_favorites():
    sca = ROOT
    me = sca.me()

    favorites = list(me.favorites())
    assert favorites == [] or isinstance(favorites[0], scapi.Track)

    track = None
    for user in sca.users():
        if user == me:
            continue
        for track in user.tracks():
            break
        if track is not None:
            break
    
    me.favorites.append(track)

    favorites = list(me.favorites())
    assert track in favorites

    me.favorites.remove(track)

    favorites = list(me.favorites())
    assert track not in favorites

def test_large_list():
    sca = ROOT
    tracks = list(sca.tracks())
    if len(tracks) < scapi.ApiConnector.LIST_LIMIT:
        for i in xrange(scapi.ApiConnector.LIST_LIMIT):            
            scapi.Track.new(title='test_track_%i' % i)
    all_tracks = sca.tracks()
    assert not isinstance(all_tracks, list)
    all_tracks = list(all_tracks)
    assert len(all_tracks) > scapi.ApiConnector.LIST_LIMIT


def test_events():
    events = list(ROOT.events())
    assert isinstance(events, list)
    assert isinstance(events[0], scapi.Event)

def test_me_having_stress():
    sca = ROOT
    for _ in xrange(20):
        setup()
        sca.me()

def test_non_global_api():
    root = scapi.Scope(CONNECTOR)
    me = root.me()
    assert isinstance(me, scapi.User)

    # now get something *from* that user
    favorites = list(me.favorites())
    assert favorites

def test_playlists():
    sca = ROOT
    playlists = list(itertools.islice(sca.playlists(), 0, 127))
    found = False
    for playlist in playlists:
        tracks = playlist.tracks
        if not isinstance(tracks, list):
            tracks = [tracks]
        for trackdata in tracks:
            print trackdata
            user = trackdata.user
            print user
            print user.tracks()
        print playlist.user
        break
