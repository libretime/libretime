from setuptools import setup
from subprocess import call
import os
import sys
from glob import glob

install_args = ['install', 'install_data', 'develop']

# XXX Definitely not the best way of doing this...
if sys.argv[1] in install_args and "--no-init-script" not in sys.argv:
    data_files = [('/etc/default', ['install/conf/airtime-celery']),
                  ('/etc/init.d', ['install/initd/airtime-celery'])]
else:
    if "--no-init-script" in sys.argv:
        sys.argv.remove("--no-init-script")
    data_files = []


def postinst():
    # Make /etc/init.d file executable and set proper
    # permissions for the defaults config file
    os.chmod('/etc/init.d/airtime-celery', 0755)
    os.chmod('/etc/default/airtime-celery', 0640)
    print "Reloading initctl configuration"
    call(['initctl', 'reload-configuration'])
    print "Setting Celery to start on boot"
    call(['update-rc.d', 'airtime-celery', 'defaults'])
    print "Run \"sudo service airtime-celery restart\" now."

setup(name='airtime-celery',
      version='0.1',
      description='Airtime Celery service',
      url='http://github.com/sourcefabric/Airtime',
      author='Sourcefabric',
      author_email='duncan.sommerville@sourcefabric.org',
      license='MIT',
      packages=['airtime-celery'],
      install_requires=[
          'soundcloud',
          'celery',
          'kombu'
      ],
      zip_safe=False,
      data_files=data_files)

if data_files:
    postinst()
