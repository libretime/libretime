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

import urllib
import urllib2
import re

import logging
import simplejson
import cgi
from scapi.MultipartPostHandler import MultipartPostHandler
from inspect import isclass
import urlparse
from scapi.authentication import BasicAuthenticator
from scapi.util import (
    escape,
    MultiDict,
    )

logging.basicConfig()
logger = logging.getLogger(__name__)

USE_PROXY = False
"""
Something like http://127.0.0.1:10000/
"""
PROXY = ''


"""
Endpoints, for reference:  
The url Soundcould offers to obtain request-tokens: 'http://api.soundcloud.com/oauth/request_token'
The url Soundcould offers to exchange access-tokens for request-tokens:  'http://api.soundcloud.com/oauth/access_token'
The url Soundcould offers to make users authorize a concrete request token:  'http://api.soundcloud.com/oauth/authorize'
"""

__all__ = ['SoundCloudAPI', 'USE_PROXY', 'PROXY']


class NoResultFromRequest(Exception):
    pass

class InvalidMethodException(Exception):

    def __init__(self, message):
        self._message = message
        Exception.__init__(self)

    def __repr__(self):
        res = Exception.__repr__(self)
        res += "\n" 
        res += "-" * 10
        res += "\nmessage:\n\n"
        res += self._message
        return res

class UnknownContentType(Exception):
    def __init__(self, msg):
        Exception.__init__(self)
        self._msg = msg

    def __repr__(self):
        return self.__class__.__name__ + ":" + self._msg

    def __str__(self):
        return str(self)

class PartitionCollectionGenerator():
  def __init__(self, scope, method, Gen, NextPartition):
    self.NextPartition = NextPartition
    self.Generator = Gen
    self.Scope = scope
    self.Method = method
    
  def __iter__(self):
    return self.Generator
  def next(self):
    return self.Generator.next()  
  def __call__(self, someParam):
      self.someParam = someParam
      for line in self.content:
          if line == someParam:
            yield line
            
  def GetNextPartition(self):
    if self.NextPartition != None:
        method = re.search('(^[a-z]+)', self.Method).group(0)
        params = re.search('\?.+', self.NextPartition).group(0)
        params = params.replace('u0026', '&')

        return self.Scope._call(method, params)
    else:
        return None

