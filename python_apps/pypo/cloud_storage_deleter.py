#!/usr/bin/python

import sys
import simplejson

from libcloud.storage.providers import get_driver
from libcloud.storage.types import Provider, ObjectDoesNotExistError

provider = str(sys.argv[1])
bucket = str(sys.argv[2])
api_key = str(sys.argv[3])
api_key_secret = str(sys.argv[4])
obj_name = str(sys.argv[5])

cls = get_driver(getattr(Provider, provider))
driver = cls(api_key, api_key_secret)

try:
    cloud_obj = driver.get_object(container_name=bucket,
                                  object_name=obj_name)
    filesize = getattr(cloud_obj, 'size')
    driver.delete_object(obj=cloud_obj)
    
    data = simplejson.dumps({"filesize": filesize})
    print data
except ObjectDoesNotExistError:
    raise Exception("Could not find object on %s in bucket: %s and object: %s" % (provider, bucket, obj_name))

