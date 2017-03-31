from setuptools import setup
from subprocess import call
import sys
import os

# Change directory since setuptools uses relative paths
script_path = os.path.dirname(os.path.realpath(__file__))
print script_path
os.chdir(script_path)

# Allows us to avoid installing the upstart init script when deploying airtime_analyzer
# on Airtime Pro:
if '--no-init-script' in sys.argv:
    data_files = []
    sys.argv.remove('--no-init-script') # super hax
else:
    data_files = [('/etc/init', ['install/upstart/airtime_analyzer.conf']),
                  ('/etc/init.d', ['install/sysvinit/airtime_analyzer'])]
    print data_files

setup(name='libretime_watch',
      version='0.1',
      description='Libretime Watch existing directory',
      url='http://github.com/libretime/libretime',
      author='HaJoHe',
      author_email='rmi@chef.net',
      license='MIT',
      packages=['libretime_watch'],
      scripts=['bin/libretime_watch'],
      install_requires=[
          'mutagen==1.31', # The Mutagen guys change stuff all the time that break our unit tests. Watch out for this.
          'pika',
          'codecs',
          'pyscopg2', # database
          'jason'
      ],
      zip_safe=False,
      data_files=data_files)

# Remind users to reload the initctl config so that "service start airtime_analyzer" works
if data_files:
    print "Remember to reload the initctl configuration"
    print "Run \"sudo initctl reload-configuration; sudo service libretime_watch restart\" now."