class ApiConnector(object):
    """
    The ApiConnector holds all the data necessary to authenticate against
    the soundcloud-api. You can instantiate several connectors if you like, but usually one 
    should be sufficient.
    """

    """
    SoundClound imposes a maximum on the number of returned items. This value is that
    maximum.
    """
    LIST_LIMIT = 50

    """
    The query-parameter that is used to request results beginning from a certain offset.
    """
    LIST_OFFSET_PARAMETER = 'offset'
    """
    The query-parameter that is used to request results being limited to a certain amount.
    
    Currently this is of no use and just for completeness sake.
    """
    LIST_LIMIT_PARAMETER = 'limit'

    def __init__(self, host, user=None, password=None, authenticator=None, base="", collapse_scope=True):
        """
        Constructor for the API-Singleton. Use it once with parameters, and then the
        subsequent calls internal to the API will work.

        @type host: str
        @param host: the host to connect to, e.g. "api.soundcloud.com". If a port is needed, use
                "api.soundcloud.com:1234"
        @type user: str
        @param user: if given, the username for basic HTTP authentication
        @type password: str
        @param password: if the user is given, you have to give a password as well
        @type authenticator: OAuthAuthenticator | BasicAuthenticator
        @param authenticator: the authenticator to use, see L{scapi.authentication}
        """
        self.host = host
        self.host = self.host.replace("http://", "")
        if self.host[-1] == '/': # Remove a trailing slash, but leave other slashes alone
          self.host = self.host[0:-1]
        
        if authenticator is not None:
            self.authenticator = authenticator
        elif user is not None and password is not None:
            self.authenticator = BasicAuthenticator(user, password)
        self._base = base
        self.collapse_scope = collapse_scope

    def normalize_method(self, method):
        """ 
        This method will take a method that has been part of a redirect of some sort
        and see if it's valid, which means that it's located beneath our base. 
        If yes, we return it normalized without that very base.
        """
        _, _, path, _, _, _ = urlparse.urlparse(method)
        if path.startswith("/"):
            path = path[1:]
        # if the base is "", we return the whole path,
        # otherwise normalize it away
        if self._base == "":
            return path
        if path.startswith(self._base):
            return path[len(self._base)-1:]
        raise InvalidMethodException("Not a valid API method: %s" % method)


    
    def fetch_request_token(self, url=None, oauth_callback="oob", oauth_verifier=None):
        """
        Helper-function for a registered consumer to obtain a request token, as
        used by oauth.

        Use it like this:

        >>> oauth_authenticator = scapi.authentication.OAuthAuthenticator(CONSUMER, 
                                                                  CONSUMER_SECRET,
                                                                  None, 
                                                                  None)

        >>> sca = scapi.ApiConnector(host=API_HOST, authenticator=oauth_authenticator)
        >>> token, secret = sca.fetch_request_token()
        >>> authorization_url = sca.get_request_token_authorization_url(token)

        Please note the None passed as  token & secret to the authenticator.
        """
        request_url = "http://" + self.host + "/oauth/request_token"
        if url is None:
            url = request_url
        req = urllib2.Request(url)        
        self.authenticator.augment_request(req, None, oauth_callback=oauth_callback, oauth_verifier=oauth_verifier)
        handlers = []
        if USE_PROXY:
            handlers.append(urllib2.ProxyHandler({'http' : PROXY}))
        opener = urllib2.build_opener(*handlers)
        handle = opener.open(req, None)
        info = handle.info()
        content = handle.read()
        params = cgi.parse_qs(content, keep_blank_values=False)
        key = params['oauth_token'][0]
        secret = params['oauth_token_secret'][0]
        return key, secret


    def fetch_access_token(self, oauth_verifier):
        """
        Helper-function for a registered consumer to exchange an access token for
        a request token.

        Use it like this:

        >>> oauth_authenticator = scapi.authentication.OAuthAuthenticator(CONSUMER, 
                                                                  CONSUMER_SECRET,
                                                                  request_token, 
                                                                  request_token_secret)

        >>> sca = scapi.ApiConnector(host=API_HOST, authenticator=oauth_authenticator)
        >>> token, secret = sca.fetch_access_token()

        Please note the values passed as token & secret to the authenticator.
        """
        access_token_url = "http://" + self.host + "/oauth/access_token"
        return self.fetch_request_token(access_token_url, oauth_verifier=oauth_verifier)
    

    def get_request_token_authorization_url(self, token):
        """
        Simple helper function to generate the url needed
        to ask a user for request token authorization.
        
        See also L{fetch_request_token}.

        Possible usage:

        >>> import webbrowser
        >>> sca = scapi.ApiConnector()
        >>> authorization_url = sca.get_request_token_authorization_url(token)
        >>> webbrowser.open(authorization_url)
        """
        
        auth_url = self.host.split("/")[0]
        auth_url = "http://" + auth_url + "/oauth/authorize"
        auth_url = auth_url.replace("api.", "")
        return "%s?oauth_token=%s" % (auth_url, token)


 
class SCRedirectHandler(urllib2.HTTPRedirectHandler):
    """
    A urllib2-Handler to deal with the redirects the RESTful API of SC uses.
    """
    alternate_method = None

    def http_error_303(self, req, fp, code, msg, hdrs):
        """
        In case of return-code 303 (See-other), we have to store the location we got
        because that will determine the actual type of resource returned.
        """
        self.alternate_method = hdrs['location']
        # for oauth, we need to re-create the whole header-shizzle. This
        # does it - it recreates a full url and signs the request
        new_url = self.alternate_method
#         if USE_PROXY:
#             import pdb; pdb.set_trace()
#             old_url = req.get_full_url()
#             protocol, host, _, _, _, _ = urlparse.urlparse(old_url)
#             new_url = urlparse.urlunparse((protocol, host, self.alternate_method, None, None, None))
        req = req.recreate_request(new_url)
        return urllib2.HTTPRedirectHandler.http_error_303(self, req, fp, code, msg, hdrs)

    def http_error_201(self, req, fp, code, msg, hdrs):
        """
        We fake a 201 being a 303 so that our redirection-scheme takes place
        for the 201 the API throws in case we created something. If the location is
        not available though, that means that whatever we created has succeded - without
        being a named resource. Assigning an asset to a track is an example of such
        case.
        """
        if 'location' not in hdrs:
            raise NoResultFromRequest()
        return self.http_error_303(req, fp, 303, msg, hdrs)

