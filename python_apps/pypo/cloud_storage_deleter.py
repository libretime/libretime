#!/usr/bin/python

import sys

from libcloud.storage.providers import get_driver
from libcloud.storage.types import Provider, ContainerDoesNotExistError, ObjectDoesNotExistError

provider = str(sys.argv[0])
bucket = str(sys.argv[1])
api_key = str(sys.argv[2])
api_key_secret = str(sys.argv[3])
obj_name = str(sys.argv[4])

cls = get_driver(getattr(Provider, provider))
driver = cls(api_key, api_key_secret)

try:
    cloud_obj = driver.get_object(container_name=bucket,
                                  object_name=obj_name)
    filesize = getattr(cloud_obj, 'size')
    driver.delete_object(obj=cloud_obj)
except ObjectDoesNotExistError:
    raise Exception("Could not find object on %s" % provider)

