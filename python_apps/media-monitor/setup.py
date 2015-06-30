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
    media_monitor_files = []
    mm2_files = []
    for root, dirnames, filenames in os.walk('media-monitor'):
        for filename in filenames:
            media_monitor_files.append(os.path.join(root, filename))
    for root, dirnames, filenames in os.walk('media-monitor2'):
        for filename in filenames:
            mm2_files.append(os.path.join(root, filename))
        
    data_files = [
                  ('/etc/init', ['install/upstart/airtime-media-monitor.conf.template']),
                  ('/etc/init.d', ['install/sysvinit/airtime-media-monitor']),
                  ('/etc/airtime', ['install/media_monitor_logging.cfg']),
                  ('/var/log/airtime/media-monitor', []),
                  ('/var/tmp/airtime/media-monitor', []),
                 ]
    print data_files

setup(name='airtime-media-monitor',
      version='1.0',
      description='Airtime Media Monitor',
      url='http://github.com/sourcefabric/Airtime',
      author='sourcefabric',
      license='AGPLv3',
      packages=['media_monitor', 'mm2', 'mm2.configs', 
                'mm2.media', 'mm2.media.monitor', 
                'mm2.media.metadata', 'mm2.media.saas'
                ],
      package_data={'': ['*.cfg']},
      scripts=['bin/airtime-media-monitor'],
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

# Reload the initctl config so that the media-monitor service works
if data_files:
    print "Reloading initctl configuration"
    #call(['initctl', 'reload-configuration'])
    print "Run \"sudo service airtime-media-monitor start\""
