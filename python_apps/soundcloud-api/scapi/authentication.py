##    SouncCloudAPI implements a Python wrapper around the SoundCloud RESTful
##    API
##
##    Copyright (C) 2008  Diez B. Roggisch
##    Contact mailto:deets@soundcloud.com
##
##    This library is free software; you can redistribute it and/or
##    modify it under the terms of the GNU Lesser General Public
##    License as published by the Free Software Foundation; either
##    version 2.1 of the License, or (at your option) any later version.
##
##    This library is distributed in the hope that it will be useful,
##    but WITHOUT ANY WARRANTY; without even the implied warranty of
##    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
##    Lesser General Public License for more details.
##
##    You should have received a copy of the GNU Lesser General Public
##    License along with this library; if not, write to the Free Software
##    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

import base64
import time, random
import urlparse
import hmac
import hashlib
from scapi.util import escape
import logging


USE_DOUBLE_ESCAPE_HACK = True
"""
There seems to be an uncertainty on the way
parameters are to be escaped. For now, this
variable switches between two escaping mechanisms.

If True, the passed parameters - GET or POST - are
escaped *twice*.
"""

logger = logging.getLogger(__name__)

class OAuthSignatureMethod_HMAC_SHA1(object):

    FORBIDDEN = ['realm', 'oauth_signature']

    def get_name(self):
        return 'HMAC-SHA1'

    def build_signature(self, request, parameters, consumer_secret, token_secret, oauth_parameters):
        if logger.level == logging.DEBUG:
            logger.debug("request: %r", request)
            logger.debug("parameters: %r", parameters)
            logger.debug("consumer_secret: %r", consumer_secret)
            logger.debug("token_secret: %r", token_secret)
            logger.debug("oauth_parameters: %r", oauth_parameters)

            
        temp = {}
        temp.update(oauth_parameters)
        for p in self.FORBIDDEN:
            if p in temp:
                del temp[p]
        if parameters is not None:
            temp.update(parameters)
        sig = (
            escape(self.get_normalized_http_method(request)),
            escape(self.get_normalized_http_url(request)),
            self.get_normalized_parameters(temp), # these are escaped in the method already
        )
        
        key = '%s&' % consumer_secret
        if token_secret is not None:
            key += token_secret
        raw = '&'.join(sig)
        logger.debug("raw basestring: %s", raw)
        logger.debug("key: %s", key)
        # hmac object
        hashed = hmac.new(key, raw, hashlib.sha1)
        # calculate the digest base 64
        signature = escape(base64.b64encode(hashed.digest()))
        return signature


    def get_normalized_http_method(self, request):
        return request.get_method().upper()


    # parses the url and rebuilds it to be scheme://host/path
    def get_normalized_http_url(self, request):
        url = request.get_full_url()
        parts = urlparse.urlparse(url)
        url_string = '%s://%s%s' % (parts.scheme, parts.netloc, parts.path)
        return url_string


    def get_normalized_parameters(self, params):
        if params is None:
            params = {}
        try:
            # exclude the signature if it exists
            del params['oauth_signature']
        except:
            pass
        key_values = []
        
        for key, values in params.iteritems():
            if isinstance(values, file):
                continue
            if isinstance(values, (int, long, float)):
                values = str(values)
            if isinstance(values, (list, tuple)):
                values = [str(v) for v in values]
            if isinstance(values, basestring):
                values = [values]
            if USE_DOUBLE_ESCAPE_HACK and not key.startswith("ouath"):
                key = escape(key)                
            for v in values:
                v = v.encode("utf-8")
                key = key.encode("utf-8")
                if USE_DOUBLE_ESCAPE_HACK and not key.startswith("oauth"):
                    # this is a dirty hack to make the
                    # thing work with the current server-side
                    # implementation. Or is it by spec? 
                    v = escape(v)
                key_values.append(escape("%s=%s" % (key, v)))
        # sort lexicographically, first after key, then after value
        key_values.sort()
        # combine key value pairs in string
        return escape('&').join(key_values)


class OAuthAuthenticator(object):
    OAUTH_API_VERSION = '1.0'
    AUTHORIZATION_HEADER = "Authorization"

    def __init__(self, consumer=None, consumer_secret=None, token=None, secret=None, signature_method=OAuthSignatureMethod_HMAC_SHA1()):
        if consumer == None:
          raise ValueError("The consumer key must be passed for all public requests; it may not be None")
        self._consumer, self._token, self._secret = consumer, token, secret
        self._consumer_secret = consumer_secret
        self._signature_method = signature_method
        random.seed()


    def augment_request(self, req, parameters, use_multipart=False, oauth_callback=None, oauth_verifier=None):
        oauth_parameters = {
            'oauth_consumer_key':     self._consumer,
            'oauth_timestamp':        self.generate_timestamp(),
            'oauth_nonce':            self.generate_nonce(),
            'oauth_version':          self.OAUTH_API_VERSION,
            'oauth_signature_method': self._signature_method.get_name(),
            #'realm' : "http://soundcloud.com",
            }
        if self._token is not None:
            oauth_parameters['oauth_token'] = self._token

        if oauth_callback is not None:
            oauth_parameters['oauth_callback'] = oauth_callback

        if oauth_verifier is not None:
            oauth_parameters['oauth_verifier'] = oauth_verifier
            
        # in case we upload large files, we don't
        # sign the request over the parameters
        # There's a bug in the OAuth 1.0 (and a) specs that says that PUT request should omit parameters from the base string.
        # This is fixed in the IETF draft, don't know when this will be released though. - HT
        if use_multipart or req.get_method() == 'PUT':
            parameters = None

        oauth_parameters['oauth_signature'] = self._signature_method.build_signature(req, 
                                                                                     parameters, 
                                                                                     self._consumer_secret, 
                                                                                     self._secret, 
                                                                                     oauth_parameters)
        def to_header(d):
            return ",".join('%s="%s"' % (key, value) for key, value in sorted(oauth_parameters.items()))

        req.add_header(self.AUTHORIZATION_HEADER, "OAuth  %s" % to_header(oauth_parameters))

    def generate_timestamp(self):
        return int(time.time())# * 1000.0)

    def generate_nonce(self, length=8):
        return ''.join(str(random.randint(0, 9)) for i in range(length))


class BasicAuthenticator(object):
    
    def __init__(self, user, password, consumer, consumer_secret):
        self._base64string = base64.encodestring("%s:%s" % (user, password))[:-1]
        self._x_auth_header = 'OAuth oauth_consumer_key="%s" oauth_consumer_secret="%s"' % (consumer, consumer_secret)

    def augment_request(self, req, parameters):
        req.add_header("Authorization", "Basic %s" % self._base64string)
        req.add_header("X-Authorization", self._x_auth_header)
