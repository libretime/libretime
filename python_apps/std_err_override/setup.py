from setuptools import setup
from subprocess import call
import sys
import os

script_path = os.path.dirname(os.path.realpath(__file__))
print script_path
os.chdir(script_path)

setup(name='std_err_override',
      version='1.0',
      description='Airtime Log Writer',
      url='http://github.com/sourcefabric/Airtime',
      author='sourcefabric',
      license='AGPLv3',
      packages=['std_err_override'],
      scripts=[],
      install_requires=[],
      zip_safe=False,
      data_files=[])