class Scope(object):
    """
    The basic means to query and create resources. The Scope uses the L{ApiConnector} to
    create the proper URIs for querying or creating resources. 

    For accessing resources from the root level, you explcitly create a Scope and pass it
    an L{ApiConnector}-instance. Then you can query it 
    or create new resources like this:

    >>> connector = scapi.ApiConnector(host='host', user='user', password='password') # initialize the API
    >>> scope = scapi.Scope(connector) # get the root scope
    >>> users = list(scope.users())
    [<scapi.User object at 0x12345>, ...]

    Please not that all resources that are lists are returned as B{generator}. So you need
    to either iterate over them, or call list(resources) on them.

    When accessing resources that belong to another resource, like contacts of a user, you access
    the parent's resource scope implicitly through the resource instance like this:

    >>> user = scope.users().next()
    >>> list(user.contacts())
    [<scapi.Contact object at 0x12345>, ...]

    """
    def __init__(self, connector, scope=None, parent=None):
        """
        Create the Scope. It can have a resource as scope, and possibly a parent-scope.

        @param connector: The connector to use.
        @type connector: ApiConnector
        @type scope: scapi.RESTBase
        @param scope: the resource to make this scope belong to
        @type parent: scapi.Scope
        @param parent: the parent scope of this scope
        """

        if scope is None:
            scope = ()
        else:
            scope = scope,
        if parent is not None:
            scope = parent._scope + scope
        self._scope = scope
        self._connector = connector

    def _get_connector(self):
        return self._connector


    def oauth_sign_get_request(self, url):
        """
        This method will take an arbitrary url, and rewrite it
        so that the current authenticator's oauth-headers are appended
        as query-parameters.

        This is used in streaming and downloading, because those content
        isn't served from the SoundCloud servers themselves.

        A usage example would look like this:

        >>> sca = scapi.Scope(connector)
        >>> track = sca.tracks(params={
              "filter" : "downloadable",
              }).next()

        
        >>> download_url = track.download_url
        >>> signed_url = track.oauth_sign_get_request(download_url)
        >>> data = urllib2.urlopen(signed_url).read()

        """
        scheme, netloc, path, params, query, fragment = urlparse.urlparse(url)

        req = urllib2.Request(url)

        all_params = {}
        if query:
            all_params.update(cgi.parse_qs(query))

        if not all_params:
            all_params = None
            
        self._connector.authenticator.augment_request(req, all_params, False)

        auth_header = req.get_header("Authorization")
        auth_header = auth_header[len("OAuth  "):]

        query_params = []
        if query:
            query_params.append(query)

        for part in auth_header.split(","):
            key, value = part.split("=")
            assert key.startswith("oauth")
            value = value[1:-1]
            query_params.append("%s=%s" % (key, value))

        query = "&".join(query_params)
        url = urlparse.urlunparse((scheme, netloc, path, params, query, fragment))
        return url


    def _create_request(self, url, connector, parameters, queryparams, alternate_http_method=None, use_multipart=False):
        """
        This method returnes the urllib2.Request to perform the actual HTTP-request.

        We return a subclass that overload the get_method-method to return a custom method like "PUT".
        Additionally, the request is enhanced with the current authenticators authorization scheme
        headers.

        @param url: the destination url
        @param connector: our connector-instance
        @param parameters: the POST-parameters to use.
        @type parameters: None|dict<str, basestring|list<basestring>>
        @param queryparams: the queryparams to use
        @type queryparams: None|dict<str, basestring|list<basestring>>
        @param alternate_http_method: an alternate HTTP-method to use
        @type alternate_http_method: str
        @return: the fully equipped request
        @rtype: urllib2.Request
        """
        class MyRequest(urllib2.Request):
            def get_method(self):
                if alternate_http_method is not None:
                    return alternate_http_method
                return urllib2.Request.get_method(self)

            def has_data(self):
                return parameters is not None

            def augment_request(self, params, use_multipart=False):
                connector.authenticator.augment_request(self, params, use_multipart)

            @classmethod
            def recreate_request(cls, location):
                return self._create_request(location, connector, None, None)
        
        req = MyRequest(url)
        all_params = {}
        if parameters is not None:
            all_params.update(parameters)
        if queryparams is not None:
            all_params.update(queryparams)
        if not all_params:
            all_params = None
        req.augment_request(all_params, use_multipart)
        req.add_header("Accept", "application/json")
        return req


    def _create_query_string(self, queryparams):
        """
        Small helpermethod to create the querystring from a dict.

        @type queryparams: None|dict<str, basestring|list<basestring>>
        @param queryparams: the queryparameters.
        @return: either the empty string, or a "?" followed by the parameters joined by "&"
        @rtype: str
        """
        if not queryparams:
            return ""
        h = []
        for key, values in queryparams.iteritems():
            if isinstance(values, (int, long, float)):
                values = str(values)
            if isinstance(values, basestring):
                values = [values]
            for v in values:
                v = v.encode("utf-8")
                h.append("%s=%s" % (key, escape(v)))
        return "?" + "&".join(h)


    def _call(self, method, *args, **kwargs):
        """
        The workhorse. It's complicated, convoluted and beyond understanding of a mortal being.

        You have been warned.
        """

        queryparams = {}
        __offset__ = ApiConnector.LIST_LIMIT
        if "__offset__" in kwargs:
            offset = kwargs.pop("__offset__")
            queryparams['offset'] = offset
            __offset__ = offset + ApiConnector.LIST_LIMIT

        if "params" in kwargs:
            queryparams.update(kwargs.pop("params"))

        # create a closure to invoke this method again with a greater offset
        _cl_method = method
        _cl_args = tuple(args)
        _cl_kwargs = {}
        _cl_kwargs.update(kwargs)
        _cl_kwargs["__offset__"] = __offset__
        def continue_list_fetching():
            return self._call(method, *_cl_args, **_cl_kwargs)
        connector = self._get_connector()
        def filelike(v):
            if isinstance(v, file):
                return True
            if hasattr(v, "read"):
                return True
            return False 
        alternate_http_method = None
        if "_alternate_http_method" in kwargs:
            alternate_http_method = kwargs.pop("_alternate_http_method")
        urlparams = kwargs if kwargs else None
        use_multipart = False
        if urlparams is not None:
            fileargs = dict((key, value) for key, value in urlparams.iteritems() if filelike(value))
            use_multipart = bool(fileargs)

        # ensure the method has a trailing /
        if method[-1] != "/":
            method = method + "/"
        if args:
            method = "%s%s" % (method, "/".join(str(a) for a in args))

        scope = ''
        if self._scope:
            scopes = self._scope
            if connector.collapse_scope:
                scopes = scopes[-1:]
            scope = "/".join([sc._scope() for sc in scopes]) + "/"
        url = "http://%(host)s/%(base)s%(scope)s%(method)s%(queryparams)s" % dict(host=connector.host, method=method, base=connector._base, scope=scope, queryparams=self._create_query_string(queryparams))

        # we need to install SCRedirectHandler
        # to gather possible See-Other redirects
        # so that we can exchange our method
        redirect_handler = SCRedirectHandler()
        handlers = [redirect_handler]
        if USE_PROXY:
            handlers.append(urllib2.ProxyHandler({'http' : PROXY}))
        req = self._create_request(url, connector, urlparams, queryparams, alternate_http_method, use_multipart)

        http_method = req.get_method()
        if urlparams is not None:
            logger.debug("Posting url: %s, method: %s", url, http_method)
        else:
            logger.debug("Fetching url: %s, method: %s", url, http_method)

            
        if use_multipart:
            handlers.extend([MultipartPostHandler])            
        else:
            if urlparams is not None:
                urlparams = urllib.urlencode(urlparams.items(), True)
        opener = urllib2.build_opener(*handlers)
        try:
            handle = opener.open(req, urlparams)
        except NoResultFromRequest:
            return None
        except urllib2.HTTPError, e:
            if http_method == "GET" and e.code == 404:
                return None
            raise

        info = handle.info()
        ct = info['Content-Type']
        content = handle.read()
        logger.debug("Content-type:%s", ct)
        logger.debug("Request Content:\n%s", content)
        if redirect_handler.alternate_method is not None:
            method = connector.normalize_method(redirect_handler.alternate_method)
            logger.debug("Method changed through redirect to: <%s>", method)

        try:
            if "application/json" in ct:
                content = content.strip()
                #If linked partitioning is on, extract the URL to the next collection:
                partition_url = None
                if method.find('linked_partitioning=1') != -1:  
                  pattern = re.compile('(next_partition_href":")(.*?)(")')
                  if pattern.search(content):
                    partition_url = pattern.search(content).group(2)

                if not content:
                    content = "{}"
                try:
                    res = simplejson.loads(content)                    
                except:
                    logger.error("Couldn't decode returned json")
                    logger.error(content)
                    raise
                res = self._map(res, method, continue_list_fetching, partition_url)
                return res
            elif len(content) <= 1:
                # this might be the famous SeeOtherSpecialCase which means that
                # all that matters is just the method
                pass
            raise UnknownContentType("%s, returned:\n%s" % (ct, content))
        finally:
            handle.close()

    def _map(self, res, method, continue_list_fetching, next_partition = None):
        """
        This method will take the JSON-result of a HTTP-call and return our domain-objects.

        It's also deep magic, don't look.
        """
        pathparts = reversed(method.split("/"))
        stack = []
        for part in pathparts:
            stack.append(part)
            if part in RESTBase.REGISTRY:
                cls = RESTBase.REGISTRY[part]
                # multiple objects, without linked partitioning
                if isinstance(res, list):
                    def result_gen():
                        count = 0
                        for item in res:
                            yield cls(item, self, stack)
                            count += 1
                        if count == ApiConnector.LIST_LIMIT:
                            for item in continue_list_fetching():
                                yield item
                    logger.debug(res)
                    return PartitionCollectionGenerator(self, method, result_gen(), next_partition)
                # multiple objects, with linked partitioning
                elif isinstance(res, dict) and res.has_key('next_partition_href'):
                  def result_gen():
                      count = 0
                      for item in res['collection']:
                          yield cls(item, self, stack)
                          count += 1
                      if count == ApiConnector.LIST_LIMIT:
                          for item in continue_list_fetching():
                              yield item
                  logger.debug(res)
                  return PartitionCollectionGenerator(self, method, result_gen(), next_partition) 
                else:
                    return cls(res, self, stack)
        logger.debug("don't know how to handle result")
        logger.debug(res)
        return res

    def __getattr__(self, _name):
        """
        Retrieve an API-method or a scoped domain-class. 
        
        If the former, result is a callable that supports the following invocations:

         - calling (...), with possible arguments (positional/keyword), return the resulting resource or list of resources.
           When calling, you can pass a keyword-argument B{params}. This must be a dict or L{MultiDict} and will be used to add additional query-get-parameters.

         - invoking append(resource) on it will PUT the resource, making it part of the current resource. Makes
           sense only if it's a collection of course.

         - invoking remove(resource) on it will DELETE the resource from it's container. Also only usable on collections.

         TODO: describe the latter 
        """
        scope = self

        class api_call(object):
            def __call__(selfish, *args, **kwargs):
                return self._call(_name, *args, **kwargs)

            def new(self, **kwargs):
                """
                Will invoke the new method on the named resource _name, with 
                self as scope.
                """
                cls = RESTBase.REGISTRY[_name]
                return cls.new(scope, **kwargs)

            def append(selfish, resource):
                """
                If the current scope is 
                """
                try:
                  self._call(_name, str(resource.id), _alternate_http_method="PUT")
                except AttributeError:
                  self._call(_name, str(resource), _alternate_http_method="PUT")

            def remove(selfish, resource):
              try:
                self._call(_name, str(resource.id), _alternate_http_method="DELETE")
              except AttributeError:
                self._call(_name, str(resource), _alternate_http_method="DELETE")
                
        if _name in RESTBase.ALL_DOMAIN_CLASSES:
            cls = RESTBase.ALL_DOMAIN_CLASSES[_name]

            class ScopeBinder(object):
                def new(self, *args, **data):

                    d = MultiDict()
                    name = cls._singleton()

                    def unfold_value(key, value):
                        if isinstance(value, (basestring, file)):
                            d.add(key, value)
                        elif isinstance(value, dict):
                            for sub_key, sub_value in value.iteritems():
                                unfold_value("%s[%s]" % (key, sub_key), sub_value)
                        else:
                            # assume iteration else
                            for sub_value in value:
                                unfold_value(key + "[]", sub_value)
                                
                        
                    for key, value in data.iteritems():
                        unfold_value("%s[%s]" % (name, key), value)

                    return scope._call(cls.KIND, **d)
                
                def create(self, **data):
                    return cls.create(scope, **data)

                def get(self, id):
                    return cls.get(scope, id)

                
            return ScopeBinder()
        return api_call()

    def __repr__(self):
        return str(self)

    def __str__(self):
        scopes = self._scope
        base = ""
        if len(scopes) > 1:
            base = str(scopes[-2])
        return base + "/" + str(scopes[-1])


