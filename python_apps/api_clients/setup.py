from __future__ import print_function
from setuptools import setup
from subprocess import call
import sys
import os

script_path = os.path.dirname(os.path.realpath(__file__))
print(script_path)
os.chdir(script_path)

setup(name='api_clients',
      version='1.0',
      description='Airtime API Client',
      url='http://github.com/sourcefabric/Airtime',
      author='sourcefabric',
      license='AGPLv3',
      packages=['api_clients'],
      scripts=[],
      install_requires=[
#           'amqplib',
#           'anyjson',
#           'argparse',
        'configobj'
#           'docopt',
#           'kombu',
#           'mutagen',
#           'poster3',
#           'PyDispatcher',
#           'pyinotify',
#           'pytz',
      ],
      zip_safe=False,
      data_files=[])
