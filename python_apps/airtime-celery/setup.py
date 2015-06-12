from setuptools import setup
from subprocess import call
import os
import sys
from glob import glob

install_args = ['install', 'install_data', 'develop']

# XXX Definitely not the best way of doing this... quite possibly the literal worst!
if sys.argv[1] in install_args:
    data_files = [('/etc/default', ['install/conf/airtime-celery']),
                  ('/etc/init.d', ['install/initd/airtime-celery'])]
    for i, arg in enumerate(sys.argv):
        if "--dev-env" in arg:
            env = arg.split('=')[1]
            data_files = [('/etc/default', ['install/conf/airtime-celery-%s' % env]),
                          ('/etc/init.d', ['install/initd/airtime-celery-%s' % env])]
            sys.argv.remove(arg)
        elif arg == "--all-envs":
            data_files = ([('/etc/default', glob('install/conf/*')),
                           ('/etc/init.d', glob('install/initd/*'))])
            sys.argv.remove(arg)
else:
    scripts = data_files = []


def postinst():
    print "Reloading initctl configuration"
    call(['initctl', 'reload-configuration'])
    # Make /etc/init.d file executable and set proper
    # permissions for the defaults config file
    for f in glob('/etc/init.d/airtime-celery*'):
        os.chmod(f, 0755)
    for f in glob('/etc/default/airtime-celery*'):
        os.chmod(f, 0640)
    # print "Setting Celery to start on boot"
    # call(['update-rc.d', 'airtime-celery', 'defaults'])
    print "Run \"sudo service airtime-celery restart\" or \"sudo service airtime-celery-%DEV_ENV% restart\" now."

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