# maybe someday I'll make that work.
# class RESTBaseMeta(type):
#     def __new__(self, name, bases, d):
#         clazz = type(name, bases, d)
#         if 'KIND' in d:
#             kind = d['KIND']
#             RESTBase.REGISTRY[kind] = clazz
#         return clazz

class RESTBase(object):
    """
    The baseclass for all our domain-objects/resources.

    
    """
    REGISTRY = {}
    
    ALL_DOMAIN_CLASSES = {}

    ALIASES = []

    KIND = None

    def __init__(self, data, scope, path_stack=None):
        self.__data = data
        self.__scope = scope
        # try and see if we can/must create an id out of our path
        logger.debug("path_stack: %r", path_stack)
        if path_stack:
            try:
                id = int(path_stack[0])
                self.__data['id'] = id
            except ValueError:
                pass

    def __getattr__(self, name):
        if name in self.__data:
            obj = self.__data[name]
            if name in RESTBase.REGISTRY:
                if isinstance(obj, dict):
                    obj = RESTBase.REGISTRY[name](obj, self.__scope)
                elif isinstance(obj, list):
                    obj = [RESTBase.REGISTRY[name](o, self.__scope) for o in obj]
                else:
                    logger.warning("Found %s in our registry, but don't know what to do with"\
                                   "the object.")
            return obj
        scope = Scope(self.__scope._get_connector(), scope=self, parent=self.__scope)
        return getattr(scope, name)

    def __setattr__(self, name, value):
        """
        This method is used to set a property, a resource or a list of resources as property of the resource the
        method is invoked on.

        For example, to set a comment on a track, do

        >>> sca = scapi.Scope(connector)
        >>> track = scapi.Track.new(title='bar', sharing="private")
        >>> comment = scapi.Comment.create(body="This is the body of my comment", timestamp=10)    
        >>> track.comments = comment

        To set a list of users as permissions, do

        >>> sca = scapi.Scope(connector)
        >>> me = sca.me()
        >>> track = scapi.Track.new(title='bar', sharing="private")
        >>> users = sca.users()
        >>> users_to_set = [user  for user in users[:10] if user != me]
        >>> track.permissions = users_to_set
        
        And finally, to simply change the title of a track, do

        >>> sca = scapi.Scope(connector)
        >>> track = sca.Track.get(track_id)
        >>> track.title = "new_title"
 
        @param name: the property name
        @type name: str
        @param value: the property, resource or resources to set
        @type value: RESTBase | list<RESTBase> | basestring | long | int | float
        @return: None
        """

        # update "private" data, such as __data
        if "_RESTBase__" in name:
            self.__dict__[name] = value
        else:
            if isinstance(value, list) and len(value):
                # the parametername is something like
                # permissions[user_id][]
                # so we try to infer that.
                parameter_name = "%s[%s_id][]" % (name, value[0]._singleton())
                values = [o.id for o in value]
                kwargs = {"_alternate_http_method" : "PUT",
                          parameter_name : values}
                self.__scope._call(self.KIND, self.id, name, **kwargs)
            elif isinstance(value, RESTBase):
                # we got a single instance, so make that an argument
                self.__scope._call(self.KIND, self.id, name, **value._as_arguments())
            else:
                # we have a simple property
                parameter_name = "%s[%s]" % (self._singleton(), name)
                kwargs = {"_alternate_http_method" : "PUT",
                          parameter_name : self._convert_value(value)}
                self.__scope._call(self.KIND, self.id, **kwargs)

    def _as_arguments(self):        
        """
        Converts a resource to a argument-string the way Rails expects it.
        """
        res = {}
        for key, value in self.__data.items():
            value = self._convert_value(value)
            res["%s[%s]" % (self._singleton(), key)] = value
        return res

    def _convert_value(self, value):
        if isinstance(value, unicode):
            value = value.encode("utf-8")
        elif isinstance(value, file):
            pass
        else:
            value = str(value)
        return value

    @classmethod
    def create(cls, scope, **data):
        """
        This is a convenience-method for creating an object that will be passed
        as parameter - e.g. a comment. A usage would look like this:

        >>> sca = scapi.Scope(connector)
        >>> track = sca.Track.new(title='bar', sharing="private")
        >>> comment = sca.Comment.create(body="This is the body of my comment", timestamp=10)    
        >>> track.comments = comment

        """
        return cls(data, scope)

    @classmethod
    def new(cls, scope, **data):
        """
        Create a new resource inside a given Scope. The actual values are in data. 

        So for creating new resources, you have two options:
        
         - create an instance directly using the class:

           >>> scope = scapi.Scope(connector)
           >>> scope.User.new(...)
           <scapi.User object at 0x1234>

         - create a instance in a certain scope:

           >>> scope = scapi.Scope(connector)
           >>> user = scapi.User("1")
           >>> track = user.tracks.new()
           <scapi.Track object at 0x1234>

        @param scope: if not empty, a one-element tuple containing the Scope
        @type scope: tuple<Scope>[1]
        @param data: the data
        @type data: dict
        @return: new instance of the resource
        """
        return getattr(scope, cls.__name__).new(**data)

    @classmethod    
    def get(cls, scope, id):
        """
        Fetch a resource by id.
        
        Simply pass a known id as argument. For example

        >>> sca = scapi.Scope(connector)
        >>> track = sca.Track.get(id)

        """
        return getattr(scope, cls.KIND)(id)
        

    def _scope(self):
        """
        Return the scope this resource lives in, which is the KIND and id
        
        @return: "<KIND>/<id>"
        """
        return "%s/%s" % (self.KIND, str(self.id))

    @classmethod
    def _singleton(cls):
        """
        This method will take a resource name like "users" and
        return the single-case, in the example "user".

        Currently, it's not very sophisticated, only strips a trailing s.
        """
        name = cls.KIND
        if name[-1] == 's':
            return name[:-1]
        raise ValueError("Can't make %s to a singleton" % name)

    def __repr__(self):
        res = []
        res.append("\n\n******\n%s:" % self.__class__.__name__)
        res.append("")
        for key, v in self.__data.iteritems():
            key = str(key)
            if isinstance(v, unicode):
                v = v.encode('utf-8')
            else:
                v = str(v)
            res.append("%s=%s" % (key, v))
        return "\n".join(res)

    def __hash__(self):
        return hash("%s%i" % (self.KIND, self.id))

    def __eq__(self, other):
        """
        Test for equality. 

        Resources are considered equal if the have the same kind and id.
        """
        if not isinstance(other, RESTBase):
            return False        
        res = self.KIND == other.KIND and self.id == other.id
        return res

    def __ne__(self, other):
        return not self == other

