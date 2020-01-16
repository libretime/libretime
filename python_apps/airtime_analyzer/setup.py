from __future__ import print_function
from setuptools import setup
from subprocess import call
import sys
import os

# Change directory since setuptools uses relative paths
script_path = os.path.dirname(os.path.realpath(__file__))
print(script_path)
os.chdir(script_path)

# Allows us to avoid installing the upstart init script when deploying airtime_analyzer
# on Airtime Pro:
if '--no-init-script' in sys.argv:
    data_files = []
    sys.argv.remove('--no-init-script') # super hax
else:
    data_files = [('/etc/init', ['install/upstart/airtime_analyzer.conf']),
                  ('/etc/init.d', ['install/sysvinit/airtime_analyzer'])]
    print(data_files)

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
          'mutagen>=1.41.1', # got rid of specific version requirement 
          'pika',
          'daemon',
          'file-magic',
          'nose',
          'coverage',
          'mock',
          'python-daemon==1.6',
          'requests>=2.7.0',
          'rgain3',
          # These next 3 are required for requests to support SSL with SNI. Learned this the hard way...
          # What sucks is that GCC is required to pip install these. 
          #'ndg-httpsclient',
          #'pyasn1',
          #'pyopenssl'
      ],
      zip_safe=False,
      data_files=data_files)

# Remind users to reload the initctl config so that "service start airtime_analyzer" works
if data_files:
    print("Remember to reload the initctl configuration")
    print("Run \"sudo initctl reload-configuration; sudo service airtime_analyzer restart\" now.")
    print("Or on Ubuntu Xenial (16.04)")
    print("Remember to reload the systemd configuration")
    print("Run \"sudo systemctl daemon-reload; sudo service airtime_analyzer restart\" now.")
