from setuptools import setup

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
          'python-magic',
          'pika',
          'nose',
          'python-daemon',
          'requests',
      ],
      zip_safe=False)