class User(RESTBase):
    """
    A user domain object/resource. 
    """
    KIND = 'users'
    ALIASES = ['me', 'permissions', 'contacts', 'user']

class Track(RESTBase):
    """
    A track domain object/resource. 
    """
    KIND = 'tracks'
    ALIASES = ['favorites']

class Comment(RESTBase):
    """
    A comment domain object/resource. 
    """
    KIND = 'comments'

class Event(RESTBase):
    """
    A event domain object/resource. 
    """
    KIND = 'events'

class Playlist(RESTBase):
    """
    A playlist/set domain object/resource
    """
    KIND = 'playlists'

class Group(RESTBase):
    """
    A group domain object/resource
    """
    KIND = 'groups'



# this registers all the RESTBase subclasses.
# One day using a metaclass will make this a tad
# less ugly.
def register_classes():
    g = {}
    g.update(globals())
    for name, cls in [(k, v) for k, v in g.iteritems() if isclass(v) and issubclass(v, RESTBase) and not v == RESTBase]:
        RESTBase.REGISTRY[cls.KIND] = cls
        RESTBase.ALL_DOMAIN_CLASSES[cls.__name__] = cls
        for alias in cls.ALIASES:
            RESTBase.REGISTRY[alias] = cls
        __all__.append(name)
register_classes()
