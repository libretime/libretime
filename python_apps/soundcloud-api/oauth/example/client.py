'''
Example consumer.
'''
import httplib
import time
import oauth.oauth as oauth
import webbrowser
from scapi import util

SERVER = 'sandbox-soundcloud.com' # Change to soundcloud.com to reach the live site
PORT = 80

REQUEST_TOKEN_URL = 'http://api.' + SERVER + '/oauth/request_token'
ACCESS_TOKEN_URL  = 'http://api.' + SERVER + '/oauth/access_token'
AUTHORIZATION_URL = 'http://'     + SERVER + '/oauth/authorize'

CALLBACK_URL = ''
RESOURCE_URL = "http://api." + SERVER + "/me"

# key and secret granted by the service provider for this consumer application - same as the MockOAuthDataStore
CONSUMER_KEY    = 'JysXkO8ErA4EluFnF5nWg'
CONSUMER_SECRET = 'fauVjm61niGckeufkmMvgUo77oWzRHdMmeylJblHk'

# example client using httplib with headers
class SimpleOAuthClient(oauth.OAuthClient):

    def __init__(self, server, port=httplib.HTTP_PORT, request_token_url='', access_token_url='', authorization_url=''):
        self.server            = server
        self.port              = port
        self.request_token_url = request_token_url
        self.access_token_url  = access_token_url
        self.authorization_url = authorization_url
        self.connection        = httplib.HTTPConnection("%s:%d" % (self.server, self.port))

    def fetch_request_token(self, oauth_request):
        # via headers
        # -> OAuthToken
        print oauth_request.to_url()
        #self.connection.request(oauth_request.http_method, self.request_token_url, headers=oauth_request.to_header()) 
        self.connection.request(oauth_request.http_method, oauth_request.to_url()) 
        response = self.connection.getresponse()
        print "response status", response.status
        return oauth.OAuthToken.from_string(response.read())

    def fetch_access_token(self, oauth_request):
        # via headers
        # -> OAuthToken
        
        # This should proably be elsewhere but stays here for now
        oauth_request.set_parameter("oauth_signature", util.escape(oauth_request.get_parameter("oauth_signature")))
        self.connection.request(oauth_request.http_method, self.access_token_url, headers=oauth_request.to_header()) 
        response = self.connection.getresponse()
        resp = response.read()
        print "*" * 90
        print "response:", resp
        print "*" * 90

        return oauth.OAuthToken.from_string(resp)

    def authorize_token(self, oauth_request):
        webbrowser.open(oauth_request.to_url())
        raw_input("press return when authorizing is finished")

        return

        # via url
        # -> typically just some okay response
        self.connection.request(oauth_request.http_method, oauth_request.to_url()) 
        response = self.connection.getresponse()
        return response.read()

    def access_resource(self, oauth_request):
        print "resource url:", oauth_request.to_url()
        webbrowser.open(oauth_request.to_url())

        return

        # via post body
        # -> some protected resources
        self.connection.request('GET', oauth_request.to_url())
        response = self.connection.getresponse()
        return response.read()

def run_example():

    # setup
    print '** OAuth Python Library Example **'
    client = SimpleOAuthClient(SERVER, PORT, REQUEST_TOKEN_URL, ACCESS_TOKEN_URL, AUTHORIZATION_URL)
    consumer = oauth.OAuthConsumer(CONSUMER_KEY, CONSUMER_SECRET)
    signature_method_plaintext = oauth.OAuthSignatureMethod_PLAINTEXT()
    signature_method_hmac_sha1 = oauth.OAuthSignatureMethod_HMAC_SHA1()
    pause()
    # get request token
    print '* Obtain a request token ...'
    pause()
    oauth_request = oauth.OAuthRequest.from_consumer_and_token(consumer, http_url=client.request_token_url)
    #oauth_request.sign_request(signature_method_plaintext, consumer, None)
    oauth_request.sign_request(signature_method_hmac_sha1, consumer, None)

    print 'REQUEST (via headers)'
    print 'parameters: %s' % str(oauth_request.parameters)
    pause()
    #import pdb; pdb.set_trace()

    token = client.fetch_request_token(oauth_request)
    print 'GOT'
    print 'key: %s' % str(token.key)
    print 'secret: %s' % str(token.secret)
    pause()

    print '* Authorize the request token ...'
    pause()
    oauth_request = oauth.OAuthRequest.from_token_and_callback(token=token, callback=CALLBACK_URL, http_url=client.authorization_url)
    print 'REQUEST (via url query string)'
    print 'parameters: %s' % str(oauth_request.parameters)
    pause()
    # this will actually occur only on some callback
    response = client.authorize_token(oauth_request)
    print 'GOT'
    print response
    pause()

    # get access token
    print '* Obtain an access token ...'
    pause()
    oauth_request = oauth.OAuthRequest.from_consumer_and_token(consumer, token=token, http_url=client.access_token_url)
    oauth_request.sign_request(signature_method_hmac_sha1, consumer, token)
    print 'REQUEST (via headers)'
    print 'parameters: %s' % str(oauth_request.parameters)
    pause()
    token = client.fetch_access_token(oauth_request)
    print 'GOT'
    print 'key: %s' % str(token.key)
    print 'secret: %s' % str(token.secret)
    pause()

    # access some protected resources
    print '* Access protected resources ...'
    pause()
    parameters = {}
    oauth_request = oauth.OAuthRequest.from_consumer_and_token(consumer, token=token, http_method='GET', http_url=RESOURCE_URL, parameters=parameters)
    oauth_request.sign_request(signature_method_hmac_sha1, consumer, token)
    print 'REQUEST (via get body)'
    print 'parameters: %s' % str(oauth_request.parameters)
    pause()
    params = client.access_resource(oauth_request)
    print 'GOT'
    print 'non-oauth parameters: %s' % params
    pause()

def pause():
    print ''
    time.sleep(1)

if __name__ == '__main__':
    run_example()
    print 'Done.'
