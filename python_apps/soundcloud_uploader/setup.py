from setuptools import setup
from subprocess import call
import os

data_files = [('/etc/default', ['install/conf/soundcloud_uploader']),
              ('/etc/init.d', ['install/upstart/soundcloud_uploader'])]
print data_files

setup(name='soundcloud_uploader',
      version='0.1',
      description='Celery SoundCloud upload worker',
      url='http://github.com/sourcefabric/Airtime',
      author='Sourcefabric',
      author_email='duncan.sommerville@sourcefabric.org',
      license='MIT',
      packages=['soundcloud_uploader'],
      scripts=['bin/soundcloud_uploader'],
      install_requires=[
          'soundcloud',
          'celery',
          'kombu'
      ],
      zip_safe=False,
      data_files=data_files)

if data_files:
    print "Reloading initctl configuration"
    call(['initctl', 'reload-configuration'])
    # Make /etc/init.d file executable and set proper
    # permissions for the defaults config file
    os.chmod('/etc/init.d/soundcloud_uploader', 0755)
    os.chmod('/etc/default/soundcloud_uploader', 0640)
    print "Setting uploader to start on boot"
    call(['update-rc.d', 'soundcloud_uploader', 'defaults'])
    print "Run \"sudo service soundcloud_uploader restart\" now."