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
    data_files = [('/etc/init', ['install/upstart/airtime_analyzer.conf'])]
    print data_files

setup(name='airtime_analyzer',
      version='0.1',
      description='Airtime Analyzer Worker and File Importer',
      url='http://github.com/sourcefabric/Airtime',
      author='Albert Santoni',
      author_email='albert.santoni@sourcefabric.org',
      license='MIT',
      packages=['airtime_analyzer'],
      scripts=['bin/airtime_analyzer'],
      install_requires=[
          'mutagen',
          'pika',
          'daemon',
          'python-magic',
          'nose',
          'coverage',
          'mock',
          'python-daemon==1.6',
          'requests>=2.7.0',
          'apache-libcloud',
          'rgain',
          'boto',
          # These next 3 are required for requests to support SSL with SNI. Learned this the hard way...
          # What sucks is that GCC is required to pip install these. 
          #'ndg-httpsclient',
          #'pyasn1',
          #'pyopenssl'
      ],
      zip_safe=False,
      data_files=data_files)

# Reload the initctl config so that "service start airtime_analyzer" works
if data_files:
    print "Reloading initctl configuration"
    call(['initctl', 'reload-configuration'])
    print "Run \"sudo service airtime_analyzer restart\" now."


# TODO: Should we start the analyzer here or not?
