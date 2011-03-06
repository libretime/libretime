import pkg_resources
import scapi
import scapi.authentication
import urllib
import logging

logger = logging.getLogger(__name__)
logger.setLevel(logging.DEBUG)
_logger = logging.getLogger("scapi")
_logger.setLevel(logging.DEBUG)

TOKEN  = "QcciYu1FSwDSGKAG2mNw"
SECRET = "gJ2ok6ULUsYQB3rsBmpHCRHoFCAPOgK8ZjoIyxzris"
CONSUMER = "Cy2eLPrIMp4vOxjz9icdQ"
CONSUMER_SECRET = "KsBa272x6M2to00Vo5FdvZXt9kakcX7CDIPJoGwTro"

def test_base64_connect():
    scapi.USE_PROXY = True
    scapi.PROXY = 'http://127.0.0.1:10000/'
    scapi.SoundCloudAPI(host='192.168.2.31:3000', authenticator=scapi.authentication.BasicAuthenticator('tiga', 'test'))
    sca = scapi.Scope()
    assert isinstance(sca.me(), scapi.User)


def test_oauth_connect():
    scapi.USE_PROXY = True
    scapi.PROXY = 'http://127.0.0.1:10000/'
    scapi.SoundCloudAPI(host='192.168.2.31:3000', 
                        authenticator=scapi.authentication.OAuthAuthenticator(CONSUMER, 
                                                                              CONSUMER_SECRET,
                                                                              TOKEN, SECRET))

    sca = scapi.Scope()
    assert isinstance(sca.me(), scapi.User)


