from setuptools import setup
from subprocess import call
import sys
import os

script_path = os.path.dirname(os.path.realpath(__file__))
print script_path
os.chdir(script_path)

# Allows us to avoid installing the upstart init script when deploying on Airtime Pro:
if '--no-init-script' in sys.argv:
    data_files = []
    sys.argv.remove('--no-init-script') # super hax
else:
    data_files = [('/etc/init/airtime', ['install/airtime-playout.conf', 'install/airtime-liquidsoap.conf'])]
    print data_files

setup(name='airtime-playout',
      version='1.0',
      description='Airtime Playout Engine',
      url='http://github.com/sourcefabric/Airtime',
      author='sourcefabric',
      license='AGPLv3',
      packages=['pypo'],
      scripts=[
          'bin/airtime-playout',
          'bin/airtime-liquidsoap'
      ],
      install_requires=[
          'amqplib',
          'anyjson',
          'argparse',
          'configobj',
          'docopt',
          'kombu',
          'mutagen',
          'poster',
          'PyDispatcher',
          'pyinotify',
          'pytz',
          'wsgiref'
      ],
      zip_safe=False,
      data_files=data_files)

# Reload the initctl config so that playout services works
if data_files:
    print "Reloading initctl configuration"
    call(['initctl', 'reload-configuration'])
    print "Run \"sudo service airtime-playout start\" and \"sudo service airtime-liquidsoap start\""
